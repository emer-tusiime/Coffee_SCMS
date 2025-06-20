@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Workforce Management</h2>
                        <a href="{{ route('factory.workforce.create') }}" class="btn btn-primary">Add New Member</a>
                    </div>

                    <div class="row">
                        @foreach($workforce as $locationId => $members)
                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>{{ $members[0]->location->name }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Role</th>
                                                        <th>Shift</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($members as $member)
                                                        <tr>
                                                            <td>{{ $member->name }}</td>
                                                            <td>{{ $member->role }}</td>
                                                            <td>{{ $member->shift_start }} - {{ $member->shift_end }}</td>
                                                            <td>
                                                                <a href="{{ route('factory.workforce.edit', $member) }}" class="btn btn-sm btn-primary">Edit</a>
                                                                <form action="{{ route('factory.workforce.destroy', $member) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        <h3>Shift Availability</h3>
                        <form action="{{ route('factory.workforce.availability') }}" method="GET">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <select name="location_id" class="form-select mb-2">
                                        <option value="">Select Location</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <input type="date" name="date" class="form-control mb-2">
                                    <button type="submit" class="btn btn-primary">Check Availability</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection