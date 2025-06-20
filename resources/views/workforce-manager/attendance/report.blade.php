@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Attendance Report</h5>
                    <div class="d-flex gap-3">
                        <form action="{{ route('workforce-manager.attendance.report') }}" method="GET" class="d-flex">
                            <input type="date" name="start_date" class="form-control" required>
                            <input type="date" name="end_date" class="form-control" required>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i> Filter
                            </button>
                        </form>
                        <a href="{{ route('workforce-manager.attendance.export', request()->only('start_date', 'end_date')) }}" class="btn btn-success">
                            <i class="fas fa-file-csv me-1"></i> Export CSV
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Employee ID</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendance as $employeeId => $records)
                                    @foreach($records as $record)
                                        <tr>
                                            <td>{{ $record->employee->name }}</td>
                                            <td>{{ $record->employee->id }}</td>
                                            <td>{{ $record->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $record->status === 'present' ? 'success' : ($record->status === 'absent' ? 'danger' : ($record->status === 'late' ? 'warning' : 'info')) }}">
                                                    {{ $record->status_label }}
                                                </span>
                                            </td>
                                            <td>{{ $record->notes }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
