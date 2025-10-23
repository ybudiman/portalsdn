<form action="{{ route('discount.update', Crypt::encrypt($discount->id)) }}" method="POST" id="formDiscount">
   @csrf
   @method('PUT')

   {{-- DISCOUNT NAME --}}
   <div class="mb-3">
      <label class="form-label">Nama Discount</label>
      <x-input-with-icon 
         label="Discount Name" 
         name="discount_name" 
         icon="ti ti-discount" 
         value="{{ old('discount_name', $discount->discount_name) }}" />
      @error('discount_name')
         <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
   </div>

   {{-- LEVEL --}}
   <div class="mb-3">
      <label class="form-label">Level</label>
      <x-input-with-icon 
         label="Level" 
         name="level" 
         icon="ti ti-layers-difference" 
         value="{{ old('level', $discount->level) }}" />
      @error('level')
         <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
   </div>

   {{-- BUSINESS AREA CODE --}}
   <div class="mb-3">
      <label class="form-label">Kode Cabang</label>
      <div class="input-group">
         <span class="input-group-text"><i class="ti ti-building-store"></i></span>
         <select name="business_area_code" class="form-select">
            <option value="">-- Pilih Kode Cabang --</option>
            @foreach ($branches as $branch)
               <option value="{{ $branch->business_area_code }}"
                  {{ old('business_area_code', $discount->business_area_code) == $branch->business_area_code ? 'selected' : '' }}>
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
   <div class="mb-3">
      <label class="form-label">Tanggal Mulai</label>
      <input 
         type="date" 
         name="start_date" 
         class="form-control" 
         value="{{ old('start_date', \Carbon\Carbon::parse($discount->start_date)->format('Y-m-d')) }}">
      @error('start_date')
         <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
   </div>

   {{-- FINISH DATE --}}
   <div class="mb-3">
      <label class="form-label">Tanggal Selesai</label>
      <input 
         type="date" 
         name="finish_date" 
         class="form-control" 
         value="{{ old('finish_date', \Carbon\Carbon::parse($discount->finish_date)->format('Y-m-d')) }}">
      @error('finish_date')
         <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
   </div>

   {{-- SUBMIT BUTTON --}}
   <div class="form-group mb-3">
      <button class="btn btn-primary w-100">
         <i class="ti ti-send me-1"></i> Update Discount
      </button>
   </div>
</form>
