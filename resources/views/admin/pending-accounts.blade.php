@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Pending Account Approvals</h3>
                    <div class="card-tools">
                        <form action="{{ route('admin.pending-accounts') }}" method="GET" class="d-flex">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Search by name or email..."
                                       value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <span class="text-muted">Showing {{ $pendingAccounts->count() }} of {{ $pendingAccounts->total() }} pending accounts</span>
                        </div>
                        <div>
                            <form action="{{ route('admin.bulk-approve') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Approve all selected accounts?')">
                                    <i class="fas fa-check"></i> Approve Selected
                                </button>
                            </form>
                            <form action="{{ route('admin.bulk-reject') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Reject all selected accounts?')">
                                    <i class="fas fa-times"></i> Reject Selected
                                </button>
                            </form>
                        </div>
                    </div>

                    @if($pendingAccounts->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No pending accounts to approve.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="select-all"></th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Contact Info</th>
                                        <th>Registration Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingAccounts as $account)
                                        <tr>
                                            <td><input type="checkbox" name="selected_accounts[]" value="{{ $account->id }}" class="account-checkbox"></td>
                                            <td>{{ $account->name }}</td>
                                            <td>{{ $account->email }}</td>
                                            <td>{{ $account->role }}</td>
                                            <td>{{ $account->contact_info }}</td>
                                            <td>{{ $account->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <form action="{{ route('admin.approve', $account) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Approve this account?')">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#rejectModal{{ $account->id }}">
                                                    <i class="fas fa-times"></i> Reject
                                                </button>

                                                <!-- Reject Modal -->
                                                <div class="modal fade" id="rejectModal{{ $account->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Reject Account</h5>
                                                                <button type="button" class="close" data-dismiss="modal">
                                                                    <span>&times;</span>
                                                                </button>
                                                            </div>
                                                            <form action="{{ route('admin.reject', $account) }}" method="POST">
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
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $pendingAccounts->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all checkbox functionality
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.account-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Add row highlighting on hover
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => {
        row.addEventListener('mouseover', () => {
            row.style.backgroundColor = '#f8f9fa';
        });
        row.addEventListener('mouseout', () => {
            row.style.backgroundColor = '';
        });
    });
});
</script>
@endpush
@endsection
