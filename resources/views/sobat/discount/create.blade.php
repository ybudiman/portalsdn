<form action="{{ route('discount.store') }}" id="formCreateDiscount" method="POST">
  @csrf

  {{-- DISCOUNT NAME --}}
  <x-input-with-icon-label
      icon="ti ti-discount"
      label="Nama Discount"
      name="discount_name"
      value="{{ old('discount_name') }}" />
  @error('discount_name') 
    <div class="text-danger small mb-2">{{ $message }}</div> 
  @enderror

  {{-- LEVEL --}}
  <x-input-with-icon-label
      icon="ti ti-layers-difference"
      label="Level"
      name="level"
      value="{{ old('level') }}" />
  @error('level') 
    <div class="text-danger small mb-2">{{ $message }}</div> 
  @enderror

  {{-- BUSINESS AREA CODE (Dropdown) --}}
  <div class="form-group mb-3">
    <label class="form-label" style="font-weight:600">Kode Cabang</label>
    <div class="input-group">
      <span class="input-group-text"><i class="ti ti-building-store"></i></span>
      <select name="business_area_code" class="form-select">
        <option value="">-- Pilih Kode Cabang --</option>
        @foreach ($branches as $branch)
          <option value="{{ $branch->business_area_code }}"
            {{ old('business_area_code') == $branch->business_area_code ? 'selected' : '' }}>
            {{ $branch->business_area_code }} - {{ $branch->business_area_name ?? '' }}
          </option>
        @endforeach
      </select>
    </div>
    @error('business_area_code') 
      <div class="text-danger small mt-1">{{ $message }}</div> 
    @enderror
  </div>

  {{-- START DATE --}}
  <div class="form-group mb-3">
    <label class="form-label" style="font-weight:600">Tanggal Mulai</label>
    <div class="input-group">
      <span class="input-group-text"><i class="ti ti-calendar-event"></i></span>
      <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
    </div>
    @error('start_date') 
      <div class="text-danger small mt-1">{{ $message }}</div> 
    @enderror
  </div>

  {{-- FINISH DATE --}}
  <div class="form-group mb-3">
    <label class="form-label" style="font-weight:600">Tanggal Selesai</label>
    <div class="input-group">
      <span class="input-group-text"><i class="ti ti-calendar-time"></i></span>
      <input type="date" name="finish_date" class="form-control" value="{{ old('finish_date') }}">
    </div>
    @error('finish_date') 
      <div class="text-danger small mt-1">{{ $message }}</div> 
    @enderror
  </div>

  {{-- SUBMIT BUTTON --}}
  <div class="form-group mb-3">
    <button class="btn btn-primary w-100" type="submit">
      <i class="ti ti-send me-1"></i> Simpan Discount
    </button>
  </div>
</form>