<form action="{{ route('principal.update', Crypt::encrypt($principal->principal_code)) }}" method="POST" id="formPrincipal">
   @csrf
   @method('PUT')

   {{-- PRINCIPAL CODE (disabled, since it’s the key) --}}
   <label class="form-label">Kode Principal</label>
   <x-input-with-icon 
      label="Principal Code" 
      name="principal_code" 
      icon="ti ti-barcode" 
      value="{{ $principal->principal_code }}" 
      disabled />

   {{-- PRINCIPAL NAME --}}
   <label class="form-label">Nama Principal</label>
   <x-input-with-icon 
      label="Principal Name" 
      name="principal_name" 
      icon="ti ti-building" 
      value="{{ old('principal_name', $principal->principal_name) }}" />

   {{-- STATUS --}}
   <div class="form-group mb-3">
      <label class="form-label">Status</label>
      <select name="status" class="form-select">
         <option value="Active"   {{ old('status', $principal->status) === 'Active'   ? 'selected' : '' }}>Active</option>
         <option value="Inactive" {{ old('status', $principal->status) === 'Inactive' ? 'selected' : '' }}>Inactive</option>
      </select>
      @error('status')
         <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
   </div>

   {{-- SUBMIT BUTTON --}}
   <div class="form-group mb-3">
      <button class="btn btn-primary w-100">
         <i class="ti ti-send me-1"></i> Submit
      </button>
   </div>
</form>