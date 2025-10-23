<?php

namespace App\Http\Controllers;

use App\Models\SobatUom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;

class ProductUomController extends Controller
{
    /**
     * Display a listing of UOMs.
     */
    public function index(Request $request)
    {
        $query = SobatUom::query();

        if ($request->filled('search_query')) {
            $search = $request->search_query;
            $query->where('uom_name', 'like', "%{$search}%")
                  ->orWhere('uom_description', 'like', "%{$search}%");
        }

        $uoms = $query->orderBy('uom_name', 'asc')->paginate(10);

        return view('sobat.product.uom.index', compact('uoms'));
    }

    /**
     * Show the form for creating a new UOM.
     */
    public function create()
    {
        return view('sobat.product.uom.create');
    }

    /**
     * Store a newly created UOM.
     */
    public function store(Request $request)
    {
        $request->validate([
            'uom_name'        => ['required','string','max:100','unique:mysqlsobat.uoms,uom_name'],
            'uom_description' => ['nullable','string','max:255'],
        ]);

        SobatUom::create([
            'uom_name'        => $request->uom_name,
            'uom_description' => $request->uom_description,
        ]);

        return redirect()->route('product.uom.index')->with('success', 'UOM berhasil ditambahkan.');
    }

    /**
     * Show the form for editing a UOM.
     */
    public function edit($id)
    {
        $id = Crypt::decrypt($id);
        $uom = SobatUom::findOrFail($id);

        return view('sobat.product.uom.edit', compact('uom'));
    }

    /**
     * Update the specified UOM.
     */
    public function update(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $uom = SobatUom::findOrFail($id);

        $request->validate([
            'uom_name'        => ['required','string','max:100', Rule::unique('mysqlsobat.uoms','uom_name')->ignore($uom->id)],
            'uom_description' => ['nullable','string','max:255'],
        ]);

        $uom->update([
            'uom_name'        => $request->uom_name,
            'uom_description' => $request->uom_description,
        ]);

        return redirect()->route('product.uom.index')->with('success', 'UOM berhasil diperbarui.');
    }

    /**
     * Remove the specified UOM.
     */
    public function destroy($id)
    {
        $uom = SobatUom::findOrFail($id);
        $uom->delete();

        return redirect()->route('product.uom.index')->with('success', 'UOM berhasil dihapus.');
    }
}