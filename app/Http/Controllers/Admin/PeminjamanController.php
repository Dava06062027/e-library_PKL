<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Perpanjangan;
use App\Models\BukuItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $query = Peminjaman::with(['member', 'bukuItem.buku', 'officer', 'pengembalian']);

        // Search
        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('member', fn($q) => $q->where('name', 'like', "%$search%"))
                    ->orWhereHas('bukuItem', fn($q) => $q->where('barcode', 'like', "%$search%"));
            });
        }

        // Filter
        if ($status = $request->status) {
            $query->where('status', $status);
        }
        if ($role = $request->role) {
            $query->whereHas('member', fn($q) => $q->where('role', $role));
        }

        $peminjamans = $query->latest()->paginate(10);

        if ($request->ajax()) {
            return view('admin.peminjamans.partials.rows', compact('peminjamans'));
        }

        return view('admin.peminjamans.index', compact('peminjamans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_member' => 'required|exists:users,id',
            'id_buku_items' => 'required|array', // Untuk bulk, misal
            'id_buku_items.*' => 'exists:buku_items,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali_rencana' => 'required|date|after_or_equal:tanggal_pinjam',
        ]);

        $officerId = Auth::id();

        // Untuk bulk peminjaman
        $created = [];
        foreach ($validated['id_buku_items'] as $itemId) {
            $peminjaman = Peminjaman::create([
                'id_member' => $validated['id_member'],
                'id_buku_item' => $itemId,
                'id_officer' => $officerId,
                'tanggal_pinjam' => $validated['tanggal_pinjam'],
                'tanggal_kembali_rencana' => $validated['tanggal_kembali_rencana'],
            ]);
            // Update status buku (trigger DB handle, atau manual)
            BukuItem::find($itemId)->update(['status' => 'Dipinjam']);
            $created[] = $peminjaman;
        }

        return response()->json(['message' => 'Peminjaman berhasil dibuat!'], 200);
    }

    public function returnStore(Request $request)
    {
        $validated = $request->validate([
            'id_peminjaman' => 'required|exists:peminjamans,id',
            'tanggal_kembali_aktual' => 'required|date',
            'kondisi_kembali' => 'required|in:Baik,Cukup,Rusak,Hilang',
            'denda_kerusakan' => 'numeric|min:0',
            'catatan' => 'nullable|string',
        ]);

        $peminjaman = Peminjaman::findOrFail($validated['id_peminjaman']);

        // Hitung denda telat
        $dueDate = Carbon::parse($peminjaman->tanggal_kembali_rencana);
        $returnDate = Carbon::parse($validated['tanggal_kembali_aktual']);
        $daysLate = max(0, $returnDate->diffInDays($dueDate));
        $dendaTelat = $daysLate * 1000; // Misal Rp1000/hari, adjust

        $totalDenda = $dendaTelat + ($validated['denda_kerusakan'] ?? 0);

        $pengembalian = Pengembalian::create([
            'id_peminjaman' => $peminjaman->id,
            'id_officer' => Auth::id(),
            'tanggal_kembali_aktual' => $validated['tanggal_kembali_aktual'],
            'denda_keterlambatan' => $dendaTelat,
            'denda_kerusakan' => $validated['denda_kerusakan'] ?? 0,
            'total_denda' => $totalDenda,
            'kondisi_kembali' => $validated['kondisi_kembali'],
            'catatan' => $validated['catatan'],
        ]);

        // Update status peminjaman dan buku (trigger DB handle, atau manual)
        $newStatus = $daysLate > 0 ? 'Telat' : 'Dikembalikan';
        if (in_array($validated['kondisi_kembali'], ['Rusak', 'Hilang'])) {
            $newStatus = $validated['kondisi_kembali'];
        }
        $peminjaman->update(['status' => $newStatus]);
        $peminjaman->bukuItem->update([
            'status' => in_array($validated['kondisi_kembali'], ['Rusak', 'Hilang']) ? $validated['kondisi_kembali'] : 'Tersedia',
            'kondisi' => $validated['kondisi_kembali'],
        ]);

        return response()->json(['message' => 'Pengembalian berhasil!'], 200);
    }

    public function extendUpdate(Request $request)
    {
        $validated = $request->validate([
            'id_peminjaman' => 'required|exists:peminjamans,id',
            'tanggal_kembali_rencana_baru' => 'required|date|after:tanggal_kembali_rencana', // Assume dari peminjaman
            'biaya' => 'numeric|min:0',
            'catatan' => 'nullable|string',
        ]);

        $peminjaman = Peminjaman::findOrFail($validated['id_peminjaman']);

        // Buat riwayat perpanjangan (jika pakai tabel)
        Perpanjangan::create([
            'id_peminjaman' => $peminjaman->id,
            'id_officer' => Auth::id(),
            'tanggal_perpanjangan' => now(),
            'due_date_lama' => $peminjaman->tanggal_kembali_rencana,
            'due_date_baru' => $validated['tanggal_kembali_rencana_baru'],
            'biaya' => $validated['biaya'] ?? 0,
            'catatan' => $validated['catatan'],
        ]);

        // Update due date
        $peminjaman->update(['tanggal_kembali_rencana' => $validated['tanggal_kembali_rencana_baru']]);

        return response()->json(['message' => 'Perpanjangan berhasil!'], 200);
    }

    // Tambah method lain jika perlu, misal edit, delete, atau API untuk select buku
    public function getBukus(Request $request)
    {
        // Untuk modal select buku, mirip tataraks
        $query = Buku::withCount(['bukuItems' => fn($q) => $q->where('status', 'Tersedia')]);

        if ($search = $request->search) {
            $query->where('judul', 'like', "%$search%")->orWhere('pengarang', 'like', "%$search%");
        }
        if ($tahun = $request->tahun) {
            $query->where('tahun_terbit', $tahun);
        }

        return $query->paginate(10);
    }

    public function getEksemplars($bukuId)
    {
        return BukuItem::where('id_buku', $bukuId)->where('status', 'Tersedia')->get();
    }
}
