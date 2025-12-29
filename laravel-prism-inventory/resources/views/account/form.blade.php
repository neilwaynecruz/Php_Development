@extends('layouts.app')

@section('title', 'Account | Change Password')

@section('nav')
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="{{ route('products.index') }}">
      <img src="{{ asset('assets/img/prism-logo.png') }}" class="prism-logo" alt="Prism Logo">
      <span class="brand-text">PRISM</span>
    </a>
    <div class="ms-auto d-flex align-items-center">
      <span class="navbar-text me-3">
        Hello, {{ e(session('user')) }}
        <span class="badge bg-warning text-dark ms-2">{{ e(session('role', 'user')) }}</span>
      </span>
      <a href="{{ route('products.index') }}" class="btn btn-outline-light btn-sm me-2">Dashboard</a>
      <a href="{{ route('logout') }}" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
  </div>
</nav>
@endsection

@section('content')
<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-sm stat-card">
      <div class="card-body">
        <h5 class="card-title text-center">Change Password</h5>
        <form action="{{ route('account.changePassword') }}" method="POST">
          @csrf
          <!-- Current Password -->
          <div class="mb-3">
            <label class="form-label">Current Password</label>
            <div class="input-group password-toggle">
              <input type="password" name="current" class="form-control" data-toggle="password" required>
              <button type="button" class="btn btn-outline-secondary toggle-visibility" aria-label="Show password">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>
          <!-- New Password -->
          <div class="mb-3">
            <label class="form-label">New Password</label>
            <div class="input-group password-toggle">
              <input type="password" name="new" class="form-control" data-toggle="password" required>
              <button type="button" class="btn btn-outline-secondary toggle-visibility" aria-label="Show password">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>
          <!-- Confirm New Password -->
          <div class="mb-3">
            <label class="form-label">Confirm New Password</label>
            <div class="input-group password-toggle">
              <input type="password" name="confirm" class="form-control" data-toggle="password" required>
              <button type="button" class="btn btn-outline-secondary toggle-visibility" aria-label="Show password">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>
          <button type="submit" name="changePass" class="btn btn-primary w-100">Update Password</button>
        </form>
        <div class="text-muted mt-3" style="font-size: 13px;">Tip: Use at least 6 characters.</div>
      </div>
    </div>
  </div>
</div>
@endsection