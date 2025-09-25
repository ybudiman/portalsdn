<form action="{{ route('customer.update', Crypt::encrypt($customer->id)) }}" method="POST" id="formCustomer">
   @csrf
   @method('PUT')

   {{-- Fullname --}}
   <div class="form-group mb-3">
   <label class="form-label">Nama Customer</label>
   <input 
      type="text" 
      name="fullname" 
      class="form-control" 
      value="{{ old('fullname', $customer->fullname) }}" 
      disabled>
   @error('fullname')
      <div class="text-danger small mt-1">{{ $message }}</div>
   @enderror
   </div>

   {{-- Verified --}}
   <div class="form-group mb-3">
      <label class="form-label">Verified</label>
      <select name="verified" class="form-select" required>
         <option value="Y" {{ old('verified', $customer->verified) === 'Y' ? 'selected' : '' }}>Y - Verified</option>
         <option value="W" {{ old('verified', $customer->verified) === 'W' ? 'selected' : '' }}>W - Waiting for email verification</option>
         <option value="N" {{ old('verified', $customer->verified) === 'N' ? 'selected' : '' }}>N - Unverified</option>
         <option value="P" {{ old('verified', $customer->verified) === 'P' ? 'selected' : '' }}>P - Pending for account verification</option>
      </select>
      @error('verified')
         <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
   </div>


   {{-- Employee ID --}}
   <div class="form-group mb-3">
      <label class="form-label">Employee ID</label>
      <x-input-with-icon 
         label="Employee ID" 
         name="employee_id" 
         icon="ti ti-id-badge" 
         value="{{ old('employee_id', $customer->employee_id) }}" />
      @error('employee_id')
         <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
   </div>

   {{-- External Customer ID --}}
   <div class="form-group mb-3">
      <label class="form-label">External Customer ID</label>
      <x-input-with-icon 
         label="External Customer ID" 
         name="external_customer_id" 
         icon="ti ti-barcode" 
         value="{{ old('external_customer_id', $customer->external_customer_id) }}" />
      @error('external_customer_id')
         <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
   </div>

   {{-- Default Delivery Type --}}
   <div class="form-group mb-3">
      <label class="form-label">Default Delivery Type</label>
      <select name="default_delivery_type" class="form-select">
         <option value="franco" {{ old('default_delivery_type', $customer->default_delivery_type) === 'franco' ? 'selected' : '' }}>Franco</option>
         <option value="loco"   {{ old('default_delivery_type', $customer->default_delivery_type) === 'loco'   ? 'selected' : '' }}>Loco</option>
      </select>
      @error('default_delivery_type')
         <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
   </div>

   {{-- Business Area Code --}}
   <div class="form-group mb-3">
      <label class="form-label">Business Area Code</label>
      <select name="business_area_code" class="form-select">
         <option value="">-- Pilih Area --</option>
         @foreach($businessAreas as $area)
            <option value="{{ $area->business_area_code }}"
               {{ old('business_area_code', $customer->business_area_code) == $area->business_area_code ? 'selected' : '' }}>
               {{ $area->business_area_code }} - {{ $area->business_area_name }}
            </option>
         @endforeach
      </select>
      @error('business_area_code')
         <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
   </div>

   {{-- Submit --}}
   <div class="form-group mb-3">
      <button class="btn btn-primary w-100">
         <i class="ti ti-send me-1"></i> Submit
      </button>
   </div>
</form>
