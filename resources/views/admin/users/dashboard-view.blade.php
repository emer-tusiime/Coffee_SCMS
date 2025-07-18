@extends('layouts.dashboard')

@section('title', 'View Dashboard - ' . $user->name . ' - Coffee SCMS')

@section('dashboard-title')
    <h1 class="m-0">View Dashboard: {{ $user->name }}</h1>
@endsection

@section('dashboard-content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-eye"></i> 
                        {{ ucfirst($user->role) }} Dashboard - {{ $user->name }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to User Details
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Read-only notice -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Read-Only View:</strong> You are viewing {{ $user->name }}'s dashboard in read-only mode. 
                        No actions can be performed from this view.
                    </div>

                    <!-- Role-specific dashboard content -->
                    @switch(strtolower($user->role))
                        @case('supplier')
                            @include('admin.users.dashboard-parts.supplier-dashboard')
                            @break
                        @case('factory')
                            @include('admin.users.dashboard-parts.factory-dashboard')
                            @break
                        @case('wholesaler')
                            @include('admin.users.dashboard-parts.wholesaler-dashboard')
                            @break
                        @case('retailer')
                            @include('admin.users.dashboard-parts.retailer-dashboard')
                            @break
                        @default
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                Dashboard view not available for this user role.
                            </div>
                    @endswitch
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 