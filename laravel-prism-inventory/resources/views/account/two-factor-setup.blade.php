@extends('layouts.app')

@section('title', 'Two-Factor Setup | PRISM')

@section('nav')
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="{{ route('products.index') }}">
      <img src="{{ asset('assets/img/prism-logo.png') }}" class="prism-logo" alt="Prism Logo">
      <span class="brand-text">PRISM</span>
    </a>
    <div class="ms-auto d-flex align-items-center gap-2">
      <a href="{{ route('account.form') }}" class="btn btn-outline-light btn-sm">Back to Account</a>
    </div>
  </div>
</nav>
@endsection

@section('content')
<div class="row justify-content-center">
  <div class="col-md-6 col-lg-5">
    <div class="card shadow-sm requisition-card">
      <div class="card-body">
        <h5 class="mb-3">Two-Factor Authentication Setup</h5>

        @if (! $qrSvg)
          <div class="alert alert-danger">
            Could not load QR code. Please try again, or disable and re-enable 2FA.
          </div>
        @else
          <p class="text-muted small">
            Scan this QR code with your authenticator app (Google Authenticator, Authy, etc.),
            then save the recovery codes in a safe place.
          </p>

          <div class="text-center mb-3">
            {!! $qrSvg !!}
          </div>
        @endif

        <h6>Recovery Codes</h6>
        @if (!empty($recoveryCodes))
          <ul class="list-unstyled small bg-dark p-2 rounded-3">
            @foreach ($recoveryCodes as $code)
              <li class="mb-1">{{ $code }}</li>
            @endforeach
          </ul>
          <p class="text-muted small">
            Each code can be used once if you lose access to your authenticator app.
          </p>
        @else
          <p class="text-muted small">
            No recovery codes available. Click "Regenerate Recovery Codes" on the Account page, then reload this page.
          </p>
        @endif

        <div class="mt-3 text-end">
          <a href="{{ route('account.form') }}" class="btn btn-outline-light btn-sm">Done</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection