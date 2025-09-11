<?php

namespace App\Http\Controllers;

use App\Models\SobatBrand;
use App\Models\SidiaCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SidiaCategoryController extends Controller
{
    // List + filter
    public function index(Request $request)
    {
        $query = SidiaCategory::query();

        if ($request->filled('category_name')) {
            $query->where('category_name', 'like', '%'.$request->category_name.'%');
        }

        $category = $query->orderBy('category_name')->paginate(10);
        $category->appends($request->all());

        return view('sidia.categories.index', compact('category'));
    }

    // ----- CREATE -----
    public function create()
    {
        return view('sidia.categories.create');
    }

    public function store(Request $request)
    {
        // catatan: koneksi model SobatBrand sudah mysqlsobat
        $request->validate([
            'category_code'     => ['required','string','max:5','unique:mysql.sidia_categories,category_code'],
            'category_name'     => ['nullable','string'],
        ]);

        $data = [
            'category_code'     => $request->category_code,
            'category_name'     => $request->category_name,
        ];

        SidiaCategory::create($data);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    // ----- EDIT -----
    // Tetap pakai brand_name terenkripsi (kompatibel dengan route kamu saat ini)
    public function edit($category_code)
    {
        $category_code = Crypt::decrypt($category_code);
        $category = SidiaCategory::where('category_code', $category_code)->firstOrFail();
        return view('sidia.categories.edit', compact('category'));
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
        $brand->save();

        return back()->with('success', 'Kategori berhasil diupdate.');
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
}
