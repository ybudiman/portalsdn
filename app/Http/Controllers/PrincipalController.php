<?php

namespace App\Http\Controllers;

use App\Models\SobatPrincipal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class PrincipalController extends Controller
{
    // ----- LIST -----
    public function index(Request $request)
    {
        $query = SobatPrincipal::query();

        if ($request->filled('principal_name')) {
            $query->where('principal_name', 'like', '%' . $request->principal_name . '%');
        }

        $principals = $query->orderBy('principal_name')->paginate(10);
        $principals->appends($request->all());

        return view('sobat.principal.index', compact('principals'));
    }

    // ----- CREATE -----
    public function create()
    {
        return view('sobat.principal.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'principal_code' => ['required', 'string', 'max:50', 'unique:mysqlsobat.principals,principal_code'],
            'principal_name' => ['required', 'string', 'max:255', 'unique:mysqlsobat.principals,principal_name'],
            'status'         => ['required', 'in:Active,Inactive,active,inactive,1,0,Y,N,true,false'],
        ]);

        $data = [
            'principal_code' => $request->principal_code,
            'principal_name' => $request->principal_name,
            'status'         => $this->normalizeStatus($request->status),
        ];

        SobatPrincipal::create($data);

        return redirect()->route('principal.index')->with('success', 'Principal berhasil ditambahkan.');
    }

    // ----- EDIT -----
    public function edit($encCode)
    {
        $principal_code = Crypt::decrypt($encCode);
        $principal = SobatPrincipal::where('principal_code', $principal_code)->firstOrFail();

        return view('sobat.principal.edit', compact('principal'));
    }

    public function update(Request $request, $encCode)
    {
        $principalCode = Crypt::decrypt($encCode);
        $principal = SobatPrincipal::where('principal_code', $principalCode)->firstOrFail();

        $request->validate([
            'principal_name' => ['required','string','max:255','unique:mysqlsobat.principals,principal_name,' . $principal->id],
            'status'         => ['nullable','in:Active,Inactive,active,inactive,1,0,Y,N,true,false'],
        ]);

        $principal->principal_name = $request->principal_name;

        if ($request->filled('status')) {
            $principal->status = $this->normalizeStatus($request->status);
        }

        $principal->save();

        return back()->with('success', 'Principal berhasil diupdate.');
    }

    // ----- DELETE -----
    public function destroy($id)
    {
        $principal = SobatPrincipal::find($id);
        if (!$principal) {
            return back()->with('error', 'Data principal tidak ditemukan.');
        }

        $principal->delete();

        return back()->with('success', 'Principal berhasil dihapus.');
    }

    // ----- UTIL -----
    private function normalizeStatus($v): string
    {
        $v = strtolower(trim((string)$v));
        if (in_array($v, ['active','aktif','1','y','true','yes'], true)) return 'Active';
        if (in_array($v, ['inactive','nonaktif','0','n','false','no'], true)) return 'Inactive';
        return 'Active';
    }
}