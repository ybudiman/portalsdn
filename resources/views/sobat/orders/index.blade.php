@extends('layouts.app')
@section('titlepage','Sales Orders')

@section('content')
@section('navigasi')
  <span>Sales Orders</span>
@endsection

<div class="row">
  <div class="col-12">
    <div class="card">
      {{-- FILTERS --}}
      <div class="card-header">
        <form class="row g-2 align-items-end" method="GET" action="{{ route('orders.index') }}">
          <div class="col-lg-4 col-md-6">
            <label class="form-label">Business Area</label>
            <input type="text" name="business_area_name" class="form-control"
                   value="{{ request('business_area_name') }}" placeholder="Cari Business Area" list="baList">
            @isset($baOptions)
              <datalist id="baList">
                @foreach($baOptions as $ba) <option value="{{ $ba }}"></option> @endforeach
              </datalist>
            @endisset
          </div>

          <div class="col-lg-2 col-md-3">
            <label class="form-label">Dari</label>
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
          </div>
          <div class="col-lg-2 col-md-3">
            <label class="form-label">Sampai</label>
            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
          </div>

          <div class="col-lg-2 col-md-4">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="">Semua</option>
              @foreach(($statusOptions ?? []) as $st)
                <option value="{{ $st }}" @selected(request('status')===$st)>{{ $st }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-lg-2 col-md-4 d-flex gap-2">
            <button class="btn btn-primary w-100">
              <i class="ti ti-search me-1"></i> Cari
            </button>
            @if(request()->hasAny(['business_area_name','date_from','date_to','status']))
              <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary" title="Reset">
                <i class="ti ti-refresh"></i>
              </a>
            @endif
          </div>
        </form>
      </div>

      {{-- LIST --}}
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
              <tr>
                <th style="white-space:nowrap">Kode</th>
                <th>Tanggal</th>
                <th>Business Area</th>
                <th>User</th>
                <th>Delivery</th>
                <th>Status</th>
                <!-- <th>Alamat Ship To</th> -->
                <th class="text-center" style="width:120px">#</th>
              </tr>
            </thead>
            <tbody>
              @forelse($orders as $o)
                <tr>
                  <td style="white-space:nowrap">{{ $o->order_code }}</td>
                  <td>{{ \Illuminate\Support\Carbon::parse($o->order_date)->format('d-m-Y') }}</td>
                  <td>{{ $o->business_area_name }}</td>
                  <td>{{ $o->fullname }}</td>
                  <td>{{ $o->delivery_type }}</td>
                  <td>
                    @php
                      $status = strtolower(trim($o->status));
                      $badgeMap = [
                        'ordered'   => 'bg-secondary',          // abu-abu
                        'delivered' => 'bg-warning text-dark',  // kuning
                        'received'  => 'bg-info',               // biru
                        'completed' => 'bg-success',            // hijau
                        'canceled'  => 'bg-danger',             // merah
                        'cancelled' => 'bg-danger',             // (varian ejaan)
                      ];
                      $cls = $badgeMap[$status] ?? 'bg-secondary';
                    @endphp

                    <span class="badge {{ $cls }} text-capitalize">{{ $status }}</span>
                  </td>
                  <!-- <td>{{ $o->alamat }}</td> -->
                  <td class="text-center">
                    <a href="{{ route('orders.show', $o->order_code) }}" class="me-2" title="Detail">
                      <i class="ti ti-file-description text-info"></i>
                    </a>
                    @can('orders.edit')
                      {{-- trigger modal, jangan direct ke route --}}
                      <a href="#" class="me-2 btnEditOrder" data-code="{{ $o->order_code }}" data-status="{{ strtolower($o->status) }}" title="Edit">
                        <i class="ti ti-edit text-success"></i>
                      </a>
                    @endcan
                    @can('orders.delete')
                      <form action="{{ route('orders.destroy', $o->order_code) }}" method="POST"
                            class="d-inline deleteform">
                        @csrf @method('DELETE')
                        <a href="#" class="delete-confirm" title="Delete">
                          <i class="ti ti-trash text-danger"></i>
                        </a>
                      </form>
                    @endcan
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center text-muted">Tidak ada data</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="d-flex justify-content-end">
          {{ $orders->links() }}
        </div>
      </div>
    </div>
  </div>
</div>
<x-modal-form id="modal" show="loadmodal" />
@endsection

@push('myscript')
{{-- SweetAlert2 (gunakan CDN jika belum dimuat di layout) --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.addEventListener('click', function(e){
    const btn = e.target.closest('.delete-confirm');
    if(!btn) return;
    e.preventDefault();
    const form = btn.closest('form');

    Swal.fire({
      title: 'Hapus order ini?',
      text: 'Tindakan ini tidak bisa dibatalkan.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, hapus',
      cancelButtonText: 'Batal',
      reverseButtons: true
    }).then((res)=>{ if(res.isConfirmed) form.submit(); });
  });

  // Loader sederhana untuk isi modal
  function loadingModal() {
    const html = `
      <div class="sk-wave sk-primary" style="margin:auto">
        <div class="sk-wave-rect"></div>
        <div class="sk-wave-rect"></div>
        <div class="sk-wave-rect"></div>
        <div class="sk-wave-rect"></div>
        <div class="sk-wave-rect"></div>
      </div>
    `;
    $("#loadmodal").html(html);
  }

  // Helper membangun URL edit dari route Blade (pakai placeholder)
  function editUrl(code) {
    const base = "{{ route('orders.edit', ':code') }}"; // "/sobat/orders/:code/edit"
    return base.replace(':code', encodeURIComponent(code));
  }

  // Klik tombol edit → buka modal dan load view edit
  document.addEventListener('click', function(e){
    const btn = e.target.closest('.btnEditOrder');
    if (!btn) return;

    e.preventDefault();
    const code = btn.dataset.code;

    $("#modal").modal("show");
    $(".modal-title").text("Update Status Order");
    loadingModal();
    $("#loadmodal").load(editUrl(code));
  });
</script>
@endpush
