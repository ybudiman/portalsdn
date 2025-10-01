<form action="{{ route('principal.store') }}" id="formCreatePrincipal" method="POST">
  @csrf

  {{-- PRINCIPAL CODE --}}
  <x-input-with-icon-label
      icon="ti ti-barcode"
      label="Kode Principal"
      name="principal_code"
      value="{{ old('principal_code') }}" />
  @error('principal_code') 
    <div class="text-danger small mb-2">{{ $message }}</div> 
  @enderror

  {{-- PRINCIPAL NAME --}}
  <x-input-with-icon-label
      icon="ti ti-building"
      label="Nama Principal"
      name="principal_name"
      value="{{ old('principal_name') }}" />
  @error('principal_name') 
    <div class="text-danger small mb-2">{{ $message }}</div> 
  @enderror

  {{-- STATUS --}}
  <div class="form-group mb-3">
    <label class="form-label" style="font-weight:600">Status</label>
    @php $st = old('status', 'Active'); @endphp
    <select name="status" class="form-select">
      <option value="Active"   {{ $st === 'Active'   ? 'selected' : '' }}>Active</option>
      <option value="Inactive" {{ $st === 'Inactive' ? 'selected' : '' }}>Inactive</option>
    </select>
    @error('status') 
      <div class="text-danger small mt-1">{{ $message }}</div> 
    @enderror
  </div>

  {{-- SUBMIT BUTTON --}}
  <div class="form-group mb-3">
    <button class="btn btn-primary w-100" type="submit">
      <i class="ti ti-send me-1"></i> Submit
    </button>
  </div>
</form>