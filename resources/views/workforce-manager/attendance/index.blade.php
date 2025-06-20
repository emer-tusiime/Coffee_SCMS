@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Employee Attendance</h5>
                    <div class="d-flex gap-3">
                        <a href="{{ route('workforce-manager.attendance.report') }}" class="btn btn-primary">
                            <i class="fas fa-file-alt me-1"></i> Attendance Report
                        </a>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#markAttendanceModal">
                            <i class="fas fa-plus me-1"></i> Mark Attendance
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendance as $record)
                                    <tr>
                                        <td>{{ $record->employee->name }}</td>
                                        <td>{{ $record->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $record->status === 'present' ? 'success' : ($record->status === 'absent' ? 'danger' : ($record->status === 'late' ? 'warning' : 'info')) }}">
                                                {{ $record->status_label }}
                                            </span>
                                        </td>
                                        <td>{{ $record->notes }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mark Attendance Modal -->
    <div class="modal fade" id="markAttendanceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mark Employee Attendance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('workforce-manager.attendance.mark') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Employee</label>
                            <select name="employee_id" class="form-select">
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="present">Present</option>
                                <option value="absent">Absent</option>
                                <option value="late">Late</option>
                                <option value="leave">On Leave</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Mark Attendance</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
