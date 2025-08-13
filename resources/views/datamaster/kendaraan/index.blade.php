@extends('layouts.app')
@section('titlepage', 'Kendaraan')

@section('content')
@section('navigasi')
    <span>Kendaraan</span>
@endsection

<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                @can('kendaraan.create')
                    <a href="#" class="btn btn-primary" id="btnCreate"><i class="fa fa-plus me-2"></i> Tambah
                        Kendaraan</a>
                    <a href="#" class="btn btn-success" id="btnImport"><i class="ti ti-file-import me-2"></i> Import Excel</a>
                @endcan
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <form action="{{ route('kendaraan.index') }}">
                            <div class="row">
                                <div class="col-lg-4 col-sm-12 col-md-12">
                                    <x-input-with-icon label="Cari Nama Kendaraan" value="{{ Request('nama_kendaraan') }}" name="nama_kendaraan"
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
                                        <th>Nama Kendaraan</th>
                                        <th>Nomor Polisi</th>
                                        <th>Cabang</th>
                                        <th>Jenis Kendaran</th>
                                        <th>Owner</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kendaraan as $d)
                                        <tr>
                                            <td>{{ $d->kode_kendaraan }}</td>
                                            <td>{{ $d->nama_kendaraan }}</td>
                                            <td>{{ $d->nomor_polisi }}</td>
                                            <td>{{ $d->kode_cabang }}</td>
                                            <td>{{ $d->kode_jeniskendaraan }}</td>
                                            <td>
                                                @if ($d->kode_pemilikkendaraan == 'E')
                                                    <span class="badge bg-success">Eksternal</span>
                                                @else
                                                    <span class="badge bg-warning">Internal</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    @can('karyawan.edit')
                                                        <div>
                                                            <a href="#" class="me-2 btnEdit" nik="{{ Crypt::encrypt($d->nik) }}">
                                                                <i class="ti ti-edit text-success"></i>
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    @can('karyawan.show')
                                                        <div>
                                                            <a href="{{ route('karyawan.show', Crypt::encrypt($d->nik)) }}" class="me-2">
                                                                <i class="ti ti-file-description text-info"></i>
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    @can('karyawan.delete')
                                                        <div>
                                                            <form method="POST" name="deleteform" class="deleteform me-1"
                                                                action="{{ route('karyawan.delete', Crypt::encrypt($d->nik)) }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <a href="#" class="delete-confirm ml-1">
                                                                    <i class="ti ti-trash text-danger"></i>
                                                                </a>
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
                            {{ $kendaraan->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<x-modal-form id="modal" show="loadmodal" />
<x-modal-form id="modalImport" show="loadmodalImport" size="modal-lg" title="Import Data Kendaraan" />
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
            $(".modal-title").text("Tambah Data Kendaraan");
            $("#loadmodal").load("{{ route('kendaraan.create') }}");
        });

        $("#btnImport").click(function() {
            $("#modalImport").modal("show");
            $("#loadmodalImport").html(`<div class="sk-wave sk-primary" style="margin:auto">
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            </div>`);
            $("#loadmodalImport").load("{{ route('karyawan.import') }}");
        });

        $(".btnEdit").click(function() {
            loading();
            const nik = $(this).attr("nik");
            $("#modal").modal("show");
            $(".modal-title").text("Edit Data Karyawan");
            $("#loadmodal").load(`/karyawan/${nik}/edit`);
        });

        $(".btnSetJamkerja").click(function() {
            const nik = $(this).attr("nik");
            $("#modalSetJamkerja").modal("show");
            $("#loadmodalSetJamkerja").html(`<div class="sk-wave sk-primary" style="margin:auto">
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            </div>`);

            $("#loadmodalSetJamkerja").load(`/karyawan/${nik}/setjamkerja`);
        });


    });
</script>
@endpush
