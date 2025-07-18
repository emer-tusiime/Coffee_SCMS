@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h2 mb-4">Production Lines</h1>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Quality Issues</th>
                            <th>Maintenance Tasks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productionLines as $line)
                        <tr>
                            <td>{{ $line->name ?? 'N/A' }}</td>
                            <td>{{ $line->status ?? 'N/A' }}</td>
                            <td>{{ $line->qualityIssues->count() ?? 0 }}</td>
                            <td>{{ $line->maintenanceTasks->count() ?? 0 }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">No production lines found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 