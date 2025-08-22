@extends('layouts.app')
@section('titlepage', 'Brand')

@section('content')
@section('navigasi')
    <span>Brand</span>
@endsection

<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                @can('brand.create')
                    <a href="#" class="btn btn-primary" id="btnCreate"><i class="fa fa-plus me-2"></i> Tambah
                        Brand</a>
                @endcan
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <form action="{{ route('brand.index') }}">
                            <div class="row">
                                <div class="col-lg-4 col-sm-12 col-md-12">
                                    <x-input-with-icon label="Cari Brand" value="{{ Request('brand_name') }}" name="brand_name"
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
                                        <th>Nama Brand</th>
                                        <th>Deskripsi</th>
                                        <th>Status</th>
                                        <th>Photo</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($brand as $d)
                                        <tr>
                                            <td>{{ $d->brand_name }}</td>
                                            <td>{{ $d->brand_description }}</td>
                                            <td>
                                                @if ($d-> status == 'Active')
                                                    <span>
                                                        {{ $d-> status }}
                                                    </span>
                                                @else
                                                    <span>
                                                        {{ $d-> status }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $file = trim((string)($d->brand_image ?? ''));
                                                @endphp

                                                @if ($file !== '')
                                                    @php
                                                    // Bangun base URL (boleh filename atau URL penuh)
                                                    $base = \Illuminate\Support\Str::startsWith($file, ['http://','https://'])
                                                            ? $file
                                                            : 'https://apisobat.sdn.id/brand-img/' . ltrim($file,'/');

                                                    // Versi untuk cache-busting: pakai updated_at agar berubah saat record disave
                                                    $ver  = $d->updated_at ? (\Illuminate\Support\Carbon::parse($d->updated_at)->timestamp) : time();
                                                    $src  = $base . (str_contains($base,'?') ? '&' : '?') . 'v=' . $ver;
                                                    @endphp

                                                    <img src="{{ $src }}" alt="{{ $d->brand_name }}"
                                                        style="height:40px;object-fit:contain;"
                                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                                                    <span class="text-muted" style="display:none;">empty</span>
                                                @else
                                                    <span class="text-muted">empty</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    @can('brand.edit')
                                                        <div>
                                                            <a href="#" class="me-2 btnEdit" brand_name="{{ Crypt::encrypt($d->brand_name) }}">
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
                                                    @can('brand.delete')
                                                        <div>
                                                            <form method="POST" action="{{ route('brand.destroy', $d->id) }}" class="deleteform d-inline">
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
                            {{ $brand->links() }}
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
            $(".modal-title").text("Tambah Data Brand");
            $("#loadmodal").load("{{ route('brand.create') }}");
        });

        $(".btnEdit").click(function() {
            loading();
            const brand_name = $(this).attr("brand_name");
            $("#modal").modal("show");
            $(".modal-title").text("Edit Data Brand");
            $("#loadmodal").load(`/sobat/brand/${brand_name}/edit`);
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