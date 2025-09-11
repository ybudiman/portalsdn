@extends('layouts.app')
@section('titlepage', 'Kategori')

@section('content')
@section('navigasi')
    <span>Kategori</span>
@endsection

<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                @can('categories.create')
                    <a href="#" class="btn btn-primary" id="btnCreate"><i class="fa fa-plus me-2"></i> Tambah
                        Kategori</a>
                @endcan
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <form action="{{ route('categories.index') }}">
                            <div class="row">
                                <div class="col-lg-4 col-sm-12 col-md-12">
                                    <x-input-with-icon label="Cari Kategori" value="{{ Request('category_name') }}" name="category_name"
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
                                        <th>Kode</th>
                                        <th>Nama Kategori</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($category as $d)
                                        <tr>
                                            <td>{{ $d->category_code }}</td>
                                            <td>{{ $d->category_name }}</td>
                                            <td>
                                                <div class="d-flex">
                                                    @can('categories.edit')
                                                        <div>
                                                            <a href="#" class="me-2 btnEdit" category_code="{{ Crypt::encrypt($d->category_code) }}">
                                                                <i class="ti ti-edit text-success"></i>
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    <!-- @can('karyawan.show')
                                                        <div>
                                                            <a href="{{ route('karyawan.show', Crypt::encrypt($d->nik)) }}" class="me-2">
                                                                <i class="ti ti-file-description text-info"></i>
                                                            </a>
                                                        </div>
                                                    @endcan -->
                                                    @can('categories.delete')
                                                        <div>
                                                            <form method="POST" action="{{ route('categories.destroy', $d->id) }}" class="deleteform d-inline">
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
                            {{ $category->links() }}
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
        $("#btnCreate").click(function() {
            $("#modal").modal("show");
            $(".modal-title").text("Tambah Data Kategori");
            $("#loadmodal").load("{{ route('categories.create') }}");
        });

        $(".btnEdit").click(function() {
            loading();
            const category_code = $(this).attr("category_code");
            $("#modal").modal("show");
            $(".modal-title").text("Edit Data Kategori");
            $("#loadmodal").load(`/sidia/categories/${category_code}/edit`);
        });

        $(document).on('click', '.delete-confirm', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');

        Swal.fire({
            title: 'Hapus brand ini?',
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