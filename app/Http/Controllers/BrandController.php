<?php

namespace App\Http\Controllers;

use App\Models\SobatBrand;
use Illuminate\Http\Request;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    // List + filter
    public function index(Request $request)
    {
        $query = SobatBrand::query();

        if ($request->filled('brand_name')) {
            $query->where('brand_name', 'like', '%'.$request->brand_name.'%');
        }

        $brand = $query->orderBy('brand_name')->paginate(10);
        $brand->appends($request->all());

        return view('sobat.brand.index', compact('brand'));
    }

    // ----- CREATE -----
    public function create()
    {
        return view('sobat.brand.create');
    }

    public function store(Request $request)
    {
        // catatan: koneksi model SobatBrand sudah mysqlsobat
        $request->validate([
            'brand_name'        => ['required','string','max:255','unique:mysqlsobat.brands,brand_name'],
            'brand_description' => ['nullable','string'],
            'status'            => ['required','in:Active,Inactive,active,inactive,1,0,Y,N,true,false'],
            'brand_image_file'  => ['nullable','file','mimes:jpeg,jpg,png,webp,gif,jfif','max:5120'], // 5MB
        ]);

        $data = [
            'brand_name'        => $request->brand_name,
            'brand_description' => $request->brand_description,
            'status'            => $this->normalizeStatus($request->status),
            'brand_image'       => null,
        ];

        // upload file (opsional)
        if ($request->hasFile('brand_image_file')) {
            $filename = $this->uploadToSobat($request->file('brand_image_file'), $data['brand_name'], true);
            if (!$filename) {
                return back()->withInput()->with('error', 'Upload gagal. Cek log untuk detail.');
            }
            $data['brand_image'] = $filename;
        }

        SobatBrand::create($data);

        return redirect()->route('brand.index')->with('success', 'Brand berhasil ditambahkan.');
    }

    // ----- EDIT -----
    // Tetap pakai brand_name terenkripsi (kompatibel dengan route kamu saat ini)
    public function edit($brand_name)
    {
        $brand_name = Crypt::decrypt($brand_name);
        $brand = SobatBrand::where('brand_name', $brand_name)->firstOrFail();
        return view('sobat.brand.edit', compact('brand'));
    }

    public function update(Request $request, $encName)
    {
        $brandName = Crypt::decrypt($encName);
        $brand = SobatBrand::where('brand_name', $brandName)->firstOrFail();

        // validasi (status opsional di edit; kalau mau wajib → ganti 'nullable' jadi 'required')
        $request->validate([
            'brand_description' => ['nullable','string'],
            'status'            => ['nullable','in:Active,Inactive,active,inactive,1,0,Y,N,true,false'],
            'brand_image_file'  => ['nullable','file','mimes:jpeg,jpg,png,webp,gif,jfif','max:5120'],
        ]);

        // update teks (pakai has agar bisa dikosongkan)
        if ($request->has('brand_description')) {
            $brand->brand_description = $request->brand_description;
        }
        if ($request->filled('status')) {
            $brand->status = $this->normalizeStatus($request->status);
        }

        // upload file (opsional)
        if ($request->hasFile('brand_image_file')) {
            $filename = $this->uploadToSobat($request->file('brand_image_file'), $brand->brand_name, true);
            if (!$filename) {
                return back()->withInput()->with('error', 'Upload gagal. Cek log untuk detail.');
            }
            $brand->brand_image = $filename;
        }

        $brand->save();

        return back()->with('success', 'Brand berhasil diupdate.');
    }

    // ----- UTIL -----
    private function uploadToSobat($file, string $brandName, bool $overwrite = true): ?string
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
                    'brand_name' => $brandName,            // nama file dibentuk dari brand_name
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
        $brand = SobatBrand::find($id);
        if (!$brand) {
            return back()->with('error', 'Data brand tidak ditemukan.');
        }

        // simpan dulu filename untuk hapus remote
        $filename = trim((string)($brand->brand_image ?? ''));

        // 1) Hapus record DB
        $brand->delete();

        // 2) Hapus file di server upload (opsional; tidak menghalangi sukses DB)
        $remoteOk = true;
        if ($filename !== '') {
            $remoteOk = $this->deleteFromSobat($filename);
        }

        if ($remoteOk) {
            return back()->with('success', 'Brand & file gambar berhasil dihapus.');
        }
        return back()->with('warning', 'Brand terhapus, namun file gambar gagal dihapus dari server.');
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
