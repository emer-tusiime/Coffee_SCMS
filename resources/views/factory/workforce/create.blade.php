@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h2 class="mb-4">Add New Workforce Member</h2>

                    <form action="{{ route('factory.workforce.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <input type="text" class="form-control @error('role') is-invalid @enderror" id="role" name="role" value="{{ old('role') }}" required>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="location_id" class="form-label">Location</label>
                            <select class="form-select @error('location_id') is-invalid @enderror" id="location_id" name="location_id" required>
                                <option value="">Select Location</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('location_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="production_line_id" class="form-label">Production Line (Optional)</label>
                            <select class="form-select @error('production_line_id') is-invalid @enderror" id="production_line_id" name="production_line_id">
                                <option value="">Select Production Line</option>
                                @foreach($productionLines as $line)
                                    <option value="{{ $line->id }}" {{ old('production_line_id') == $line->id ? 'selected' : '' }}>
                                        {{ $line->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('production_line_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="shift_start" class="form-label">Shift Start</label>
                                <input type="time" class="form-control @error('shift_start') is-invalid @enderror" id="shift_start" name="shift_start" value="{{ old('shift_start') }}" required>
                                @error('shift_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="shift_end" class="form-label">Shift End</label>
                                <input type="time" class="form-control @error('shift_end') is-invalid @enderror" id="shift_end" name="shift_end" value="{{ old('shift_end') }}" required>
                                @error('shift_end')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Add Workforce Member</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
