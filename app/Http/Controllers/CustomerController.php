<?php

namespace App\Http\Controllers;

use App\Models\BusinessArea;
use App\Models\SobatCustomer;
use Illuminate\Http\Request;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    // List + filter
    public function index(Request $request)
    {
        $query = SobatCustomer::query();

        if ($request->filled('search_query')) {
            $query->where('email', 'like', '%'.$request->search_query.'%')
                    ->orWhere('fullname', 'like', '%'.$request->search_query.'%')
                    ->orWhere('external_customer_id', $request->search_query)
                    ->orWhere('customer_id', $request->search_query)
                    ->orWhere('default_delivery_type', $request->search_query)
                    ->orWhere('business_area_code', $request->search_query);
        }

        $customers = $query->orderBy('fullname')->paginate(10);
        $customers->appends($request->all());

        return view('sobat.customer.index', compact('customers'));
    }

    // ----- CREATE -----
    public function create()
    {
        return view('sobat.customer.create');
    }

    public function store(Request $request)
    {
        // catatan: koneksi model SobatCustomer sudah mysqlsobat
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

        SobatCustomer::create($data);

        return redirect()->route('customer.index')->with('success', 'customer berhasil ditambahkan.');
    }

    // ----- EDIT -----
    // Tetap pakai fullname terenkripsi (kompatibel dengan route kamu saat ini)
    public function edit($id)
    {
        $id = Crypt::decrypt($id);
        $customer = SobatCustomer::findOrFail($id);

        $businessAreas = BusinessArea::select('business_area_code', 'business_area_name')
            ->orderBy('business_area_code')
            ->get();

        return view('sobat.customer.edit', compact('customer', 'businessAreas'));
    }
    public function update(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $customer = SobatCustomer::findOrFail($id);

        $request->validate([
            'fullname' => [
                'required',
                'string',
                'max:255',
                Rule::unique('mysqlsobat.users', 'fullname')->ignore($customer->id),
            ],
            'verified' => [
                'required',
                Rule::in(['Y', 'W', 'N', 'P']),
            ],
            'employee_id' => [
                'nullable',
                'string',
                'max:50',
            ],
            'external_customer_id' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('mysqlsobat.users', 'external_customer_id')->ignore($customer->id),
            ],
            'default_delivery_type' => [
                'required',
                Rule::in(['Franco', 'Loco']),
            ],
            'business_area_code' => [
                'required',
                'string',
                Rule::exists('mysqlsobat.business_area', 'business_area_code'),
            ],
        ]);

        // Mass assign safely
        $customer->update([
            'fullname'             => $request->fullname,
            'verified'             => $request->verified,
            'employee_id'          => $request->employee_id,
            'external_customer_id' => $request->external_customer_id,
            'default_delivery_type'=> $request->default_delivery_type,
            'business_area_code'   => $request->business_area_code,
        ]);

        return redirect()->route('customer.index')->with('success', 'Customer berhasil diupdate.');
    }

    public function show($id)
    {
        $id = Crypt::decrypt($id);
        $customer = SobatCustomer::findOrFail($id);
        // $karyawan = SobatCustomer::where('id', $id)
        //     ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
        //     ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
        //     ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
        //     ->join('status_kawin', 'karyawan.kode_status_kawin', '=', 'status_kawin.kode_status_kawin')
        //     ->leftJoin('karyawan as atasan', 'karyawan.nik_atasan', '=', 'atasan.nik')
        //     ->select(
        //         'karyawan.*',
        //         'cabang.nama_cabang',
        //         'departemen.nama_dept',
        //         'jabatan.nama_jabatan',
        //         'status_kawin.status_kawin',
        //         'atasan.nama_karyawan as nama_atasan'
        //     )

        //     ->first();
        // $user_karyawan = Userkaryawan::where('nik', $nik)->first();
        // $user = $user_karyawan ? User::where('id', $user_karyawan->id_user)->first() : null;
        // $data['karyawan'] = $karyawan;
        // $data['user'] = $user;
        return view('sobat.customer.show', compact('customer'));
    }


    // ----- UTIL -----
    private function uploadToSobat($file, string $customerName, bool $overwrite = true): ?string
    {
        $uploadUrl = config('services.sobat.upload_url')   ?: env('SOBAT_UPLOAD_URL');
        $token     = config('services.sobat.upload_token') ?: env('SOBAT_UPLOAD_TOKEN');

        Log::info('SOBAT upload config', ['url' => $uploadUrl, 'has_token' => !empty($token)]);

        if (empty($uploadUrl) || empty($token)) {
            Log::error('SOBAT upload config is empty');
            return null;
        }

        try {
            $resp = Http::withToken($token)
                ->timeout(30)
                // ->withOptions(['verify' => false]) // aktifkan hanya bila perlu debug SSL
                ->attach('file', fopen($file->getRealPath(), 'r'), $file->getClientOriginalName())
                ->post($uploadUrl, [
                    'fullname' => $customerName,            // nama file dibentuk dari fullname
                    'overwrite'  => $overwrite ? '1' : '0',
                ]);

            Log::info('Upload response', ['status' => $resp->status(), 'body' => $resp->body()]);

            if (!$resp->successful()) {
                return null;
            }

            $payload = $resp->json();
            return $payload['filename'] ?? null;
        } catch (ConnectionException $e) {
            Log::error('Upload connection error', ['message' => $e->getMessage()]);
            return null;
        } catch (\Throwable $e) {
            Log::error('Upload unexpected error', ['message' => $e->getMessage()]);
            return null;
        }
    }

    private function normalizeStatus($v): string
    {
        $v = strtolower(trim((string)$v));
        if (in_array($v, ['active','aktif','1','y','true','yes'], true))     return 'Active';
        if (in_array($v, ['inactive','nonaktif','0','n','false','no'], true)) return 'Inactive';
        return 'Active';
    }

    public function destroy($id)
    {
        $customer = SobatCustomer::find($id);
        if (!$customer) {
            return back()->with('error', 'Data customer tidak ditemukan.');
        }

        // simpan dulu filename untuk hapus remote
        $filename = trim((string)($customer->customer_image ?? ''));

        // 1) Hapus record DB
        $customer->delete();

        // 2) Hapus file di server upload (opsional; tidak menghalangi sukses DB)
        $remoteOk = true;
        if ($filename !== '') {
            $remoteOk = $this->deleteFromSobat($filename);
        }

        if ($remoteOk) {
            return back()->with('success', 'customer & file gambar berhasil dihapus.');
        }
        return back()->with('warning', 'customer terhapus, namun file gambar gagal dihapus dari server.');
    }

    /**
     * Hapus file pada server upload Sobat.
     * Terima filename atau full URL (akan diambil basename-nya).
     */
    private function deleteFromSobat(string $value): bool
    {
        $token = config('services.sobat.upload_token') ?: env('SOBAT_UPLOAD_TOKEN');
        $url   = config('services.sobat.delete_url') ?: env('SOBAT_DELETE_URL');

        if (empty($token) || empty($url)) {
            Log::warning('SOBAT delete config empty', ['url'=>$url,'has_token'=>!empty($token)]);
            return false;
        }

        // ekstrak filename jika yang dikirim URL penuh
        $filename = $this->extractFilename($value);

        try {
            // Banyak server menolak body di DELETE → pakai POST sederhana
            $resp = Http::withToken($token)->timeout(20)->post($url, [
                'filename' => $filename,
            ]);
            Log::info('Delete response', ['status'=>$resp->status(), 'body'=>$resp->body()]);

            if ($resp->successful()) {
                return true;
            }

            // anggap sukses jika file tidak ada di server tujuan
            if ($resp->status() === 200 && str_contains($resp->body(), 'not_found')) {
                return true;
            }
        } catch (\Throwable $e) {
            Log::error('Delete remote file error', ['msg'=>$e->getMessage()]);
        }
        return false;
    }

    private function extractFilename(string $v): string
    {
        $v = trim($v);
        if (preg_match('~^https?://~i', $v)) {
            $path = parse_url($v, PHP_URL_PATH) ?? '';
            return basename($path);
        }
        return basename($v);
    }
}
