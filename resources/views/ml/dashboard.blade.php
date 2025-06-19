@extends('layouts.dashboard')

@section('title', 'ML Insights - Coffee SCMS')

@section('dashboard-content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Machine Learning Insights</h1>

    <!-- Demand Predictions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Demand Predictions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($predictions as $prediction)
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $prediction['month'] }}</h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h4 class="mb-0">{{ number_format($prediction['predicted_demand']) }} units</h4>
                                        <span class="badge bg-info">{{ $prediction['confidence'] }}% confidence</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quality Predictions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quality Predictions for Current Batches</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Batch ID</th>
                                    <th>Predicted Quality</th>
                                    <th>Temperature</th>
                                    <th>Humidity</th>
                                    <th>Roasting Time</th>
                                    <th>Bean Type</th>
                                    <th>Processing</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($qualityPredictions as $prediction)
                                <tr>
                                    <td>#{{ $prediction['batch_id'] }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1" style="height: 5px;">
                                                <div class="progress-bar bg-{{ $prediction['predicted_quality'] >= 80 ? 'success' : ($prediction['predicted_quality'] >= 60 ? 'warning' : 'danger') }}"
                                                     style="width: {{ $prediction['predicted_quality'] }}%">
                                                </div>
                                            </div>
                                            <span class="ms-2">{{ $prediction['predicted_quality'] }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $prediction['factors']['temperature']['status'] === 'optimal' ? 'success' : 'warning' }}">
                                            {{ $prediction['factors']['temperature']['message'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $prediction['factors']['humidity']['status'] === 'optimal' ? 'success' : 'warning' }}">
                                            {{ $prediction['factors']['humidity']['message'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $prediction['factors']['roasting_time']['status'] === 'optimal' ? 'success' : 'warning' }}">
                                            {{ $prediction['factors']['roasting_time']['message'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $prediction['factors']['bean_type']['message'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $prediction['factors']['processing']['message'] }}
                                        </span>
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

    <!-- Production Optimization -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Optimal Batch Size</h5>
                </div>
                <div class="card-body">
                    <h3 class="mb-0">{{ $optimization['optimal_batch_size'] }} kg</h3>
                    <p class="text-muted">Based on historical efficiency data</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Optimal Roasting Time</h5>
                </div>
                <div class="card-body">
                    <h3 class="mb-0">{{ $optimization['optimal_roasting_time'] }} minutes</h3>
                    <p class="text-muted">For best quality score</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Next Maintenance Due</h5>
                </div>
                <div class="card-body">
                    <h3 class="mb-0">{{ $optimization['suggested_maintenance_schedule']['next_maintenance_due']->format('M d, Y') }}</h3>
                    <p class="text-muted">{{ $optimization['suggested_maintenance_schedule']['suggested_interval_days'] }} days interval</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Priority Equipment -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Priority Equipment for Maintenance</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($optimization['suggested_maintenance_schedule']['priority_equipment'] as $equipment => $issues)
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $equipment }}</h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Issue Count:</span>
                                        <span class="badge bg-warning">{{ $issues }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Add any custom JavaScript for the ML dashboard here
</script>
@endpush
