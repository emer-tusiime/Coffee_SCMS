@extends('layouts.dashboard')

@section('title', 'Settings - Coffee SCMS')

@section('dashboard-title')
    <i class="fas fa-cog me-2"></i>System Settings
@endsection

@section('dashboard-content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- General Settings -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-sliders-h me-2"></i>General Settings
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="company_name" class="form-label">Company Name</label>
                            <input type="text" class="form-control" id="company_name" name="company_name"
                                   value="{{ old('company_name', config('app.name')) }}">
                        </div>

                        <div class="mb-3">
                            <label for="timezone" class="form-label">Timezone</label>
                            <select class="form-select" id="timezone" name="timezone">
                                @foreach(timezone_identifiers_list() as $timezone)
                                    <option value="{{ $timezone }}"
                                            {{ config('app.timezone') == $timezone ? 'selected' : '' }}>
                                        {{ $timezone }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="date_format" class="form-label">Date Format</label>
                            <select class="form-select" id="date_format" name="date_format">
                                <option value="Y-m-d">YYYY-MM-DD</option>
                                <option value="d/m/Y">DD/MM/YYYY</option>
                                <option value="m/d/Y">MM/DD/YYYY</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Email Settings -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-envelope me-2"></i>Email Settings
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.email') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="mail_from_address" class="form-label">From Address</label>
                            <input type="email" class="form-control" id="mail_from_address"
                                   name="mail_from_address" value="{{ config('mail.from.address') }}">
                        </div>

                        <div class="mb-3">
                            <label for="mail_from_name" class="form-label">From Name</label>
                            <input type="text" class="form-control" id="mail_from_name"
                                   name="mail_from_name" value="{{ config('mail.from.name') }}">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save Email Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- System Information -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle me-2"></i>System Information
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>PHP Version:</strong> {{ PHP_VERSION }}</p>
                            <!-- <p><strong>Laravel Version:</strong> {{ app()->version() }}</p> -->
                        </div>
                        <div class="col-md-4">
                            <p><strong>Server:</strong> {{ $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' }}</p>
                            <p><strong>Database:</strong> {{ config('database.default') }}</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Environment:</strong> {{ config('app.env') }}</p>
                            <p><strong>Debug Mode:</strong> {{ config('app.debug') ? 'Enabled' : 'Disabled' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
