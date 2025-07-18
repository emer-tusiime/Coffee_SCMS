@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Registration Successful</div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <h4>Thank you for registering!</h4>
                        <p>Your account is currently pending admin approval.</p>
                        <p>You will receive an email notification once your account has been approved.</p>
                        <p>In the meantime, please wait for our administrators to review your application.</p>
                        <p>Thank you for your patience.</p>
                    </div>
                    <div class="text-center">
                        <a href="{{ route('login') }}" class="btn btn-primary">Go to Login Page</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
