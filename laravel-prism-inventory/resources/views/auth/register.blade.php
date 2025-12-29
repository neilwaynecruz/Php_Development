@extends('layouts.app')

@section('title', 'Register | Product Inventory System')

@section('nav')
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="{{ route('login.form') }}">
      <img src="{{ asset('assets/img/prism-logo.png') }}" class="prism-logo" alt="Prism Logo">
      <span class="brand-text">PRISM</span>
    </a>
  </div>
</nav>
@endsection

@section('content')
<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="card shadow-sm auth-card">
      <div class="card-body p-4">
        <h4 class="text-center mb-3 brand-text brand-accent">Create Account</h4>
        <form action="{{ route('register.post') }}" method="POST">
          @csrf
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" placeholder="Enter username" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group password-toggle">
              <input type="password" name="password" class="form-control" placeholder="Enter password" data-toggle="password" required>
              <button type="button" class="btn btn-outline-secondary toggle-visibility" aria-label="Show password">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <div class="input-group password-toggle">
              <input type="password" name="confirm" class="form-control" placeholder="Confirm password" data-toggle="password" required>
              <button type="button" class="btn btn-outline-secondary toggle-visibility" aria-label="Show password">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>
          <button type="submit" name="btnRegister" class="btn btn-primary w-100">Register</button>
        </form>
        <p class="text-center mt-3 text-muted" style="font-size: 13px;">
          Already have an account? <a href="{{ route('login.form') }}">Login here</a>.
        </p>
      </div>
    </div>
  </div>
</div>
@endsection