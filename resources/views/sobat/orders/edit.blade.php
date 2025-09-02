{{-- resources/views/sobat/orders/edit.blade.php --}}
@php
  $status = strtolower($header->status ?? '');
  $mode   = $mode ?? null; // 'deliver' | 'receive' | null

  $nextStatus = match ($status) {
    'ordered'   => 'delivered',
    'delivered' => 'received',
    'received'  => 'completed',
    default     => null,
  };
@endphp

<div>
  <form action="{{ route('orders.update', $header->order_code) }}" method="POST" id="formUpdateOrder">
    @csrf
    @method('PUT')

    {{-- bawa info ke controller --}}
    <input type="hidden" name="mode" value="{{ $mode }}">
    <input type="hidden" name="next_status" value="{{ $nextStatus }}">

    <div class="mb-2">
      <div class="small text-muted">Order Code</div>
      <div class="fw-semibold">{{ $header->order_code }}</div>
    </div>

    <div class="mb-3">
      <div class="small text-muted">Status Sekarang</div>
      <span class="badge bg-primary text-capitalize">{{ $status }}</span>
    </div>

    @if ($mode === 'deliver')
      {{-- STATUS ordered -> delivered --}}
      <div class="mb-3">
        <label class="form-label fw-semibold">Estimated Arrival Date</label>
        <div class="input-group">
          <input type="text"
                 name="estimated_arrival_date"
                 id="estimated_arrival_date"
                 class="form-control"
                 placeholder="YYYY-MM-DD"
                 value="{{ old('estimated_arrival_date', optional($header->estimated_arrival_date)->format('Y-m-d') ?? '') }}">
          <button class="btn btn-outline-secondary" type="button" id="btnPickEta" title="Pilih tanggal">
            <i class="ti ti-calendar"></i>
          </button>
        </div>
        <small class="text-muted">
          Mengubah tanggal ini akan mengubah status menjadi <b>delivered</b>.
          Pastikan ada detail dengan <b>Delivered Qty &gt; 0</b>.
        </small>
      </div>

    @elseif ($mode === 'receive')
      {{-- STATUS delivered -> received --}}
      <div class="mb-3">
        <label class="form-label fw-semibold">Actual Received Date</label>
        <div class="input-group">
          <input type="text"
                 name="actual_received_date"
                 id="actual_received_date"
                 class="form-control"
                 placeholder="YYYY-MM-DD"
                 value="{{ old('actual_received_date', optional($header->actual_received_date)->format('Y-m-d') ?? '') }}">
          <button class="btn btn-outline-secondary" type="button" id="btnPickArd" title="Pilih tanggal">
            <i class="ti ti-calendar"></i>
          </button>
        </div>
        <small class="text-muted">
          Mengubah tanggal ini akan mengubah status menjadi <b>received</b>.
          Pastikan ada detail dengan <b>Received Qty &gt; 0</b>.
        </small>
      </div>

    @else
      <div class="alert alert-info">
        Status <b>{{ $header->status }}</b> tidak dapat diubah melalui form ini.
      </div>
    @endif

    <div class="d-flex flex-wrap gap-2">
      {{-- Update ke status berikutnya --}}
      <button type="submit" class="btn btn-primary" name="action" value="update_status" {{ $nextStatus ? '' : 'disabled' }}>
        <i class="ti ti-arrow-right me-1"></i>
        Update Status
        @if($nextStatus) <span class="ms-1">→ {{ ucfirst($nextStatus) }}</span> @endif
      </button>

      {{-- Cancel hanya jika masih ordered --}}
      @if ($status === 'ordered')
        <button type="submit" class="btn btn-outline-danger" name="action" value="cancel_order">
          <i class="ti ti-ban me-1"></i> Cancel Order
        </button>
      @endif

      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
        Tutup
      </button>
    </div>
  </form>
</div>

{{-- Inline loader + init agar bekerja saat view ini di-load via AJAX --}}
<script>
(function(){
  // Tambah CSS flatpickr + z-index fix sekali saja
  function ensureFlatpickrCss() {
    if (!document.getElementById('flatpickr-cdn-css')) {
      const link = document.createElement('link');
      link.id = 'flatpickr-cdn-css';
      link.rel = 'stylesheet';
      link.href = 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css';
      document.head.appendChild(link);
    }
    if (!document.getElementById('flatpickr-zfix')) {
      const style = document.createElement('style');
      style.id = 'flatpickr-zfix';
      style.textContent = '.flatpickr-calendar{z-index:200000 !important;}';
      document.head.appendChild(style);
    }
  }

  // Load JS flatpickr jika belum ada
  function ensureFlatpickrJs() {
    return new Promise((resolve) => {
      if (window.flatpickr) return resolve();
      const s = document.createElement('script');
      s.src = 'https://cdn.jsdelivr.net/npm/flatpickr';
      s.onload = resolve;
      document.body.appendChild(s);
    });
  }

  function initPickers() {
    if (!window.flatpickr) return;

    const modal = document.getElementById('modal') || document.body;

    const setup = (inputId, btnId) => {
      const input = document.getElementById(inputId);
      if (!input) return;
      if (input._flatpickr) return; // jangan re-init

      const fp = flatpickr(input, {
        dateFormat: 'Y-m-d',
        allowInput: true,
        appendTo: modal,      // pastikan muncul di atas modal
        static: true,         // stabil di dalam modal
        disableMobile: true
      });

      const btn = document.getElementById(btnId);
      if (btn) btn.addEventListener('click', () => fp.open());
    };

    setup('estimated_arrival_date', 'btnPickEta');
    setup('actual_received_date',   'btnPickArd');
  }

  // Eksekusi berurutan
  ensureFlatpickrCss();
  ensureFlatpickrJs().then(initPickers);
})();
</script>
