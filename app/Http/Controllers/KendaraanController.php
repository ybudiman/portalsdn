<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;

class KendaraanController extends Controller
{
    public function index(Request $request)
    {

        $query = Kendaraan::query();
        if (!empty($request->nama_kendaraan)) {
            $query->where('nama_kendaraan', 'like', '%' . $request->nama_kendaraan . '%');
        }
        $query->orderBy('kode_kendaraan');
        $kendaraan = $query->paginate(10);
        $kendaraan->appends(request()->all());
        return view('datamaster.kendaraan.index', compact('kendaraan'));
    }
}
