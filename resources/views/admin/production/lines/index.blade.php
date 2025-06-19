@extends('layouts.dashboard')

@section('title', 'Production Lines - Coffee SCMS')

@section('dashboard-title')
    <i class="fas fa-industry me-2"></i>Production Lines
@endsection

@section('dashboard-actions')
    <a href="{{ route('admin.production.lines.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Add Production Line
    </a>
@endsection

@section('dashboard-content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header border-0">
            <h3 class="card-title">All Production Lines</h3>
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
                        <th>Description</th>
                        <th>Capacity</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productionLines as $line)
                        <tr>
                            <td>{{ $line->name }}</td>
                            <td>{{ Str::limit($line->description, 50) }}</td>
                            <td>{{ number_format($line->capacity) }} units/day</td>
                            <td>{!! $line->status_badge !!}</td>
                            <td>{{ $line->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.production.lines.show', $line) }}"
                                       class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.production.lines.edit', $line) }}"
                                       class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger"
                                            onclick="if(confirm('Are you sure you want to delete this production line?')) {
                                                document.getElementById('delete-line-{{ $line->id }}').submit();
                                            }" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-line-{{ $line->id }}"
                                      action="{{ route('admin.production.lines.destroy', $line) }}"
                                      method="POST"
                                      style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-industry fa-2x mb-3"></i>
                                    <p class="mb-0">No production lines found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
