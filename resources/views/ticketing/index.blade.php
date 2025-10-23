@extends('layouts.app')
@section('titlepage', 'Ticketing')

@section('content')
@section('navigasi')
    <span>Ticketing</span>
@endsection

<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="card mb-4">
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success mt-2">{{ session('success') }}</div>
                @endif

                {{-- ================== Charts Section ================== --}}
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <canvas id="environmentChart"></canvas>
                    </div>
                    <div class="col-md-6 mb-4">
                        <canvas id="productChart"></canvas>
                    </div>
                    <div class="col-md-6 mb-4">
                        <canvas id="priorityChart"></canvas>
                    </div>
                    <div class="col-md-6 mb-4">
                        <canvas id="typeChart"></canvas>
                    </div>
                    <div class="col-md-12 mb-4">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>

                {{-- ================== Filter and Table ================== --}}
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    {{-- Search Form --}}
                    <form action="{{ route('ticketing.index') }}" method="GET" class="d-flex align-items-center gap-2">
                        <x-input-with-icon
                            label=""
                            placeholder="Cari Ticket"
                            value="{{ Request('search') }}"
                            name="search"
                            icon="ti ti-search"
                        />
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="ti ti-search me-1"></i> Cari
                        </button>
                    </form>

                    {{-- Upload Form --}}
                    <form action="{{ route('ticketing.upload') }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-2">
                        @csrf
                        <input type="file" name="file" id="file" class="form-control form-control-sm" required style="max-width: 230px;">
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fa fa-upload me-1"></i> Upload
                        </button>
                    </form>
                </div>


                <div class="table-responsive mb-2 mt-4">
                    <table class="table table-hover table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Product</th>
                                <th>Environment</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Created At</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets as $ticket)
                                <tr>
                                    <td>{{ $ticket->code }}</td>
                                    <td>{{ $ticket->name }}</td>
                                    <td>{{ $ticket->product }}</td>
                                    <td>{{ $ticket->environment }}</td>
                                    <td>
                                        @if (strtolower($ticket->status) == 'waiting for user feedback')
                                            <span class="badge bg-warning text-dark">{{ $ticket->status }}</span>
                                        @elseif (strtolower($ticket->status) == 'closed')
                                            <span class="badge bg-success">{{ $ticket->status }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $ticket->status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $ticket->priority }}</td>
                                    <td>{{ $ticket->created_at }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="#" class="me-2 btnDetail" ticket="{{ $ticket->Id }}">
                                                <i class="ti ti-file-description text-info"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if(method_exists($tickets, 'links'))
                    <div style="float: right;">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<x-modal-form id="modal" show="loadmodal" />
@endsection

@push('myscript')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartData = @json($chartData);

    // === Environment Chart ===
    new Chart(document.getElementById('environmentChart'), {
        type: 'pie',
        data: {
            labels: Object.keys(chartData.environmentCount),
            datasets: [{
                data: Object.values(chartData.environmentCount),
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
            }]
        },
        options: { plugins: { title: { display: true, text: 'Tickets by Environment' } } }
    });

    // === Product Chart ===
    new Chart(document.getElementById('productChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(chartData.productCount),
            datasets: [{
                data: Object.values(chartData.productCount),
                backgroundColor: [
                    '#e74a3b', // red
                    '#858796', // gray
                    '#f6c23e', // yellow
                    '#1cc88a', // green
                    '#36b9cc', // teal
                    '#4e73df', // blue
                    '#a569bd', // purple
                    '#ff9f40', // orange
                    '#20c997', // emerald
                    '#2e59d9'  // deep blue
                ],
            }]
        },
        options: { plugins: { title: { display: true, text: 'Tickets by Product' } } }
    });

    // === Priority Chart ===
    new Chart(document.getElementById('priorityChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(chartData.priorityCount),
            datasets: [{
                label: 'Tickets',
                data: Object.values(chartData.priorityCount),
                backgroundColor: '#4e73df',
            }]
        },
        options: { plugins: { title: { display: true, text: 'Tickets by Priority' } } }
    });

    // === Type Chart ===
    new Chart(document.getElementById('typeChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(chartData.typeCount),
            datasets: [{
                label: 'Tickets',
                data: Object.values(chartData.typeCount),
                backgroundColor: '#1cc88a',
            }]
        },
        options: { plugins: { title: { display: true, text: 'Tickets by Type' } } }
    });

    // === Monthly Chart ===
    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: Object.keys(chartData.monthlyCount),
            datasets: [{
                label: 'Tickets Created',
                data: Object.values(chartData.monthlyCount),
                borderColor: '#f6c23e',
                backgroundColor: 'rgba(246, 194, 62, 0.2)',
                fill: true,
                tension: 0.3,
            }]
        },
        options: { plugins: { title: { display: true, text: 'Monthly Ticket Trends' } } }
    });
</script>
@endpush
