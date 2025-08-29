@extends('layouts.app')
@section('titlepage','Detail Order '.$header->order_code)

@section('content')
@php
  $status = strtolower((string)$header->status);
  $canEditAll    = auth()->user()->can('orders.edit');
  $canEditDeliver = $status === 'ordered';
  $canEditReceive = $status === 'delivered';
@endphp

<div class="card">
  <div class="card-header">
    <strong>{{ $header->order_code }}</strong>
    <span class="ms-2 text-muted">{{ \Illuminate\Support\Carbon::parse($header->order_date)->format('d-m-Y') }}</span>
    <span class="badge bg-primary ms-2">{{ $header->status }}</span>
  </div>
  <div class="card-body">
    <div class="row mb-3">
      <div class="col-md-4"><b>Business Area:</b> {{ $header->business_area_name }}</div>
      <div class="col-md-4"><b>User:</b> {{ $header->fullname }}</div>
      <div class="col-md-4"><b>Delivery:</b> {{ $header->delivery_type }}</div>
      <div class="col-md-12 mt-2"><b>Alamat Ship To:</b> {{ $header->alamat }}</div>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>Item</th>
            <th>Nama</th>
            <th class="text-end">Order Qty</th>
            <th class="text-end">Deliver Qty</th>
            <th class="text-end">Received Qty</th>
            <th class="text-end">Harga</th>
            <th class="text-end">Subtotal</th>
          </tr>
        </thead>
        <tbody>
        @foreach($details as $i => $d)
          @php
            $orderQty   = (int)($d->quantity_order ?? 0);
            $deliverQty = (int)($d->quantity_delivered ?? 0);
            $receiveQty = (int)($d->quantity_received ?? 0);
            $price      = (int)($d->price ?? 0);
          @endphp
          <tr data-row-id="{{ $d->id }}">
            <td>{{ $i+1 }}</td>
            <td>{{ $d->product_code ?? '-' }}</td>
            <td>{{ $d->product_name ?? '-' }}</td>
            <td class="text-end">{{ number_format($orderQty) }}</td>

            {{-- Deliver Qty --}}
            <td class="text-end">
              @if($canEditDeliver)
                <input type="number"
                       class="form-control form-control-sm text-end so-inline"
                       min="0" max="{{ $orderQty }}"
                       value="{{ $deliverQty }}"
                       data-field="quantity_delivered"
                       data-id="{{ $d->id }}"
                       aria-label="Deliver Qty">
                <small class="text-muted d-block">max {{ $orderQty }}</small>
              @else
                {{ number_format($deliverQty) }}
              @endif
            </td>

            {{-- Received Qty --}}
            <td class="text-end">
              @if($canEditReceive)
                <input type="number"
                       class="form-control form-control-sm text-end so-inline"
                       min="0" max="{{ $deliverQty }}"
                       value="{{ $receiveQty }}"
                       data-field="quantity_received"
                       data-id="{{ $d->id }}"
                       aria-label="Received Qty">
                <small class="text-muted d-block">max {{ $deliverQty }}</small>
              @else
                {{ number_format($receiveQty) }}
              @endif
            </td>

            <td class="text-end">{{ number_format($price) }}</td>
            <td class="text-end">{{ number_format($orderQty * $price) }}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>

    <a href="{{ route('orders.index') }}" class="btn btn-secondary mt-2">
      <i class="ti ti-arrow-left me-1"></i> Kembali
    </a>
  </div>
</div>
@endsection

@push('myscript')
{{-- SweetAlert2 (CDN) --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function(){
  const token   = '{{ csrf_token() }}';
  const baseUrl = "{{ route('orders.detail.update', ['order_code' => $header->order_code, 'detail' => 0]) }}";

  function buildUrl(id){ return baseUrl.replace(/\/0$/, '/' + id); }

  // helper notifikasi
  function toastOk(title='Tersimpan'){
    Swal.fire({ toast:true, position:'top', icon:'success', title, timer:1200, showConfirmButton:false });
  }
  function modalErr(title='Gagal', text='Terjadi kesalahan'){
    Swal.fire({ icon:'error', title, text });
  }
  function modalWarn(title='Tidak valid', html=''){
    Swal.fire({ icon:'warning', title, html });
  }

  async function saveInline(el){
    const id    = el.dataset.id;
    const field = el.dataset.field;
    let   value = el.value;
    value = value === '' ? 0 : parseInt(value, 10);
    if (Number.isNaN(value)) value = 0;

    try{
      const res = await fetch(buildUrl(id), {
        method: 'PATCH',
        headers: {
          'X-CSRF-TOKEN': token,
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ field, value })
      });

      // Unauthorized (403) dari gate/policy
      if (res.status === 403){
        // kembalikan ke nilai sebelumnya
        if (el.dataset.prev !== undefined) el.value = el.dataset.prev;
        Swal.fire({
          icon:'error',
          title:'Tidak diizinkan',
          text:'Anda tidak memiliki izin untuk mengubah order ini.'
        });
        return;
      }

      // Parse JSON kalau ada
      let data = {};
      try { data = await res.json(); } catch(_) {}

      // Validasi gagal (422) atau error custom backend
      if (!res.ok || data.status !== 'ok'){
        let msg = data.message || 'Gagal menyimpan.';
        if (res.status === 422 && data.errors){
          msg += '\n' + Object.values(data.errors).flat().join('\n');
        }
        modalWarn('Tidak bisa menyimpan', msg.replace(/\n/g,'<br>'));
        // rollback ke nilai sebelumnya (atau yang dikembalikan server)
        if (data.value !== undefined) el.value = data.value;
        else if (el.dataset.prev !== undefined) el.value = el.dataset.prev;
        el.focus();
        return;
      }

      // Sukses → gunakan nilai final dari server (sudah di-clamp backend)
      el.dataset.prev = data.value;
      el.value = data.value;
      toastOk();
    }catch(e){
      if (el.dataset.prev !== undefined) el.value = el.dataset.prev;
      modalErr('Gagal menyimpan', e.message);
    }
  }

  // simpan nilai sebelumnya saat fokus, agar bisa rollback jika gagal
  document.querySelectorAll('.so-inline').forEach((el)=>{
    el.addEventListener('focus', ()=>{ el.dataset.prev = el.value; });
    el.addEventListener('change', ()=> saveInline(el));
    el.addEventListener('keydown', (e)=> {
      if(e.key === 'Enter'){
        e.preventDefault();
        saveInline(el);
        el.blur();
      }
    });
  });
})();
</script>
@endpush
