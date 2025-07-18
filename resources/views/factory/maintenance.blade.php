@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h2 mb-4">Maintenance Tasks</h1>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Production Line</th>
                            <th>Status</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($maintenanceTasks as $task)
                        <tr>
                            <td>{{ $task->id }}</td>
                            <td>{{ $task->productionLine->name ?? 'N/A' }}</td>
                            <td>{{ $task->status }}</td>
                            <td>{{ $task->due_date }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">No maintenance tasks found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $maintenanceTasks->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 