<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\SobatBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;

class BrandController extends Controller
{
    // Default page
    public function index(Request $request)
    {

        $query = SobatBrand::query();
        if (!empty($request->brand_name)) {
            $query->where('brand_name', 'like', '%' . $request->brand_name . '%');
        }
        $query->orderBy('brand_name');
        $brand = $query->paginate(10);
        $brand->appends(request()->all());
        return view('sobat.brand.index', compact('brand'));
    }

    public function edit($brand_name)
    {
        $brand_name = Crypt::decrypt($brand_name);
        $data['brand'] = SobatBrand::where('brand_name', $brand_name)->first();
        return view('sobat.brand.edit', $data);
    }

    public function update(Request $request, $encName)
    {
        $brandName = Crypt::decrypt($encName);
        $brand = SobatBrand::where('brand_name', $brandName)->firstOrFail();

        // Validasi: pakai file|mimes (lebih toleran daripada 'image')
        $request->validate([
            'brand_description' => ['nullable','string'],
            'status'            => ['nullable','in:Active,Inactive,active,inactive,1,0,Y,N,true,false'],
            'brand_image_file'  => ['nullable','file','mimes:jpeg,jpg,png,webp,gif,jfif','max:5120'],
        ]);

        // Update teks (pakai has agar bisa di-blank)
        if ($request->has('brand_description')) {
            $brand->brand_description = $request->brand_description;
        }
        if ($request->filled('status')) {
            $brand->status = $this->normalizeStatus($request->status);
        }

        $uploadUrl = config('services.sobat.upload_url') ?: env('SOBAT_UPLOAD_URL');
        $token     = config('services.sobat.upload_token') ?: env('SOBAT_UPLOAD_TOKEN');

        Log::info('SOBAT upload config', ['url' => $uploadUrl, 'has_token' => !empty($token)]);

        if ($request->hasFile('brand_image_file')) {
            if (empty($uploadUrl) || empty($token)) {
                return back()->with('error', 'Konfigurasi upload belum diset (URL/token kosong).')->withInput();
            }

            $file = $request->file('brand_image_file');

            Log::info('Uploading brand image...', [
                'brand'       => $brand->brand_name,
                'client_name' => $file->getClientOriginalName(),
                'mime'        => $file->getMimeType(),
                'size'        => $file->getSize(),
            ]);

            try {
                $resp = Http::withToken($token)
                    ->timeout(30)
                    // Jika ada masalah SSL sementara: ->withOptions(['verify' => false])
                    ->attach('file', fopen($file->getRealPath(), 'r'), $file->getClientOriginalName())
                    ->post($uploadUrl, [
                        'brand_name' => $brand->brand_name,
                        'overwrite'  => '1',
                    ]);

                Log::info('Upload response', ['status' => $resp->status(), 'body' => $resp->body()]);

                if (!$resp->successful()) {
                    return back()->with('error', 'Upload gagal: '.$resp->status().' '.$resp->body())->withInput();
                }

                $payload = $resp->json();
                if (empty($payload['filename'])) {
                    return back()->with('error', 'Upload gagal: response tanpa filename')->withInput();
                }

                // Simpan filename dari API upload
                $brand->brand_image = $payload['filename'];
            } catch (ConnectionException $e) {
                Log::error('Upload connection error', ['message' => $e->getMessage()]);
                return back()->with('error', 'Upload gagal (koneksi): '.$e->getMessage())->withInput();
            } catch (\Throwable $e) {
                Log::error('Upload unexpected error', ['message' => $e->getMessage()]);
                return back()->with('error', 'Upload gagal (server): '.$e->getMessage())->withInput();
            }
        }

        $brand->save();
        return back()->with('success', 'Brand berhasil diupdate.');
    }

    private function normalizeStatus($v): string
    {
        $v = strtolower(trim((string)$v));
        if (in_array($v, ['active','aktif','1','y','true','yes'], true))     return 'Active';
        if (in_array($v, ['inactive','nonaktif','0','n','false','no'], true)) return 'Inactive';
        return 'Active';
    }
}
