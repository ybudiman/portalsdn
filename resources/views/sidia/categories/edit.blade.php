<form action="{{ route('categories.update', Crypt::encrypt($category->category_code)) }}" method="POST" id="formCategories" enctype="multipart/form-data">
   @csrf
   @method('PUT')
   <label class="form-label">Kode Kategori</label>
   <x-input-with-icon label="Kode Kategori" name="category_code" icon="ti ti-barcode" value="{{ $category->category_code }}" disabled />
   <label class="form-label">Kategori Deskripsi</label>
   <x-input-with-icon label="Kategori Deskripsi" name="category_name" icon="ti ti-building" value="{{ old('category_name', $category->category_name) }}" />

   <div class="form-group mb-3">
      <button class="btn btn-primary w-100"><i class="ti ti-send me-1"></i> Submit</button>
   </div>
</form>
