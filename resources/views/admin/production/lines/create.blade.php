@extends('layouts.dashboard')

@section('title', 'Create Production Line - Coffee SCMS')

@section('dashboard-title')
    <i class="fas fa-industry me-2"></i>Create Production Line
@endsection

@section('dashboard-actions')
    <a href="{{ route('admin.production.lines.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Production Lines
    </a>
@endsection

@section('dashboard-content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.production.lines.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Production Line Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="capacity" class="form-label">Daily Capacity (units/day)</label>
                    <input type="number" class="form-control @error('capacity') is-invalid @enderror"
                           id="capacity" name="capacity" value="{{ old('capacity') }}" required min="0">
                    @error('capacity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select @error('status') is-invalid @enderror"
                            id="status" name="status" required>
                        <option value="">Select Status</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Create Production Line
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
