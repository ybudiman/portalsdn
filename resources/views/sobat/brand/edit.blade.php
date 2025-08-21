<form action="{{ route('brand.update', Crypt::encrypt($brand->brand_name)) }}" method="POST" id="formBrand" enctype="multipart/form-data">
   @csrf
   @method('PUT')
   <label class="form-label">Nama Brand</label>
   <x-input-with-icon label="Brand Name" name="brand_name" icon="ti ti-barcode" value="{{ $brand->brand_name }}" disabled />
   <label class="form-label">Deskripsi Brand</label>
   <x-input-with-icon label="Description" name="brand_description" icon="ti ti-building" value="{{ old('brand_description', $brand->brand_description) }}" />
   <div class="form-group mb-3">
      <label class="form-label">Status</label>
      <select name="status" class="form-select">
            <option value="Active"   {{ old('status', $brand->status) === 'Active'   ? 'selected' : '' }}>Active</option>
            <option value="Inactive" {{ old('status', $brand->status) === 'Inactive' ? 'selected' : '' }}>Inactive</option>
      </select>
      @error('status')
         <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
   </div>
   @php
       $file = trim((string)($brand->brand_image ?? ''));
       $src  = $file === ''
            ? asset('images/placeholder-brand.png')
            : (preg_match('~^https?://~i', $file)
                ? $file
                : 'https://apisobat.sdn.id/brand-img/'.ltrim($file,'/'));
   @endphp

   <div class="form-group mb-3">
      <div class="d-flex align-items-center gap-3">
         <img id="previewBrandImage" src="{{ $src }}" alt="{{ $brand->brand_name }}"
              style="height:64px;object-fit:contain;border:1px solid #eee;border-radius:.5rem;padding:4px;background:#fff;">
         <div class="flex-grow-1">
            <input type="file" name="brand_image_file" id="brand_image_file" class="form-control" accept="image/*">
            <small class="text-muted">
               (Opsional) Pilih file gambar baru. Biarkan kosong untuk tetap memakai gambar saat ini.
            </small>
         </div>
      </div>
      {{-- Simpan nilai lama untuk jaga-jaga di controller --}}
      <input type="hidden" name="brand_image_old" value="{{ $brand->brand_image }}">
   </div>

   <div class="form-group mb-3">
      <button class="btn btn-primary w-100"><i class="ti ti-send me-1"></i> Submit</button>
   </div>
</form>
<!-- <script src="{{ asset('assets/js/pages/departemen.js') }}"></script> -->
<script>
document.getElementById('brand_image_file')?.addEventListener('change', function (e) {
  const f = e.target.files?.[0];
  if (f) document.getElementById('previewBrandImage').src = URL.createObjectURL(f);
});
</script>