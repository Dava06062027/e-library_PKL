<?php

// app/Http/Controllers/PenerbitController.php

namespace App\Http\Controllers;

use App\Models\Penerbit;
use Illuminate\Http\Request;

class PenerbitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $penerbits = Penerbit::query()
            ->when($request->search, function($query) use ($request) {
                $query->where('nama', 'like', '%' . $request->search . '%')
                    ->orWhere('alamat', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            })
            ->Paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('penerbits.partials.rows', compact('penerbits'));
        }

        return view('penerbits.index', compact('penerbits'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:penerbits,nama',
            'alamat' => 'nullable|string|max:255',
            'no_telepon' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
        ]);

        $penerbit = Penerbit::create($validated);

        return response()->json([
            'message' => 'Penerbit created successfully',
            'penerbit' => $penerbit
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $penerbit = Penerbit::findOrFail($id);
        return response()->json($penerbit);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $penerbit = Penerbit::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:penerbits,nama,' . $id,
            'alamat' => 'nullable|string|max:255',
            'no_telepon' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
        ]);

        $penerbit->update($validated);

        return response()->json([
            'message' => 'Penerbit updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $penerbit = Penerbit::findOrFail($id);
        $penerbit->delete();

        return response()->json([
            'message' => 'Penerbit deleted successfully'
        ]);
    }

    /**
     * Delete selected penerbits
     */
    public function destroySelected(Request $request)
    {
        $ids = $request->json('ids');
        Penerbit::whereIn('id', $ids)->delete();

        return response()->json([
            'message' => 'Selected penerbits deleted successfully'
        ]);
    }
}
