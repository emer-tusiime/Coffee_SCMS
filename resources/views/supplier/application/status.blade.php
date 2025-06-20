@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0" style="backdrop-filter: blur(8px); background: rgba(255,255,255,0.7);">
                <div class="card-body text-center">
                    <h2 class="mb-4">Vendor Application Status</h2>
                    <p class="lead">Your application status will appear here.</p>
                    @if(session('success'))
                        <div class="alert alert-success mt-3">{{ session('success') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 