<form action="{{ route('product.update', Crypt::encrypt($product->id)) }}" method="POST" id="formProduct">
    @csrf
    @method('PUT')

    {{-- PRODUCT CODE (readonly) --}}
    <label class="form-label" style="font-weight:600">Kode Product</label>
    <x-input-with-icon 
        label="Product Code" 
        name="product_code" 
        icon="ti ti-barcode" 
        value="{{ $product->product_code }}" 
        disabled />

   {{-- EXTERNAL CODE --}}
   <label class="form-label" style="font-weight:600">Kode Product External</label>
    <x-input-with-icon 
        label="External Code" 
        name="external_product_code" 
        icon="ti ti-hash" 
        value="{{ old('external_product_code', $product->external_product_code) }}" />

    {{-- PRODUCT NAME --}}
    <label class="form-label" style="font-weight:600">Nama Product</label>
    <x-input-with-icon 
        label="Product Name" 
        name="product_name" 
        icon="ti ti-package" 
        value="{{ old('product_name', $product->product_name) }}" />

    {{-- DESCRIPTION --}}
    <div class="form-group mb-3 mt-2">
        <label class="form-label" style="font-weight:600">Deskripsi</label>
        <textarea name="product_description" class="form-control" rows="3">{{ old('product_description', $product->product_description) }}</textarea>
    </div>

    {{-- IMAGE (optional upload if needed) --}}
    <div class="form-group mb-3">
        <label class="form-label" style="font-weight:600">Product Image</label>
        <input type="file" name="product_image" class="form-control">
        @if($product->product_image_url)
            <img src="{{ $product->product_image_url }}" alt="Product Image" class="mt-2" style="height:60px;object-fit:contain;">
        @endif
    </div>

    {{-- CATEGORY --}}
    <div class="form-group mb-3 mt-2">
        <label class="form-label" style="font-weight:600">Kategori</label>
        <select name="category_id" class="form-select">
            <option value="">-- Pilih Kategori --</option>
            @foreach($categories as $c)
                <option value="{{ $c->id }}" {{ old('category_id', $product->category_id) == $c->id ? 'selected' : '' }}>
                    {{ $c->category_name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- PRINCIPAL --}}
    <div class="form-group mb-3">
        <label class="form-label" style="font-weight:600">Principal</label>
        <select name="principal_id" class="form-select">
            <option value="">-- Pilih Principal --</option>
            @foreach($principals as $pr)
                <option value="{{ $pr->id }}" {{ old('principal_id', $product->principal_id) == $pr->id ? 'selected' : '' }}>
                    {{ $pr->principal_name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- BRAND --}}
    <div class="form-group mb-3">
        <label class="form-label" style="font-weight:600">Brand</label>
        <select name="brand_id" class="form-select">
            <option value="">-- Pilih Brand --</option>
            @foreach($brands as $b)
                <option value="{{ $b->id }}" {{ old('brand_id', $product->brand_id) == $b->id ? 'selected' : '' }}>
                    {{ $b->brand_name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- IS NEW --}}
    <div class="form-group mb-3">
        <label class="form-label" style="font-weight:600">New Product?</label>
        <select name="isNew" class="form-select">
            <option value="1" {{ old('isNew', $product->isNew) == 1 ? 'selected' : '' }}>Yes</option>
            <option value="0" {{ old('isNew', $product->isNew) == 0 ? 'selected' : '' }}>No</option>
        </select>
    </div>

    {{-- FEATURED --}}
    <div class="form-group mb-3">
        <label class="form-label" style="font-weight:600">Featured?</label>
        <select name="isFeatured" class="form-select">
            <option value="1" {{ old('isFeatured', $product->isFeatured) == 1 ? 'selected' : '' }}>Yes</option>
            <option value="0" {{ old('isFeatured', $product->isFeatured) == 0 ? 'selected' : '' }}>No</option>
        </select>
    </div>

    {{-- TAXABLE --}}
    <div class="form-group mb-3">
        <label class="form-label" style="font-weight:600">Taxable?</label>
        <select name="taxable" class="form-select">
            <option value="1" {{ old('taxable', $product->taxable) == 1 ? 'selected' : '' }}>Yes</option>
            <option value="0" {{ old('taxable', $product->taxable) == 0 ? 'selected' : '' }}>No</option>
        </select>
    </div>

    {{-- TAX --}}
    <div class="form-group mb-3">
        <label class="form-label" style="font-weight:600">Tax</label>
        <select name="tax_id" class="form-select">
            <option value="">-- Pilih Pajak --</option>
            @foreach($taxes as $t)
                <option value="{{ $t->id }}" {{ old('tax_id', $product->tax_id) == $t->id ? 'selected' : '' }}>
                    {{ $t->tax_name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- MIN ORDER UOM --}}
    <div class="form-group mb-3">
        <label class="form-label" style="font-weight:600">Min. Order UOM</label>
        <select name="min_order_uom_id" class="form-select">
            <option value="">-- Pilih UOM --</option>
            @foreach($uoms as $u)
                <option value="{{ $u->id }}" {{ old('min_order_uom_id', $product->min_order_uom_id) == $u->id ? 'selected' : '' }}>
                    {{ $u->uom_name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- STATUS --}}
    <div class="form-group mb-3">
        <label class="form-label" style="font-weight:600">Status</label>
        <select name="status" class="form-select">
            <option value="Active"   {{ old('status', $product->status) === 'Active' ? 'selected' : '' }}>Active</option>
            <option value="Inactive" {{ old('status', $product->status) === 'Inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>

    {{-- SUBMIT --}}
    <div class="form-group mb-3">
        <button class="btn btn-primary w-100">
            <i class="ti ti-send me-1"></i> Update Product
        </button>
    </div>
</form>
