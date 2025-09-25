<?php

namespace App\Http\Controllers;

use App\Models\BusinessArea;
use App\Models\SobatCustomer;
use App\Models\SobatCustomerKTP;
use App\Models\SobatCustomerDomicile;
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
                Rule::in(['franco', 'loco']),
            ],
            'business_area_code' => [
                'required',
                'string',
                Rule::exists('mysqlsobat.business_area', 'business_area_code'),
            ],
        ]);

        // Mass assign safely
        $customer->update([
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

        // Get KTP image filename from DB
        $ktpFile = SobatCustomerKTP::where('user_id', $id)
            ->where('status', 'Active')
            ->value('ktp_image');

        $ktpImage = null;
        if ($ktpFile) {
            $baseUrl = rtrim(env('SOBAT_USER_DOCUMENT_IMAGE_BASE_URL'), '/');

            // Example: SB-000123_KTP.jpeg
            $fileParts = explode('.', $ktpFile);
            $filenameWithoutExt = $fileParts[0]; // SB-000123_KTP
            $extension = $fileParts[1];         // jpeg

            // Extract user folder => part before "_KTP"
            $userFolder = explode('_', $filenameWithoutExt)[0]; // SB-000123

            // Build final URL
            $ktpImage = "{$baseUrl}/{$userFolder}/{$filenameWithoutExt}.{$extension}";
        }

        // Get Domicile image filename from DB
        $domicileFile = SobatCustomerDomicile::where('user_id', $id)
            ->where('status', 'Active')
            ->value('image_rumah');

        $domicileImage = null;
        if ($domicileFile) {
            $baseUrl = rtrim(env('SOBAT_USER_DOCUMENT_IMAGE_BASE_URL'), '/');

            // Example: SB-000123_house.jpeg
            $fileParts = explode('.', $domicileFile);
            $filenameWithoutExt = $fileParts[0]; // SB-000123_house
            $extension = $fileParts[1];          // jpeg

            // Extract user folder => part before "_house"
            $userFolder = explode('_', $filenameWithoutExt)[0]; // SB-000123

            // Build final URL
            $domicileImage = "{$baseUrl}/{$userFolder}/{$filenameWithoutExt}.{$extension}";
        }

        return view('sobat.customer.show', compact('customer', 'ktpImage', 'domicileImage'));
    }


    // ----- UTIL -----
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

        $customer->delete();
        return back()->with('warning', 'customer terhapus, namun file gambar gagal dihapus dari server.');
    }
}
