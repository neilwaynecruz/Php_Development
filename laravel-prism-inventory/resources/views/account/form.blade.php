@extends('layouts.app')

@section('title', 'Account | Change Password')

@section('nav')
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="{{ route('products.index') }}">
      <img src="{{ asset('assets/img/prism-logo.png') }}" class="prism-logo" alt="Prism Logo">
      <span class="brand-text">PRISM</span>
    </a>
    <div class="ms-auto d-flex align-items-center gap-2">
      <span class="navbar-text">
        Hello, {{ e(session('user')) }}
        <span class="badge bg-warning text-dark ms-2">{{ e(session('role', 'user')) }}</span>
      </span>
      <a href="{{ route('products.index') }}" class="btn btn-outline-light btn-sm">Dashboard</a>
      <a href="{{ route('logout') }}" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
  </div>
</nav>
@endsection

@section('content')
<div class="row justify-content-center">
  <div class="col-md-6 col-lg-5">

    <div class="text-center text-muted small mb-2">
      Role: <strong>{{ e(session('role', 'none')) }}</strong> |
      Auth: <strong>{{ auth()->check() ? 'logged-in' : 'guest' }}</strong>
    </div>

    @if (session('message')) {!! session('message') !!} @endif
    @if (session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if (session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Change Password card --}}
    <div class="card shadow-sm stat-card mb-4">
      <div class="card-body">
        <h5 class="card-title text-center">Change Password</h5>
        <form action="{{ route('account.changePassword') }}" method="POST">
          @csrf

          <div class="mb-3">
            <label class="form-label">Current Password</label>
            <div class="input-group password-toggle">
              <input type="password" name="current" class="form-control" data-toggle="password" required>
              <button type="button" class="btn btn-outline-secondary toggle-visibility" aria-label="Show password">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">New Password</label>
            <div class="input-group password-toggle">
              <input type="password" name="new" class="form-control" data-toggle="password" required>
              <button type="button" class="btn btn-outline-secondary toggle-visibility" aria-label="Show password">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>

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

    {{-- Two-Factor Authentication card (ALL users) --}}
    @if (auth()->check())
      @php
        /** @var \App\Models\User $currentUser */
        $currentUser = auth()->user();
        $has2fa = $currentUser->hasTwoFactorEnabled();
      @endphp

      <div class="card shadow-sm requisition-card">
        <div class="card-body">
          <h5 class="mb-1">Two-Factor Authentication ({{ e(session('role', 'user')) }})</h5>

          <div class="mb-2">
            <span class="badge {{ $has2fa ? 'bg-success' : 'bg-secondary' }}">
              {{ $has2fa ? 'Enabled' : 'Disabled' }}
            </span>
          </div>

          <p class="text-muted small mb-3">
            Increase your account security by enabling two-factor authentication.
          </p>

          <div class="d-flex flex-column gap-2">

            {{-- Enable 2FA --}}
            <form method="POST" action="{{ route('two-factor.enable') }}">
              @csrf
              <button type="submit" class="btn btn-primary btn-sm w-100" {{ $has2fa ? 'disabled' : '' }}>
                Enable 2FA
              </button>
            </form>

            {{-- Disable 2FA --}}
            <form method="POST" action="{{ route('two-factor.disable') }}">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-outline-danger btn-sm w-100" {{ $has2fa ? '' : 'disabled' }}>
                Disable 2FA
              </button>
            </form>

            {{-- Regenerate recovery codes --}}
            <form method="POST" action="{{ route('two-factor.recovery-codes') }}">
              @csrf
              <button type="submit" class="btn btn-outline-secondary btn-sm w-100" {{ $has2fa ? '' : 'disabled' }}>
                Regenerate Recovery Codes
              </button>
            </form>

            {{-- View QR & Recovery Codes --}}
            <a href="{{ route('account.twoFactorSetup') }}"
               class="btn btn-outline-secondary btn-sm w-100 {{ $has2fa ? '' : 'disabled' }}"
               {{ $has2fa ? '' : 'aria-disabled=true' }}>
              View QR &amp; Recovery Codes
            </a>
          </div>

          <div class="text-muted small mt-2">
            After enabling, you’ll be asked for a 2FA code when logging in.
          </div>
        </div>
      </div>
    @endif

  </div>
</div>
@endsection