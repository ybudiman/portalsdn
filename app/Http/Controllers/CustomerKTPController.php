<?php

namespace App\Http\Controllers;

use App\Models\BusinessArea;
use App\Models\Provinsi;
use App\Models\SobatCustomerKTP;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class CustomerKTPController extends Controller
{
    public function index(Request $request, $id)
    {
        $userId = Crypt::decrypt($id);
        $query = SobatCustomerKTP::query()
            ->where('user_id','=', $userId)
            ->leftJoin('provinsi', 'user_ktp.kode_provinsi_ktp', '=', 'provinsi.kode_provinsi')
            ->leftJoin('kota', 'user_ktp.kode_kota_ktp', '=', 'kota.kode_kota')
            ->leftJoin('kecamatan', 'user_ktp.kode_kecamatan', '=', 'kecamatan.kode_kecamatan')
            ->leftJoin('kelurahan', 'user_ktp.kode_kelurahan', '=', 'kelurahan.kode_kelurahan')
            ->select(
                'user_ktp.*',
                'provinsi.nama_provinsi AS nama_provinsi',
                'kota.nama_kota AS nama_kota',
                'kecamatan.nama_kecamatan AS nama_kecamatan',
                'kelurahan.nama_kelurahan AS nama_kelurahan',
            );

        if ($request->filled('search_query')) {
            $query->where('user_id', $request->search_query)
                    ->orWhere('nama', 'LIKE', '%'.$request->search_query.'%')
                    // ->orWhere('kode_provinsi_kota', $request->search_query)
                    // ->orWhere('kode_kota_ktp', $request->search_query)
                    ->orWhere('NIK', 'LIKE', '%'.$request->search_query.'%')
                    ->orWhere('TTL', 'LIKE', '%'.$request->search_query.'%')
                    ->orWhere('alamat', 'LIKE', '%'.$request->search_query.'%')
                    ->orWhere('jenis_kelamin', $request->search_query)
                    // ->orWhere('kode_kelurahan', $request->search_query)
                    ->orWhere('agama', $request->search_query)
                    // ->orWhere('kode_kecamatan', $request->search_query)
                    ->orWhere('status', $request->search_query);
        }

        $customerKTPs = $query->orderBy('nama')->paginate(10);
        $customerKTPs->appends($request->all());

        // 🔹 Get Active KTP filename
        $activeKTPFile = SobatCustomerKTP::where('user_id', $userId)
            ->where('status', 'Active')
            ->value('ktp_image');

        $activeKTPImage = null;
        if ($activeKTPFile) {
            $baseUrl = rtrim(env('SOBAT_USER_DOCUMENT_IMAGE_BASE_URL'), '/');

            // Example: SB-000123_KTP.jpeg
            $fileParts = explode('.', $activeKTPFile);
            $filenameWithoutExt = $fileParts[0]; // SB-000123_KTP
            $extension = $fileParts[1] ?? '';    // jpeg

            // Extract user folder (SB-000123)
            $userFolder = explode('_', $filenameWithoutExt)[0];

            // Build full image URL
            $activeKTPImage = "{$baseUrl}/{$userFolder}/{$filenameWithoutExt}.{$extension}";
        }
        
        return view('sobat.customer.ktp.index', compact('customerKTPs', 'activeKTPImage'));
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
    public function edit($id, $ktpId)
    {
        $ktpId = Crypt::decrypt($ktpId);
        $customerKTP = SobatCustomerKTP::findOrFail($ktpId);

        $provinsi = Provinsi::select('nama_provinsi', 'kode_provinsi')->orderBy('nama_provinsi')->get();

        $businessAreas = BusinessArea::select('business_area_code', 'business_area_name')->orderBy('business_area_code')->get();

        return view('sobat.customer.ktp.edit', compact('customerKTP', 'businessAreas', 'provinsi'));
    }
    public function update(Request $request, $id, $ktpId)
    {
        $ktpId = Crypt::decrypt($ktpId);
        $customerKTP = SobatCustomerKTP::findOrFail($ktpId);

        $request->validate([
            'nama'          => 'required|string|max:255',
            'NIK'           => 'required|string|max:20|unique:mysqlsobat.user_ktp,NIK,' . $customerKTP->id,
            'TTL'           => 'required|string|max:100',
            'jenis_kelamin' => 'required|String|in:Perempuan,Laki-laki',
            'agama'         => 'nullable|string|max:50',
            'alamat'        => 'required|string|max:500',
            'rt_rw'         => 'nullable|string|max:20',

            'provinsi_ktp'  => 'required|string|exists:mysqlsobat.provinsi,kode_provinsi',
            'kota_ktp'      => 'required|string|exists:mysqlsobat.kota,kode_kota',
            'kecamatan'     => 'required|string|exists:mysqlsobat.kecamatan,kode_kecamatan',
            'kelurahan'     => 'required|string|exists:mysqlsobat.kelurahan,kode_kelurahan',

            'status'        => 'required|in:Active,Inactive',
        ]);

        $customerKTP->update([
            'nama'          => $request->nama,
            'NIK'           => $request->NIK,
            'TTL'           => $request->TTL,
            'jenis_kelamin' => $request->jenis_kelamin,
            'agama'         => $request->agama,
            'alamat'        => $request->alamat,
            'rt_rw'         => $request->rt_rw,

            'kode_provinsi_ktp'  => $request->provinsi_ktp,
            'kode_kota_ktp'      => $request->kota_ktp,
            'kode_kecamatan'=> $request->kecamatan,
            'kode_kelurahan'=> $request->kelurahan,

            'status'        => $request->status,
        ]);

        return redirect()
            ->route('customer.ktp.index', $id) // only customerId
            ->with('success', 'Customer KTP berhasil diupdate.');
    }

    public function show($id)
    {
        $id = Crypt::decrypt($id);
        $customer = SobatCustomerKTP::findOrFail($id);
        return view('sobat.customer.show', compact('customer'));
    }
}
