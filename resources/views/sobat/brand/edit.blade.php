<form action="{{ route('brand.update', Crypt::encrypt($brand->brand_name)) }}" method="POST" id="formBrand">
   @csrf
   @method('PUT')
   <x-input-with-icon label="Brand Name" name="brand_name" icon="ti ti-barcode" value="{{ $brand->brand_name }}" disabled />
   <x-input-with-icon label="Description" name="nama_dept" icon="ti ti-building" value="{{ $brand->brand_description }}" />
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

   <di class="form-group mb-3">
      <button class="btn btn-primary w-100"><i class="ti ti-send me-1"></i> Submit</button>
   </di>
</form>
<script src="{{ asset('assets/js/pages/departemen.js') }}"></script>