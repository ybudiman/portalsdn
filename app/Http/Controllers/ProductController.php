<?php

namespace App\Http\Controllers;

use App\Models\SobatBrand;
use App\Models\SobatCategory;
use App\Models\SobatPrincipal;
use App\Models\SobatProduct;
use App\Models\SobatProductMedia;
use App\Models\SobatTax;
use App\Models\SobatUom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    // ----- LIST -----
    public function index(Request $request)
    {
        $query = SobatProduct::query();

        if ($request->filled('search_query')) {
            $query->where('product_name', 'like', '%'.$request->search_query.'%')
                  ->orWhere('product_code', $request->search_query);
        }

        $products = $query->orderBy('product_name')->paginate(10);
        $products->appends($request->all());

        return view('sobat.product.index', compact('products'));
    }

    // ----- CREATE -----
    public function create()
    {
        $categories = SobatCategory::all();
        $principals = SobatPrincipal::all();
        $brands     = SobatBrand::all();
        $taxes      = SobatTax::all();
        $uoms       = SobatUom::all();

        return view('sobat.product.create', compact(
            'categories', 
            'principals', 
            'brands', 
            'taxes', 
            'uoms'
        ));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'product_code'   => ['required','string','max:50','unique:mysqlsobat.products,product_code'],
            'product_name'   => ['required','string','max:255','unique:mysqlsobat.products,product_name'],
            'product_description' => ['nullable','string'],
            'external_product_code' => ['nullable','string','max:100'],
            'category_id'    => ['nullable','exists:mysqlsobat.categories,id'],
            'principal_id'   => ['nullable','exists:mysqlsobat.principals,id'],
            'brand_id'       => ['nullable','exists:mysqlsobat.brands,id'],
            'isNew'          => ['required','in:Y,N'],
            'isFeatured'     => ['required','in:Y,N'],
            'taxable'        => ['required','in:Y,N'],
            'tax_id'         => ['nullable','exists:mysqlsobat.tax,id'],
            'min_order_uom_id' => ['nullable','exists:mysqlsobat.unit_of_measurement,id'],
            'status'         => ['required','in:Active,Inactive'],
            'product_image'  => ['nullable','file','mimes:jpeg,jpg,png,webp,gif','max:5120'], // 5MB
        ]);

        $data = [
            'product_code'   => $request->product_code,
            'product_name'   => $request->product_name,
            'product_description' => $request->product_description,
            'external_product_code' => $request->external_product_code,
            'category_id'    => $request->category_id,
            'principal_id'   => $request->principal_id,
            'brand_id'       => $request->brand_id,
            'isNew'          => $request->isNew,
            'isFeatured'     => $request->isFeatured,
            'taxable'        => $request->taxable,
            'tax_id'         => $request->tax_id,
            'min_order_uom_id' => $request->min_order_uom_id,
            'status'         => $request->status,
            'product_image'  => null,
        ];

        // Handle image upload (if any)
        if ($request->hasFile('product_image')) {
            // You can either use Storage::put() or your custom uploadToSobat() helper
            $filename = $this->uploadToSobat(
                $request->file('product_image'),
                $data['product_name'],
                true
            );

            if (!$filename) {
                return back()->withInput()->with('error', 'Upload gagal. Cek log untuk detail.');
            }

            $data['product_image'] = $filename;
        }

        SobatProduct::create($data);

        return redirect()
            ->route('product.index')
            ->with('success', 'Product berhasil ditambahkan.');
    }


    // ----- EDIT -----
    public function edit($id)
    {
        $id = Crypt::decrypt($id);
        $product = SobatProduct::findOrFail($id);

        // Eager-load related dropdown data
        $categories = SobatCategory::all();
        $principals = SobatPrincipal::all();
        $brands     = SobatBrand::all();
        $taxes      = SobatTax::all();
        $uoms       = SobatUom::all();

        return view('sobat.product.edit', compact(
            'product', 
            'categories', 
            'principals', 
            'brands', 
            'taxes', 
            'uoms'
        ));
    }


    public function update(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $product = SobatProduct::findOrFail($id);

        $request->validate([
            'product_name' => ['required','string','max:255', Rule::unique('mysqlsobat.products','product_name')->ignore($product->id)],
            'description'  => ['nullable','string'],
            'stock'        => ['required','integer','min:0'],
            'status'       => ['required','in:Active,Inactive,active,inactive,1,0,Y,N,true,false'],
            'product_image' => ['nullable','file','mimes:jpeg,jpg,png,webp,gif','max:5120'],
        ]);

        $product->product_name = $request->product_name;
        $product->description  = $request->description;
        $product->stock        = $request->stock;
        $product->status       = $this->normalizeStatus($request->status);

        if ($request->hasFile('product_image')) {
            $filename = $this->uploadToSobat($request->file('product_image'), $product->product_code, true);
            if (!$filename) {
                return back()->withInput()->with('error', 'Upload gagal. Cek log untuk detail.');
            }
            $product->product_image = $filename;
        }

        $product->save();

        return redirect()->route('product.index')->with('success', 'Product berhasil diupdate.');
    }

    // ----- SHOW -----
    public function show($id)
    {
        $id = Crypt::decrypt($id);
        $product = SobatProduct::findOrFail($id);

        $productImages = SobatProductMedia::where('product_id', $id)
            ->where("media_type", "image")
            ->get();

        $baseUrl = rtrim(env('SOBAT_PRODUCT_IMAGE_BASE_URL'), '/');

        foreach ($productImages as $image) {
            $filename = $image->media_filepath; 
            if (!$filename) {
                continue;
            }

            $fileParts = explode('.', $filename);
            $filenameWithoutExt = $fileParts[0] ?? '';
            $extension = $fileParts[1] ?? '';

            if (!$filenameWithoutExt || !$extension) {
                continue;
            }

            $userFolder = explode('_', $filenameWithoutExt)[0];

            // Add new dynamic attribute "url"
            $image->url = "{$baseUrl}/{$filenameWithoutExt}.{$extension}";
        }

        return view('sobat.product.show', compact('product', 'productImages'));
    }



    // ----- DELETE -----
    public function destroy($id)
    {
        $product = SobatProduct::find($id);
        if (!$product) {
            return back()->with('error', 'Data product tidak ditemukan.');
        }

        $filename = trim((string)($product->product_image ?? ''));
        $product->delete();

        $remoteOk = true;
        if ($filename !== '') {
            $remoteOk = $this->deleteFromSobat($filename);
        }

        if ($remoteOk) {
            return back()->with('success', 'Product & file gambar berhasil dihapus.');
        }
        return back()->with('warning', 'Product terhapus, namun file gambar gagal dihapus dari server.');
    }

    // ----- UTIL -----
    private function normalizeStatus($v): string
    {
        $v = strtolower(trim((string)$v));
        if (in_array($v, ['active','1','y','true','yes'], true)) return 'Active';
        if (in_array($v, ['inactive','0','n','false','no'], true)) return 'Inactive';
        return 'Active';
    }

    private function uploadToSobat($file, string $productCode, bool $overwrite = true): ?string
    {
        $filePath = $file->getRealPath();
        $ext      = $file->getClientOriginalExtension();

        $response = Http::attach(
            'file',
            fopen($filePath, 'r'),
            $productCode . '.' . $ext
        )->post(env('SOBAT_UPLOAD_URL'), [
            'function' => "UPLOAD PRODUCT IMAGE",
        ]);

        return $productCode . '.' . $ext;
    }

    private function deleteFromSobat(string $value): bool
    {
        $filename = basename($value);
        try {
            $response = Http::post(env('SOBAT_DELETE_URL'), [
                'function'  => "DELETE FILE",
                'type'      => "Product",
                'file_name' => $filename
            ]);

            if ($response->successful()) {
                return true;
            }

            if ($response->status() === 200 && str_contains($response->body(), 'not_found')) {
                return true;
            }
        } catch(\Throwable $e) {
            Log::error('Delete remote file error', ['msg'=>$e->getMessage()]);
        }

        return false;
    }
}
