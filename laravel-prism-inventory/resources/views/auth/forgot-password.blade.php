@extends('layouts.app')

@section('title', 'Forgot Password | PRISM')

@section('content')
  @if (session('message')) {!! session('message') !!} @endif

  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="mb-3">Forgot Password</h5>
          <p class="text-muted small">
            Enter your username or email. If we find a matching account with an email address,
            we will send a password reset link.
          </p>

          <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-3">
              <label class="form-label">Username or Email</label>
              <input type="text"
                     name="identifier"
                     class="form-control @error('identifier') is-invalid @enderror"
                     value="{{ old('identifier') }}"
                     required
                     autofocus>
              @error('identifier')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <button class="btn btn-primary w-100">Send Reset Link</button>
          </form>

          <div class="mt-3 text-center">
            <a href="{{ route('login.form') }}" class="small">Back to login</a>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection