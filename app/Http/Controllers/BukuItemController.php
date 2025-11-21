<?php

namespace App\Http\Controllers;

use App\Models\BukuItem;
use App\Models\Buku;
use App\Models\Rak;
use Illuminate\Http\Request;

class BukuItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $items = BukuItem::with(['buku', 'rak'])
            ->when($request->search, function($query) use ($request) {
                $query->where('barcode', 'like', '%' . $request->search . '%');
            })
            ->when($request->kondisi, function($query) use ($request) {
                $query->where('kondisi', $request->kondisi);
            })
            ->when($request->status, function($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->buku, function($query) use ($request) {
                $query->where('id_buku', $request->buku);
            })
            ->when($request->rak, function($query) use ($request) {
                $query->where('id_rak', $request->rak);
            })
            ->Paginate(10)
            ->withQueryString();

        $bukus = Buku::all();
        $raks = Rak::all();

        if ($request->ajax()) {
            return view('buku-items.partials.rows', compact('items'));
        }

        return view('buku-items.index', compact('items', 'bukus', 'raks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_buku' => 'required|exists:bukus,id',
            'kondisi' => 'required|in:Baik,Rusak,Hilang',
            'status' => 'required|in:Tersedia,Dipinjam,Reparasi',
            'sumber' => 'required|in:Hibah,Beli',
            'id_rak' => 'nullable|exists:raks,id',
            // No barcode validation, assuming trigger handles it
        ]);

        $item = BukuItem::create($validated);

        return response()->json([
            'message' => 'Buku Item created successfully',
            'item' => $item
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = BukuItem::with(['buku', 'rak'])->findOrFail($id);
        return response()->json($item);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = BukuItem::findOrFail($id);

        $validated = $request->validate([
            'id_buku' => 'required|exists:bukus,id',
            'kondisi' => 'required|in:Baik,Rusak,Hilang',
            'status' => 'required|in:Tersedia,Dipinjam,Reparasi',
            'sumber' => 'required|in:Hibah,Beli',
            'id_rak' => 'nullable|exists:raks,id',
            // No barcode validation, don't touch it
        ]);

        $item->update($validated);

        return response()->json([
            'message' => 'Buku Item updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = BukuItem::findOrFail($id);
        $item->delete();

        return response()->json([
            'message' => 'Buku Item deleted successfully'
        ]);
    }

    /**
     * Delete selected items
     */
    public function destroySelected(Request $request)
    {
        $ids = $request->json('ids');
        BukuItem::whereIn('id', $ids)->delete();

        return response()->json([
            'message' => 'Selected buku items deleted successfully'
        ]);
    }

    // Existing method from previous
    public function searchByBuku($id_buku)
    {
        $items = BukuItem::with('rak')->where('id_buku', $id_buku)->get();
        return response()->json($items);
    }

    public function searchByRak($id_rak)
    {
        $items = BukuItem::with(['buku', 'rak'])->where('id_rak', $id_rak)->get();
        return response()->json($items);
    }
}
