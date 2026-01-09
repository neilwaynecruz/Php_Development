@extends('layouts.app')

@section('title', 'Inventory Login')

@section('nav')
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="#">
      <img src="{{ asset('assets/img/prism-logo.png') }}" class="prism-logo" alt="Prism Logo">
      <span class="brand-text">PRISM</span>
    </a>
  </div>
</nav>
@endsection

@section('content')
@if (session('message')) {!! session('message') !!} @endif

<div class="row justify-content-center">
  <div class="col-md-6 col-lg-5">
    <div class="card shadow-sm stat-card">
      <div class="card-body">
        <h3 class="text-center mb-4" style="font-weight: 700; font-size: 28px;">
          <span style="background: linear-gradient(90deg,#ff4b8b,#7c5cff); -webkit-background-clip:text; color:transparent;">
            Inventory Login
          </span>
        </h3>

        <form method="POST" action="{{ route('login.post') }}">
          @csrf

          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text"
                   name="username"
                   class="form-control @error('username') is-invalid @enderror"
                   value="{{ old('username') }}"
                   required
                   autofocus
                   placeholder="Enter username">
            @error('username')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group password-toggle">
              <input type="password"
                     name="password"
                     class="form-control @error('password') is-invalid @enderror"
                     data-toggle="password"
                     required
                     placeholder="Enter password">
              <button type="button" class="btn btn-outline-secondary toggle-visibility" aria-label="Show password">
                <i class="fa-solid fa-eye"></i>
              </button>
              @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="mb-3 form-check">
            <input type="checkbox" name="remember" class="form-check-input" id="rememberCheck">
            <label class="form-check-label small" for="rememberCheck">Remember me</label>
          </div>

          <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        {{-- Hint row --}}
        <div class="mt-3 small text-muted d-flex justify-content-between">
          <span>Use admin / admin123</span>
          <span>&nbsp;</span>
        </div>

        {{-- Links row: Create account (left) | Forgot password? (right) --}}
        <div class="mt-1 d-flex justify-content-between align-items-center small">
          <a href="{{ route('register.form') }}">Create an account</a>
          <a href="{{ route('password.request') }}">Forgot password?</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection