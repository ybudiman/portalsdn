<div>
   <form action="{{ route('customer.domisili.update', [Crypt::encrypt($customerDomicile->user_id), Crypt::encrypt($customerDomicile->id)]) }}" 
         method="POST" 
         id="formCustomerDomicile">
      @csrf
      @method('PUT')

      {{-- Alamat --}}
      <div class="form-group mb-3">
         <label class="form-label">Alamat</label>
         <textarea name="alamat" class="form-control" rows="2">{{ old('alamat', $customerDomicile->alamat) }}</textarea>
         @error('alamat')
            <div class="text-danger small mt-1">{{ $message }}</div>
         @enderror
      </div>

      {{-- Provinsi --}}
      <div class="form-group mb-3">
         <label class="form-label">Provinsi</label>
         <select id="provinsi" name="kode_provinsi" class="form-select">
            <option value="">-- Pilih Provinsi --</option>
            @foreach($provinsi as $p)
               <option value="{{ $p->kode_provinsi }}" 
                  {{ old('kode_provinsi', $customerDomicile->kode_provinsi) == $p->kode_provinsi ? 'selected' : '' }}>
                  {{ $p->nama_provinsi }}
               </option>
            @endforeach
         </select>
         @error('kode_provinsi')
            <div class="text-danger small mt-1">{{ $message }}</div>
         @enderror
      </div>

      {{-- Kota --}}
      <div class="form-group mb-3">
         <label class="form-label">Kota</label>
         <select id="kota" name="kode_kota" class="form-select">
            <option value="">-- Pilih Kota --</option>
         </select>
         @error('kode_kota')
            <div class="text-danger small mt-1">{{ $message }}</div>
         @enderror
      </div>

      {{-- Kecamatan --}}
      <div class="form-group mb-3">
         <label class="form-label">Kecamatan</label>
         <select id="kecamatan" name="kode_kecamatan" class="form-select">
            <option value="">-- Pilih Kecamatan --</option>
         </select>
         @error('kode_kecamatan')
            <div class="text-danger small mt-1">{{ $message }}</div>
         @enderror
      </div>

      {{-- Kelurahan --}}
      <div class="form-group mb-3">
         <label class="form-label">Kelurahan</label>
         <select id="kelurahan" name="kode_kelurahan" class="form-select">
            <option value="">-- Pilih Kelurahan --</option>
         </select>
         @error('kode_kelurahan')
            <div class="text-danger small mt-1">{{ $message }}</div>
         @enderror
      </div>



      {{-- Kode Pos --}}
      <div class="form-group mb-3">
         <label class="form-label">Kode Pos</label>
         <x-input-with-icon 
            label="Kode Pos" 
            name="kode_pos" 
            icon="ti ti-mail" 
            value="{{ old('kode_pos', $customerDomicile->kode_pos) }}" />
         @error('kode_pos')
            <div class="text-danger small mt-1">{{ $message }}</div>
         @enderror
      </div>

      {{-- Longitude --}}
      <div class="form-group mb-3">
         <label class="form-label">Longitude</label>
         <x-input-with-icon 
            label="Longitude" 
            name="longitude" 
            icon="ti ti-compass" 
            value="{{ old('longitude', $customerDomicile->longitude) }}" />
         @error('longitude')
            <div class="text-danger small mt-1">{{ $message }}</div>
         @enderror
      </div>

      {{-- Latitude --}}
      <div class="form-group mb-3">
         <label class="form-label">Latitude</label>
         <x-input-with-icon 
            label="Latitude" 
            name="latitude" 
            icon="ti ti-compass" 
            value="{{ old('latitude', $customerDomicile->latitude) }}" />
         @error('latitude')
            <div class="text-danger small mt-1">{{ $message }}</div>
         @enderror
      </div>

      {{-- Status --}}
      <div class="form-group mb-3">
         <label class="form-label">Status</label>
         <select name="status" class="form-select">
            <option value="Active" {{ old('status', $customerDomicile->status) === 'Active' ? 'selected' : '' }}>Active</option>
            <option value="Inactive" {{ old('status', $customerDomicile->status) === 'Inactive' ? 'selected' : '' }}>Inactive</option>
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

   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script>
      $(function () {
         // Province → City
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

                  // Auto-select if editing
                  let selectedKota = "{{ old('kode_kota', $customerDomicile->kode_kota) }}";
                  if (selectedKota) {
                     $('#kota').val(selectedKota).trigger('change');
                  }
               });
            }
         });

         // City → District
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

                  let selectedKecamatan = "{{ old('kode_kecamatan', $customerDomicile->kode_kecamatan) }}";
                  if (selectedKecamatan) {
                     $('#kecamatan').val(selectedKecamatan).trigger('change');
                  }
               });
            }
         });

         // District → Subdistrict
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

                  let selectedKelurahan = "{{ old('kode_kelurahan', $customerDomicile->kode_kelurahan) }}";
                  if (selectedKelurahan) {
                     $('#kelurahan').val(selectedKelurahan);
                  }
               });
            }
         });

         // On page load → trigger province to repopulate hierarchy
         let selectedProv = "{{ old('kode_provinsi', $customerDomicile->kode_provinsi) }}";
         if (selectedProv) {
            $('#provinsi').val(selectedProv).trigger('change');
         }
      });
   </script>

</div>
