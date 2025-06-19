@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block bg-dark sidebar min-vh-100">
            <div class="position-sticky">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('factory.dashboard') ? 'active' : '' }} text-white" href="{{ route('factory.dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('factory.production.lines') ? 'active' : '' }} text-white" href="{{ route('factory.production.lines') }}">
                            <i class="fas fa-industry"></i>
                            Production Lines
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('factory.quality.control') ? 'active' : '' }} text-white" href="{{ route('factory.quality.control') }}">
                            <i class="fas fa-check-circle"></i>
                            Quality Control
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('factory.maintenance') ? 'active' : '' }} text-white" href="{{ route('factory.maintenance') }}">
                            <i class="fas fa-tools"></i>
                            Maintenance
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('factory.workforce') ? 'active' : '' }} text-white" href="{{ route('factory.workforce') }}">
                            <i class="fas fa-users"></i>
                            Workforce
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-10 ms-sm-auto px-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Factory Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary">Print</button>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                        <i class="fas fa-calendar"></i>
                        This week
                    </button>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body">
                            <h5 class="card-title">Production Efficiency</h5>
                            <h2 class="card-text">{{ number_format($productionEfficiency, 1) }}%</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body">
                            <h5 class="card-title">Active Production Lines</h5>
                            <h2 class="card-text">{{ $productionLines->where('status', 'active')->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-dark h-100">
                        <div class="card-body">
                            <h5 class="card-title">Quality Issues Today</h5>
                            <h2 class="card-text">{{ $todayQualityIssues->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white h-100">
                        <div class="card-body">
                            <h5 class="card-title">Pending Maintenance</h5>
                            <h2 class="card-text">{{ $pendingMaintenance->count() }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Production Lines Status -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Production Lines Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Line Name</th>
                                            <th>Status</th>
                                            <th>Current Batch</th>
                                            <th>Efficiency</th>
                                            <th>Quality Issues</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($productionLines as $line)
                                        <tr>
                                            <td>{{ $line->name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $line->status === 'active' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($line->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $line->current_batch ?? 'N/A' }}</td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" style="width: {{ $line->efficiency }}%">
                                                        {{ $line->efficiency }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $line->quality_issues_count }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary">Details</button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Workforce Status -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Workforce Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="border rounded p-3">
                                        <h6>Total Workers</h6>
                                        <h3>{{ $workforceStatus['total_workers'] }}</h3>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded p-3">
                                        <h6>Present Today</h6>
                                        <h3>{{ $workforceStatus['present_today'] }}</h3>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <h6>Shift Distribution</h6>
                                    <div class="progress">
                                        @foreach($workforceStatus['shift_distribution'] as $shift => $count)
                                        <div class="progress-bar bg-{{ $loop->iteration == 1 ? 'primary' : ($loop->iteration == 2 ? 'success' : 'info') }}"
                                             role="progressbar"
                                             style="width: {{ ($count / $workforceStatus['total_workers']) * 100 }}%">
                                            {{ ucfirst($shift) }}
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Quality Predictions</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="qualityPredictionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Initialize Quality Prediction Chart
    const ctx = document.getElementById('qualityPredictionChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($qualityPredictions)) !!},
            datasets: [{
                label: 'Predicted Quality Score',
                data: {!! json_encode(array_values($qualityPredictions)) !!},
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
</script>
@endpush
@endsection
