<?php

// app/Http/Controllers/SubKategoriController.php - Update field to 'nama'

namespace App\Http\Controllers;

use App\Models\SubKategori;
use App\Models\Kategori;
use Illuminate\Http\Request;

class SubKategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $subkategoris = SubKategori::with('kategori')
            ->when($request->search, function($query) use ($request) {
                $query->where('nama', 'like', '%' . $request->search . '%');
            })
            ->when($request->kategori, function($query) use ($request) {
                $query->where('id_kategori', $request->kategori);
            })
            ->Paginate(10)
            ->withQueryString();

        $kategoris = Kategori::all();

        if ($request->ajax()) {
            return view('sub_kategoris.partials.rows', compact('subkategoris'));
        }

        return view('sub_kategoris.index', compact('subkategoris', 'kategoris'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:sub_kategoris,nama',
            'id_kategori' => 'required|exists:kategoris,id',
        ]);

        $subkategori = SubKategori::create($validated);

        return response()->json([
            'message' => 'Sub Kategori created successfully',
            'subkategori' => $subkategori
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $subkategori = SubKategori::with('kategori')->findOrFail($id);
        return response()->json($subkategori);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $subkategori = SubKategori::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:sub_kategoris,nama,' . $id,
            'id_kategori' => 'required|exists:kategoris,id',
        ]);

        $subkategori->update($validated);

        return response()->json([
            'message' => 'Sub Kategori updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subkategori = SubKategori::findOrFail($id);
        $subkategori->delete();

        return response()->json([
            'message' => 'Sub Kategori deleted successfully'
        ]);
    }

    /**
     * Delete selected subkategoris
     */
    public function destroySelected(Request $request)
    {
        $ids = $request->json('ids');
        if (empty($ids)) {
            return response()->json(['message' => 'No items selected'], 400);
        }
        SubKategori::whereIn('id', $ids)->delete();

        return response()->json([
            'message' => 'Selected sub kategoris deleted successfully'
        ]);
    }

    /**
     * Search subkategoris by kategori id
     */
    public function searchByKategori($id_kategori)
    {
        $subkategoris = SubKategori::where('id_kategori', $id_kategori)->get();
        return response()->json($subkategoris);
    }
}
