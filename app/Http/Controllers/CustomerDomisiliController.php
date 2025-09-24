<?php

namespace App\Http\Controllers;

use App\Models\BusinessArea;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Kota;
use App\Models\Provinsi;
use App\Models\SobatCustomerDomicile;
use App\Models\SobatCustomerKTP;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class CustomerDomisiliController extends Controller
{
    public function index(Request $request, $id)
    {
        $userId = Crypt::decrypt($id);
        $query = SobatCustomerDomicile::query()
            ->where('user_id','=', $userId)
            ->leftJoin('provinsi', 'domisili.kode_provinsi', '=', 'provinsi.kode_provinsi')
            ->leftJoin('kota', 'domisili.kode_kota', '=', 'kota.kode_kota')
            ->leftJoin('kecamatan', 'domisili.kode_kecamatan', '=', 'kecamatan.kode_kecamatan')
            ->leftJoin('kelurahan', 'domisili.kode_kelurahan', '=', 'kelurahan.kode_kelurahan')
            ->select(
                'domisili.*',
                'provinsi.nama_provinsi AS nama_provinsi',
                'kota.nama_kota AS nama_kota',
                'kecamatan.nama_kecamatan AS nama_kecamatan',
                'kelurahan.nama_kelurahan AS nama_kelurahan',
            );

        if ($request->filled('search_query')) {
            $query->where('user_id', $request->search_query)
                    ->orWhere('alamat', 'LIKE', '%'.$request->search_query.'%')
                    ->orWhere('kode_provinsi', $request->search_query)
                    ->orWhere('kode_kota', $request->search_query)
                    ->orWhere('kode_kecamatan', $request->search_query)
                    ->orWhere('kode_kelurahan', $request->search_query)
                    ->orWhere('kode_pos', 'LIKE', '%'.$request->search_query.'%')
                    ->orWhere('latitude', 'LIKE', '%'.$request->search_query.'%')
                    ->orWhere('longitude', 'LIKE', '%'.$request->search_query.'%')
                    ->orWhere('status', $request->search_query);
        }

        $customerDomiciles = $query->orderBy('user_id')->paginate(10);
        $customerDomiciles->appends($request->all());

        // 🔹 Get Active KTP filename
        $activeDomicileFile = SobatCustomerDomicile::where('user_id', $userId)
            ->where('status', 'Active')
            ->value('image_rumah');

        $activeDomicileImage = null;
        if ($activeDomicileFile) {
            $baseUrl = rtrim(env('SOBAT_USER_DOCUMENT_IMAGE_BASE_URL'), '/');

            // Example: SB-000123_KTP.jpeg
            $fileParts = explode('.', $activeDomicileFile);
            $filenameWithoutExt = $fileParts[0]; // SB-000123_KTP
            $extension = $fileParts[1] ?? '';    // jpeg

            // Extract user folder (SB-000123)
            $userFolder = explode('_', $filenameWithoutExt)[0];

            // Build full image URL
            $activeDomicileImage = "{$baseUrl}/{$userFolder}/{$filenameWithoutExt}.{$extension}";
        }
        
        return view('sobat.customer.domisili.index', compact('customerDomiciles', 'activeDomicileImage'));
    }

    // ----- CREATE -----
    public function create()
    {
        return view('sobat.customer.create');
    }

    public function store(Request $request)
    {
        // catatan: koneksi model SobatCustomerKTP sudah mysqlsobat
        $request->validate([
            'fullname'        => ['required','string','max:255','unique:mysqlsobat.customers,fullname'],
            'customer_description' => ['nullable','string'],
            'status'            => ['required','in:Active,Inactive,active,inactive,1,0,Y,N,true,false'],
            'customer_image_file'  => ['nullable','file','mimes:jpeg,jpg,png,webp,gif,jfif','max:5120'], // 5MB
        ]);

        $data = [
            'fullname'        => $request->fullname,
            'customer_description' => $request->customer_description,
            'status'            => $this->normalizeStatus($request->status),
            'customer_image'       => null,
        ];

        // upload file (opsional)
        if ($request->hasFile('customer_image_file')) {
            $filename = $this->uploadToSobat($request->file('customer_image_file'), $data['fullname'], true);
            if (!$filename) {
                return back()->withInput()->with('error', 'Upload gagal. Cek log untuk detail.');
            }
            $data['customer_image'] = $filename;
        }

        SobatCustomerKTP::create($data);

        return redirect()->route('customer.index')->with('success', 'customer berhasil ditambahkan.');
    }

    // ----- EDIT -----
    public function edit($id, $domisiliId)
    {
        $domisiliId = Crypt::decrypt($domisiliId);
        $customerDomicile = SobatCustomerDomicile::findOrFail($domisiliId);

        $provinsi = Provinsi::orderBy('nama_provinsi')->get();

        return view('sobat.customer.domisili.edit', compact(
            'customerDomicile',
            'provinsi'
        ));
    }
    public function update(Request $request, $customerId, $domisiliId)
    {
        $domisiliId = Crypt::decrypt($domisiliId);
        $domisili   = SobatCustomerDomicile::findOrFail($domisiliId);

        $request->validate([
            'alamat'         => 'required|string|max:500',
            'kode_provinsi'  => 'required|string|exists:mysqlsobat.provinsi,kode_provinsi',
            'kode_kota'      => 'required|string|exists:mysqlsobat.kota,kode_kota',
            'kode_kecamatan' => 'required|string|exists:mysqlsobat.kecamatan,kode_kecamatan',
            'kode_kelurahan' => 'required|string|exists:mysqlsobat.kelurahan,kode_kelurahan',
            'kode_pos'       => 'required|string|max:10',
            'longitude'      => 'nullable|numeric',
            'latitude'       => 'nullable|numeric',
            'status'         => 'required|in:Active,Inactive',
        ]);

        $domisili->update([
            'alamat'         => $request->alamat,
            'kode_provinsi'  => $request->kode_provinsi,
            'kode_kota'      => $request->kode_kota,
            'kode_kecamatan' => $request->kode_kecamatan,
            'kode_kelurahan' => $request->kode_kelurahan,
            'kode_pos'       => $request->kode_pos,
            'longitude'      => $request->longitude,
            'latitude'       => $request->latitude,
            'status'         => $request->status,
        ]);

        return redirect()
            ->route('customer.domisili.index', $customerId)
            ->with('success', 'Domisili Customer berhasil diupdate.');
    }


    public function show($id)
    {
        $id = Crypt::decrypt($id);
        $customer = SobatCustomerKTP::findOrFail($id);
        return view('sobat.customer.show', compact('customer'));
    }
}
