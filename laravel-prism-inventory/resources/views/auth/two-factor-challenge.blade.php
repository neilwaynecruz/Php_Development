@extends('layouts.app')

@section('title', 'Two-Factor Challenge | PRISM')

@section('content')
@if (session('message')) {!! session('message') !!} @endif
@if (session('status'))
  <div class="alert alert-info">{{ session('status') }}</div>
@endif

<div class="row justify-content-center">
  <div class="col-md-6 col-lg-5">
    <div class="card shadow-sm requisition-card">
      <div class="card-body">
        <h5 class="mb-3 text-center">Two-Factor Authentication</h5>
        <p class="text-muted small">
          Enter the 6-digit code from your authenticator app,
          or one of your recovery codes if you don’t have access to the app.
        </p>

        <form method="POST" action="{{ url('/two-factor-challenge') }}">
          @csrf

          {{-- Authenticator code --}}
          <div class="mb-3">
            <label class="form-label">Authentication Code</label>
            <input type="text"
                   name="code"
                   inputmode="numeric"
                   autocomplete="one-time-code"
                   class="form-control @error('code') is-invalid @enderror"
                   placeholder="123456">
            @error('code')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="text-center my-2 text-muted small">— or —</div>

          {{-- Recovery code --}}
          <div class="mb-3">
            <label class="form-label">Recovery Code</label>
            <input type="text"
                   name="recovery_code"
                   class="form-control @error('recovery_code') is-invalid @enderror"
                   placeholder="one-of-your-recovery-codes">
            @error('recovery_code')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <button class="btn btn-primary w-100">Verify</button>
        </form>

        <div class="mt-3 text-center">
          <a href="{{ route('login.form') }}" class="small">Back to login</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection