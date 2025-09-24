@extends('layouts.app')
@section('titlepage', 'Customer')

@section('content')
@section('navigasi')
    <span>Domisili Customer</span>
@endsection

<div class="d-flex justify-content-center mb-3">
    <div style="width: 300px; aspect-ratio: 1/1; overflow: hidden; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
        @if(!empty($activeDomicileImage))
            <a href="{{ $activeDomicileImage }}" target="_blank">
                <img src="{{ $activeDomicileImage }}" 
                     alt="Domicile Image" 
                     class="w-100 h-100"
                     style="object-fit: cover; cursor: pointer;">
            </a>
        @else
            <img src="{{ asset('assets/img/avatars/No_Image_Available.jpg') }}" 
                 alt="No Domicile Available" 
                 class="w-100 h-100"
                 style="object-fit: cover;">
        @endif
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                @can('customer.create')
                    <a href="#" class="btn btn-primary" id="btnCreate"><i class="fa fa-plus me-2"></i> Tambah Domisili Customer</a>
                @endcan
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive mb-2">
                            <table class="table table-hover table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>User ID</th>
                                        <th>Alamat</th>
                                        <th>Provinsi</th>
                                        <th>Kota</th>
                                        <th>Kecamatan</th>
                                        <th>Kelurahan</th>
                                        <th>Kode Pos</th>
                                        <th>Longitude</th>
                                        <th>Latitude</th>
                                        <th>Status</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customerDomiciles as $customerDomicile)
                                        @php
                                            $rowClass = match ($customerDomicile->status) {
                                                'Active' => 'table-success', // green
                                                'Inactive' => 'table-danger',  // red
                                                default => '',
                                            };
                                        @endphp
                                        <tr class="{{ $rowClass }}">
                                            <td>{{ $customerDomicile->user_id }}</td>
                                            <td>{{ $customerDomicile->alamat }}</td>
                                            <td>{{ $customerDomicile->nama_provinsi }}</td>
                                            <td>{{ $customerDomicile->nama_kota }}</td>
                                            <td>{{ $customerDomicile->nama_kecamatan }}</td>
                                            <td>{{ $customerDomicile->nama_kelurahan }}</td>
                                            <td>{{ $customerDomicile->kode_pos }}</td>
                                            <td>{{ $customerDomicile->longitude }}</td>
                                            <td>{{ $customerDomicile->latitude }}</td>
                                            <td>{{ $customerDomicile->status }}</td>
                                            <td>
                                                <div class="d-flex">
                                                    @can('customer.edit')
                                                        <div>
                                                            <a href="#" class="me-2 btnEdit" customer_id="{{ Crypt::encrypt($customerDomicile->user_id) }}" domisili_id = "{{ Crypt::encrypt($customerDomicile->id) }}">
                                                                <i class="ti ti-edit text-success"></i>
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    @can('customer.delete')
                                                        <div>
                                                            <form method="POST" action="{{ route('customer.domisili.destroy', $customerDomicile->id) }}" class="deleteform d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button" class="btn btn-link p-0 delete-confirm" title="Delete">
                                                                    <i class="ti ti-trash text-danger"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div style="float: right;">
                            {{ $customerDomiciles->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<x-modal-form id="modal" show="loadmodal" />
@endsection
@push('myscript')
<script>
    $(function() {
        function loading() {
            $("#loadmodal").html(`<div class="sk-wave sk-primary" style="margin:auto">
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            </div>`);
        };
        loading();

        $(".btnEdit").click(function() {
            loading();
            const id = $(this).attr("customer_id");
            const domisiliId = $(this).attr("domisili_id");
            $("#modal").modal("show");
            $(".modal-title").text("Edit Data Domisili Customer");
            $("#loadmodal").load(`/sobat/customer/${id}/domisili/${domisiliId}/edit`);
        });

        $(document).on('click', '.delete-confirm', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');

        Swal.fire({
            title: 'Hapus customerDomicile ini?',
            text: 'Tindakan ini tidak bisa dibatalkan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
            form.trigger('submit');
            }
        });
        });
    });
</script>
@endpush