<form action="{{ route('product.store') }}" id="formCreateProduct" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- PRODUCT CODE --}}
    <div class="form-group mb-3">
        <label class="form-label" style="font-weight:600">Kode Produk</label>
        <x-input-with-icon 
            label="Product Code" 
            icon="ti ti-barcode" 
            name="product_code" 
            value="{{ old('product_code') }}" />
        @error('product_code')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- EXTERNAL CODE --}}
    <div class="form-group mb-3">
        <label class="form-label" style="font-weight:600">External Code</label>
        <x-input-with-icon 
            label="External Code" 
            icon="ti ti-hash" 
            name="external_product_code" 
            value="{{ old('external_product_code') }}" />
        @error('external_product_code')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- PRODUCT NAME --}}
    <div class="form-group mb-3">
        <label class="form-label" style="font-weight:600">Nama Produk</label>
        <x-input-with-icon 
            label="Product Name" 
            icon="ti ti-package" 
            name="product_name" 
            value="{{ old('product_name') }}" />
        @error('product_name')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- DESCRIPTION --}}
    <div class="form-group mb-3">
        <label class="form-label" style="font-weight:600">Deskripsi</label>
        <textarea name="product_description" class="form-control" rows="3">{{ old('product_description') }}</textarea>
        @error('product_description')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- IMAGE --}}
    <div class="form-group mb-3">
        <label class="form-label" style="font-weight:600">Gambar Produk</label>
        <input type="file" name="product_image" class="form-control">
        @error('product_image')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- CATEGORY --}}
    <div class="form-group mb-3">
        <label class="form-label" style="font-weight:600">Kategori</label>
        <select name="category_id" class="form-select">
            <option value="">-- Pilih Kategori --</option>
            @foreach($categories as $c)
                <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>
                    {{ $c->category_name }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- PRINCIPAL --}}
    <div class="form-group mb-3">
        <label class="form-label" style="font-weight:600">Principal</label>
        <select name="principal_id" class="form-select">
            <option value="">-- Pilih Principal --</option>
            @foreach($principals as $pr)
                <option value="{{ $pr->id }}" {{ old('principal_id') == $pr->id ? 'selected' : '' }}>
                    {{ $pr->principal_name }}
                </option>
            @endforeach
        </select>
        @error('principal_id')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- BRAND --}}
    <div class="form-group mb-3">
        <label class="form-label" style="font-weight:600">Brand</label>
        <select name="brand_id" class="form-select">
            <option value="">-- Pilih Brand --</option>
            @foreach($brands as $b)
                <option value="{{ $b->id }}" {{ old('brand_id') == $b->id ? 'selected' : '' }}>
                    {{ $b->brand_name }}
                </option>
            @endforeach
        </select>
        @error('brand_id')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- IS NEW --}}
    <div class="form-group mb-3">
        <label class="form-label" style="font-weight:600">New Product?</label>
        <select name="isNew" class="form-select">
            <option value="Y" {{ old('isNew') == 'Y' ? 'selected' : '' }}>Yes</option>
            <option value="N" {{ old('isNew') == 'N' ? 'selected' : '' }}>No</option>
        </select>
    </div>

    {{-- FEATURED --}}
    <div class="form-group mb-3">
        <label class="form-label" style="font-weight:600">Featured?</label>
        <select name="isFeatured" class="form-select">
            <option value="Y" {{ old('isFeatured') == 'Y' ? 'selected' : '' }}>Yes</option>
            <option value="N" {{ old('isFeatured') == 'N' ? 'selected' : '' }}>No</option>
        </select>
    </div>

    {{-- TAXABLE --}}
    <div class="form-group mb-3">
        <label class="form-label" style="font-weight:600">Taxable?</label>
        <select name="taxable" class="form-select">
            <option value="Y" {{ old('taxable') == 'Y' ? 'selected' : '' }}>Yes</option>
            <option value="N" {{ old('taxable') == 'N' ? 'selected' : '' }}>No</option>
        </select>
    </div>

    {{-- TAX --}}
    <div class="form-group mb-3">
        <label class="form-label" style="font-weight:600">Tax</label>
        <select name="tax_id" class="form-select">
            <option value="">-- Pilih Pajak --</option>
            @foreach($taxes as $t)
                <option value="{{ $t->id }}" {{ old('tax_id') == $t->id ? 'selected' : '' }}>
                    {{ $t->tax_name }}
                </option>
            @endforeach
        </select>
        @error('tax_id')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- MIN ORDER UOM --}}
    <div class="form-group mb-3">
        <label class="form-label" style="font-weight:600">Min. Order UOM</label>
        <select name="min_order_uom_id" class="form-select">
            <option value="">-- Pilih UOM --</option>
            @foreach($uoms as $u)
                <option value="{{ $u->id }}" {{ old('min_order_uom_id') == $u->id ? 'selected' : '' }}>
                    {{ $u->uom_name }}
                </option>
            @endforeach
        </select>
        @error('min_order_uom_id')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- STATUS --}}
    <div class="form-group mb-3">
        <label class="form-label" style="font-weight:600">Status</label>
        @php $st = old('status', 'Active'); @endphp
        <select name="status" class="form-select">
            <option value="Active"   {{ $st === 'Active' ? 'selected' : '' }}>Active</option>
            <option value="Inactive" {{ $st === 'Inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>

    {{-- SUBMIT --}}
    <div class="form-group mb-3">
        <button class="btn btn-primary w-100" type="submit">
            <i class="ti ti-send me-1"></i> Simpan Produk
        </button>
    </div>
</form>