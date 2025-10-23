<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\SobatDiscountHeader;
use App\Models\SobatDiscountDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class DiscountController extends Controller
{
    // ----- LIST -----
    public function index(Request $request)
    {
        // ✅ Include both header and related details
        $query = SobatDiscountHeader::with('details');

        // ✅ Filter logic
        if ($request->filled('discount_name')) {
            $query->where('discount_name', 'like', '%' . $request->discount_name . '%');
        }

        // ✅ Sort by start_date or finish_date
        $discounts = $query->orderBy('start_date', 'desc')
                           ->orderBy('finish_date', 'desc')
                           ->paginate(10);

        $discounts->appends($request->all());

        // dd($discounts);

        // ✅ Send to view
        return view('sobat.discount.index', compact('discounts'));
    }

    // ----- CREATE -----
    public function create()
    {
        $branches = DB::connection('mysqlsobat')
            ->table('business_area')
            ->select('business_area_code', 'business_area_name')
            ->orderBy('business_area_code', 'asc')
            ->get();

        return view('sobat.discount.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'discount_name' => ['required', 'string', 'max:255'],
            'start_date'    => ['required', 'date'],
            'finish_date'   => ['required', 'date', 'after_or_equal:start_date'],
            'business_area_code'   => ['required', 'string', 'max:50'],
            'details'       => ['required', 'array', 'min:1'],
            'details.*.sku' => ['required', 'string', 'max:100'],
            'details.*.discount_type'  => ['required', 'string', 'max:50'],
            'details.*.discount_value' => ['required', 'numeric', 'min:0'],
            'details.*.sku_uom'        => ['required', 'string', 'max:50'],
        ]);

        DB::transaction(function () use ($request) {
            $header = SobatDiscountHeader::create([
                'discount_name' => $request->discount_name,
                'start_date'    => $request->start_date,
                'finish_date'   => $request->finish_date,
                'business_area_code'   => $request->business_area_code,
                'status'        => $this->normalizeStatus($request->status),
            ]);

            foreach ($request->details as $detail) {
                $header->details()->create([
                    'sku'            => $detail['sku'],
                    'discount_type'  => $detail['discount_type'],
                    'discount_value' => $detail['discount_value'],
                    'sku_uom'        => $detail['sku_uom'],
                ]);
            }
        });

        return redirect()->route('discount.index')->with('success', 'Discount berhasil ditambahkan.');
    }

    // ----- EDIT -----
    public function edit($encId)
    {
        $id = Crypt::decrypt($encId);
        $discount = SobatDiscountHeader::with('details')->findOrFail($id);

        // Branch Code options
        $branches = DB::connection('mysqlsobat')
            ->table('business_area')
            ->select('business_area_code', 'business_area_name')
            ->orderBy('business_area_code', 'asc')
            ->get();

        return view('sobat.discount.edit', compact('discount', 'branches'));
    }

    public function update(Request $request, $encId)
    {
        $id = Crypt::decrypt($encId);
        $discount = SobatDiscountHeader::with('details')->findOrFail($id);

        $request->validate([
            'discount_name' => ['required', 'string', 'max:255'],
            'start_date'    => ['required', 'date'],
            'finish_date'   => ['required', 'date', 'after_or_equal:start_date'],
            'business_area_code'   => ['required', 'string', 'max:50'],
            'status'        => ['nullable', 'in:Active,Inactive,active,inactive,1,0,Y,N,true,false'],
            'details'       => ['required', 'array', 'min:1'],
            'details.*.sku' => ['required', 'string', 'max:100'],
            'details.*.discount_type'  => ['required', 'string', 'max:50'],
            'details.*.discount_value' => ['required', 'numeric', 'min:0'],
            'details.*.sku_uom'        => ['required', 'string', 'max:50'],
        ]);

        DB::transaction(function () use ($request, $discount) {
            $discount->update([
                'discount_name'         => $request->discount_name,
                'start_date'            => $request->start_date,
                'finish_date'           => $request->finish_date,
                'business_area_code'    => $request->business_area_code,
                'status'                => $this->normalizeStatus($request->status ?? $discount->status),
            ]);

            // Replace old details
            $discount->details()->delete();

            foreach ($request->details as $detail) {
                $discount->details()->create([
                    'sku'            => $detail['sku'],
                    'discount_type'  => $detail['discount_type'],
                    'discount_value' => $detail['discount_value'],
                    'sku_uom'        => $detail['sku_uom'],
                ]);
            }
        });

        return back()->with('success', 'Discount berhasil diupdate.');
    }

    // ----- DELETE -----
    public function destroy($id)
    {
        $discount = SobatDiscountHeader::with('details')->find($id);
        if (!$discount) {
            return back()->with('error', 'Data discount tidak ditemukan.');
        }

        DB::transaction(function () use ($discount) {
            $discount->details()->delete();
            $discount->delete();
        });

        return back()->with('success', 'Discount berhasil dihapus.');
    }

    // Discount Details
    public function destroyDetail($id)
    {
        $detail = SobatDiscountDetail::find($id);
        if (!$detail) {
            return back()->with('error', 'Data detail discount tidak ditemukan.');
        }

        $detail->delete();

        return back()->with('success', 'Detail discount berhasil dihapus.');
    }

    // ----- UTIL -----
    private function normalizeStatus($v): string
    {
        $v = strtolower(trim((string)$v));
        if (in_array($v, ['active', 'aktif', '1', 'y', 'true', 'yes'], true)) return 'Active';
        if (in_array($v, ['inactive', 'nonaktif', '0', 'n', 'false', 'no'], true)) return 'Inactive';
        return 'Active';
    }
}
