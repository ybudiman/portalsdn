<form action="{{ route('category.store') }}" id="formCreateCategory" method="POST" enctype="multipart/form-data">
  @csrf

  {{-- CATEGORY NAME --}}
  <x-input-with-icon-label
      icon="ti ti-category"
      label="Nama Kategori"
      name="category_name"
      value="{{ old('category_name') }}" />
  @error('category_name') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

  {{-- DESCRIPTION --}}
  <x-input-with-icon-label
      icon="ti ti-file-description"
      label="Deskripsi"
      name="category_description"
      value="{{ old('category_description') }}" />
  @error('category_description') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

  {{-- STATUS --}}
  <div class="form-group mb-3">
    <label class="form-label" style="font-weight:600">Status</label>
    @php $st = old('status', 'Active'); @endphp
    <select name="status" class="form-select">
      <option value="Active"   {{ $st === 'Active'   ? 'selected' : '' }}>Active</option>
      <option value="Inactive" {{ $st === 'Inactive' ? 'selected' : '' }}>Inactive</option>
    </select>
    @error('status') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
  </div>

  {{-- CATEGORY PHOTO (preview + upload) --}}
  @php $placeholder = asset('images/placeholder-category.png'); @endphp
  <div class="form-group mb-3">
    <label class="form-label" style="font-weight:600">Foto Kategori</label>
    <div class="d-flex align-items-center gap-3">
      <img id="previewCategoryImage" src="{{ $placeholder }}" alt="preview"
           style="height:64px;object-fit:contain;border:1px solid #eee;border-radius:.5rem;padding:4px;background:#fff;"
           onerror="this.src='{{ $placeholder }}'">
      <div class="flex-grow-1">
        <input type="file" name="category_image_file" id="category_image_file" class="form-control" accept="image/*">
        <small class="text-muted">Opsional. Jika diisi, file akan diunggah dan nama file mengikuti Category Name.</small>
      </div>
    </div>
    @error('category_image_file') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
  </div>

  <div class="form-group mb-3">
    <button class="btn btn-primary w-100" type="submit">
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