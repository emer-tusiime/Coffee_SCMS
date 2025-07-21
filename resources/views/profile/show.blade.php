@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-white d-flex align-items-center">
                    <img src="{{ $user->profile_image_url }}" alt="Profile Image" class="rounded-circle me-3" width="80" height="80" style="object-fit:cover;">
                    <div>
                        <h4 class="mb-0">{{ $user->name }}</h4>
                        <div class="text-muted">{{ $user->email }}</div>
                        <div class="text-muted">Role: {{ ucfirst($user->role) }}</div>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary ms-auto">Edit Profile</a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Name</dt>
                        <dd class="col-sm-8">{{ $user->name }}</dd>
                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8">{{ $user->email }}</dd>
                        <dt class="col-sm-4">Role</dt>
                        <dd class="col-sm-8">{{ ucfirst($user->role) }}</dd>
                        <dt class="col-sm-4">Contact Info</dt>
                        <dd class="col-sm-8">{{ $user->contact_info ?? '-' }}</dd>
                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8">{{ ucfirst($user->status) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 