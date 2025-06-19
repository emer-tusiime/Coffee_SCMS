@extends('layouts.dashboard')

@section('title', 'Admin Dashboard - Coffee SCMS')

@section('dashboard-title')
    <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
@endsection

@section('dashboard-actions')
    <div class="btn-group">
        <button type="button" class="btn btn-outline-primary" id="export-report">
            <i class="fas fa-download me-1"></i> Export Report
        </button>
        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#quickActionsModal">
            <i class="fas fa-bolt me-1"></i> Quick Actions
        </button>
    </div>
@endsection

@section('dashboard-content')
    <!-- Quick Stats -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-gradient-primary">
                <div class="inner">
                    <h3>{{ $totalOrders }}</h3>
                    <p>New Orders</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-gradient-success">
                <div class="inner">
                    <h3>{{ $productionEfficiency }}%</h3>
                    <p>Production Efficiency</p>
                </div>
                <div class="icon">
                    <i class="fas fa-industry"></i>
                </div>
                <a href="{{ route('admin.production.efficiency') }}" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-gradient-warning">
                <div class="inner">
                    <h3>{{ $inventoryAlerts }}</h3>
                    <p>Inventory Alerts</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <a href="{{ route('admin.inventory.alerts') }}" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-gradient-danger">
                <div class="inner">
                    <h3>{{ $qualityIssues }}</h3>
                    <p>Quality Issues</p>
                </div>
                <div class="icon">
                    <i class="fas fa-bug"></i>
                </div>
                <a href="{{ route('admin.quality.issues') }}" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Production Line Status -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-industry me-2"></i>Production Line Status
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="maximize">
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Line</th>
                                    <th>Status</th>
                                    <th>Efficiency</th>
                                    <th>Current Batch</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productionLines as $line)
                                <tr>
                                    <td>{{ $line['name'] }}</td>
                                    <td>
                                        <span class="badge bg-{{ $line['status'] === 'active' ? 'success' : ($line['status'] === 'maintenance' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($line['status']) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar bg-{{ $line['efficiency'] >= 80 ? 'success' : ($line['efficiency'] >= 60 ? 'warning' : 'danger') }}"
                                                role="progressbar" style="width: {{ $line['efficiency'] }}%">
                                                {{ $line['efficiency'] }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $line['current_batch'] ?? 'N/A' }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-warning" title="Maintenance">
                                                <i class="fas fa-tools"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ML Insights -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-brain me-2"></i>ML Insights
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Demand Prediction</h6>
                                <small class="text-success">+15%</small>
                            </div>
                            <p class="mb-1">Expected increase in coffee demand next month.</p>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Quality Prediction</h6>
                                <small class="text-warning">Alert</small>
                            </div>
                            <p class="mb-1">Potential quality issues in Line A next week.</p>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Maintenance Prediction</h6>
                                <small class="text-info">Scheduled</small>
                            </div>
                            <p class="mb-1">Line B maintenance recommended in 5 days.</p>
                        </a>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('admin.ml-insights') }}" class="text-primary">View All Insights</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Quality Issues -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-circle me-2"></i>Recent Quality Issues
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Issue</th>
                                    <th>Line</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentQualityIssues as $issue)
                                <tr>
                                    <td>#{{ $issue->id }}</td>
                                    <td>{{ Str::limit($issue->description, 30) }}</td>
                                    <td>{{ $issue->production_line }}</td>
                                    <td>
                                        <span class="badge bg-{{ $issue->status_color }}">
                                            {{ $issue->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.quality.issues.show', $issue->id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('admin.quality.issues') }}" class="text-primary">View All Issues</a>
                </div>
            </div>
        </div>

        <!-- Supplier Performance -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header border-0">
                    <h3 class="card-title">
                        <i class="fas fa-truck me-2"></i>Supplier Performance
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Supplier</th>
                                    <th>On-Time</th>
                                    <th>Quality</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($supplierPerformance as $supplier)
                                <tr>
                                    <td>{{ $supplier->name }}</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar bg-success"
                                                 role="progressbar"
                                                 style="width: {{ $supplier->on_time_delivery }}%">
                                                {{ $supplier->on_time_delivery }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="me-2">{{ $supplier->quality_score }}/10</span>
                                            <div class="stars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star{{ $i <= ($supplier->quality_score/2) ? ' text-warning' : ' text-muted' }}"></i>
                                                @endfor
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $supplier->status === 'active' ? 'success' : 'warning' }}">
                                            {{ ucfirst($supplier->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('admin.suppliers.index') }}" class="text-primary">View All Suppliers</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .small-box {
        border-radius: 0.5rem;
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        display: block;
        margin-bottom: 20px;
        position: relative;
    }
    .small-box .icon {
        color: rgba(0,0,0,.15);
        z-index: 0;
    }
    .small-box .icon i {
        font-size: 70px;
        position: absolute;
        right: 15px;
        top: 15px;
        transition: transform .3s linear;
    }
    .small-box:hover .icon i {
        transform: scale(1.1);
    }
    .small-box .inner {
        padding: 20px;
    }
    .small-box .inner h3 {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0;
        white-space: nowrap;
        padding: 0;
    }
    .small-box .inner p {
        font-size: 1rem;
        margin-bottom: 0;
    }
    .small-box .small-box-footer {
        background-color: rgba(0,0,0,.1);
        color: rgba(255,255,255,.8);
        display: block;
        padding: 3px 0;
        position: relative;
        text-align: center;
        text-decoration: none;
        z-index: 10;
    }
    .small-box .small-box-footer:hover {
        background-color: rgba(0,0,0,.15);
        color: #fff;
    }
    .progress {
        height: 0.5rem;
    }
    .stars {
        font-size: 0.8rem;
    }
    .card {
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        margin-bottom: 1rem;
    }
    .card-header {
        background-color: transparent;
        border-bottom: 1px solid rgba(0,0,0,.125);
        padding: 0.75rem 1.25rem;
        position: relative;
        border-top-left-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize any charts or interactive elements here
});
</script>
@endpush
