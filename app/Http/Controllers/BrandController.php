<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\SobatBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

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

    // Function create on view
    // public function create(){
    //     $data=array(
    //         'title'             => 'Add Brand',
    //         'menuCategory'      => 'active',
    //     );
    //     return view('sobat/superadmin/brand/create',$data);
    // }

    // // Function show image
    // public function showImage($filename)
    // {
    //     $disk = Storage::disk('minio');

    //     $filePath = 'brands/' . $filename; 
    //     $filePathFallback = 'brands/no-image.png'; 

    //     if ($disk->exists($filePath)) {
    //         $file = $disk->get($filePath);
    //         $mime = $disk->mimeType($filePath);
    //     } else {
    //         $file = $disk->get($filePathFallback);
    //         $mime = $disk->mimeType($filePathFallback);
    //     }

    //     return response($file)->header('Content-Type', $mime);
    // }

    // // Function save to database
    // public function store(Request $request){
    //     if (SobatBrand::where('brand_name', $request->categoryname)->exists()) {
    //         return back()->withErrors(['brandname' => 'Brand name already exists.'])->withInput();
    //     }

    //     $request->validate([
    //        'brandname'               => 'required',
    //        'branddescription'        => 'required',
    //        'brandstatus'             => 'required',
    //        'brandphoto'              => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    //     ],[
    //         'brandname.required'                => 'Brand name can not be empty !!',
    //         'branddescription.required'         => 'Brand description can not be empty !!',
    //         'brandstatus.required'              => 'Status can not be empty !!',
    //     ]);

    //     try {
    //             $path = null;

    //             // Upload dan update
    //             if ($request->hasFile('brandphoto')) {
    //                 $file = $request->file('brandphoto');

    //                 // Validate size
    //                 if ($file->getSize() > 20480) {
    //                     return back()->withErrors(['brandphoto' => 'Image size must not exceed 20 KB.'])->withInput();
    //                 }

    //                 // Validate dimension
    //                 list($width, $height) = getimagesize($file);
    //                 if ($width > 200 || $height > 200) {
    //                     return back()->withErrors(['brandphoto' => 'Image dimensions less than 200x200 pixels.'])->withInput();
    //                 }

    //                 // Save to database
    //                 $brand = SobatBrand::create([
    //                     'brand_name'        => $request->brandname,
    //                     'brand_description' => $request->branddescription,
    //                     'status'            => $request->brandstatus,
    //                     'brand_image'       => null, 
    //                 ]);

    //                 $extension = $file->getClientOriginalExtension();
    //                 $filename = $brand->id . '.' . $extension;

    //                 // Save to folder 'brands' in MinIO
    //                 $path = Storage::disk('minio')->putFileAs('brands', $file, $filename);

    //                 // Update field brand_image
    //                 $brand->update([
    //                     'brand_image' => $path, 
    //                 ]);
    //             }
    //         return redirect()->route('brand')->with('success', 'Brand added successfully.');
    //     } catch (Exception $e) {
    //         dd('Upload Error: ' . $e->getMessage());
    //     }
    // }

    // public function edit($id){
    //     $data=array(
    //         'title'             => 'Edit Brand',
    //         'menuBrand'      => 'active',
    //         'brandedit'          => SobatBrand::findOrFail($id),
    //     );
    //     return view('sobat/superadmin/brand/edit',$data);
    // }

    // public function update(Request $request, $id){
    //     $request->validate([
    //        'branddescription'        => 'required',
    //        'brandstatus'             => 'required',
    //        'brandphoto'              => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    //     ],[
    //         'branddescription.required'         => 'Brand description can not be empty !!',
    //         'brandstatus.required'              => 'Status can not be empty !!',
    //     ]);

    //     try {
    //             $path = null;
    //             $brand = SobatBrand::findOrFail($id);

    //             // Update field 
    //             $brand->brand_description = $request->branddescription;
    //             $brand->status            = $request->brandstatus;

    //             if ($request->hasFile('brandphoto')) {
    //                 $file = $request->file('brandphoto');
                    
    //                 if ($file->getSize() > 20480) {
    //                     return back()->withErrors(['brandphoto' => 'Image size must not exceed 20 KB.'])->withInput();
    //                 }

    //                 list($width, $height) = getimagesize($file);
    //                 if ($width > 200 || $height > 200) {
    //                     return back()->withErrors(['brandphoto' => 'Image dimensions must be exactly 200x200 pixels.'])->withInput();
    //                 }

    //                 $extension = $file->getClientOriginalExtension();
    //                 $filename = $id . '.' . $extension;

    //                 $path = Storage::disk('minio')->putFileAs('brands', $file, $filename);

    //                 $brand->brand_image = $path;
    //             }

    //             $brand->save();

    //         return redirect()->route('brand')->with('success', 'Brand successfully updated.');
    //     } catch (Exception $e) {
    //         dd('Upload Error: ' . $e->getMessage());
    //     }
    // }

    // public function destroy($id){
    //     $brand = SobatBrand::findOrFail($id);
    //     $brand->delete();
    //     $path = $brand->brand_image;
    //     Storage::disk('minio')->delete($path);
    //     return redirect()->route('brand')->with('success', 'Category successfully deleted.');
    // }

    // public function filter(Request $request)
    // {
    //     $query = SobatBrand::query();

    //     if ($request->filled('brand_name')) {
    //         $query->where('brand_name', 'like', '%' . $request->brand_name . '%');
    //     }

    //     if ($request->filled('status')) {
    //         $query->where('status', $request->status);
    //     }

    //     $brand = $query->get();

    //     $data = [
    //         'title' => 'Brand',
    //         'menuBrand' => 'active',
    //         'brand' => $brand,
    //     ];

    //     return view('sobat/superadmin/brand/index', $data);
    // }
}
