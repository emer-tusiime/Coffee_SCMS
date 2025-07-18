@extends('layouts.app')

@section('styles')
<link href="/css/auth.css" rel="stylesheet">
@endsection

@section('content')
<style>
    body {
        min-height: 100vh;
        background: url('/images/coffeeBG.jpg') no-repeat center center fixed;
        background-size: cover;
        position: relative;
    }
    .dark-overlay {
        position: fixed;
        top: 0; left: 0; width: 100vw; height: 100vh;
        background: rgba(255, 255, 255, 0.25);
        z-index: 0;
    }
    .auth-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 1;
    }
    .glass-card {
        background: rgba(255, 255, 255, 0.85);
        border-radius: 24px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.10);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1.5px solid rgba(111, 78, 55, 0.10);
        padding: 2.5rem 2.5rem 2rem 2.5rem;
        max-width: 430px;
        margin: auto;
        position: relative;
        z-index: 2;
        animation: fadeInUp 1s cubic-bezier(.39,.575,.565,1) both;
    }
    @keyframes fadeInUp {
        0% { opacity: 0; transform: translateY(40px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .coffee-logo {
        display: block;
        margin: 0 auto 1.5rem auto;
        width: 90px;
        height: 90px;
        border-radius: 50%;
        box-shadow: 0 2px 12px rgba(0,0,0,0.10);
        background: #fff;
        object-fit: cover;
        border: 3px solid #6f4e37;
    }
    .coffee-title {
        color: #6f4e37;
        font-weight: 800;
        text-align: center;
        font-size: 2rem;
        margin-bottom: 0.25rem;
        letter-spacing: 1px;
    }
    .coffee-tagline {
        color: #a67c52;
        font-size: 1.1rem;
        text-align: center;
        margin-bottom: 2rem;
        font-weight: 500;
    }
    .form-label, .form-check-label {
        color: #6f4e37;
    }
    .form-control, .form-select {
        background: rgba(255,255,255,0.95);
        border: 1px solid #c0a98e;
        color: #6f4e37;
        border-radius: 10px;
        font-size: 1rem;
    }
    .form-control:focus, .form-select:focus {
        background: #fff;
        color: #6f4e37;
        border-color: #a67c52;
        box-shadow: 0 0 0 2px #a67c5233;
    }
    .form-select option {
        color: #6f4e37;
        background: #fff;
    }
    .btn-primary {
        background: linear-gradient(90deg, #a67c52 0%, #e6ccb2 100%);
        border: none;
        font-weight: 700;
        font-size: 1.1rem;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(111, 78, 55, 0.10);
        transition: background 0.2s;
        color: #fff;
    }
    .btn-primary:hover {
        background: linear-gradient(90deg, #e6ccb2 0%, #a67c52 100%);
        color: #6f4e37;
    }
    .btn-link, a {
        color: #a67c52;
    }
</style>

<div class="auth-wrapper">
    <div class="glass-card">
        <img src="/images/coffee.png" alt="Coffee SCMS Logo" class="coffee-logo">
        <div class="coffee-title">Create Your Coffee SCMS Account</div>
        <div class="coffee-tagline">Join the Coffee Supply Chain Revolution.</div>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="mb-4">
                <label for="name" class="form-label">Full Name</label>
                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Enter name">

                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="form-label">Email Address</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="ccc@gmail.com">

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="contact_info" class="form-label">Contact Information</label>
                <input id="contact_info" type="text" class="form-control @error('contact_info') is-invalid @enderror" name="contact_info" value="{{ old('contact_info') }}" required autocomplete="contact_info" placeholder="Enter contact info">
                <div class="form-text">Please provide your contact information (phone number or business email).</div>

                @error('contact_info')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select id="role" class="form-select @error('role') is-invalid @enderror" name="role" required>
                    <option value="" disabled selected>Select role</option>
                    <option value="supplier">Supplier</option>
                    <option value="factory">Factory</option>
                    <option value="retailer">Retailer</option>
                    <option value="wholesaler">Wholesaler</option>
                </select>

                @error('role')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Enter password">

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password-confirm" class="form-label">Confirm Password</label>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm password">
            </div>

            <div class="mb-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-user-plus me-2"></i> Create Account
                </button>
            </div>

            <div class="text-center">
                <p class="mb-0">Already have an account?
                    <a href="{{ route('login') }}" class="fw-bold">Login Here</a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
