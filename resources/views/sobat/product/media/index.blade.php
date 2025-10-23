@extends('layouts.app')
@section('titlepage', 'Product')

@section('content')
@section('navigasi')
    <span>Product</span>
@endsection

<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                @can('product.create')
                    <a href="#" class="btn btn-primary" id="btnCreate"><i class="fa fa-plus me-2"></i> Tambah Product</a>
                @endcan
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-12">
                        <form action="{{ route('product.index') }}">
                            <div class="row">
                                <div class="col-lg-4 col-sm-12 col-md-12">
                                    <x-input-with-icon 
                                        label="Cari Product" 
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
                                <th>Deskripsi</th>
                                <th>Gambar</th>
                                <th>Baru?</th>
                                <th>Featured?</th>
                                <th>External Code</th>
                                <th>Kategori</th>
                                <th>Principal</th>
                                <th>Brand</th>
                                <th>Taxable</th>
                                <th>Tax ID</th>
                                <th>Min. Order UOM</th>
                                <th>Status</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $p)
                                <tr>
                                    <td>{{ $p->product_code }}</td>
                                    <td>{{ $p->product_name }}</td>
                                    <td>
                                        @php $desc = $p->product_description ?? ''; @endphp
                                        @if(strlen($desc) > 50)
                                            <span class="short-desc">{{ Str::limit($desc, 50) }}</span>
                                            <span class="full-desc" style="display:none;">{{ $desc }}</span>
                                            <a href="#" class="see-more text-primary small">See more</a>
                                        @else
                                            {{ $desc }}
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $file = trim((string)($p->product_image ?? ''));
                                        @endphp

                                        @if ($file !== '')
                                            @php
                                                // Base URL: if already absolute (http/https), use directly; otherwise prepend product path
                                                $base = \Illuminate\Support\Str::startsWith($file, ['http://','https://'])
                                                        ? $file
                                                        : 'https://apisobat.sdn.id/prod-img/' . ltrim($file,'/');

                                                // Cache-busting param using updated_at (fallback to current timestamp)
                                                $ver  = $p->updated_at ? (\Illuminate\Support\Carbon::parse($p->updated_at)->timestamp) : time();
                                                $src  = $base . (str_contains($base,'?') ? '&' : '?') . 'v=' . $ver;
                                            @endphp

                                            <img src="{{ $src }}" 
                                                alt="{{ $p->product_name }}"
                                                style="height:40px;object-fit:contain;"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                                            <span class="text-muted" style="display:none;">empty</span>
                                        @else
                                            <span class="text-muted">empty</span>
                                        @endif
                                    </td>

                                    <td>{!! $p->isNew ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' !!}</td>
                                    <td>{!! $p->isFeatured ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' !!}</td>
                                    <td>{{ $p->external_product_code }}</td>
                                    <td>{{ $p->category?->category_name ?? '-' }}</td>
                                    <td>{{ $p->principal?->principal_name ?? '-' }}</td>
                                    <td>{{ $p->brand?->brand_name ?? '-' }}</td>
                                    <td>{!! $p->taxable ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>' !!}</td>
                                    <td>{{ $p->tax?->tax_name ?? '-' }}</td>
                                    <td>{{ $p->minOrderUom?->uom_name ?? '-' }}</td>
                                    <td>
                                        @if ($p->status == 'Active')
                                            <span class="badge bg-success">{{ $p->status }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ $p->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            @can('product.edit')
                                                <div>
                                                    <a href="#" class="me-2 btnEdit" productId="{{ Crypt::encrypt($p->id) }}">
                                                        <i class="ti ti-edit text-success"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('product.show')
                                                <div>
                                                    <a href="{{ route('product.show', Crypt::encrypt($p->id)) }}" class="me-2">
                                                        <i class="ti ti-file-description text-info"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('product.delete')
                                                <div>
                                                    <form method="POST" action="{{ route('product.destroy', $p->id) }}" class="deleteform d-inline">
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
                    {{ $products->links() }}
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
            $(".modal-title").text("Tambah Data Product");
            $("#loadmodal").load("{{ route('product.create') }}");
        });

        // EDIT
        $(".btnEdit").click(function() {
            loading();
            const productId = $(this).attr("productId");
            $("#modal").modal("show");
            $(".modal-title").text("Edit Data Product");
            $("#loadmodal").load(`/sobat/product/${productId}/edit`);
        });

        // DELETE CONFIRM
        $(document).on('click', '.delete-confirm', function (e) {
            e.preventDefault();
            const form = $(this).closest('form');

            Swal.fire({
                title: 'Hapus product ini?',
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

        $(document).on('click', '.see-more', function(e) {
            e.preventDefault();
            const cell = $(this).closest('td');
            const shortDesc = cell.find('.short-desc');
            const fullDesc = cell.find('.full-desc');

            if (shortDesc.is(':visible')) {
                shortDesc.hide();
                fullDesc.show();
                $(this).text('See less');
            } else {
                fullDesc.hide();
                shortDesc.show();
                $(this).text('See more');
            }
        });
    });
</script>
@endpush