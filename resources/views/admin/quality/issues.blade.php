@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Quality Issues</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Quality Issues List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product</th>
                            <th>Issue Type</th>
                            <th>Severity</th>
                            <th>Status</th>
                            <th>Reported By</th>
                            <th>Reported At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($qualityIssues as $issue)
                            <tr>
                                <td>{{ $issue->id }}</td>
                                <td>{{ $issue->product->name }}</td>
                                <td>{{ $issue->issue_type }}</td>
                                <td>
                                    <span class="badge badge-{{ $issue->severity === 'critical' ? 'danger' : ($issue->severity === 'high' ? 'warning' : ($issue->severity === 'medium' ? 'info' : 'secondary')) }}">
                                        {{ ucfirst($issue->severity) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $issue->status === 'open' ? 'danger' : ($issue->status === 'in_progress' ? 'warning' : ($issue->status === 'resolved' ? 'success' : 'secondary')) }}">
                                        {{ ucfirst(str_replace('_', ' ', $issue->status)) }}
                                    </span>
                                </td>
                                <td>{{ $issue->reportedBy->name }}</td>
                                <td>{{ $issue->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="#" class="btn btn-info btn-sm" data-toggle="modal" data-target="#issueModal{{ $issue->id }}">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>

                            <!-- Issue Details Modal -->
                            <div class="modal fade" id="issueModal{{ $issue->id }}" tabindex="-1" role="dialog" aria-labelledby="issueModalLabel{{ $issue->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="issueModalLabel{{ $issue->id }}">Quality Issue Details</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>Product:</strong> {{ $issue->product->name }}</p>
                                                    <p><strong>Issue Type:</strong> {{ $issue->issue_type }}</p>
                                                    <p><strong>Severity:</strong> {{ ucfirst($issue->severity) }}</p>
                                                    <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $issue->status)) }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Reported By:</strong> {{ $issue->reportedBy->name }}</p>
                                                    <p><strong>Reported At:</strong> {{ $issue->created_at->format('Y-m-d H:i') }}</p>
                                                    @if($issue->resolved_at)
                                                        <p><strong>Resolved At:</strong> {{ $issue->resolved_at->format('Y-m-d H:i') }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <h6>Description:</h6>
                                                    <p>{{ $issue->description }}</p>
                                                </div>
                                            </div>
                                            @if($issue->resolution)
                                                <div class="row mt-3">
                                                    <div class="col-12">
                                                        <h6>Resolution:</h6>
                                                        <p>{{ $issue->resolution }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No quality issues found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
