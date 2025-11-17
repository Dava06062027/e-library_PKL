<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BukuItem;
use App\Models\Rak;
use App\Models\Tatarak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TataraksController extends Controller
{
    public function index(Request $request)
    {
        $query = Tatarak::with(['bukuItem.buku', 'rak', 'user']);

        // Search by barcode or user name
        if ($request->has('q') && $request->q !== '') {
            $search = $request->q;
            $query->whereHas('bukuItem', function ($q) use ($search) {
                $q->where('barcode', 'like', "%{$search}%");
            })->orWhereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Filter by rak
        if ($request->has('rak') && $request->rak !== '') {
            $query->where('id_rak', $request->rak);
        }

        $tataraks = $query->paginate(15);

        // ✅ LOGGING UNTUK DEBUG
        \Log::info('Tataraks Query', [
            'count' => $tataraks->count(),
            'total' => $tataraks->total(),
            'first_item' => $tataraks->first()
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'rows' => view('admin.tataraks.partials.rows', compact('tataraks'))->render(),
                'pagination' => view('admin.tataraks.partials.pagination', compact('tataraks'))->render(),
                'total' => $tataraks->total()
            ]);
        }

        return view('admin.tataraks.index', compact('tataraks'));
    }

    public function show(Tatarak $tatarak)
    {
        return response()->json($tatarak->load(['bukuItem.buku', 'rak', 'user']));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_buku_item' => 'required|exists:buku_items,id',
            'id_rak' => 'required|exists:raks,id',
            'kolom' => 'required|integer|min:1',
            'baris' => 'required|integer|min:1',
        ]);

        $validated['id_user'] = Auth::id();

        $tatarak = Tatarak::create($validated);

        return response()->json(['message' => 'Penataan berhasil dicatat', 'tatarak' => $tatarak]);
    }

    public function update(Request $request, Tatarak $tatarak)
    {
        if (Auth::user()->role === 'Officer' && $tatarak->id_user !== Auth::id()) {
            return response()->json(['error' => 'Anda hanya bisa edit transaksi sendiri'], 403);
        }

        $validated = $request->validate([
            'id_buku_item' => 'required|exists:buku_items,id',
            'id_rak' => 'required|exists:raks,id',
            'kolom' => 'required|integer|min:1',
            'baris' => 'required|integer|min:1',
        ]);

        // Jika buku_item berubah, kembalikan id_rak lama ke NULL
        if ($tatarak->id_buku_item !== $validated['id_buku_item']) {
            BukuItem::where('id', $tatarak->id_buku_item)->update(['id_rak' => null]);
        }

        // Update tatarak
        $tatarak->update($validated);

        // Update id_rak di BukuItem baru
        BukuItem::where('id', $validated['id_buku_item'])->update(['id_rak' => $validated['id_rak']]);

        return response()->json(['message' => 'Penataan berhasil diperbarui']);
    }

    public function destroy(Tatarak $tatarak)
    {
        if (Auth::user()->role !== 'Admin') {
            return response()->json(['error' => 'Hanya Admin yang bisa hapus transaksi'], 403);
        }

        // Kembalikan id_rak di BukuItem menjadi NULL
        BukuItem::where('id', $tatarak->id_buku_item)->update(['id_rak' => null]);

        $tatarak->delete();

        return response()->json(['message' => 'Penataan dihapus dan eksemplar dikembalikan']);
    }

    public function destroySelected(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['error' => 'Tidak ada yang dipilih'], 400);
        }

        if (Auth::user()->role !== 'Admin') {
            return response()->json(['error' => 'Hanya Admin yang bisa hapus transaksi'], 403);
        }

        // Ambil id_buku_item dari tatarak yang akan dihapus
        $tataraks = Tatarak::whereIn('id', $ids)->get();
        $bukuItemIds = $tataraks->pluck('id_buku_item')->toArray();

        // Kembalikan id_rak menjadi NULL
        BukuItem::whereIn('id', $bukuItemIds)->update(['id_rak' => null]);

        // Hapus tatarak
        Tatarak::whereIn('id', $ids)->delete();

        return response()->json(['message' => count($ids) . ' penataan dihapus dan eksemplar dikembalikan']);
    }

    public function availableItems()
    {
        $tataedIds = Tatarak::pluck('id_buku_item')->toArray();
        $availableItems = BukuItem::with('buku')
            ->whereNotIn('id', $tataedIds)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'barcode' => $item->barcode,
                    'buku_judul' => $item->buku->judul
                ];
            });

        return response()->json($availableItems);
    }


    /**
     * Get available eksemplar untuk buku tertentu (belum ditata)
     */
    public function availableEksemplarByBuku($id_buku)
    {
        // Ambil eksemplar dari buku ini yang belum ditata (id_rak masih NULL) dan statusnya "Tersedia"
        $availableItems = BukuItem::where('id_buku', $id_buku)
            ->whereNull('id_rak') // Yang belum ditata
            ->where('status', 'Tersedia') // Hanya yang tersedia
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'barcode' => $item->barcode,
                    'kondisi' => $item->kondisi,
                    'status' => $item->status,
                    'sumber' => $item->sumber
                ];
            });

        return response()->json($availableItems);
    }

    /**
     * Get kategori dari buku tertentu
     */
    public function getBukuKategori($id_buku)
    {
        $buku = \App\Models\Buku::findOrFail($id_buku);

        return response()->json([
            'id_kategori' => $buku->id_kategori,
            'kategori_nama' => $buku->kategori->nama ?? 'N/A'
        ]);
    }

    /**
     * Get rak berdasarkan kategori
     */
    public function getRakByKategori(Request $request)
    {
        $query = Rak::with('kategori');

        // Filter berdasarkan kategori jika ada
        if ($request->has('kategoris') && $request->kategoris) {
            $kategoriIds = explode(',', $request->kategoris);
            $query->whereIn('id_kategori', $kategoriIds);
        }

        $raks = $query->get()->map(function($rak) {
            return [
                'id' => $rak->id,
                'nama' => $rak->nama,
                'kolom' => $rak->kolom,
                'baris' => $rak->baris,
                'kapasitas' => $rak->kapasitas,
                'kategori_nama' => $rak->kategori->nama ?? 'N/A'
            ];
        });

        return response()->json($raks);
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'id_buku_items' => 'required|array|min:1',
            'id_buku_items.*' => 'required|exists:buku_items,id',
            'id_rak' => 'required|exists:raks,id',
            'positions' => 'required|array',
            'positions.*.kolom' => 'required|integer|min:1',
            'positions.*.baris' => 'required|integer|min:1',
        ]);

        $userId = Auth::id();
        $created = [];
        $rak = Rak::find($validated['id_rak']);

        // Cek kapasitas rak global
        if (count($validated['id_buku_items']) > $rak->kapasitas) {
            return response()->json(['error' => 'Jumlah item melebihi kapasitas rak'], 400);
        }

        // Validasi: Pastikan semua buku_items belum ditata (id_rak masih NULL)
        $alreadyTataed = BukuItem::whereIn('id', $validated['id_buku_items'])
            ->whereNotNull('id_rak')
            ->pluck('barcode')
            ->toArray();

        if (!empty($alreadyTataed)) {
            return response()->json([
                'error' => 'Beberapa eksemplar sudah ditata: ' . implode(', ', $alreadyTataed)
            ], 400);
        }

        foreach ($validated['id_buku_items'] as $index => $idBukuItem) {
            $position = $validated['positions'][$index];

            // Cek posisi overflow
            if ($position['kolom'] > $rak->kolom || $position['baris'] > $rak->baris) {
                return response()->json([
                    'error' => "Posisi {$position['kolom']}/{$position['baris']} melebihi ukuran rak"
                ], 400);
            }

            // Cek overlap posisi
            $existing = Tatarak::where('id_rak', $validated['id_rak'])
                ->where('kolom', $position['kolom'])
                ->where('baris', $position['baris'])
                ->exists();

            if ($existing) {
                return response()->json([
                    'error' => "Posisi {$position['kolom']}/{$position['baris']} sudah terisi"
                ], 400);
            }

            // Buat record tatarak
            $tatarak = Tatarak::create([
                'id_buku_item' => $idBukuItem,
                'id_rak' => $validated['id_rak'],
                'kolom' => $position['kolom'],
                'baris' => $position['baris'],
                'id_user' => $userId,
            ]);
            $created[] = $tatarak;

            // Update id_rak di BukuItem
            BukuItem::where('id', $idBukuItem)->update(['id_rak' => $validated['id_rak']]);
        }

        return response()->json([
            'message' => count($created) . ' eksemplar berhasil ditata',
            'tataraks' => $created
        ]);
    }


    public function searchBukuDatatable(Request $request)
    {
        $query = \App\Models\Buku::query();

        // FILTER: Hanya tampilkan buku yang masih punya eksemplar belum ditata (id_rak = NULL)
        $query->whereHas('items', function($q) {
            $q->whereNull('id_rak')
                ->where('status', 'Tersedia');
        });

        // Search global (judul or pengarang)
        if ($request->has('search') && !empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('pengarang', 'like', "%{$search}%");
            });
        }

        // Filter tahun_terbit (column 3 di table)
        if ($request->has('columns') && isset($request->columns[3]['search']['value']) && $request->columns[3]['search']['value']) {
            $tahun = $request->columns[3]['search']['value'];
            $query->where('tahun_terbit', $tahun);
        }

        // Count total BEFORE pagination
        $total = $query->count();

        // Order
        if ($request->has('order') && !empty($request->order)) {
            $orderColIndex = $request->order[0]['column'];
            $orderCol = $request->columns[$orderColIndex]['data'] ?? 'id';
            $orderDir = $request->order[0]['dir'] ?? 'asc';

            // Validasi column name untuk security
            $allowedColumns = ['id', 'judul', 'pengarang', 'tahun_terbit'];
            if (in_array($orderCol, $allowedColumns)) {
                $query->orderBy($orderCol, $orderDir);
            }
        }

        // Pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $bukus = $query->skip($start)->take($length)->get();

        // Map data
        $data = $bukus->map(function ($buku) {
            // Hitung jumlah eksemplar yang belum ditata (id_rak = NULL) dan tersedia
            $eksemplarTersedia = \App\Models\BukuItem::where('id_buku', $buku->id)
                ->whereNull('id_rak')
                ->where('status', 'Tersedia')
                ->count();

            return [
                'id' => $buku->id,
                'judul' => $buku->judul,
                'pengarang' => $buku->pengarang ?? 'N/A',
                'tahun_terbit' => $buku->tahun_terbit ?? 'N/A',
                'eksemplar_tersedia' => $eksemplarTersedia,
            ];
        });

        // Return proper JSON response for DataTables
        return response()->json([
            'draw' => intval($request->input('draw', 1)),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data
        ]);
    }

}


