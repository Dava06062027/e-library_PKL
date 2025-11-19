<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BukuItem;
use App\Models\Rak;
use App\Models\Tatarak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TataraksController extends Controller
{
    const MAX_BOOKS_PER_CELL = 10; // Maksimal 10 buku per cell

    public function index(Request $request)
    {
        $query = Tatarak::with(['bukuItem.buku', 'rak.lokasi', 'user']);

        if ($request->has('q') && $request->q !== '') {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->whereHas('bukuItem', function ($subQ) use ($search) {
                    $subQ->where('barcode', 'like', "%{$search}%")
                        ->orWhereHas('buku', function($bukuQ) use ($search) {
                            $bukuQ->where('judul', 'like', "%{$search}%");
                        });
                })->orWhereHas('user', function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->has('rak') && $request->rak !== '') {
            $query->where('id_rak', $request->rak);
        }

        if ($request->has('role') && $request->role !== '') {
            $role = $request->role;
            $query->whereHas('user', function($q) use ($role) {
                $q->where('role', $role);
            });
        }

        $tataraks = $query->latest()->paginate(15);

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
        return response()->json($tatarak->load(['bukuItem.buku', 'rak.lokasi', 'user']));
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

        // Validasi kapasitas cell
        $cellCount = Tatarak::where('id_rak', $validated['id_rak'])
            ->where('kolom', $validated['kolom'])
            ->where('baris', $validated['baris'])
            ->count();

        if ($cellCount >= self::MAX_BOOKS_PER_CELL) {
            return response()->json([
                'error' => "Cell sudah penuh (maksimal " . self::MAX_BOOKS_PER_CELL . " eksemplar)"
            ], 400);
        }

        // Validasi: Satu baris hanya untuk 1 judul
        $bukuItem = BukuItem::with('buku')->find($validated['id_buku_item']);
        $existingInRow = Tatarak::where('id_rak', $validated['id_rak'])
            ->where('baris', $validated['baris'])
            ->with('bukuItem.buku')
            ->get();

        if ($existingInRow->isNotEmpty()) {
            $existingJudul = $existingInRow->first()->bukuItem->buku->judul;
            if ($existingJudul !== $bukuItem->buku->judul) {
                return response()->json([
                    'error' => "Baris {$validated['baris']} sudah berisi buku dengan judul '{$existingJudul}'. Gunakan baris lain untuk judul berbeda."
                ], 400);
            }
        }

        $tatarak = Tatarak::create($validated);
        BukuItem::where('id', $validated['id_buku_item'])->update(['id_rak' => $validated['id_rak']]);

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

        // Validasi kapasitas cell (exclude current record)
        $cellCount = Tatarak::where('id_rak', $validated['id_rak'])
            ->where('kolom', $validated['kolom'])
            ->where('baris', $validated['baris'])
            ->where('id', '!=', $tatarak->id)
            ->count();

        if ($cellCount >= self::MAX_BOOKS_PER_CELL) {
            return response()->json([
                'error' => "Cell sudah penuh (maksimal " . self::MAX_BOOKS_PER_CELL . " eksemplar)"
            ], 400);
        }

        // Validasi: Satu baris hanya untuk 1 judul
        $bukuItem = BukuItem::with('buku')->find($validated['id_buku_item']);
        $existingInRow = Tatarak::where('id_rak', $validated['id_rak'])
            ->where('baris', $validated['baris'])
            ->where('id', '!=', $tatarak->id)
            ->with('bukuItem.buku')
            ->get();

        if ($existingInRow->isNotEmpty()) {
            $existingJudul = $existingInRow->first()->bukuItem->buku->judul;
            if ($existingJudul !== $bukuItem->buku->judul) {
                return response()->json([
                    'error' => "Baris {$validated['baris']} sudah berisi buku dengan judul '{$existingJudul}'. Gunakan baris lain untuk judul berbeda."
                ], 400);
            }
        }

        if ($tatarak->id_buku_item !== $validated['id_buku_item']) {
            BukuItem::where('id', $tatarak->id_buku_item)->update(['id_rak' => null]);
        }

        $tatarak->update($validated);
        BukuItem::where('id', $validated['id_buku_item'])->update(['id_rak' => $validated['id_rak']]);

        return response()->json(['message' => 'Penataan berhasil diperbarui']);
    }

    public function destroy(Tatarak $tatarak)
    {
        if (Auth::user()->role !== 'Admin') {
            return response()->json(['error' => 'Hanya Admin yang bisa hapus transaksi'], 403);
        }

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

        $tataraks = Tatarak::whereIn('id', $ids)->get();
        $bukuItemIds = $tataraks->pluck('id_buku_item')->toArray();

        BukuItem::whereIn('id', $bukuItemIds)->update(['id_rak' => null]);
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

    public function availableEksemplarByBuku($id_buku)
    {
        $availableItems = BukuItem::where('id_buku', $id_buku)
            ->whereNull('id_rak')
            ->where('status', 'Tersedia')
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

    public function getBukuKategori($id_buku)
    {
        $buku = \App\Models\Buku::findOrFail($id_buku);

        return response()->json([
            'id_kategori' => $buku->id_kategori,
            'kategori_nama' => $buku->kategori->nama ?? 'N/A'
        ]);
    }

    public function getRakByKategori(Request $request)
    {
        $query = Rak::with('kategori');

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
        ]);

        $userId = Auth::id();
        $rak = Rak::find($validated['id_rak']);
        $bukuItems = BukuItem::with('buku')->whereIn('id', $validated['id_buku_items'])->get();

        // Validasi: Semua eksemplar harus dari judul yang sama
        $judulSet = $bukuItems->pluck('buku.judul')->unique();
        if ($judulSet->count() > 1) {
            return response()->json([
                'error' => 'Semua eksemplar harus dari judul buku yang sama. Judul ditemukan: ' . $judulSet->implode(', ')
            ], 400);
        }

        $judul = $judulSet->first();

        // Validasi: Pastikan belum ditata
        $alreadyTataed = BukuItem::whereIn('id', $validated['id_buku_items'])
            ->whereNotNull('id_rak')
            ->pluck('barcode')
            ->toArray();

        if (!empty($alreadyTataed)) {
            return response()->json([
                'error' => 'Beberapa eksemplar sudah ditata: ' . implode(', ', $alreadyTataed)
            ], 400);
        }

        // Cari baris kosong atau baris dengan judul yang sama
        $targetBaris = $this->findAvailableRow($rak->id, $judul, count($validated['id_buku_items']));

        if (!$targetBaris) {
            return response()->json([
                'error' => 'Tidak ada baris yang tersedia di rak ini untuk menampung ' . count($validated['id_buku_items']) . ' eksemplar'
            ], 400);
        }

        $created = [];
        $currentKolom = 1;
        $currentBaris = $targetBaris['start_baris'];

        DB::beginTransaction();
        try {
            foreach ($validated['id_buku_items'] as $idBukuItem) {
                // Cek apakah cell sudah penuh
                $cellCount = Tatarak::where('id_rak', $rak->id)
                    ->where('kolom', $currentKolom)
                    ->where('baris', $currentBaris)
                    ->count();

                // Jika cell penuh, pindah ke kolom berikutnya
                if ($cellCount >= self::MAX_BOOKS_PER_CELL) {
                    $currentKolom++;

                    // Jika kolom habis, pindah ke baris berikutnya
                    if ($currentKolom > $rak->kolom) {
                        $currentKolom = 1;
                        $currentBaris++;

                        // Validasi: Baris baru harus masih dalam ukuran rak
                        if ($currentBaris > $rak->baris) {
                            throw new \Exception('Rak tidak cukup untuk menampung semua eksemplar');
                        }
                    }
                }

                // Buat record tatarak
                $tatarak = Tatarak::create([
                    'id_buku_item' => $idBukuItem,
                    'id_rak' => $rak->id,
                    'kolom' => $currentKolom,
                    'baris' => $currentBaris,
                    'id_user' => $userId,
                ]);
                $created[] = $tatarak;

                // Update id_rak di BukuItem
                BukuItem::where('id', $idBukuItem)->update(['id_rak' => $rak->id]);
            }

            DB::commit();

            return response()->json([
                'message' => count($created) . ' eksemplar berhasil ditata di baris ' . $targetBaris['start_baris'],
                'tataraks' => $created
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Mencari baris yang tersedia untuk menampung eksemplar dengan judul tertentu
     *
     * @param int $rakId
     * @param string $judul
     * @param int $jumlahEksemplar
     * @return array|null ['start_baris' => int, 'available_space' => int]
     */
    private function findAvailableRow($rakId, $judul, $jumlahEksemplar)
    {
        $rak = Rak::find($rakId);

        // Hitung berapa cell yang dibutuhkan
        $cellsNeeded = ceil($jumlahEksemplar / self::MAX_BOOKS_PER_CELL);

        // Cek setiap baris
        for ($baris = 1; $baris <= $rak->baris; $baris++) {
            // Ambil semua tatarak di baris ini
            $tataraksInRow = Tatarak::where('id_rak', $rakId)
                ->where('baris', $baris)
                ->with('bukuItem.buku')
                ->get();

            // Jika baris kosong, bisa digunakan
            if ($tataraksInRow->isEmpty()) {
                return [
                    'start_baris' => $baris,
                    'available_space' => $rak->kolom * self::MAX_BOOKS_PER_CELL
                ];
            }

            // Cek apakah baris ini berisi judul yang sama
            $existingJudul = $tataraksInRow->first()->bukuItem->buku->judul;
            if ($existingJudul !== $judul) {
                continue; // Skip baris ini karena judulnya berbeda
            }

            // Hitung sisa kapasitas di baris ini
            $usedCells = 0;
            for ($kolom = 1; $kolom <= $rak->kolom; $kolom++) {
                $cellCount = Tatarak::where('id_rak', $rakId)
                    ->where('kolom', $kolom)
                    ->where('baris', $baris)
                    ->count();

                if ($cellCount > 0) {
                    $usedCells++;
                }
            }

            $availableCells = $rak->kolom - $usedCells;
            $availableSpace = ($availableCells * self::MAX_BOOKS_PER_CELL);

            // Tambahkan sisa ruang di cell yang belum penuh
            foreach ($tataraksInRow as $tatarak) {
                $cellCount = Tatarak::where('id_rak', $rakId)
                    ->where('kolom', $tatarak->kolom)
                    ->where('baris', $tatarak->baris)
                    ->count();

                $availableSpace += (self::MAX_BOOKS_PER_CELL - $cellCount);
            }

            if ($availableSpace >= $jumlahEksemplar) {
                return [
                    'start_baris' => $baris,
                    'available_space' => $availableSpace
                ];
            }
        }

        return null; // Tidak ada baris yang tersedia
    }

    public function searchBukuDatatable(Request $request)
    {
        $query = \App\Models\Buku::query();

        // Filter: Hanya buku yang punya eksemplar belum ditata
        $query->whereHas('items', function($q) {
            $q->whereNull('id_rak')
                ->where('status', 'Tersedia');
        });

        // Global search
        if ($request->has('search') && !empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('pengarang', 'like', "%{$search}%");
            });
        }

        // Filter tahun_terbit
        if ($request->has('columns') && isset($request->columns[3]['search']['value']) && $request->columns[3]['search']['value']) {
            $tahun = $request->columns[3]['search']['value'];
            $query->where('tahun_terbit', $tahun);
        }

        $total = $query->count();

        // Ordering
        if ($request->has('order') && !empty($request->order)) {
            $orderColIndex = $request->order[0]['column'];
            $orderCol = $request->columns[$orderColIndex]['data'] ?? 'id';
            $orderDir = $request->order[0]['dir'] ?? 'asc';

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

        return response()->json([
            'draw' => intval($request->input('draw', 1)),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data
        ]);
    }
}
