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
        background: rgba(30, 20, 10, 0.7);
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
        background: rgba(34, 34, 34, 0.65);
        border-radius: 24px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1.5px solid rgba(255, 255, 255, 0.18);
        padding: 2.5rem 2.5rem 2rem 2.5rem;
        max-width: 400px;
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
        color: #fff;
        font-weight: 800;
        text-align: center;
        font-size: 2rem;
        margin-bottom: 0.25rem;
        letter-spacing: 1px;
    }
    .coffee-tagline {
        color: #c0a98e;
        font-size: 1.1rem;
        text-align: center;
        margin-bottom: 2rem;
        font-weight: 500;
    }
    .form-label, .form-check-label {
        color: #e0d6ce;
    }
    .form-control {
        background: rgba(255,255,255,0.08);
        border: 1px solid #6f4e37;
        color: #fff;
        border-radius: 10px;
    }
    .form-control:focus {
        background: rgba(255,255,255,0.15);
        color: #fff;
        border-color: #c0a98e;
        box-shadow: 0 0 0 2px #6f4e3733;
    }
    .btn-primary {
        background: linear-gradient(90deg, #6f4e37 0%, #a67c52 100%);
        border: none;
        font-weight: 700;
        font-size: 1.1rem;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(111, 78, 55, 0.18);
        transition: background 0.2s;
    }
    .btn-primary:hover {
        background: linear-gradient(90deg, #a67c52 0%, #6f4e37 100%);
    }
    .btn-link, a {
        color: #c0a98e;
    }
    .btn-link:hover, a:hover {
        color: #fff;
        text-decoration: underline;
    }
</style>
<div class="dark-overlay"></div>
<div class="auth-wrapper">
    <div class="glass-card">
        <img src="/images/coffee.png" alt="Coffee SCMS Logo" class="coffee-logo">
        <div class="coffee-title">Welcome to Coffee SCMS</div>
        <div class="coffee-tagline">From Bean to Cup, Managed with Care</div>
        <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate>
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                </div>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                </div>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="mb-3 form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">Remember Me</label>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </button>
            </div>
            <div class="text-center mt-3">
                @if (Route::has('password.request'))
                    <a class="btn btn-link" href="{{ route('password.request') }}">Forgot Your Password?</a>
                @endif
            </div>
        </form>
        <div class="text-center mt-3">
            <p class="mb-0">Don't have an account?
                <a href="{{ route('register') }}" class="fw-bold">Register here</a>
            </p>
        </div>
    </div>
</div>
@endsection
