<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\PeminjamanItem;
use App\Models\Perpanjangan;
use App\Models\BukuItem;
use App\Models\Buku;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    // =====================================================
    // INDEX - List all transactions with filters & search
    // =====================================================
    public function index(Request $request)
    {
        $query = Peminjaman::with(['member', 'officer', 'items.bukuItem.buku', 'perpanjangans']);

        // SEARCH: member name, transaction number, book title, barcode
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%$search%")
                    ->orWhereHas('member', fn($subQ) => $subQ->where('name', 'like', "%$search%"))
                    ->orWhereHas('officer', fn($subQ) => $subQ->where('name', 'like', "%$search%"))
                    ->orWhereHas('items.bukuItem.buku', fn($subQ) => $subQ->where('judul', 'like', "%$search%"))
                    ->orWhereHas('items.bukuItem', fn($subQ) => $subQ->where('barcode', 'like', "%$search%"));
            });
        }

        // FILTER: Status Transaksi
        if ($status = $request->input('status')) {
            $query->where('status_transaksi', $status);
        }

        // FILTER: Tanggal (pinjam atau due date)
        if ($tanggal = $request->input('tanggal')) {
            $query->where(function ($q) use ($tanggal) {
                $q->whereDate('tanggal_pinjam', $tanggal)
                    ->orWhereDate('tanggal_kembali_rencana', $tanggal);
            });
        }

        // FILTER: Keterlambatan (only for Dipinjam/Diperpanjang status)
        if ($keterlambatan = $request->input('keterlambatan')) {
            $query->whereIn('status_transaksi', ['Dipinjam', 'Diperpanjang']);

            if ($keterlambatan === 'tepat_waktu') {
                $query->whereDate('tanggal_kembali_rencana', '>=', now()->toDateString());
            } elseif ($keterlambatan === 'telat') {
                $query->whereDate('tanggal_kembali_rencana', '<', now()->toDateString());
            }
        }

        $peminjamans = $query->latest('id')->paginate(10);

        if ($request->ajax()) {
            // Return only the rows HTML for Ajax requests
            $rowsHtml = view('admin.peminjamans.partials.rows', compact('peminjamans'))->render();
            $paginationHtml = view('admin.peminjamans.partials.pagination', compact('peminjamans'))->render();

            return response()->json([
                'rows' => $rowsHtml,
                'pagination' => $paginationHtml
            ]);
        }

        return view('admin.peminjamans.index', compact('peminjamans'));
    }

    // =====================================================
    // SHOW - Get single transaction detail
    // =====================================================
    public function show($id)
    {
        $peminjaman = Peminjaman::with([
            'member',
            'officer',
            'items.bukuItem.buku',
            'perpanjangans.officer'
        ])->findOrFail($id);

        $items = $peminjaman->items->map(function($item) {
            return [
                'id' => $item->id,
                'buku_judul' => $item->bukuItem->buku->judul ?? 'N/A',
                'barcode' => $item->bukuItem->barcode ?? 'N/A',
                'kondisi_pinjam' => $item->kondisi_pinjam,
                'status_item' => $item->status_item,
                'tanggal_kembali_aktual' => $item->tanggal_kembali_aktual ? $item->tanggal_kembali_aktual->format('d M Y') : null,
                'kondisi_kembali' => $item->kondisi_kembali,
                'denda_keterlambatan' => number_format($item->denda_keterlambatan, 0, ',', '.'),
                'denda_kerusakan' => number_format($item->denda_kerusakan, 0, ',', '.'),
                'total_denda_item' => number_format($item->total_denda_item, 0, ',', '.'),
            ];
        });

        $perpanjangans = $peminjaman->perpanjangans->map(function($ext) {
            return [
                'tanggal_perpanjangan' => $ext->tanggal_perpanjangan->format('d M Y'),
                'due_date_lama' => $ext->due_date_lama->format('d M Y'),
                'due_date_baru' => $ext->due_date_baru->format('d M Y'),
                'hari_perpanjangan' => $ext->hari_perpanjangan,
                'biaya' => number_format($ext->biaya, 0, ',', '.'),
                'officer_name' => $ext->officer->name ?? 'N/A',
            ];
        });

        return response()->json([
            'id' => $peminjaman->id,
            'transaction_number' => $peminjaman->transaction_number,
            'member_name' => $peminjaman->member->name ?? 'N/A',
            'member_email' => $peminjaman->member->email ?? 'N/A',
            'officer_name' => $peminjaman->officer->name ?? 'N/A',
            'tanggal_pinjam' => $peminjaman->tanggal_pinjam->format('d M Y'),
            'tanggal_kembali_rencana' => $peminjaman->tanggal_kembali_rencana->format('d M Y'),
            'status_transaksi' => $peminjaman->status_transaksi,
            'total_items' => $peminjaman->total_items,
            'items_dikembalikan' => $peminjaman->items_dikembalikan,
            'total_denda' => number_format($peminjaman->total_denda, 0, ',', '.'),
            'days_late' => $peminjaman->days_late,
            'catatan' => $peminjaman->catatan ?? '-',
            'items' => $items,
            'perpanjangans' => $perpanjangans,
        ]);
    }

    // =====================================================
    // STORE - Create new transaction
    // =====================================================
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_member' => 'required|exists:users,id,role,Member',
                'id_buku_items' => 'required|array|min:1|max:2',
                'id_buku_items.*' => 'required|exists:buku_items,id',
                'tanggal_pinjam' => 'required|date',
                'tanggal_kembali_rencana' => 'required|date|after_or_equal:tanggal_pinjam',
                'catatan' => 'nullable|string|max:500',
            ]);

            DB::beginTransaction();

            // Check member eligibility
            $activeCount = PeminjamanItem::whereHas('peminjaman', function($q) use ($validated) {
                $q->where('id_member', $validated['id_member'])
                    ->whereIn('status_transaksi', ['Dipinjam', 'Diperpanjang']);
            })->where('status_item', 'Dipinjam')->count();

            if ($activeCount + count($validated['id_buku_items']) > 2) {
                throw new \Exception('Member sudah mencapai limit 2 peminjaman aktif!');
            }

            // Check if all items are available
            $unavailableItems = BukuItem::whereIn('id', $validated['id_buku_items'])
                ->where('status', '!=', 'Tersedia')
                ->pluck('barcode');

            if ($unavailableItems->isNotEmpty()) {
                throw new \Exception('Item tidak tersedia: ' . $unavailableItems->implode(', '));
            }

            // Create transaction
            $peminjaman = Peminjaman::create([
                'id_member' => $validated['id_member'],
                'id_officer' => Auth::id(),
                'tanggal_pinjam' => $validated['tanggal_pinjam'],
                'tanggal_kembali_rencana' => $validated['tanggal_kembali_rencana'],
                'status_transaksi' => 'Dipinjam',
                'catatan' => $validated['catatan'] ?? null,
            ]);

            // Create items
            foreach ($validated['id_buku_items'] as $idBukuItem) {
                PeminjamanItem::create([
                    'id_peminjaman' => $peminjaman->id,
                    'id_buku_item' => $idBukuItem,
                    'kondisi_pinjam' => 'Baik',
                    'status_item' => 'Dipinjam',
                ]);

                // Update buku_item status
                BukuItem::where('id', $idBukuItem)->update(['status' => 'Dipinjam']);
            }

            DB::commit();

            return response()->json([
                'message' => 'Peminjaman berhasil dibuat!',
                'transaction_number' => $peminjaman->transaction_number
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Store peminjaman error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // =====================================================
    // RETURN - Process return (partial or full)
    // =====================================================
    public function returnStore(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_peminjaman' => 'required|exists:peminjamans,id',
                'items' => 'required|array|min:1',
                'items.*.id_item' => 'required|exists:peminjaman_items,id',
                'items.*.kondisi_kembali' => 'required|in:Baik,Cukup,Rusak,Hilang',
                'items.*.denda_kerusakan' => 'required|numeric|min:0',
                'tanggal_kembali_aktual' => 'required|date',
                'catatan' => 'nullable|string|max:500',
            ]);

            DB::beginTransaction();

            $peminjaman = Peminjaman::findOrFail($validated['id_peminjaman']);
            $today = Carbon::parse($validated['tanggal_kembali_aktual']);
            $dueDate = Carbon::parse($peminjaman->tanggal_kembali_rencana);

            // Calculate late days
            $daysLate = max(0, $today->diffInDays($dueDate, false) * -1);
            $dendaPerHari = 1000;

            foreach ($validated['items'] as $itemData) {
                $item = PeminjamanItem::findOrFail($itemData['id_item']);

                if ($item->status_item !== 'Dipinjam') {
                    throw new \Exception('Item sudah dikembalikan sebelumnya!');
                }

                $dendaKeterlambatan = $daysLate * $dendaPerHari;
                $dendaKerusakan = $itemData['denda_kerusakan'];
                $totalDendaItem = $dendaKeterlambatan + $dendaKerusakan;

                // Update item
                $item->update([
                    'status_item' => 'Dikembalikan',
                    'tanggal_kembali_aktual' => $validated['tanggal_kembali_aktual'],
                    'kondisi_kembali' => $itemData['kondisi_kembali'],
                    'denda_keterlambatan' => $dendaKeterlambatan,
                    'denda_kerusakan' => $dendaKerusakan,
                    'total_denda_item' => $totalDendaItem,
                    'catatan_pengembalian' => $validated['catatan'],
                ]);

                // Update buku_item status based on condition
                $newStatus = match($itemData['kondisi_kembali']) {
                    'Hilang' => 'Hilang',
                    'Rusak' => 'Reparasi',
                    default => 'Tersedia'
                };

                $newKondisi = match($itemData['kondisi_kembali']) {
                    'Hilang' => 'Hilang',
                    'Rusak' => 'Rusak',
                    'Cukup' => 'Baik',
                    default => 'Baik'
                };

                BukuItem::where('id', $item->id_buku_item)->update([
                    'status' => $newStatus,
                    'kondisi' => $newKondisi
                ]);
            }

            DB::commit();

            // Refresh peminjaman to get updated totals
            $peminjaman->refresh();

            return response()->json([
                'message' => 'Pengembalian berhasil diproses!',
                'total_denda' => $peminjaman->total_denda,
                'days_late' => $daysLate,
                'items_dikembalikan' => $peminjaman->items_dikembalikan,
                'status_transaksi' => $peminjaman->status_transaksi
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Return error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // =====================================================
    // EXTEND - Perpanjangan transaksi
    // =====================================================
    public function extendUpdate(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_peminjaman' => 'required|exists:peminjamans,id',
                'tanggal_kembali_rencana_baru' => 'required|date',
                'catatan' => 'nullable|string|max:500',
            ]);

            DB::beginTransaction();

            $peminjaman = Peminjaman::findOrFail($validated['id_peminjaman']);

            if (!in_array($peminjaman->status_transaksi, ['Dipinjam', 'Diperpanjang'])) {
                throw new \Exception('Hanya transaksi Dipinjam/Diperpanjang yang bisa diperpanjang!');
            }

            $oldDueDate = Carbon::parse($peminjaman->tanggal_kembali_rencana);
            $newDueDate = Carbon::parse($validated['tanggal_kembali_rencana_baru']);
            $today = Carbon::today();

            // Validation: Max 5 days extension
            $extensionDays = $newDueDate->diffInDays($oldDueDate);
            if ($extensionDays > 5) {
                throw new \Exception('Perpanjangan maksimal 5 hari!');
            }

            // Calculate late fee if any
            $daysLate = max(0, $today->diffInDays($oldDueDate, false) * -1);
            $biaya = $daysLate * 1000;

            // Create perpanjangan record
            Perpanjangan::create([
                'id_peminjaman' => $peminjaman->id,
                'id_officer' => Auth::id(),
                'tanggal_perpanjangan' => $today,
                'due_date_lama' => $oldDueDate,
                'due_date_baru' => $newDueDate,
                'hari_perpanjangan' => $extensionDays,
                'biaya' => $biaya,
                'catatan' => $validated['catatan'],
            ]);

            // Update peminjaman due date and status
            $peminjaman->update([
                'tanggal_kembali_rencana' => $newDueDate,
                'status_transaksi' => 'Diperpanjang',
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Perpanjangan berhasil diproses!',
                'new_due_date' => $newDueDate->format('d M Y'),
                'biaya' => $biaya,
                'days_late' => $daysLate
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Extend error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // =====================================================
    // DELETE SELECTED - Bulk delete
    // =====================================================
    public function destroySelected(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return response()->json(['error' => 'Tidak ada yang dipilih'], 400);
            }

            if (Auth::user()->role !== 'Admin') {
                return response()->json(['error' => 'Hanya Admin yang bisa hapus'], 403);
            }

            DB::beginTransaction();

            $peminjamans = Peminjaman::whereIn('id', $ids)->get();

            foreach ($peminjamans as $peminjaman) {
                // Return all items to Tersedia
                foreach ($peminjaman->items as $item) {
                    if ($item->status_item == 'Dipinjam') {
                        BukuItem::where('id', $item->id_buku_item)->update([
                            'status' => 'Tersedia'
                        ]);
                    }
                }

                $peminjaman->delete(); // Cascade will handle items and perpanjangans
            }

            DB::commit();

            return response()->json([
                'message' => count($ids) . ' peminjaman berhasil dihapus'
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Delete error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // =====================================================
    // HELPER METHODS - DataTables & Eligibility
    // =====================================================
    public function searchMemberDatatable(Request $request)
    {
        $query = User::where('role', 'Member');

        if ($search = $request->input('search.value')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        $total = $query->count();

        $members = $query->skip($request->input('start', 0))
            ->take($request->input('length', 10))
            ->get();

        $data = $members->map(function($member) {
            $activeBorrows = PeminjamanItem::whereHas('peminjaman', function($q) use ($member) {
                $q->where('id_member', $member->id)->whereIn('status_transaksi', ['Dipinjam', 'Diperpanjang']);
            })->where('status_item', 'Dipinjam')->count();

            return [
                'id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
                'pinjaman_aktif' => $activeBorrows,
                'can_borrow' => $activeBorrows < 2,
                'status' => $activeBorrows < 2 ? 'Available' : 'Full',
                'status_color' => $activeBorrows < 2 ? 'success' : 'danger',
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data
        ]);
    }

    public function checkMemberEligibility($memberId)
    {
        $activeCount = PeminjamanItem::whereHas('peminjaman', function($q) use ($memberId) {
            $q->where('id_member', $memberId)->whereIn('status_transaksi', ['Dipinjam', 'Diperpanjang']);
        })->where('status_item', 'Dipinjam')->count();

        return response()->json([
            'eligible' => $activeCount < 2,
            'active_borrows' => $activeCount,
            'remaining_slots' => max(0, 2 - $activeCount),
            'message' => $activeCount >= 2 ? 'Member sudah mencapai limit peminjaman!' : 'Member eligible'
        ]);
    }

    public function searchBukuDatatable(Request $request)
    {
        $query = Buku::with(['penerbit', 'kategori']);

        if ($search = $request->input('search.value')) {
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%$search%")
                    ->orWhere('pengarang', 'like', "%$search%");
            });
        }

        // Filter by tahun_terbit if provided (from column search)
        if ($tahun = $request->input('columns.3.search.value')) {
            $query->where('tahun_terbit', $tahun);
        }

        $total = $query->count();

        $bukus = $query->skip($request->input('start', 0))
            ->take($request->input('length', 10))
            ->get();

        $data = $bukus->map(function($buku) {
            $available = BukuItem::where('id_buku', $buku->id)
                ->where('status', 'Tersedia')
                ->where('kondisi', 'Baik')
                ->count();

            return [
                'id' => $buku->id,
                'judul' => $buku->judul,
                'pengarang' => $buku->pengarang,
                'tahun_terbit' => $buku->tahun_terbit,
                'eksemplar_tersedia' => $available,
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data
        ]);
    }

    public function availableEksemplarByBuku($id_buku)
    {
        $items = BukuItem::where('id_buku', $id_buku)
            ->where('status', 'Tersedia')
            ->where('kondisi', 'Baik')
            ->get(['id', 'barcode', 'kondisi', 'status', 'sumber']);

        return response()->json($items);
    }
}
