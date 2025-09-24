@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-profile.css') }}" />
@section('titlepage', 'Customer')

@section('content')
@section('navigasi')
    <span class="text-muted">Customer/</span> Detail
@endsection
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="user-profile-header-banner">
                <img src="{{ asset('assets/img/pages/profile-bg.jpg') }}" alt="Banner image" class="rounded-top">
            </div>
            <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
                <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
                    @if (Storage::disk('public')->exists('/customer/' . $customer->foto))
                        <img src="{{ getfotoKaryawan($customer->foto) }}" alt="user image" class="d-block  ms-0 ms-sm-4 rounded " height="150"
                            width="140" style="object-fit: cover">
                    @else
                        <img src="{{ asset('assets/img/avatars/No_Image_Available.jpg') }}" alt="user image"
                            class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img" width="150">
                    @endif

                </div>
                <div class="flex-grow-1 mt-3 mt-sm-5">
                    <div
                        class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
                        <div class="user-profile-info">
                            <h4>{{ textCamelCase($customer->nama_karyawan) }}</h4>
                            <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                                <li class="list-inline-item d-flex gap-1">
                                    <i class="ti ti-barcode"></i> {{ textCamelCase($customer->nik) }}
                                </li>
                                <li class="list-inline-item d-flex gap-1">
                                    <i class="ti ti-building"></i> {{ textCamelCase($customer->nama_cabang) }}
                                </li>
                                <li class="list-inline-item d-flex gap-1"><i class="ti ti-building-arch"></i>
                                    {{ textCamelCase($customer->nama_dept) }}
                                </li>
                                <li class="list-inline-item d-flex gap-1">
                                    <i class="ti ti-user"></i> {{ textCamelCase($customer->nama_jabatan) }}
                                </li>
                            </ul>
                        </div>
                        @if ($customer->status_aktif_karyawan === '1')
                            <a href="javascript:void(0)" class="btn btn-success waves-effect waves-light">
                                <i class="ti ti-check me-1"></i> Aktif
                            </a>
                        @else
                            <a href="javascript:void(0)" class="btn btn-danger waves-effect waves-light">
                                <i class="ti ti-check me-1"></i> Nonaktif
                            </a>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- User Profile Content -->
<div class="row">
    <div class="col-xl-3 col-lg-5 col-md-5">
        <!-- About User -->
        <div class="card mb-4">
            <div class="card-body">
                <small class="card-text text-uppercase">Data Customer</small>
                <ul class="list-unstyled mb-4 mt-3">
                    <li class="d-flex align-items-center mb-3">
                        <i class="ti ti-barcode text-heading"></i>
                        <span class="fw-medium mx-2 text-heading">Customer ID:</span>
                        <span>{{ $customer->customer_id }}</span>
                    </li>
                    <li class="d-flex align-items-center mb-3">
                        <i class="ti ti-barcode-2 text-heading"></i>
                        <span class="fw-medium mx-2 text-heading">External Customer ID:</span>
                        <span>{{ $customer->external_customer_id }}</span>
                    </li>
                    <li class="d-flex align-items-center mb-3">
                        <i class="ti ti-user text-heading"></i>
                        <span class="fw-medium mx-2 text-heading">Full Name:</span>
                        <span>{{ $customer->fullname }}</span>
                    </li>
                    <li class="d-flex align-items-center mb-3">
                        <i class="ti ti-mail text-heading"></i>
                        <span class="fw-medium mx-2 text-heading">Email:</span>
                        <span>{{ $customer->email }}</span>
                    </li>
                    <li class="d-flex align-items-center mb-3">
                        <i class="ti ti-phone text-heading"></i>
                        <span class="fw-medium mx-2 text-heading">Phone Number:</span>
                        <span>{{ $customer->phone_number }}</span>
                    </li>
                    <li class="d-flex align-items-center mb-3">
                        <i class="ti ti-check text-heading"></i>
                        <span class="fw-medium mx-2 text-heading">Verified:</span>
                        <span>
                            @switch($customer->verified)
                                @case('Y') Yes @break
                                @case('N') No @break
                                @case('W') Waiting @break
                                @case('P') Pending @break
                                @default {{ $customer->verified }}
                            @endswitch
                        </span>
                    </li>
                    <li class="d-flex align-items-center mb-3">
                        <i class="ti ti-id text-heading"></i>
                        <span class="fw-medium mx-2 text-heading">Employee ID:</span>
                        <span>{{ $customer->employee_id ?? '-' }}</span>
                    </li>
                    <li class="d-flex align-items-center mb-3">
                        <i class="ti ti-truck-delivery text-heading"></i>
                        <span class="fw-medium mx-2 text-heading">Delivery Type:</span>
                        <span>{{ $customer->default_delivery_type ?? '-' }}</span>
                    </li>
                    <li class="d-flex align-items-center mb-3">
                        <i class="ti ti-building text-heading"></i>
                        <span class="fw-medium mx-2 text-heading">Business Area Code:</span>
                        <span>{{ $customer->business_area_code }}</span>
                    </li>
                    <li class="d-flex align-items-center mb-3">
                        <i class="ti ti-coins text-heading"></i>
                        <span class="fw-medium mx-2 text-heading">Points:</span>
                        <span>{{ $customer->point }}</span>
                    </li>
                    <li class="d-flex align-items-center mb-3">
                        <i class="ti ti-calendar text-heading"></i>
                        <span class="fw-medium mx-2 text-heading">Created At:</span>
                        <span>{{ $customer->created_at?->format('d M Y H:i') }}</span>
                    </li>
                </ul>


            </div>
        </div>
    </div>
    <div class="col-xl-9 col-lg-7 col-md-7">
        <!-- Activity Timeline -->
        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-pills flex-column flex-sm-row mb-4">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('customer.ktp.index', Crypt::encrypt($customer->id)) }}"><i class="ti-xs ti ti-home-move me-1"></i>KTP</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('customer.domisili.index', Crypt::encrypt($customer->id)) }}"><i class="ti-xs ti ti-coins me-1"></i>Domisili</a>
                    </li>
                    {{-- <li class="nav-item">
                  <a class="nav-link" href="{{ route('customer.dokumen', Crypt::encrypt($customer->nik)) }}"><i
                        class="ti-xs ti ti-file-stack me-1"></i> Dokumen</a>
               </li> --}}
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card card-action mb-4">
                    <div class="card-header align-items-center d-flex justify-content-between">
                        <h5 class="mb-0"><i class="ti ti-face-id me-2"></i>Dokumen Customer</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- KTP Image -->
                            <div class="col-md-6 text-center">
                                <h6 class="mb-2">KTP</h6>
                                @if(!empty($ktpImage))
                                    <a href="{{ $ktpImage }}" target="_blank">
                                        <div style="width: 300px; height: 300px; margin: 0 auto; overflow: hidden; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                                            <img src="{{ $ktpImage }}" 
                                                alt="KTP Image" 
                                                style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                    </a>
                                @else
                                    <div style="width: 300px; height: 300px; margin: 0 auto; overflow: hidden; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                                        <img src="{{ asset('assets/img/avatars/No_Image_Available.jpg') }}" 
                                            alt="No KTP Available" 
                                            style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                @endif
                            </div>

                            <!-- Domicile Image -->
                            <div class="col-md-6 text-center">
                                <h6 class="mb-2">Domisili</h6>
                                @if(!empty($domicileImage))
                                    <a href="{{ $domicileImage }}" target="_blank">
                                        <div style="width: 300px; height: 300px; margin: 0 auto; overflow: hidden; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                                            <img src="{{ $domicileImage }}" 
                                                alt="Domicile Image" 
                                                style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                    </a>
                                @else
                                    <div style="width: 300px; height: 300px; margin: 0 auto; overflow: hidden; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                                        <img src="{{ asset('assets/img/avatars/No_Image_Available.jpg') }}" 
                                            alt="No Domicile Available" 
                                            style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<x-modal-form id="modal" show="loadmodal" size="modal-lg" />
<!--/ User Profile Content -->

@endsection