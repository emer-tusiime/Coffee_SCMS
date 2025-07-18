@php
    use App\Models\User;
@endphp

@extends('layouts.dashboard')

@section('title', 'Users - Coffee SCMS')

@section('dashboard-title')
    <i class="fas fa-users me-2"></i>Users
@endsection

@section('dashboard-actions')
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Add User
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
            <h3 class="card-title">All Users</h3>
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
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge bg-info">
                                    {{ User::getRoles()[$user->role] ?? ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'danger' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <button type="button" class="btn btn-sm btn-danger" title="Delete"
                                                onclick="if(confirm('Are you sure you want to delete this user?')) { document.getElementById('delete-user-{{ $user->id }}').submit(); }">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                    @if($user->approved && $user->status === 'active' && !$user->isAdmin())
                                        <a href="{{ route('admin.users.view-dashboard', $user->id) }}" class="btn btn-sm btn-success" style="font-weight:bold;" title="View Dashboard (Test)">
                                            <i class="fas fa-eye"></i> Dashboard
                                        </a>
                                    @endif
                                </div>
                                <form id="delete-user-{{ $user->id }}"
                                      action="{{ route('admin.users.destroy', $user) }}"
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
                                    <i class="fas fa-users fa-2x mb-3"></i>
                                    <p class="mb-0">No users found</p>
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
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }
</style>
@endpush
