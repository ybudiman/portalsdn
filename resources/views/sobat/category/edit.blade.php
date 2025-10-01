<form action="{{ route('category.update', Crypt::encrypt($category->category_name)) }}" method="POST" id="formCategory" enctype="multipart/form-data">
   @csrf
   @method('PUT')

   {{-- CATEGORY NAME (disabled, since it’s the key) --}}
   <label class="form-label">Nama Kategori</label>
   <x-input-with-icon 
      label="Category Name" 
      name="category_name" 
      icon="ti ti-barcode" 
      value="{{ $category->category_name }}" 
      disabled />

   {{-- DESCRIPTION --}}
   <label class="form-label">Deskripsi Kategori</label>
   <x-input-with-icon 
      label="Description" 
      name="category_description" 
      icon="ti ti-building" 
      value="{{ old('category_description', $category->category_description) }}" />

   {{-- STATUS --}}
   <div class="form-group mb-3">
      <label class="form-label">Status</label>
      <select name="status" class="form-select">
         <option value="Active"   {{ old('status', $category->status) === 'Active'   ? 'selected' : '' }}>Active</option>
         <option value="Inactive" {{ old('status', $category->status) === 'Inactive' ? 'selected' : '' }}>Inactive</option>
      </select>
      @error('status')
         <div class="text-danger small mt-1">{{ $message }}</div>
      @enderror
   </div>

   {{-- IMAGE HANDLING --}}
   @php
       $file = trim((string)($category->category_image ?? ''));
       $src  = $file === ''
            ? asset('images/placeholder-category.png')
            : (preg_match('~^https?://~i', $file)
                ? $file
                : 'https://apisobat.sdn.id/category-img/'.ltrim($file,'/'));
   @endphp

   <div class="form-group mb-3">
      <div class="d-flex align-items-center gap-3">
         <img id="previewCategoryImage" src="{{ $src }}" alt="{{ $category->category_name }}"
              style="height:64px;object-fit:contain;border:1px solid #eee;border-radius:.5rem;padding:4px;background:#fff;">
         <div class="flex-grow-1">
            <input type="file" name="category_image_file" id="category_image_file" class="form-control" accept="image/*">
            <small class="text-muted">
               (Opsional) Pilih file gambar baru. Biarkan kosong untuk tetap memakai gambar saat ini.
            </small>
         </div>
      </div>
      {{-- Simpan nilai lama untuk jaga-jaga di controller --}}
      <input type="hidden" name="category_image_old" value="{{ $category->category_image }}">
   </div>

   {{-- SUBMIT BUTTON --}}
   <div class="form-group mb-3">
      <button class="btn btn-primary w-100">
         <i class="ti ti-send me-1"></i> Submit
      </button>
   </div>
</form>

<script>
document.getElementById('category_image_file')?.addEventListener('change', function (e) {
  const f = e.target.files?.[0];
  if (f) document.getElementById('previewCategoryImage').src = URL.createObjectURL(f);
});
</script>