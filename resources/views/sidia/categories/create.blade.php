<form action="{{ route('categories.store') }}" id="formCreateCategories" method="POST" enctype="multipart/form-data">
  @csrf

  {{-- KODE KATEGORI --}}
  <x-input-with-icon-label
      icon="ti ti-building"
      label="Kode Kategori"
      name="category_code"
      value="{{ old('category_code') }}" />
  @error('category_code') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

  {{-- KATEGORI --}}
  <x-input-with-icon-label
      icon="ti ti-barcode"
      label="Kategori"
      name="category_name"
      value="{{ old('category_name') }}" />
  @error('category_name') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

  <div class="form-group mb-3">
    <button class="btn btn-primary w-100" type="submit">
      <i class="ti ti-send me-1"></i> Submit
    </button>
  </div>
</form>
