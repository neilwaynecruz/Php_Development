@extends('layouts.app')

@section('title', 'Reset Password | PRISM')

@section('content')
  @if (session('message')) {!! session('message') !!} @endif

  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="mb-3">Reset Password</h5>

          <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email"
                     name="email"
                     class="form-control @error('email') is-invalid @enderror"
                     value="{{ request('email') ?? old('email') }}"
                     required>
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">New Password</label>
              <input type="password"
                     name="password"
                     class="form-control @error('password') is-invalid @enderror"
                     required>
              @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Confirm Password</label>
              <input type="password"
                     name="password_confirmation"
                     class="form-control"
                     required>
            </div>

            <button class="btn btn-primary w-100">Reset Password</button>
          </form>

          <div class="mt-3 text-center">
            <a href="{{ route('login.form') }}" class="small">Back to login</a>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection