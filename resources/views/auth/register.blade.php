@extends('layouts.app')

@section('content')
<style>
    body {
        background: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1500&q=80') no-repeat center center fixed;
        background-size: cover;
        min-height: 100vh;
        position: relative;
    }
    .dark-overlay {
        position: fixed;
        top: 0; left: 0; width: 100vw; height: 100vh;
        background: rgba(30, 20, 10, 0.5);
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
        background: rgba(255,255,255,0.85);
        border-radius: 24px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1.5px solid rgba(111, 78, 55, 0.12);
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
        box-shadow: 0 2px 12px rgba(0,0,0,0.18);
        background: #fff;
        object-fit: cover;
        border: 3px solid #6f4e37;
    }
    .coffee-title {
        color: #3e2723;
        font-weight: 800;
        text-align: center;
        font-size: 2rem;
        margin-bottom: 0.25rem;
        letter-spacing: 1px;
    }
    .coffee-tagline {
        color: #6f4e37;
        font-size: 1.1rem;
        text-align: center;
        margin-bottom: 2rem;
        font-weight: 500;
    }
    .form-label, .form-check-label {
        color: #3e2723;
        font-weight: 500;
    }
    .form-control, .form-select {
        background: rgba(255,255,255,0.95);
        border: 1px solid #a67c52;
        color: #3e2723;
        border-radius: 10px;
        font-size: 1rem;
    }
    .form-control:focus, .form-select:focus {
        background: #fff;
        color: #3e2723;
        border-color: #6f4e37;
        box-shadow: 0 0 0 2px #a67c5233;
    }
    .form-select option {
        color: #3e2723;
        background: #fff;
    }
    .btn-primary {
        background: linear-gradient(90deg, #6f4e37 0%, #a67c52 100%);
        border: none;
        font-weight: 700;
        font-size: 1.1rem;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(111, 78, 55, 0.18);
        transition: background 0.2s;
        color: #fff;
    }
    .btn-primary:hover {
        background: linear-gradient(90deg, #a67c52 0%, #6f4e37 100%);
        color: #fff;
    }
    .btn-link, a {
        color: #6f4e37;
    }
    .btn-link:hover, a:hover {
        color: #3e2723;
        text-decoration: underline;
    }
</style>
<div class="dark-overlay"></div>
<div class="auth-wrapper">
    <div class="glass-card">
        <img src="/images/coffee.png" alt="Coffee SCMS Logo" class="coffee-logo">
        <div class="coffee-title">Create Your Coffee SCMS Account</div>
        <div class="coffee-tagline">Join the Coffee Supply Chain Revolution.</div>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="mb-4">
                <label for="name" class="form-label">Full Name</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0"><i class="fas fa-user text-muted"></i></span>
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Enter your full name">
                </div>
                @error('name')
                    <span class="invalid-feedback d-block mt-1"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="mb-4">
                <label for="email" class="form-label">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0"><i class="fas fa-envelope text-muted"></i></span>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Enter your email">
                </div>
                @error('email')
                    <span class="invalid-feedback d-block mt-1"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select id="role" class="form-select @error('role') is-invalid @enderror" name="role" required>
                    <option value="customer">Customer</option>
                    <option value="supplier">Supplier</option>
                    <option value="factory">Factory</option>
                    <option value="retailer">Retailer</option>
                    <option value="wholesaler">Wholesaler</option>
                    <option value="workforce_manager">Workforce Manager</option>
                </select>
                @error('role')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0"><i class="fas fa-lock text-muted"></i></span>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Create a password">
                </div>
                @error('password')
                    <span class="invalid-feedback d-block mt-1"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="mb-4">
                <label for="password-confirm" class="form-label">Confirm Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0"><i class="fas fa-lock text-muted"></i></span>
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your password">
                </div>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-user-plus me-2"></i> Create Account
                </button>
            </div>
            <div class="text-center mt-4">
                <p class="mb-0">Already have an account?
                    <a href="{{ route('login') }}" class="fw-bold">Login Here</a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
