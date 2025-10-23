@extends('layouts.app')
@section('titlepage', 'Category')

@section('content')
@section('navigasi')
    <span>Kategori</span>
@endsection

<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                @can('category.create')
                    <a href="#" class="btn btn-primary" id="btnCreate"><i class="fa fa-plus me-2"></i> Tambah Kategori</a>
                @endcan
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <form action="{{ route('category.index') }}">
                            <div class="row">
                                <div class="col-lg-4 col-sm-12 col-md-12">
                                    <x-input-with-icon label="Cari Category" value="{{ Request('category_name') }}" name="category_name"
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
                                        <th>Nama Kategori</th>
                                        <th>Deskripsi</th>
                                        <th>Status</th>
                                        <th>Photo</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categories as $c)
                                        <tr>
                                            <td>{{ $c->category_name }}</td>
                                            <td>{{ $c->category_description }}</td>
                                            <td>
                                                @if ($c-> status == 'Active')
                                                    <span>
                                                        {{ $c-> status }}
                                                    </span>
                                                @else
                                                    <span>
                                                        {{ $c-> status }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $file = trim((string)($c->category_image ?? ''));
                                                @endphp

                                                @if ($file !== '')
                                                    @php
                                                    // Bangun base URL (boleh filename atau URL penuh)
                                                    $base = \Illuminate\Support\Str::startsWith($file, ['http://','https://'])
                                                            ? $file
                                                            : 'https://apisobat.sdn.id/cat-img/' . ltrim($file,'/');

                                                    // Versi untuk cache-busting: pakai updated_at agar berubah saat record disave
                                                    $ver  = $c->updated_at ? (\Illuminate\Support\Carbon::parse($c->updated_at)->timestamp) : time();
                                                    $src  = $base . (str_contains($base,'?') ? '&' : '?') . 'v=' . $ver;
                                                    @endphp

                                                    <img src="{{ $src }}" alt="{{ $c->category_name }}"
                                                        style="height:40px;object-fit:contain;"
                                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                                                    <span class="text-muted" style="display:none;">empty</span>
                                                @else
                                                    <span class="text-muted">empty</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    @can('category.edit')
                                                        <div>
                                                            <a href="#" class="me-2 btnEdit" category_name="{{ Crypt::encrypt($c->category_name) }}">
                                                                <i class="ti ti-edit text-success"></i>
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    @can('category.delete')
                                                        <div>
                                                            <form method="POST" action="{{ route('category.destroy', $c->id) }}" class="deleteform d-inline">
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
                            {{ $categories->links() }}
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
            $("#loadmodal").load("{{ route('category.create') }}");
        });

        $(".btnEdit").click(function() {
            loading();
            const category_name = $(this).attr("category_name");
            $("#modal").modal("show");
            $(".modal-title").text("Edit Data Kategori");
            $("#loadmodal").load(`/sobat/category/${category_name}/edit`);
        });

        $(document).on('click', '.delete-confirm', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');

        Swal.fire({
            title: 'Hapus category ini?',
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