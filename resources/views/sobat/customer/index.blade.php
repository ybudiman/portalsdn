@extends('layouts.app')
@section('titlepage', 'Customer')

@section('content')
@section('navigasi')
    <span>Customer</span>
@endsection

<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                @can('customer.create')
                    <a href="#" class="btn btn-primary" id="btnCreate"><i class="fa fa-plus me-2"></i> Tambah Customer</a>
                @endcan
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <form action="{{ route('customer.index') }}">
                            <div class="row">
                                <div class="col-lg-4 col-sm-12 col-md-12">
                                    <x-input-with-icon label="Cari Customer" value="{{ Request('search_query') }}" name="search_query"
                                        icon="ti ti-search" />
                                </div>
                                <div class="col-lg-2 col-sm-12 col-md-12">
                                    <button class="btn btn-primary"><i class="ti ti-icons ti-search me-1"></i>Cari</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">

                        <div class="table-responsive mb-2">
                            <table class="table  table-hover table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Customer ID</th>
                                        <th>External Customer ID</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>No Hp</th>
                                        <th>Verified</th>
                                        <th>Employee ID</th>
                                        <th>Delivery Type</th>
                                        <th>Point</th>
                                        <th>Business Area Code</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customers as $customer)
                                        @php
                                            $rowClass = match ($customer->verified) {
                                                'Y' => 'table-success', // green
                                                'W' => 'table-danger', // red
                                                'N' => 'table-warning',  // yellow
                                                'P' => 'table-info',    // blue
                                                default => '',
                                            };
                                        @endphp
                                        <tr class="{{ $rowClass }}">
                                            <td>{{ $customer->customer_id }}</td>
                                            <td>{{ $customer->external_customer_id }}</td>
                                            <td>{{ $customer->fullname }}</td>
                                            <td>{{ $customer->email }}</td>
                                            <td>{{ $customer->phone_number }}</td>
                                            <td>{{ $customer->verified }}</td>
                                            <td>{{ $customer->employee_id }}</td>
                                            <td>{{ $customer->default_delivery_type }}</td>
                                            <td>{{ $customer->point }}</td>
                                            <td>{{ $customer->business_area_code }}</td>
                                            <td>
                                                <div class="d-flex">
                                                    @can('customer.edit')
                                                        <div>
                                                            <a href="#" class="me-2 btnEdit" customer_id="{{ Crypt::encrypt($customer->id) }}">
                                                                <i class="ti ti-edit text-success"></i>
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    @can('customer.show')
                                                        <div>
                                                            <a href="{{ route('customer.show', Crypt::encrypt($customer->id)) }}" class="me-2">
                                                                <i class="ti ti-file-description text-info"></i>
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    @can('customer.delete')
                                                        <div>
                                                            <form method="POST" action="{{ route('customer.destroy', $customer->id) }}" class="deleteform d-inline">
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
                            {{ $customers->links() }}
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
            $("#modal").modal("show");
            $(".modal-title").text("Edit Data Customer");
            $("#loadmodal").load(`/sobat/customer/${id}/edit`);
        });

        $(document).on('click', '.delete-confirm', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');

        Swal.fire({
            title: 'Hapus customer ini?',
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