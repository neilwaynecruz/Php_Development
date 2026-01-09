@extends('layouts.app')

@section('title', 'Create Account')

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
            Create Account
          </span>
        </h3>

        <form method="POST" action="{{ route('register.post') }}">
          @csrf

          {{-- Username --}}
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

          {{-- Email --}}
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email"
                   name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}"
                   required
                   placeholder="Enter email address">
            @error('email')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Password --}}
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

          {{-- Confirm Password --}}
          <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <div class="input-group password-toggle">
              <input type="password"
                     name="password_confirmation"
                     class="form-control"
                     data-toggle="password"
                     required
                     placeholder="Confirm password">
              <button type="button" class="btn btn-outline-secondary toggle-visibility" aria-label="Show password">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>

          <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>

        <div class="mt-3 text-center small">
          Already have an account? <a href="{{ route('login.form') }}">Login here.</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection