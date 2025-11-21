<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\SubKategori;
use App\Models\Penerbit;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $bukus = Buku::with(['kategori', 'subKategori', 'penerbit'])
            ->when($request->search, function($query) use ($request) {
                $query->where('judul', 'like', '%' . $request->search . '%')
                    ->orWhere('pengarang', 'like', '%' . $request->search . '%');
            })
            ->when($request->kategori, function($query) use ($request) {
                $query->where('id_kategori', $request->kategori);
            })
            ->when($request->subkategori, function($query) use ($request) {
                $query->where('id_sub_kategori', $request->subkategori);
            })
            ->paginate(10)
            ->withQueryString(); // Preserve query params for pagination

        $kategoris = Kategori::all();
        $subkategoris = SubKategori::all();
        $penerbits = Penerbit::all();

        if ($request->ajax()) {
            return view('bukus.partials.rows', compact('bukus'));
        }

        return view('bukus.index', compact('bukus', 'kategoris', 'subkategoris', 'penerbits'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Not needed since using modal
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'pengarang' => 'required|string|max:255',
            'tahun_terbit' => 'required|integer|min:1900|max:' . (date('Y') + 5),
            'isbn' => 'nullable|string|max:50',
            'barcode' => 'required|string|max:50|unique:bukus,barcode',
            'id_penerbit' => 'required|exists:penerbits,id',
            'id_kategori' => 'required|exists:kategoris,id',
            'id_sub_kategori' => 'required|exists:sub_kategoris,id',
        ]);

        $buku = Buku::create($validated);

        return response()->json([
            'message' => 'Buku created successfully',
            'buku' => $buku
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $buku = Buku::with(['penerbit', 'kategori', 'subKategori'])->findOrFail($id);
        return response()->json($buku);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Not needed since using modal
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $buku = Buku::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'pengarang' => 'required|string|max:255',
            'tahun_terbit' => 'required|integer|min:1900|max:' . (date('Y') + 5),
            'isbn' => 'nullable|string|max:50',
            'barcode' => 'required|string|max:50|unique:bukus,barcode,' . $id,
            'id_penerbit' => 'required|exists:penerbits,id',
            'id_kategori' => 'required|exists:kategoris,id',
            'id_sub_kategori' => 'required|exists:sub_kategoris,id',
        ]);

        $buku->update($validated);

        return response()->json([
            'message' => 'Buku updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $buku = Buku::findOrFail($id);
        $buku->delete();

        return response()->json([
            'message' => 'Buku deleted successfully'
        ]);
    }

    /**
     * Delete selected bukus
     */
    public function destroySelected(Request $request)
    {
        $ids = $request->json('ids');
        if (empty($ids)) {
            return response()->json(['message' => 'No IDs provided'], 400);
        }
        Buku::whereIn('id', $ids)->delete();
        return response()->json(['message' => 'Selected bukus deleted successfully']);
    }

    public function searchByPenerbit($id_penerbit)
    {
        $bukus = Buku::with(['penerbit', 'kategori', 'subKategori'])->where('id_penerbit', $id_penerbit)->get();
        return response()->json($bukus);
    }

    public function searchBySubKategori($id_sub_kategori)
    {
        $bukus = Buku::with(['penerbit', 'kategori', 'subKategori'])->where('id_sub_kategori', $id_sub_kategori)->get();
        return response()->json($bukus);
    }
}
