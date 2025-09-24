<form action="{{ route('brand.store') }}" id="formCreateBrand" method="POST" enctype="multipart/form-data">
  @csrf

  {{-- BRAND NAME --}}
  <x-input-with-icon-label
      icon="ti ti-barcode"
      label="Brand Name"
      name="brand_name"
      value="{{ old('brand_name') }}" />
  @error('brand_name') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

  {{-- DESCRIPTION --}}
  <x-input-with-icon-label
      icon="ti ti-building"
      label="Description"
      name="brand_description"
      value="{{ old('brand_description') }}" />
  @error('brand_description') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

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

  {{-- BRAND PHOTO (preview + upload) --}}
  @php $placeholder = asset('images/placeholder-brand.png'); @endphp
  <div class="form-group mb-3">
    <label class="form-label" style="font-weight:600">Brand Photo</label>
    <div class="d-flex align-items-center gap-3">
      <img id="previewBrandImage" src="{{ $placeholder }}" alt="preview"
           style="height:64px;object-fit:contain;border:1px solid #eee;border-radius:.5rem;padding:4px;background:#fff;"
           onerror="this.src='{{ $placeholder }}'">
      <div class="flex-grow-1">
        <input type="file" name="brand_image_file" id="brand_image_file" class="form-control" accept="image/*">
        <small class="text-muted">Opsional. Jika diisi, file akan diunggah dan nama file mengikuti Brand Name.</small>
      </div>
    </div>
    @error('brand_image_file') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
  </div>

  <div class="form-group mb-3">
    <button class="btn btn-primary w-100" type="submit">
      <i class="ti ti-send me-1"></i> Submit
    </button>
  </div>
</form>

<script>
  document.getElementById('brand_image_file')?.addEventListener('change', function (e) {
    const f = e.target.files?.[0];
    if (f) document.getElementById('previewBrandImage').src = URL.createObjectURL(f);
  });
</script>
