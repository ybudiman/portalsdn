<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesOrderController extends Controller
{
    private function sobat() {
        return DB::connection('mysqlsobat');
    }

    /**
     * Tampilkan daftar sales order dengan filter.
     * Filters:
     * - business_area_name (LIKE)
     * - date_from (>= oh.order_date)
     * - date_to   (<= oh.order_date)
     * - status    (=)
     */
    public function index(Request $r)
    {
        $q = $this->baseQuery();

        // --- Filters ---
        if ($r->filled('business_area_name')) {
            $q->where('ba.business_area_name', 'like', '%'.$r->business_area_name.'%');
        }
        if ($r->filled('status')) {
            $q->where('oh.status', $r->status);
        }
        if ($r->filled('date_from')) {
            $q->whereDate('oh.order_date', '>=', $r->date_from);
        }
        if ($r->filled('date_to')) {
            $q->whereDate('oh.order_date', '<=', $r->date_to);
        }

        // --- Result + pagination ---
        $orders = $q->orderByDesc('oh.order_date')
                    ->orderBy('oh.order_code')
                    ->paginate(15);

        // bawa query string agar paging tetap membawa filter
        $orders->appends($r->query());

        // opsi status (silakan sesuaikan dengan skema sistemmu)
        $statusOptions = $this->statusOptions();

        // opsional: dropdown business area (jika ingin pilih dari list, bukan ketik bebas)
        $baOptions = $this->sobat()->table('business_area')
            ->select('business_area_name')
            ->orderBy('business_area_name')
            ->pluck('business_area_name');

        return view('sobat.orders.index', compact('orders', 'statusOptions', 'baOptions'));
    }

    /**
     * Detail order: header + detail.
     */
    public function show(string $order_code)
    {
        $header = $this->baseQuery()
            ->where('oh.order_code', $order_code)
            ->first();

        abort_if(!$header, 404, 'Order tidak ditemukan');

        // Ambil detail (sesuaikan kolom tabelmu)
        $details = $this->sobat()->table('order_detail')
            ->where('order_code', $order_code)
            ->get();

        return view('sobat.orders.show', compact('header', 'details'));
    }

    /**
     * Edit header (contoh sederhana).
     * Buatkan view sobat/orders/edit.blade.php sesuai kebutuhan.
     */
    public function edit(string $order_code)
    {
        $header = $this->sobat()->table('order_header')->where('order_code', $order_code)->first();
        abort_if(!$header, 404, 'Order tidak ditemukan');

        $statusOptions = $this->statusOptions();

        return view('sobat.orders.edit', compact('header', 'statusOptions'));
    }

    /**
     * Update header (contoh: status & delivery_type).
     */
    public function update(Request $r, string $order_code)
    {
        $r->validate([
            'status'        => ['required', 'in:'.implode(',', $this->statusOptions())],
            'delivery_type' => ['nullable', 'string', 'max:50'],
            // tambahkan rule lain sesuai kolom header yang diizinkan diubah
        ]);

        $affected = $this->sobat()->table('order_header')
            ->where('order_code', $order_code)
            ->update([
                'status'        => $r->status,
                'delivery_type' => $r->delivery_type,
                // kolom lain...
                'updated_at'    => now(), // jika kolom ada
            ]);

        if (!$affected) {
            return back()->with('error', 'Tidak ada perubahan atau order tidak ditemukan.');
        }

        return redirect()->route('orders.index')->with('success', 'Order berhasil diupdate.');
    }

    /**
     * Hapus order (detail lalu header) dalam transaksi.
     */
    public function destroy(string $order_code)
    {
        $this->sobat()->beginTransaction();
        try {
            DB::table('order_detail')->where('order_code', $order_code)->delete();
            $deleted = DB::table('order_header')->where('order_code', $order_code)->delete();

            if (!$deleted) {
                DB::rollBack();
                return back()->with('error', 'Order tidak ditemukan atau sudah dihapus.');
            }

            DB::commit();
            return back()->with('success', 'Order berhasil dihapus.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Delete order failed', ['order_code' => $order_code, 'err' => $e->getMessage()]);
            return back()->with('error', 'Gagal menghapus order.');
        }
    }

    /**
     * Query dasar untuk header + join ke BA, user, domisili.
     */
    private function baseQuery() {
        return $this->sobat()->table('order_header as oh')
            ->leftJoin('business_area as ba', 'ba.business_area_code', '=', 'oh.business_area_code')
            ->leftJoin('users as u', 'u.id', '=', 'oh.user_id')
            ->leftJoin('domisili as d', function ($j) {
                $j->on('d.id', '=', 'oh.ship_to')->on('d.user_id', '=', 'oh.user_id');
            })
            ->select([
                'oh.order_code','oh.order_date','oh.business_area_code',
                'ba.business_area_name','oh.user_id','u.fullname',
                'oh.delivery_type','oh.status','d.alamat',
            ]);
    }

    /**
     * Daftar status yang diizinkan (silakan sesuaikan).
     */
    private function statusOptions(): array
    {
        return [
            'Draft', 'Submitted', 'Approved', 'Rejected',
            'Shipped', 'Delivered', 'Cancelled',
            'Active', 'Inactive',
        ];
    }
}
