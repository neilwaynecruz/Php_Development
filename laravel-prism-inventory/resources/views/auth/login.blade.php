@extends('layouts.app')

@section('title', 'Login | Product Inventory System')

@section('nav')
<nav class="navbar navbar-expand-lg navbar-dark margin">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="{{ route('login.form') }}">
      <img src="{{ asset('assets/img/prism-logo.png') }}" class="prism-logo" alt="Prism Logo">
      <span class="brand-text brand-accent">PRISM</span>
    </a>
  </div>
</nav>
@endsection

@section('content')
<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="card shadow-sm auth-card">
      <div class="card-body p-4">
        <h4 class="text-center mb-3"><span class="brand-text brand-accent">Inventory Login</span></h4>
        <form action="{{ route('login.post') }}" method="POST">
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
          <button type="submit" name="btnLogin" class="btn btn-primary w-100">Login</button>
        </form>
        <div class="d-flex justify-content-between mt-3">
          <small class="text-muted">Use admin / admin123</small>
          <a href="{{ route('register.form') }}" class="small">Create an account</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection