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

                        <div class="table-responsive mb-2">
                            <table class="table table-hover table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>User ID</th>
                                        <th>Provinsi KTP</th>
                                        <th>Kota KTP</th>
                                        <th>Nama</th>
                                        <th>NIK</th>
                                        <th>TTL</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Agama</th>
                                        <th>Alamat</th>
                                        <th>RT/RW</th>
                                        <th>Kecamatan</th>
                                        <th>Kelurahan</th>
                                        <th>Status</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customerKTPs as $customerKTP)
                                        @php
                                            $rowClass = match ($customerKTP->status) {
                                                'Active' => 'table-success', // green
                                                'Inactive' => 'table-danger',  // red
                                                default => '',
                                            };
                                        @endphp
                                        <tr class="{{ $rowClass }}">
                                            <td>{{ $customerKTP->user_id }}</td>
                                            <td>{{ $customerKTP->provinsi_ktp }}</td>
                                            <td>{{ $customerKTP->kota_ktp }}</td>
                                            <td>{{ $customerKTP->nama }}</td>
                                            <td>{{ $customerKTP->NIK }}</td>
                                            <td>{{ $customerKTP->TTL }}</td>
                                            <td>{{ $customerKTP->jenis_kelamin }}</td>
                                            <td>{{ $customerKTP->agama }}</td>
                                            <td>{{ $customerKTP->alamat }}</td>
                                            <td>{{ $customerKTP->rt_rw }}</td>
                                            <td>{{ $customerKTP->kecamatan }}</td>
                                            <td>{{ $customerKTP->kelurahan }}</td>
                                            <td>{{ $customerKTP->status }}</td>
                                            <td>
                                                <div class="d-flex">
                                                    @can('customer.edit')
                                                        <div>
                                                            <a href="#" class="me-2 btnEdit" customer_id="{{ Crypt::encrypt($customerKTP->user_id) }}" ktp_id = "{{ Crypt::encrypt($customerKTP->id) }}">
                                                                <i class="ti ti-edit text-success"></i>
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    @can('customer.delete')
                                                        <div>
                                                            <form method="POST" action="{{ route('customer.ktp.destroy', $customerKTP->id) }}" class="deleteform d-inline">
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
                            {{ $customerKTPs->links() }}
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
            const ktpId = $(this).attr("ktp_id");
            $("#modal").modal("show");
            $(".modal-title").text("Edit Data KTP Customer");
            $("#loadmodal").load(`/sobat/customer/${id}/ktp/${ktpId}/edit`);
        });

        $(document).on('click', '.delete-confirm', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');

        Swal.fire({
            title: 'Hapus customerKTP ini?',
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