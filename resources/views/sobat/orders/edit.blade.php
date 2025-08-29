{{-- resources/views/sobat/orders/edit.blade.php --}}
<div>
  <form action="{{ route('orders.update', $header->order_code) }}" method="POST" id="formUpdateOrder">
    @csrf
    @method('PUT')

    <div class="mb-2">
      <div class="small text-muted">Order Code</div>
      <div class="fw-semibold">{{ $header->order_code }}</div>
    </div>
    <div class="mb-3">
      <div class="small text-muted">Status Sekarang</div>
      <span class="badge bg-primary text-capitalize">{{ $header->status }}</span>
    </div>

    @if ($mode === 'deliver')
      {{-- STATUS ordered -> delivered --}}
      <div class="mb-3">
        <label class="form-label fw-semibold">Estimated Arrival Date</label>
        <input type="text"
               name="estimated_arrival_date"
               class="form-control flatpickr-date"
               placeholder="YYYY-MM-DD"
               value="{{ old('estimated_arrival_date', optional($header->estimated_arrival_date)->format('Y-m-d') ?? '') }}">
        <small class="text-muted">
          Mengubah tanggal ini akan mengubah status order menjadi <b>delivered</b>.
          Sebelumnya pastikan ada detail dengan <b>Delivered Qty &gt; 0</b>.
        </small>
      </div>

    @elseif ($mode === 'receive')
      {{-- STATUS delivered -> received --}}
      <div class="mb-3">
        <label class="form-label fw-semibold">Actual Received Date</label>
        <input type="text"
               name="actual_received_date"
               class="form-control flatpickr-date"
               placeholder="YYYY-MM-DD"
               value="{{ old('actual_received_date', optional($header->actual_received_date)->format('Y-m-d') ?? '') }}">
        <small class="text-muted">
          Mengubah tanggal ini akan mengubah status order menjadi <b>received</b>.
          Sebelumnya pastikan ada detail dengan <b>Received Qty &gt; 0</b>.
        </small>
      </div>

    @else
      <div class="alert alert-info">
        Status <b>{{ $header->status }}</b> tidak dapat diubah melalui form ini.
      </div>
    @endif

    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-primary" {{ $mode ? '' : 'disabled' }}>
        <i class="ti ti-device-floppy me-1"></i> Simpan
      </button>
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
        Batal
      </button>
    </div>
  </form>
</div>

@push('myscript')
<script>
  // init flatpickr bila ada di halamanmu
  if (window.flatpickr) {
    document.querySelectorAll('.flatpickr-date').forEach(el => {
      flatpickr(el, { dateFormat: 'Y-m-d' });
    });
  }

  // optional: submit -> loading
  document.getElementById('formUpdateOrder')?.addEventListener('submit', function(){
    // bisa tambahkan spinner dsb
  });
</script>
@endpush
