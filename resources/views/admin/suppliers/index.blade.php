@extends('layouts.dashboard')

@section('title', 'Suppliers - Coffee SCMS')

@section('dashboard-title')
    <i class="fas fa-truck me-2"></i>Suppliers
@endsection

@section('dashboard-actions')
    <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Add Supplier
    </a>
@endsection

@section('dashboard-content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header border-0">
            <h3 class="card-title">All Suppliers</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Contact Info</th>
                        <th>Status</th>
                        <th>Delivery Rate</th>
                        <th>Quality Score</th>
                        <th>Performance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->contact_info }}</td>
                            <td>
                                <span class="badge bg-{{ $supplier->status === 'approved' ? 'success' : ($supplier->status === 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($supplier->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="progress" style="height: 0.5rem;">
                                    <div class="progress-bar bg-{{ $supplier->delivery_rate >= 90 ? 'success' : ($supplier->delivery_rate >= 70 ? 'warning' : 'danger') }}"
                                         role="progressbar"
                                         style="width: {{ $supplier->delivery_rate }}%"
                                         title="{{ $supplier->delivery_rate }}% On-Time Delivery">
                                    </div>
                                </div>
                                <small class="text-muted">{{ $supplier->delivery_rate }}%</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="stars me-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star{{ $i <= ($supplier->quality_score/2) ? ' text-warning' : ' text-muted' }}"></i>
                                        @endfor
                                    </div>
                                    <small class="text-muted">{{ $supplier->quality_score }}/10</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $supplier->performance_status === 'excellent' ? 'success' : ($supplier->performance_status === 'good' ? 'primary' : ($supplier->performance_status === 'fair' ? 'warning' : 'danger')) }}">
                                    {{ ucfirst($supplier->performance_status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.suppliers.show', $supplier) }}" class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" title="Delete"
                                            onclick="if(confirm('Are you sure you want to delete this supplier?')) { document.getElementById('delete-supplier-{{ $supplier->id }}').submit(); }">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-supplier-{{ $supplier->id }}"
                                      action="{{ route('admin.suppliers.destroy', $supplier) }}"
                                      method="POST"
                                      style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-2x mb-3"></i>
                                    <p class="mb-0">No suppliers found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .progress {
        height: 0.5rem;
        margin-bottom: 0.25rem;
    }
    .stars {
        font-size: 0.8rem;
    }
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }
</style>
@endpush
