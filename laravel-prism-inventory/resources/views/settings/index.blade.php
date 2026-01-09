@extends('layouts.app')

@section('title', 'System Settings | Product Inventory System')

@section('nav')
<nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="{{ route('products.index') }}">
      <img src="{{ asset('assets/img/prism-logo.png') }}" class="prism-logo" alt="Logo">
      <span class="brand-text">
        {{ app(\App\Services\SettingsService::class)->get('brand_text', 'PRISM') }}
      </span>
    </a>
    <div class="ms-auto d-flex align-items-center">
      <a href="{{ route('products.index') }}" class="btn btn-outline-light btn-sm me-2">Dashboard</a>
      <a href="{{ route('users.index') }}" class="btn btn-outline-light btn-sm me-2">Users</a>
      <a href="{{ route('logs.index') }}" class="btn btn-outline-light btn-sm me-2">Logs</a>
      <a href="{{ route('app.logout') }}" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
  </div>
</nav>
@endsection

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title mb-3">System Settings</h5>

        @if (session('message')) {!! session('message') !!} @endif

        <form method="POST" action="{{ route('settings.save') }}" class="row g-3">
          @csrf

          <div class="col-12">
            <label class="form-label">Brand Text</label>
            <input type="text" name="brand_text" class="form-control @error('brand_text') is-invalid @enderror" value="{{ old('brand_text', $brandText) }}" maxlength="64" required>
            @error('brand_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text">Shown in the navbar next to the logo.</div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Low Stock Threshold</label>
            <input type="number" name="low_stock_threshold" class="form-control @error('low_stock_threshold') is-invalid @enderror" value="{{ old('low_stock_threshold', $lowStockThreshold) }}" min="0" step="1" required>
            @error('low_stock_threshold')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text">Rows with quantity ≤ threshold show a yellow badge.</div>
          </div>

          <div class="col-md-6 d-flex align-items-end">
            <div class="form-check">
              <input type="checkbox" name="show_total_to_user" id="show_total_to_user" class="form-check-input" {{ old('show_total_to_user', $showTotalToUser ? 'on' : '') ? 'checked' : '' }}>
              <label for="show_total_to_user" class="form-check-label">Show “Total Stock Value” to non-admin users</label>
            </div>
          </div>

          <div class="col-12">
            <button class="btn btn-primary">Save Settings</button>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Back</a>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>
@endsection