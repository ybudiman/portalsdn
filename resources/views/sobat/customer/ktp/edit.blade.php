<div>
   <img src="" alt="">
   <form action="{{ route('customer.ktp.update', [Crypt::encrypt($customerKTP->user_id), Crypt::encrypt($customerKTP->id)]) }}" method="POST" id="formCustomerKTP">
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
            <option value="Laki-laki" {{ old('jenis_kelamin', $customerKTP->jenis_kelamin) === 'Laki-laki' ? 'selected' : '' }}>Laki-Laki</option>
            <option value="Perempuan" {{ old('jenis_kelamin', $customerKTP->jenis_kelamin) === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
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
   
      {{-- Provinsi --}}
      <div class="form-group mb-3">
         <label class="form-label">Provinsi</label>
         <select id="provinsi" name="provinsi_ktp" class="form-select">
            <option value="">-- Pilih Provinsi --</option>
            @foreach($provinsi as $prov)
               <option value="{{ $prov->kode_provinsi }}" 
                  {{ old('provinsi_ktp', $customerKTP->provinsi_ktp) == $prov->kode_provinsi ? 'selected' : '' }}>
                  {{ $prov->nama_provinsi }}
               </option>
            @endforeach
         </select>
         @error('provinsi_ktp')
            <div class="text-danger small mt-1">{{ $message }}</div>
         @enderror
      </div>

      {{-- Kota --}}
      <div class="form-group mb-3">
         <label class="form-label">Kota</label>
         <select id="kota" name="kota_ktp" class="form-select">
            <option value="">-- Pilih Kota --</option>
         </select>
      </div>

      {{-- Kecamatan --}}
      <div class="form-group mb-3">
         <label class="form-label">Kecamatan</label>
         <select id="kecamatan" name="kecamatan" class="form-select">
            <option value="">-- Pilih Kecamatan --</option>
         </select>
      </div>

      {{-- Kelurahan --}}
      <div class="form-group mb-3">
         <label class="form-label">Kelurahan</label>
         <select id="kelurahan" name="kelurahan" class="form-select">
            <option value="">-- Pilih Kelurahan --</option>
         </select>
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
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
   $(function () {
      // When province changes → load cities
      $('#provinsi').on('change', function () {
         let provCode = $(this).val();
         $('#kota').html('<option value="">-- Pilih Kota --</option>');
         $('#kecamatan').html('<option value="">-- Pilih Kecamatan --</option>');
         $('#kelurahan').html('<option value="">-- Pilih Kelurahan --</option>');

         if (provCode) {
            $.getJSON(`/area/kota/${provCode}`, function (data) {
               let options = '<option value="">-- Pilih Kota --</option>';
               data.forEach(item => {
                  options += `<option value="${item.kode_kota}">${item.nama_kota}</option>`;
               });
               $('#kota').html(options);
            });
         }
      });

      // When city changes → load districts
      $('#kota').on('change', function () {
         let kotaCode = $(this).val();
         $('#kecamatan').html('<option value="">-- Pilih Kecamatan --</option>');
         $('#kelurahan').html('<option value="">-- Pilih Kelurahan --</option>');

         if (kotaCode) {
            $.getJSON(`/area/kecamatan/${kotaCode}`, function (data) {
               let options = '<option value="">-- Pilih Kecamatan --</option>';
               data.forEach(item => {
                  options += `<option value="${item.kode_kecamatan}">${item.nama_kecamatan}</option>`;
               });
               $('#kecamatan').html(options);
            });
         }
      });

      // When district changes → load subdistricts
      $('#kecamatan').on('change', function () {
         let kecCode = $(this).val();
         $('#kelurahan').html('<option value="">-- Pilih Kelurahan --</option>');

         if (kecCode) {
            $.getJSON(`/area/kelurahan/${kecCode}`, function (data) {
               let options = '<option value="">-- Pilih Kelurahan --</option>';
               data.forEach(item => {
                  options += `<option value="${item.kode_kelurahan}">${item.nama_kelurahan}</option>`;
               });
               $('#kelurahan').html(options);
            });
         }
      });
   });
</script>
