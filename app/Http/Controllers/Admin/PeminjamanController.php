<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Perpanjangan;
use App\Models\BukuItem;
use App\Models\Buku;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    // REPLACE index() method in PeminjamanController.php

    public function index(Request $request)
    {
        $query = Peminjaman::with(['member', 'bukuItem.buku', 'officer', 'pengembalian', 'perpanjangans']);

        // ✅ ENHANCED SEARCH: member, judul buku, barcode, petugas
        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                // Search by member name
                $q->whereHas('member', fn($subQ) => $subQ->where('name', 'like', "%$search%"))
                    // Search by buku judul
                    ->orWhereHas('bukuItem.buku', fn($subQ) => $subQ->where('judul', 'like', "%$search%"))
                    // Search by barcode
                    ->orWhereHas('bukuItem', fn($subQ) => $subQ->where('barcode', 'like', "%$search%"))
                    // Search by officer name
                    ->orWhereHas('officer', fn($subQ) => $subQ->where('name', 'like', "%$search%"));
            });
        }

        // ✅ FILTER 1: Status (Dipinjam / Dikembalikan)
        if ($status = $request->status) {
            $query->where('status', $status);
        }

        // ✅ FILTER 2: Tanggal (pinjam/due/kembali di tanggal ini)
        if ($tanggal = $request->tanggal) {
            $query->where(function ($q) use ($tanggal) {
                // Tanggal pinjam
                $q->whereDate('tanggal_pinjam', $tanggal)
                    // Due date
                    ->orWhereDate('tanggal_kembali_rencana', $tanggal)
                    // Tanggal kembali aktual
                    ->orWhereHas('pengembalian', fn($subQ) => $subQ->whereDate('tanggal_kembali_aktual', $tanggal));
            });
        }

        // ✅ FILTER 3: Status Keterlambatan (hanya untuk Dipinjam)
        if ($keterlambatan = $request->keterlambatan) {
            $query->where('status', 'Dipinjam');

            if ($keterlambatan === 'tepat_waktu') {
                // Due date >= today
                $query->whereDate('tanggal_kembali_rencana', '>=', now()->toDateString());
            } elseif ($keterlambatan === 'telat') {
                // Due date < today (sudah melewati due date)
                $query->whereDate('tanggal_kembali_rencana', '<', now()->toDateString());
            }
        }

        $peminjamans = $query->latest('id')->paginate(10);

        if ($request->ajax()) {
            return view('admin.peminjamans.partials.rows', compact('peminjamans'));
        }

        return view('admin.peminjamans.index', compact('peminjamans'));
    }

    /**
     * Get single peminjaman data (for extend modal)
     */
    public function show($id)
    {
        try {
            $peminjaman = Peminjaman::with(['member', 'bukuItem.buku', 'officer', 'pengembalian', 'perpanjangans'])
                ->findOrFail($id);

            return response()->json([
                'id' => $peminjaman->id,
                'id_member' => $peminjaman->id_member,
                'member_name' => $peminjaman->member->name ?? 'N/A',
                'buku_judul' => $peminjaman->bukuItem->buku->judul ?? 'N/A',
                'barcode' => $peminjaman->bukuItem->barcode ?? 'N/A',
                'tanggal_pinjam' => $peminjaman->tanggal_pinjam->format('Y-m-d'),
                'tanggal_kembali_rencana' => $peminjaman->tanggal_kembali_rencana->format('Y-m-d'),
                'status' => $peminjaman->status,
                'catatan' => $peminjaman->catatan,
            ]);

        } catch (\Exception $e) {
            \Log::error('Show peminjaman error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * DataTables serverside untuk daftar member dengan info pinjaman aktif
     */
    public function searchMemberDatatable(Request $request)
    {
        try {
            \Log::info('searchMemberDatatable called', ['request' => $request->all()]);

            $query = User::where('role', 'Member');

            // Search global (name or email)
            if ($request->has('search') && isset($request->search['value']) && $request->search['value'] != '') {
                $search = $request->search['value'];
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Count total
            $totalRecords = User::where('role', 'Member')->count();
            $filteredRecords = $query->count();

            // Order
            if ($request->has('order') && count($request->order) > 0) {
                $orderColIndex = $request->order[0]['column'];

                if (isset($request->columns[$orderColIndex])) {
                    $orderCol = $request->columns[$orderColIndex]['data'];
                    $orderDir = $request->order[0]['dir'];

                    $allowedColumns = ['id', 'name', 'email'];
                    if (in_array($orderCol, $allowedColumns)) {
                        $query->orderBy($orderCol, $orderDir);
                    }
                }
            } else {
                $query->orderBy('id', 'desc');
            }

            // Pagination
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            $members = $query->skip($start)->take($length)->get();

            // Map data dengan info pinjaman aktif
            $data = $members->map(function ($member) {
                // Hitung pinjaman aktif (status = 'Dipinjam')
                $pinjamanAktif = Peminjaman::where('id_member', $member->id)
                    ->where('status', 'Dipinjam')
                    ->count();

                // Status: Available jika pinjaman < 2, Full jika = 2
                $status = 'Available';
                $statusColor = 'success';
                if ($pinjamanAktif >= 2) {
                    $status = 'Full (Max)';
                    $statusColor = 'danger';
                } elseif ($pinjamanAktif == 1) {
                    $status = 'Available (1 slot)';
                    $statusColor = 'warning';
                }

                $canBorrow = $pinjamanAktif < 2;

                return [
                    'id' => $member->id,
                    'name' => $member->name ?? 'N/A',
                    'email' => $member->email ?? 'N/A',
                    'pinjaman_aktif' => $pinjamanAktif,
                    'status' => $status,
                    'status_color' => $statusColor,
                    'can_borrow' => $canBorrow,
                ];
            });

            $response = [
                'draw' => intval($request->input('draw', 1)),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data->toArray()
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            \Log::error('searchMemberDatatable error: ' . $e->getMessage());

            return response()->json([
                'draw' => intval($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check apakah member eligible untuk meminjam (max 2 eksemplar aktif)
     */
    public function checkMemberEligibility($memberId)
    {
        try {
            $member = User::findOrFail($memberId);

            if ($member->role !== 'Member') {
                return response()->json([
                    'eligible' => false,
                    'message' => 'User bukan Member'
                ], 400);
            }

            $pinjamanAktif = Peminjaman::where('id_member', $memberId)
                ->where('status', 'Dipinjam')
                ->count();

            $eligible = $pinjamanAktif < 2;
            $remainingSlots = max(0, 2 - $pinjamanAktif);

            return response()->json([
                'eligible' => $eligible,
                'pinjaman_aktif' => $pinjamanAktif,
                'remaining_slots' => $remainingSlots,
                'max_allowed' => 2,
                'message' => $eligible
                    ? "Member dapat meminjam (sisa slot: $remainingSlots)"
                    : "Member sudah mencapai batas maksimal peminjaman (2 eksemplar)"
            ]);

        } catch (\Exception $e) {
            \Log::error('checkMemberEligibility error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * DataTables serverside untuk daftar buku (hanya yang punya eksemplar tersedia untuk dipinjam)
     */
    public function searchBukuDatatable(Request $request)
    {
        try {
            \Log::info('searchBukuDatatable (Peminjaman) called', ['request' => $request->all()]);

            $query = Buku::query();

            // ✅ CRITICAL: Hanya buku yang punya eksemplar dengan kondisi "Baik" DAN status "Tersedia"
            $query->whereHas('items', function($q) {
                $q->where('kondisi', 'Baik')
                    ->where('status', 'Tersedia');
            });

            // Search global (judul or pengarang)
            if ($request->has('search') && isset($request->search['value']) && $request->search['value'] != '') {
                $search = $request->search['value'];
                $query->where(function ($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                        ->orWhere('pengarang', 'like', "%{$search}%");
                });
            }

            // Filter tahun_terbit (column 3)
            if ($request->has('columns') &&
                isset($request->columns[3]) &&
                isset($request->columns[3]['search']['value']) &&
                $request->columns[3]['search']['value'] != '') {
                $tahun = $request->columns[3]['search']['value'];
                $query->where('tahun_terbit', $tahun);
            }

            // Count total
            $totalRecords = Buku::whereHas('items', function($q) {
                $q->where('kondisi', 'Baik')->where('status', 'Tersedia');
            })->count();

            $filteredRecords = $query->count();

            // Order
            if ($request->has('order') && count($request->order) > 0) {
                $orderColIndex = $request->order[0]['column'];

                if (isset($request->columns[$orderColIndex])) {
                    $orderCol = $request->columns[$orderColIndex]['data'];
                    $orderDir = $request->order[0]['dir'];

                    $allowedColumns = ['id', 'judul', 'pengarang', 'tahun_terbit'];
                    if (in_array($orderCol, $allowedColumns)) {
                        $query->orderBy($orderCol, $orderDir);
                    }
                }
            } else {
                $query->orderBy('id', 'desc');
            }

            // Pagination
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            $bukus = $query->skip($start)->take($length)->get();

            // Map data
            $data = $bukus->map(function ($buku) {
                // Hitung eksemplar yang kondisi Baik DAN status Tersedia
                $eksemplarTersedia = BukuItem::where('id_buku', $buku->id)
                    ->where('kondisi', 'Baik')
                    ->where('status', 'Tersedia')
                    ->count();

                return [
                    'id' => $buku->id,
                    'judul' => $buku->judul ?? 'N/A',
                    'pengarang' => $buku->pengarang ?? 'N/A',
                    'tahun_terbit' => $buku->tahun_terbit ?? 'N/A',
                    'eksemplar_tersedia' => $eksemplarTersedia,
                ];
            });

            $response = [
                'draw' => intval($request->input('draw', 1)),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data->toArray()
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            \Log::error('searchBukuDatatable (Peminjaman) error: ' . $e->getMessage());

            return response()->json([
                'draw' => intval($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available eksemplar untuk buku tertentu (kondisi Baik + status Tersedia)
     */
    public function availableEksemplarByBuku($id_buku)
    {
        try {
            // ✅ CRITICAL: Hanya eksemplar dengan kondisi "Baik" DAN status "Tersedia"
            $availableItems = BukuItem::where('id_buku', $id_buku)
                ->where('kondisi', 'Baik')
                ->where('status', 'Tersedia')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'barcode' => $item->barcode,
                        'kondisi' => $item->kondisi,
                        'status' => $item->status,
                        'sumber' => $item->sumber ?? 'N/A'
                    ];
                });

            return response()->json($availableItems);

        } catch (\Exception $e) {
            \Log::error('availableEksemplarByBuku error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_member' => 'required|exists:users,id',
                'id_buku_items' => 'required|array|min:1',
                'id_buku_items.*' => 'exists:buku_items,id',
                'tanggal_pinjam' => 'required|date',
                'tanggal_kembali_rencana' => 'required|date|after_or_equal:tanggal_pinjam',
                'catatan' => 'nullable|string',
            ]);

            // ✅ VALIDATION 1: Member role check
            $member = User::findOrFail($validated['id_member']);
            if ($member->role !== 'Member') {
                return response()->json(['error' => 'User bukan Member'], 400);
            }

            // ✅ VALIDATION 2: Check member eligibility (max 2 pinjaman aktif)
            $pinjamanAktif = Peminjaman::where('id_member', $validated['id_member'])
                ->where('status', 'Dipinjam')
                ->count();

            $totalNewPinjam = count($validated['id_buku_items']);
            $totalAfter = $pinjamanAktif + $totalNewPinjam;

            if ($totalAfter > 2) {
                return response()->json([
                    'error' => "Member sudah memiliki {$pinjamanAktif} pinjaman aktif. Tidak bisa meminjam {$totalNewPinjam} eksemplar lagi (max 2 total)"
                ], 400);
            }

            // ✅ VALIDATION 3: Tanggal pinjam harus hari ini
            $today = Carbon::today()->toDateString();
            if ($validated['tanggal_pinjam'] !== $today) {
                return response()->json([
                    'error' => 'Tanggal pinjam harus hari ini (' . Carbon::today()->format('d/m/Y') . ')'
                ], 400);
            }

            // ✅ VALIDATION 4: Max 7 hari periode peminjaman
            $tanggalPinjam = Carbon::parse($validated['tanggal_pinjam']);
            $tanggalKembali = Carbon::parse($validated['tanggal_kembali_rencana']);
            $diffDays = $tanggalPinjam->diffInDays($tanggalKembali);

            if ($diffDays > 7) {
                return response()->json([
                    'error' => "Periode peminjaman maksimal 7 hari. Anda pilih {$diffDays} hari."
                ], 400);
            }

            if ($diffDays < 1) {
                return response()->json([
                    'error' => 'Tanggal kembali minimal 1 hari dari tanggal pinjam.'
                ], 400);
            }

            // ✅ VALIDATION 5: All buku_items available (kondisi Baik + status Tersedia)
            $unavailableItems = BukuItem::whereIn('id', $validated['id_buku_items'])
                ->where(function($q) {
                    $q->where('kondisi', '!=', 'Baik')
                        ->orWhere('status', '!=', 'Tersedia');
                })
                ->pluck('barcode')
                ->toArray();

            if (!empty($unavailableItems)) {
                return response()->json([
                    'error' => 'Beberapa eksemplar tidak tersedia: ' . implode(', ', $unavailableItems)
                ], 400);
            }

            $officerId = Auth::id();
            $created = [];

            // Create peminjaman records
            foreach ($validated['id_buku_items'] as $itemId) {
                $peminjaman = Peminjaman::create([
                    'id_member' => $validated['id_member'],
                    'id_buku_item' => $itemId,
                    'id_officer' => $officerId,
                    'tanggal_pinjam' => $validated['tanggal_pinjam'],
                    'tanggal_kembali_rencana' => $validated['tanggal_kembali_rencana'],
                    'status' => 'Dipinjam',
                    'catatan' => $validated['catatan'] ?? null,
                ]);

                // Update buku_item status (trigger handles this, but explicit)
                BukuItem::where('id', $itemId)->update(['status' => 'Dipinjam']);

                $created[] = $peminjaman;
            }

            return response()->json([
                'message' => count($created) . ' peminjaman berhasil dibuat! Periode: ' . $diffDays . ' hari.',
                'peminjamans' => $created
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Peminjaman store error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
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
        $daysLate = max(0, $returnDate->diffInDays($dueDate, false));
        $daysLate = abs($daysLate);

        if ($returnDate->gt($dueDate)) {
            $dendaTelat = $daysLate * 1000;
        } else {
            $dendaTelat = 0;
        }

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

        // Status handled by trigger
        return response()->json(['message' => 'Pengembalian berhasil!'], 200);
    }

    public function extendUpdate(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_peminjaman' => 'required|exists:peminjamans,id',
                'tanggal_kembali_rencana_baru' => 'required|date',
                'biaya' => 'numeric|min:0',
                'catatan' => 'nullable|string',
            ]);

            $peminjaman = Peminjaman::with('perpanjangans')->findOrFail($validated['id_peminjaman']);

            // ✅ VALIDATION 1: Peminjaman harus masih aktif
            if ($peminjaman->status !== 'Dipinjam') {
                return response()->json([
                    'error' => 'Peminjaman ini tidak aktif (status: ' . $peminjaman->status . ')'
                ], 400);
            }

            // ✅ VALIDATION 2: Max 1x perpanjangan per periode
            $perpanjanganCount = $peminjaman->perpanjangans()->count();
            if ($perpanjanganCount >= 1) {
                return response()->json([
                    'error' => 'Peminjaman ini sudah diperpanjang 1x. Tidak bisa perpanjang lagi pada periode ini.'
                ], 400);
            }

            $today = Carbon::today();
            $newDueDate = Carbon::parse($validated['tanggal_kembali_rencana_baru']);

            // ✅ VALIDATION 3: Tanggal baru harus setelah hari ini
            if ($newDueDate->lte($today)) {
                return response()->json([
                    'error' => 'Tanggal perpanjangan harus setelah hari ini'
                ], 400);
            }

            $oldDueDate = Carbon::parse($peminjaman->tanggal_kembali_rencana);
            $maxAllowedDate = $oldDueDate->copy()->addDays(5);

            // ✅ VALIDATION 4: Max 5 hari dari due date lama
            if ($newDueDate->gt($maxAllowedDate)) {
                return response()->json([
                    'error' => 'Perpanjangan maksimal 5 hari dari due date lama (' .
                        $oldDueDate->format('d/m/Y') . '). Max: ' .
                        $maxAllowedDate->format('d/m/Y')
                ], 400);
            }

            // ✅ CALCULATE DENDA: Jika perpanjang setelah due date
            $daysLate = 0;
            $dendaKeterlambatan = 0;

            if ($today->gt($oldDueDate)) {
                $daysLate = $today->diffInDays($oldDueDate);
                $dendaKeterlambatan = $daysLate * 1000; // Rp 1.000/hari
            }

            // Create perpanjangan record
            $perpanjangan = Perpanjangan::create([
                'id_peminjaman' => $peminjaman->id,
                'id_officer' => Auth::id(),
                'tanggal_perpanjangan' => $today->toDateString(),
                'due_date_lama' => $peminjaman->tanggal_kembali_rencana,
                'due_date_baru' => $validated['tanggal_kembali_rencana_baru'],
                'biaya' => $dendaKeterlambatan,
                'catatan' => $validated['catatan'] .
                    ($dendaKeterlambatan > 0 ?
                        " | Denda keterlambatan: Rp " . number_format($dendaKeterlambatan, 0, ',', '.') .
                        " ({$daysLate} hari x Rp 1.000)" : ''),
            ]);

            // Update peminjaman due date (trigger will handle, but explicit is better)
            $peminjaman->update([
                'tanggal_kembali_rencana' => $validated['tanggal_kembali_rencana_baru']
            ]);

            return response()->json([
                'message' => 'Perpanjangan berhasil!' .
                    ($dendaKeterlambatan > 0 ?
                        ' Denda keterlambatan: Rp ' . number_format($dendaKeterlambatan, 0, ',', '.') : ''),
                'perpanjangan' => $perpanjangan,
                'denda' => $dendaKeterlambatan,
                'days_late' => $daysLate,
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Perpanjangan error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

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

            $peminjamans = Peminjaman::whereIn('id', $ids)->get();

            foreach ($peminjamans as $peminjaman) {
                if ($peminjaman->status == 'Dipinjam') {
                    BukuItem::where('id', $peminjaman->id_buku_item)
                        ->update(['status' => 'Tersedia']);
                }

                $peminjaman->pengembalian()->delete();
                $peminjaman->perpanjangans()->delete();
            }

            Peminjaman::whereIn('id', $ids)->delete();

            return response()->json([
                'message' => count($ids) . ' peminjaman berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            \Log::error('destroySelected error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
