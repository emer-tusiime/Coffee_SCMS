@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Production Efficiency</h1>
            <p class="text-muted mb-0">Monitor and manage production line efficiency</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="display-4 mb-0">{{ number_format($overallEfficiency, 1) }}%</h3>
                            <div class="text-white-50">Overall Efficiency</div>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-line fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="display-4 mb-0">{{ $stats['active_lines'] }}</h3>
                            <div class="text-white-50">Active Lines</div>
                        </div>
                        <div class="icon">
                            <i class="fas fa-industry fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="display-4 mb-0">{{ $stats['maintenance_lines'] }}</h3>
                            <div class="text-white-50">In Maintenance</div>
                        </div>
                        <div class="icon">
                            <i class="fas fa-tools fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="display-4 mb-0">{{ $lowEfficiencyLines->count() }}</h3>
                            <div class="text-white-50">Low Efficiency Lines</div>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-triangle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Production Lines Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Production Lines</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Line Name</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Efficiency</th>
                            <th class="px-4 py-3">Current Batch</th>
                            <th class="px-4 py-3">Last Maintenance</th>
                            <th class="px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productionLines as $line)
                            <tr>
                                <td class="px-4 py-3">{{ $line->name }}</td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-{{ $line->status === 'active' ? 'success' : ($line->status === 'maintenance' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($line->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1" style="height: 8px;">
                                            <div class="progress-bar {{ $line->efficiency < 80 ? 'bg-danger' : 'bg-success' }}"
                                                 role="progressbar"
                                                 style="width: {{ $line->efficiency }}%"
                                                 aria-valuenow="{{ $line->efficiency }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100"></div>
                                        </div>
                                        <span class="ms-2">{{ number_format($line->efficiency, 1) }}%</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">{{ $line->current_batch ?? 'No active batch' }}</td>
                                <td class="px-4 py-3">{{ $line->last_maintenance_date ? $line->last_maintenance_date->format('M d, Y') : 'Never' }}</td>
                                <td class="px-4 py-3">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#updateEfficiency{{ $line->id }}">
                                        Update Efficiency
                                    </button>
                                </td>
                            </tr>

                            <!-- Update Efficiency Modal -->
                            <div class="modal fade" id="updateEfficiency{{ $line->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.production.update-efficiency', $line->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Update Efficiency - {{ $line->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="efficiency{{ $line->id }}" class="form-label">Efficiency (%)</label>
                                                    <input type="number" class="form-control" id="efficiency{{ $line->id }}"
                                                           name="efficiency" value="{{ $line->efficiency }}"
                                                           min="0" max="100" step="0.1" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
