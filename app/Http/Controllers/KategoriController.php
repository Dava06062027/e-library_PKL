<?php

// app/Http/Controllers/KategoriController.php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $kategoris = Kategori::query()
            ->when($request->search, function($query) use ($request) {
                $query->where('nama', 'like', '%' . $request->search . '%');
            })
            ->Paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('kategoris.partials.rows', compact('kategoris'));
        }

        return view('kategoris.index', compact('kategoris'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:kategoris,nama',
        ]);

        $kategori = Kategori::create($validated);

        return response()->json([
            'message' => 'Kategori created successfully',
            'kategori' => $kategori
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $kategori = Kategori::findOrFail($id);
        return response()->json($kategori);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $kategori = Kategori::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:kategoris,nama,' . $id,
        ]);

        $kategori->update($validated);

        return response()->json([
            'message' => 'Kategori updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->delete();

        return response()->json([
            'message' => 'Kategori deleted successfully'
        ]);
    }

    /**
     * Delete selected kategoris
     */
    public function destroySelected(Request $request)
    {
        $ids = $request->json('ids');
        Kategori::whereIn('id', $ids)->delete();

        return response()->json([
            'message' => 'Selected kategoris deleted successfully'
        ]);
    }
}
