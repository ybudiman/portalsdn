@extends('layouts.app')
@section('titlepage', 'UOM')

@section('content')
@section('navigasi')
    <span>UOM</span>
@endsection

<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                @can('uom.create')
                    <a href="#" class="btn btn-primary" id="btnCreate">
                        <i class="fa fa-plus me-2"></i> Tambah UOM
                    </a>
                @endcan
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-12">
                        <form action="{{ route('product.uom.index') }}">
                            <div class="row">
                                <div class="col-lg-4 col-sm-12 col-md-12">
                                    <x-input-with-icon 
                                        label="Cari UOM" 
                                        value="{{ Request('search_query') }}" 
                                        name="search_query"
                                        icon="ti ti-search" />
                                </div>
                                <div class="col-lg-2 col-sm-12 col-md-12">
                                    <button class="btn btn-primary">
                                        <i class="ti ti-icons ti-search me-1"></i>Cari
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive mb-2">
                    <table class="table table-hover table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Status</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($uoms as $u)
                                <tr>
                                    <td>{{ $u->uom_code }}</td>
                                    <td>{{ $u->uom_name }}</td>
                                    <td>
                                        @if ($u->status == 'Active')
                                            <span class="badge bg-success">{{ $u->status }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ $u->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            @can('uom.edit')
                                                <div>
                                                    <a href="#" class="me-2 btnEdit" uomId="{{ Crypt::encrypt($u->id) }}">
                                                        <i class="ti ti-edit text-success"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('uom.show')
                                                <div>
                                                    <a href="{{ route('uom.show', Crypt::encrypt($u->id)) }}" class="me-2">
                                                        <i class="ti ti-file-description text-info"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('uom.delete')
                                                <div>
                                                    <form method="POST" action="{{ route('uom.destroy', $u->id) }}" class="deleteform d-inline">
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
                    {{ $uoms->links() }}
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

        // CREATE
        $("#btnCreate").click(function() {
            $("#modal").modal("show");
            $(".modal-title").text("Tambah Data UOM");
            $("#loadmodal").load("{{ route('uom.create') }}");
        });

        // EDIT
        $(".btnEdit").click(function() {
            loading();
            const uomId = $(this).attr("uomId");
            $("#modal").modal("show");
            $(".modal-title").text("Edit Data UOM");
            $("#loadmodal").load(`/sobat/uom/${uomId}/edit`);
        });

        // DELETE CONFIRM
        $(document).on('click', '.delete-confirm', function (e) {
            e.preventDefault();
            const form = $(this).closest('form');

            Swal.fire({
                title: 'Hapus UOM ini?',
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
