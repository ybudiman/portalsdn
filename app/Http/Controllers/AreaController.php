<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Kota;
use Illuminate\Http\Request;

class AreaController extends Controller
{
   public function getKota($provinsiCode)
    {
        $kota = Kota::where('kode_provinsi', $provinsiCode)
                    ->orderBy('nama_kota')
                    ->get(['kode_kota', 'nama_kota']); // only what frontend needs

        return response()->json($kota);
    }


    public function getKecamatan($kotaCode)
    {
        $kecamatan = Kecamatan::where('kode_kota', $kotaCode)
                            ->orderBy('nama_kecamatan')
                            ->get(['kode_kecamatan', 'nama_kecamatan']);
        return response()->json($kecamatan);
    }

    public function getKelurahan($kecamatanCode)
    {
        $kelurahan = Kelurahan::where('kode_kecamatan', $kecamatanCode)
                            ->orderBy('nama_kelurahan')
                            ->get(['kode_kelurahan', 'nama_kelurahan']);
        return response()->json($kelurahan);
    }


}   
