<form action="{{ route('customer.ktp.update', [$customerKTP->user_id, $customerKTP->id]) }}" method="POST" id="formCustomerKTP">
   @csrf
   @method('PUT')

   {{-- Nama --}}
   <div class="form-group mb-3">
      <label class="form-label">Nama</label>
      <x-input-with-icon 
         label="Nama" 
         name="nama" 
         icon="ti ti-user" 
         value="{{ old('nama', $customerKTP->nama) }}" 
         required />
      @error('nama')
         <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
   </div>

   {{-- NIK --}}
   <div class="form-group mb-3">
      <label class="form-label">NIK</label>
      <x-input-with-icon 
         label="NIK" 
         name="NIK" 
         icon="ti ti-id-badge" 
         value="{{ old('NIK', $customerKTP->NIK) }}" 
         required />
      @error('NIK')
         <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
   </div>

   {{-- TTL --}}
   <div class="form-group mb-3">
      <label class="form-label">Tempat Tanggal Lahir</label>
      <x-input-with-icon 
         label="TTL" 
         name="TTL" 
         icon="ti ti-calendar" 
         value="{{ old('TTL', $customerKTP->TTL) }}" />
      @error('TTL')
         <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
   </div>

   {{-- Jenis Kelamin --}}
   <div class="form-group mb-3">
      <label class="form-label">Jenis Kelamin</label>
      <select name="jenis_kelamin" class="form-select">
         <option value="L" {{ old('jenis_kelamin', $customerKTP->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-Laki</option>
         <option value="P" {{ old('jenis_kelamin', $customerKTP->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option>
      </select>
      @error('jenis_kelamin')
         <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
   </div>

   {{-- Agama --}}
   <div class="form-group mb-3">
      <label class="form-label">Agama</label>
      <x-input-with-icon 
         label="Agama" 
         name="agama" 
         icon="ti ti-heart" 
         value="{{ old('agama', $customerKTP->agama) }}" />
      @error('agama')
         <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
   </div>

   {{-- Alamat --}}
   <div class="form-group mb-3">
      <label class="form-label">Alamat</label>
      <textarea name="alamat" class="form-control">{{ old('alamat', $customerKTP->alamat) }}</textarea>
      @error('alamat')
         <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
   </div>

   {{-- RT/RW --}}
   <div class="form-group mb-3">
      <label class="form-label">RT/RW</label>
      <x-input-with-icon 
         label="RT/RW" 
         name="rt_rw" 
         icon="ti ti-home" 
         value="{{ old('rt_rw', $customerKTP->rt_rw) }}" />
      @error('rt_rw')
         <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
   </div>

   {{-- Kecamatan --}}
   <div class="form-group mb-3">
      <label class="form-label">Kecamatan</label>
      <x-input-with-icon 
         label="Kecamatan" 
         name="kecamatan" 
         icon="ti ti-map" 
         value="{{ old('kecamatan', $customerKTP->kecamatan) }}" />
      @error('kecamatan')
         <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
   </div>

   {{-- Kelurahan --}}
   <div class="form-group mb-3">
      <label class="form-label">Kelurahan</label>
      <x-input-with-icon 
         label="Kelurahan" 
         name="kelurahan" 
         icon="ti ti-map-pin" 
         value="{{ old('kelurahan', $customerKTP->kelurahan) }}" />
      @error('kelurahan')
         <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
   </div>

   {{-- Status --}}
   <div class="form-group mb-3">
      <label class="form-label">Status</label>
      <select name="status" class="form-select">
         <option value="Active" {{ old('status', $customerKTP->status) === 'Active' ? 'selected' : '' }}>Active</option>
         <option value="Inactive" {{ old('status', $customerKTP->status) === 'Inactive' ? 'selected' : '' }}>Inactive</option>
      </select>
      @error('status')
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
