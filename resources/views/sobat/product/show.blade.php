@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-profile.css') }}" />
@section('titlepage', 'Product')

@section('content')
@section('navigasi')
    <span class="text-muted">Product /</span> Detail
@endsection

<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="user-profile-header-banner">
                <img src="{{ asset('assets/img/pages/profile-bg.jpg') }}" alt="Banner image" class="rounded-top">
            </div>
            <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
                <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
                    @if (!empty($product->product_image_url))
                        <img src="{{ $product->product_image_url }}" 
                            alt="Product image" 
                            class="d-block ms-0 ms-sm-4 rounded" 
                            height="150" width="140" style="object-fit: cover">
                    @else
                        <img src="{{ asset('assets/img/avatars/No_Image_Available.jpg') }}" 
                            alt="No product image"
                            class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img" width="150">
                    @endif
                </div>

                <div class="flex-grow-1 mt-3 mt-sm-5">
                    <div
                        class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
                        <div class="user-profile-info">
                            <h4>{{ $product->product_name }}</h4>
                            <ul class="list-inline mb-0 d-flex align-items-center flex-wrap gap-3">
                                <li class="list-inline-item d-flex gap-1">
                                    <i class="ti ti-barcode"></i> {{ $product->product_code }}
                                </li>
                                <li class="list-inline-item d-flex gap-1">
                                    <i class="ti ti-tag"></i> {{ $product->category?->category_name ?? '-' }}
                                </li>
                                <li class="list-inline-item d-flex gap-1">
                                    <i class="ti ti-building"></i> {{ $product->principal?->principal_name ?? '-' }}
                                </li>
                                <li class="list-inline-item d-flex gap-1">
                                    <i class="ti ti-crown"></i> {{ $product->brand?->brand_name ?? '-' }}
                                </li>
                            </ul>
                        </div>

                        @if ($product->status === 'Active')
                            <a href="javascript:void(0)" class="btn btn-success waves-effect waves-light">
                                <i class="ti ti-check me-1"></i> Active
                            </a>
                        @else
                            <a href="javascript:void(0)" class="btn btn-danger waves-effect waves-light">
                                <i class="ti ti-x me-1"></i> Inactive
                            </a>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Product Content -->
<div class="row">
    <div class="col-xl-3 col-lg-5 col-md-5">
        <div class="card mb-4">
            <div class="card-body">
                <small class="card-text text-uppercase">Product Info</small>
                <ul class="list-unstyled mb-4 mt-3">
                    <li class="d-flex align-items-center mb-3">
                        <i class="ti ti-barcode text-heading"></i>
                        <span class="fw-medium mx-2 text-heading">External Product Code:</span>
                        <span>{{ $product->external_product_code }}</span>
                    </li>
                    <li class="d-flex align-items-center mb-3">
                        <i class="ti ti-package text-heading"></i>
                        <span class="fw-medium mx-2 text-heading">Product Name:</span>
                        <span>{{ $product->product_name }}</span>
                    </li>
                    <li class="d-flex align-items-center mb-3">
                        <i class="ti ti-sparkles text-heading"></i>
                        <span class="fw-medium mx-2 text-heading">New?:</span>
                        <span>{!! $product->isNew ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' !!}</span>
                    </li>
                    <li class="d-flex align-items-center mb-3">
                        <i class="ti ti-star text-heading"></i>
                        <span class="fw-medium mx-2 text-heading">Featured?:</span>
                        <span>{!! $product->isFeatured ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' !!}</span>
                    </li>
                    <li class="d-flex align-items-center mb-3">
                        <i class="ti ti-tag text-heading"></i>
                        <span class="fw-medium mx-2 text-heading">Category:</span>
                        <span>{{ $product->category?->category_name ?? '-' }}</span>
                    </li>
                    <li class="d-flex align-items-center mb-3">
                        <i class="ti ti-currency-dollar text-heading"></i>
                        <span class="fw-medium mx-2 text-heading">Taxable:</span>
                        <span>{!! $product->taxable ? 'Yes' : 'No' !!}</span>
                    </li>
                    <li class="d-flex align-items-center mb-3">
                        <i class="ti ti-receipt text-heading"></i>
                        <span class="fw-medium mx-2 text-heading">Tax:</span>
                        <span>{{ $product->tax?->tax_name ?? '-'}}</span>
                    </li>
                    <li class="d-flex align-items-center mb-3">
                        <i class="ti ti-building text-heading"></i>
                        <span class="fw-medium mx-2 text-heading">Principal:</span>
                        <span>{{ $product->principal?->principal_name ?? '-' }}</span>
                    </li>
                    <li class="d-flex align-items-center mb-3">
                        <i class="ti ti-crown text-heading"></i>
                        <span class="fw-medium mx-2 text-heading">Brand:</span>
                        <span>{{ $product->brand?->brand_name ?? '-' }}</span>
                    </li>
                    <li class="d-flex align-items-center mb-3">
                        <i class="ti ti-box-seam text-heading"></i>
                        <span class="fw-medium mx-2 text-heading">Min. Order UOM:</span>
                        <span>{{ $product->uom?->uom_name ?? '-' }}</span>
                    </li>
                    <li class="d-flex align-items-center mb-3">
                        <i class="ti ti-calendar text-heading"></i>
                        <span class="fw-medium mx-2 text-heading">Created At:</span>
                        <span>{{ $product->created_at?->format('d M Y H:i') }}</span>
                    </li>
                    <li class="d-flex align-items-center mb-3">
                        <i class="ti ti-user text-heading"></i>
                        <span class="fw-medium mx-2 text-heading">Created By:</span>
                        <span>{{ $product->created_by }}</span>
                    </li>
                    <li class="d-flex align-items-center mb-3">
                        <i class="ti ti-calendar text-heading"></i>
                        <span class="fw-medium mx-2 text-heading">Updated At:</span>
                        <span>{{ $product->updated_at?->format('d M Y H:i') }}</span>
                    </li>
                    <li class="d-flex align-items-center mb-3">
                        <i class="ti ti-user text-heading"></i>
                        <span class="fw-medium mx-2 text-heading">Updated By:</span>
                        <span>{{ $product->updated_by }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Product Submenu + Gallery -->
    <div class="col-xl-9 col-lg-7 col-md-7">
        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-pills flex-column flex-sm-row mb-4">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('product.media.index', Crypt::encrypt($product->id)) }}"><i class="ti-xs ti ti-photo me-1"></i>Media</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('product.uom.index', Crypt::encrypt($product->id)) }}"><i class="ti-xs ti ti-scale me-1"></i>Uom & Stock</a>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Product Gallery -->
        <div class="card card-action mb-4">
            <div class="card-header align-items-center d-flex justify-content-between">
                <h5 class="mb-0"><i class="ti ti-photo me-2"></i> Product Gallery</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @forelse($productImages as $img)
                        <div class="col-md-4 text-center">
                            <a href="{{ $img->url }}" target="_blank">
                                <img src="{{ $img->url }}" 
                                     alt="Gallery image" 
                                     class="img-fluid rounded shadow-sm"
                                     style="max-height:200px;object-fit:cover;">
                            </a>
                        </div>
                    @empty
                        <p class="text-muted">No gallery images available for this product.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<x-modal-form id="modal" show="loadmodal" size="modal-lg" />
@endsection