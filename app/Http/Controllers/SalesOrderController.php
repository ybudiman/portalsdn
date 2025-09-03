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
        $details = $this->sobat()->table('order_detail as od')
        ->leftJoin('products as p', 'p.id', '=', 'od.product_id')
        ->where('od.order_header_id', $header->id)
        ->select([
            'od.id',
            'od.product_id',
            // normalisasi: pakai kolom yang ada di DB-mu (pilih salah satu)
            DB::raw('COALESCE(od.quantity_order, 0) as quantity_order'),
            DB::raw('COALESCE(od.quantity_delivered, 0) as quantity_delivered'),
            DB::raw('COALESCE(od.quantity_received, 0) as quantity_received'),
            DB::raw('COALESCE(od.price, 0) as price'),
            'p.product_code',
            'p.product_name',
        ])
        ->get();

        return view('sobat.orders.show', compact('header', 'details'));
    }

    public function edit(string $order_code)
    {
        // ambil header minimal (pakai koneksi mysqlsobat)
        $header = $this->sobat()->table('order_header')
            ->select('id','order_code','status',
                    'estimated_arrival_date','actual_received_date')
            ->where('order_code', $order_code)
            ->first();

        abort_if(!$header, 404, 'Order tidak ditemukan');

        // tentukan mode form di popup
        // ordered  -> form Estimated Arrival (menuju delivered)
        // delivered-> form Actual Received (menuju received)
        $mode = null;
        if (strtolower($header->status) === 'ordered')   $mode = 'deliver';
        if (strtolower($header->status) === 'delivered') $mode = 'receive';

        return view('sobat.orders.edit', compact('header','mode'));
    }

    public function update(Request $r, string $order_code)
    {
        $header = $this->sobat()->table('order_header')
            ->where('order_code', $order_code)
            ->first();

        abort_if(!$header, 404, 'Order tidak ditemukan');

        $statusNow = strtolower($header->status);

        // --- TRANSISI ordered -> delivered ---
        if ($statusNow === 'ordered') {
            $r->validate([
                'estimated_arrival_date' => ['required','date'],
            ]);

            // cek ada qty delivered > 0 di detail
            $sumDelivered = $this->sobat()->table('order_detail')
                ->where('order_header_id', $header->id)
                ->sum('quantity_delivered');

            if ((int)$sumDelivered <= 0) {
                return back()->with('error',
                    'Tidak bisa ubah ke Delivered: belum ada Quantity Delivered (>0) pada detail.');
            }

            $affected = $this->sobat()->table('order_header')
                ->where('order_code', $order_code)
                ->update([
                    'estimated_arrival_date' => $r->estimated_arrival_date,
                    'status'                 => 'delivered',
                    'updated_at'             => now(),
                ]);

            return redirect()
                ->route('orders.show', $order_code)
                ->with($affected ? 'success' : 'error',
                    $affected ? 'Order di-update ke Delivered.'
                                : 'Tidak ada perubahan.');
        }

        // --- TRANSISI delivered -> received ---
        if ($statusNow === 'delivered') {
            $r->validate([
                'actual_received_date' => ['required','date'],
            ]);

            // cek ada qty received > 0 di detail
            $sumReceived = $this->sobat()->table('order_detail')
                ->where('order_header_id', $header->id)
                ->sum('quantity_received');

            if ((int)$sumReceived <= 0) {
                return back()->with('error',
                    'Tidak bisa ubah ke Received: belum ada Quantity Received (>0) pada detail.');
            }

            $affected = $this->sobat()->table('order_header')
                ->where('order_code', $order_code)
                ->update([
                    'actual_received_date' => $r->actual_received_date,
                    'status'               => 'received',
                    'updated_at'           => now(),
                ]);

            return redirect()
                ->route('orders.show', $order_code)
                ->with($affected ? 'success' : 'error',
                    $affected ? 'Order di-update ke Received.'
                                : 'Tidak ada perubahan.');
        }

        // selain dua status itu, tampilkan pesan
        return back()->with(
            'error',
            'Status saat ini tidak dapat diubah melalui popup ini.'
        );
    }

    /**
     * Hapus order (detail lalu header) dalam transaksi.
     */
    public function destroy(string $order_code)
    {
        // cari id header dulu
        $header = $this->sobat()->table('order_header')
            ->select('id')
            ->where('order_code', $order_code)
            ->first();

        if (!$header) {
            return back()->with('error', 'Order tidak ditemukan atau sudah dihapus.');
        }

        $this->sobat()->beginTransaction();
        try {
            $this->sobat()->table('order_detail')
                ->where('order_header_id', $header->id)
                ->delete();

            $this->sobat()->table('order_header')
                ->where('id', $header->id)
                ->delete();

            $this->sobat()->commit();
            return back()->with('success', 'Order berhasil dihapus.');
        } catch (\Throwable $e) {
            $this->sobat()->rollBack();
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
                'oh.id',
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
            'Ordered', 'Delivered', 'Received', 'Completed', 'Cancelled',
        ];
    }

    public function updateDetail(Request $r, string $order_code, int $detailId)
    {
        // Ambil header & status
        $header = $this->sobat()->table('order_header')
            ->select('id','status')
            ->where('order_code', $order_code)
            ->first();

        if (!$header) {
            return response()->json(['status'=>'error','message'=>'Order tidak ditemukan.'], 404);
        }

        // Ambil detail dan pastikan milik header ini
        $detail = $this->sobat()->table('order_detail')
            ->where('id', $detailId)
            ->first();

        if (!$detail || (int)$detail->order_header_id !== (int)$header->id) {
            return response()->json(['status'=>'error','message'=>'Detail tidak cocok dengan order.'], 404);
        }

        $field  = (string)$r->input('field');   // 'quantity_delivered' / 'quantity_received'
        $value  = (int)($r->input('value', 0));
        $status = strtolower((string)$header->status);

        $orderQty   = (int)($detail->quantity_order ?? 0);
        $deliverQty = (int)($detail->quantity_delivered ?? 0);
        $receiveQty = (int)($detail->quantity_received ?? 0);

        if ($field === 'quantity_delivered') {
            if ($status !== 'ordered') {
                return response()->json([
                    'status'=>'error',
                    'message'=>'Hanya bisa edit Deliver Qty pada status ordered.',
                    'value' => $deliverQty
                ], 403);
            }
            if ($value < 0) $value = 0;
            if ($value > $orderQty) $value = $orderQty;

            $this->sobat()->table('order_detail')->where('id', $detailId)->update([
                'quantity_delivered' => $value,
                'updated_at' => now(),
            ]);

            return response()->json(['status'=>'ok','value'=>$value]);
        }

        if ($field === 'quantity_received') {
            if ($status !== 'delivered') {
                return response()->json([
                    'status'=>'error',
                    'message'=>'Hanya bisa edit Received Qty pada status delivered.',
                    'value' => $receiveQty
                ], 403);
            }
            if ($value < 0) $value = 0;
            if ($value > $deliverQty) $value = $deliverQty;

            $this->sobat()->table('order_detail')->where('id', $detailId)->update([
                'quantity_received' => $value,
                'updated_at' => now(),
            ]);

            return response()->json(['status'=>'ok','value'=>$value]);
        }

        return response()->json(['status'=>'error','message'=>'Field tidak diizinkan.'], 422);
    }

}
