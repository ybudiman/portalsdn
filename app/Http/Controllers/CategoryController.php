<?php

namespace App\Http\Controllers;

use App\Models\SobatCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    // ----- LIST -----
    public function index(Request $request)
    {
        $query = SobatCategory::query();

        if ($request->filled('category_name')) {
            $query->where('category_name', 'like', '%' . $request->category_name . '%');
        }

        $categories = $query->orderBy('category_name')->paginate(10);
        $categories->appends($request->all());

        return view('sobat.category.index', compact('categories'));
    }

    // ----- CREATE -----
    public function create()
    {
        return view('sobat.category.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name'        => ['required', 'string', 'max:255', 'unique:mysqlsobat.categories,category_name'],
            'category_description' => ['nullable', 'string'],
            'status'               => ['required', 'in:Active,Inactive,active,inactive,1,0,Y,N,true,false'],
            'category_image_file'  => ['nullable','file','mimes:jpeg,jpg,png,webp,gif,jfif','max:5120'],
        ]);

        $data = [
            'category_name'        => $request->category_name,
            'category_description' => $request->category_description,
            'status'               => $this->normalizeStatus($request->status),
            'category_image'       => null,
        ];

        // upload file (opsional)
        if ($request->hasFile('category_image_file')) {
            $filename = $this->uploadToSobat($request->file('category_image_file'), $data['category_name'], true);
            if (!$filename) {
                return back()->withInput()->with('error', 'Upload gagal. Cek log untuk detail.');
            }
            $data['category_image'] = $filename;
        }

        SobatCategory::create($data);

        return redirect()->route('category.index')->with('success', 'Category berhasil ditambahkan.');
    }

    // ----- EDIT -----
    public function edit($encName)
    {
        $category_name = Crypt::decrypt($encName);
        $category = SobatCategory::where('category_name', $category_name)->firstOrFail();

        return view('sobat.category.edit', compact('category'));
    }

    public function update(Request $request, $encName)
    {
        $categoryName = Crypt::decrypt($encName);
        $category = SobatCategory::where('category_name', $categoryName)->firstOrFail();

        $request->validate([
            'category_description' => ['nullable','string'],
            'status'               => ['nullable','in:Active,Inactive,active,inactive,1,0,Y,N,true,false'],
            'category_image_file'  => ['nullable','file','mimes:jpeg,jpg,png,webp,gif,jfif','max:5120'],
        ]);

        if ($request->has('category_description')) {
            $category->category_description = $request->category_description;
        }
        if ($request->filled('status')) {
            $category->status = $this->normalizeStatus($request->status);
        }

        if ($request->hasFile('category_image_file')) {
            $filename = $this->uploadToSobat($request->file('category_image_file'), $category->category_name, true);
            if (!$filename) {
                return back()->withInput()->with('error', 'Upload gagal. Cek log untuk detail.');
            }
            $category->category_image = $filename;
        }

        $category->save();

        return back()->with('success', 'Category berhasil diupdate.');
    }

    // ----- DELETE -----
    public function destroy($id)
    {
        $category = SobatCategory::find($id);
        if (!$category) {
            return back()->with('error', 'Data category tidak ditemukan.');
        }

        $filename = trim((string)($category->category_image ?? ''));

        $category->delete();

        $remoteOk = true;
        if ($filename !== '') {
            $remoteOk = $this->deleteFromSobat($filename);
        }

        if ($remoteOk) {
            return back()->with('success', 'Category & file gambar berhasil dihapus.');
        }
        return back()->with('warning', 'Category terhapus, namun file gambar gagal dihapus dari server.');
    }

    // ----- UTIL -----
    private function normalizeStatus($v): string
    {
        $v = strtolower(trim((string)$v));
        if (in_array($v, ['active','aktif','1','y','true','yes'], true)) return 'Active';
        if (in_array($v, ['inactive','nonaktif','0','n','false','no'], true)) return 'Inactive';
        return 'Active';
    }

    private function uploadToSobat($file, string $categoryName, bool $overwrite = true): ?string
    {
        // If $file is an instance of Illuminate\Http\UploadedFile
        $filePath = $file->getRealPath();
        $ext      = $file->getClientOriginalExtension(); // just the extension

        $response = Http::attach(
            'file',                         // must match backend: $_FILES['file']
            fopen($filePath, 'r'),          // file content
            $categoryName . '.' . $ext      // force filename: categoryName.ext
        )->post(env('SOBAT_UPLOAD_URL'), [
            'function'      => "UPLOAD CATEGORY IMAGE",
        ]);

        return $categoryName . '.' . $ext;
    }

    private function deleteFromSobat(string $value): bool
    {
        $filename = $this->extractFilename($value);
        try {
            $response = Http::post(env('SOBAT_DELETE_URL'), [
                'function'  => "DELETE FILE",
                'type'      => "Category",
                'file_name' => $filename     
            ]);

            if ($response->successful()) {
                return true;
            }

            if ($response->status() === 200 && str_contains($response->body(), 'not_found')) {
                return true;
            }
        } catch(\Throwable $e){
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