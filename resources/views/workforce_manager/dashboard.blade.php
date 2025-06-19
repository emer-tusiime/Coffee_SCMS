@extends('layouts.dashboard_admin')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4"><i class="fa fa-users-cog"></i> Workforce Manager Dashboard</h2>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card border-info shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Workforce Members</h5>
                    <p class="card-text fs-2 fw-bold">{{ $workforceCount ?? 0 }}</p>
                    <a href="{{ route('workforce.members.index') }}" class="btn btn-info btn-sm">View Members</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Active Shifts</h5>
                    <p class="card-text fs-2 fw-bold">{{ $activeShifts ?? 0 }}</p>
                    <a href="{{ route('workforce.shifts.index') }}" class="btn btn-success btn-sm">View Shifts</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-warning shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Pending Approvals</h5>
                    <p class="card-text fs-2 fw-bold">{{ $pendingApprovals ?? 0 }}</p>
                    <a href="{{ route('workforce.approvals.index') }}" class="btn btn-warning btn-sm">View Approvals</a>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-5">
        <h4>Recent Activity</h4>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Member</th>
                        <th>Activity</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentActivities as $activity)
                        <tr>
                            <td>{{ $activity->id }}</td>
                            <td>{{ $activity->member->name ?? 'N/A' }}</td>
                            <td>{{ $activity->description }}</td>
                            <td>
                                <span class="badge bg-{{ $activity->status === 'approved' ? 'success' : 'warning' }}">
                                    {{ ucfirst($activity->status) }}
                                </span>
                            </td>
                            <td>{{ $activity->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No recent activity found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection