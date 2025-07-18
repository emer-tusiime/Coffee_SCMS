@extends('layouts.dashboard')

@section('title', 'User Details - Coffee SCMS')

@section('dashboard-title')
    <h1 class="m-0">User Details</h1>
@endsection

@section('dashboard-content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">User Information</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Users
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name</label>
                                <p class="form-control-plaintext">{{ $user->name }}</p>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <p class="form-control-plaintext">{{ $user->email }}</p>
                            </div>
                            <div class="form-group">
                                <label>Role</label>
                                <p class="form-control-plaintext">{{ $user->role }}</p>
                            </div>
                            <div class="form-group">
                                <label>Contact Info</label>
                                <p class="form-control-plaintext">{{ $user->contact_info }}</p>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-{{ $user->status === 'active' ? 'success' : ($user->status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </p>
                            </div>
                            <div class="form-group">
                                <label>Registration Date</label>
                                <p class="form-control-plaintext">{{ $user->created_at->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Approved</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-{{ $user->approved ? 'success' : 'danger' }}">
                                        {{ $user->approved ? 'Yes' : 'No' }}
                                    </span>
                                </p>
                            </div>
                            <div class="form-group">
                                <label>Approval Message</label>
                                <p class="form-control-plaintext">{{ $user->approval_message }}</p>
                            </div>
                            <div class="form-group">
                                <label>Last Updated</label>
                                <p class="form-control-plaintext">{{ $user->updated_at->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    @if($user->status === 'pending' && !$user->approved)
                        <div class="d-flex justify-content-between">
                            <!-- Approve Button -->
                            <form action="{{ route('admin.accounts.approve', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('Approve this account?')">
                                    <i class="fas fa-check-circle text-success fa-2x"></i>
                                    <span class="ml-2">Approve Account</span>
                                </button>
                            </form>

                            <!-- Reject Button -->
                            <button type="button" class="btn btn-danger btn-lg" data-toggle="modal" data-target="#rejectModal">
                                <i class="fas fa-times-circle text-danger fa-2x"></i>
                                <span class="ml-2">Reject Account</span>
                            </button>
                        </div>
                    @elseif($user->approved && $user->status === 'active' && !$user->isAdmin())
                        <div class="d-flex justify-content-between">
                            <!-- View Dashboard Button -->
                            <a href="{{ route('admin.users.view-dashboard', $user->id) }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-eye text-primary fa-2x"></i>
                                <span class="ml-2">View Dashboard</span>
                            </a>
                            
                            <!-- Edit User Button -->
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-info btn-lg">
                                <i class="fas fa-edit text-info fa-2x"></i>
                                <span class="ml-2">Edit User</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Account</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.accounts.reject', $user) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejection_reason">Reason for rejection:</label>
                        <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
