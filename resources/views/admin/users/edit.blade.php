@extends('layouts.dashboard')

@section('title', 'Edit User - Coffee SCMS')

@section('dashboard-title')
    <h1 class="m-0">Edit User</h1>
@endsection

@section('dashboard-content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit User Information</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Users
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ old('email', $user->email) }}" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="role">Role</label>
                                <select class="form-control" id="role" name="role" required>
                                    @foreach($roles as $role)
                                        <option value="{{ $role }}" {{ $user->role === $role ? 'selected' : '' }}>
                                            {{ ucfirst($role) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="contact_info">Contact Info</label>
                                <input type="text" class="form-control" id="contact_info" name="contact_info" 
                                       value="{{ old('contact_info', $user->contact_info) }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="password">Password (leave empty to keep current)</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="password_confirmation">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation" 
                                       name="password_confirmation">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" name="status" required>
                                <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="pending" {{ $user->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="approved" name="approved" 
                                       {{ $user->approved ? 'checked' : '' }}>
                                <label class="custom-control-label" for="approved">Approved</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Approval Message</label>
                            <textarea class="form-control" name="approval_message" rows="3">{{ old('approval_message', $user->approval_message) }}</textarea>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
