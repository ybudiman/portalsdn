@extends('layouts.app')
@section('titlepage', 'Discount')

@section('content')
@section('navigasi')
    <span>Discount</span>
@endsection

<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="ti ti-discount me-2"></i> Daftar Discount</h5>
                @can('discount.create')
                    <a href="#" class="btn btn-primary" id="btnCreate">
                        <i class="fa fa-plus me-2"></i> Tambah Discount
                    </a>
                @endcan
            </div>

            <div class="card-body">
                {{-- Filter --}}
                <form action="{{ route('discount.index') }}" class="mb-3">
                    <div class="row">
                        <div class="col-lg-4 col-sm-12">
                            <x-input-with-icon 
                                label="Cari Discount" 
                                value="{{ Request('discount_name') }}" 
                                name="discount_name"
                                icon="ti ti-search" />
                        </div>
                        <div class="col-lg-2 col-sm-12">
                            <button class="btn btn-primary w-100">
                                <i class="ti ti-search me-1"></i> Cari
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Table --}}
                <div class="table-responsive mb-2">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Nama Discount</th>
                                <th>Branch Code</th>
                                <th>Level</th>
                                <th>Start Date</th>
                                <th>Finish Date</th>
                                <th width="10%">#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($discounts as $d)
                                <tr data-bs-toggle="collapse" 
                                    data-bs-target="#details-{{ $d->id }}" 
                                    class="accordion-toggle cursor-pointer">
                                    <td>
                                        <i class="ti ti-chevron-down me-2 text-muted"></i>
                                        {{ $d->discount_name }}
                                    </td>
                                    <td>{{ $d->business_area_code ?? '-' }}</td>
                                    <td>{{ $d->level }}</td>
                                    <td>{{ \Carbon\Carbon::parse($d->start_date)->format('d M Y')  }}</td>
                                    <td>{{ \Carbon\Carbon::parse($d->finish_date)->format('d M Y')  }}</td>
                                    <td>
                                        <div class="d-flex">
                                            @can('discount.edit')
                                                <a href="#" class="me-2 btnEdit" discount_id="{{ Crypt::encrypt($d->id) }}">
                                                    <i class="ti ti-edit text-success"></i>
                                                </a>
                                            @endcan
                                            @can('discount.delete')
                                                <form method="POST" 
                                                      action="{{ route('discount.destroy', $d->id) }}" 
                                                      class="deleteform d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-link p-0 delete-confirm" title="Delete">
                                                        <i class="ti ti-trash text-danger"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>

                                {{-- Discount Details Row --}}
                                <tr>
                                    <td colspan="6" class="p-0">
                                        <div id="details-{{ $d->id }}" class="collapse">
                                            <div class="p-3 bg-light">
                                                @if ($d->details->count())
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered mb-0">
                                                            <thead class="bg-secondary text-white">
                                                                <tr>
                                                                    <th>SKU</th>
                                                                    <th>Discount Name</th>
                                                                    <th>Min Qty</th>
                                                                    <th>Max Qty</th>
                                                                    <th>Discount Type</th>
                                                                    <th>Discount Value</th>
                                                                    <th>UOM</th>
                                                                    <th width="10%">#</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($d->details as $detail)
                                                                    <tr>
                                                                        <td>{{ explode('-', $detail->product_uom_code)[0] }}</td>
                                                                        <td>{{ $detail->discount_name }}</td>
                                                                        <td>{{ $detail->min_qty }}</td>
                                                                        <td>{{ $detail->max_qty }}</td>
                                                                        <td>{{ $detail->discount_type }}</td>
                                                                        <td>{{ number_format($detail->discount_value, 2) }}</td>
                                                                        <td>{{ explode('-', $detail->product_uom_code)[1] ?? '-' }}</td>
                                                                        <td>
                                                                            <div class="d-flex">
                                                                                @can('discount.edit')
                                                                                    <a href="#" class="me-2 btnEditDetail" detail_id="{{ Crypt::encrypt($detail->id) }}">
                                                                                        <i class="ti ti-edit text-success"></i>
                                                                                    </a>
                                                                                @endcan
                                                                                @can('discount.delete')
                                                                                    <form method="POST" 
                                                                                        action="{{ route('discount.detail.destroy', $detail->id) }}" 
                                                                                        class="deleteform d-inline">
                                                                                        @csrf
                                                                                        @method('DELETE')
                                                                                        <button type="button" class="btn btn-link p-0 delete-confirm" title="Delete">
                                                                                            <i class="ti ti-trash text-danger"></i>
                                                                                        </button>
                                                                                    </form>
                                                                                @endcan
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>

                                                        </table>
                                                    </div>
                                                @else
                                                    <p class="text-muted mb-0">Tidak ada detail untuk discount ini.</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Tidak ada data discount</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="float-end">
                    {{ $discounts->links() }}
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
        $("#loadmodal").html(`
            <div class="sk-wave sk-primary" style="margin:auto">
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
                <div class="sk-wave-rect"></div>
            </div>`);
    }

    // Create
    $("#btnCreate").click(function() {
        $("#modal").modal("show");
        $(".modal-title").text("Tambah Data Discount");
        loading();
        $("#loadmodal").load("{{ route('discount.create') }}");
    });

    // Edit
    $(".btnEdit").click(function() {
        loading();
        const discount_id = $(this).attr("discount_id");
        $("#modal").modal("show");
        $(".modal-title").text("Edit Data Discount");
        $("#loadmodal").load(`/sobat/discount/${discount_id}/edit`);
    });

    // Delete confirmation
    $(document).on('click', '.delete-confirm', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');
        Swal.fire({
            title: 'Hapus discount ini?',
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
