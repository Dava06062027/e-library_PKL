<?php

// app/Http/Controllers/RakController.php

namespace App\Http\Controllers;

use App\Models\Rak;
use App\Models\LokasiRak;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RakController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $raks = Rak::with(['lokasi', 'kategori'])
            ->when($request->search, function($query) use ($request) {
                $query->where('nama', 'like', '%' . $request->search . '%')
                    ->orWhere('barcode', 'like', '%' . $request->search . '%');
            })
            ->when($request->lokasi, function($query) use ($request) {
                $query->where('id_lokasi', $request->lokasi);
            })
            ->when($request->kategori, function($query) use ($request) {
                $query->where('id_kategori', $request->kategori);
            })
            ->Paginate(10)
            ->withQueryString();

        $lokasis = LokasiRak::all();
        $kategoris = Kategori::all();

        if ($request->ajax()) {
            return view('raks.partials.rows', compact('raks'));
        }

        return view('raks.index', compact('raks', 'lokasis', 'kategoris'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'barcode' => 'required|string|max:50|unique:raks,barcode',
            'nama' => 'required|string|max:100',
            'kolom' => 'required|integer|min:1',
            'baris' => 'required|integer|min:1',
            'kapasitas' => 'required|integer|min:1',
            'id_lokasi' => 'required|exists:lokasi_raks,id',
            'id_kategori' => 'required|exists:kategoris,id',
        ]);

        $rak = Rak::create($validated);

        return response()->json([
            'message' => 'Rak created successfully',
            'rak' => $rak
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $rak = Rak::with(['lokasi', 'kategori'])->findOrFail($id);
        return response()->json($rak);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rak = Rak::findOrFail($id);

        $validated = $request->validate([
            'barcode' => 'required|string|max:50|unique:raks,barcode,' . $id,
            'nama' => 'required|string|max:100',
            'kolom' => 'required|integer|min:1',
            'baris' => 'required|integer|min:1',
            'kapasitas' => 'required|integer|min:1',
            'id_lokasi' => 'required|exists:lokasi_raks,id',
            'id_kategori' => 'required|exists:kategoris,id',
        ]);

        $rak->update($validated);

        return response()->json([
            'message' => 'Rak updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $rak = Rak::findOrFail($id);
        $rak->delete();

        return response()->json([
            'message' => 'Rak deleted successfully'
        ]);
    }

    /**
     * Delete selected raks
     */
    public function destroySelected(Request $request)
    {
        $ids = $request->json('ids');

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'No valid IDs provided'], 400);
        }

        // Log untuk debug: Cek IDs apa yang masuk
        Log::info('Attempting to delete Raks with IDs: ' . json_encode($ids));

        // Delete pakai whereIn (aman, tidak lempar exception jika tidak ada match)
        $deletedCount = Rak::whereIn('id', $ids)->delete();

        if ($deletedCount === 0) {
            // Jika tidak ada yang di-delete (IDs invalid), return custom message tanpa exception
            return response()->json(['message' => 'No Raks found to delete'], 404);
        }

        return response()->json(['message' => 'Selected raks deleted successfully']);
    }
}
