@extends('layouts.app')

@section('title', 'Users | Product Inventory System')

@section('nav')
<nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="{{ route('products.index') }}">
      <img src="{{ asset('assets/img/prism-logo.png') }}" class="prism-logo" alt="Prism Logo">
      <span class="brand-text">PRISM</span>
    </a>
    <div class="ms-auto d-flex align-items-center">
      <a href="{{ route('products.index') }}" class="btn btn-outline-light btn-sm me-2">Dashboard</a>
      <a href="{{ route('logs.index') }}" class="btn btn-outline-light btn-sm me-2">Logs</a>
      <a href="{{ route('app.logout') }}" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
  </div>
</nav>
@endsection

@section('content')
<div class="row g-4">
  <!-- Add User Form -->
  <div class="col-md-5">
    <div class="card shadow-sm card-lift">
      <div class="card-body">
        <h5 class="card-title mb-3">Add User</h5>
        <form action="{{ route('users.create') }}" method="POST">
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
            <label class="form-label">Role</label>
            <select name="role" class="form-select" required>
              <option value="user">User</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <button type="submit" name="create" class="btn btn-primary w-100">Create User</button>
        </form>
      </div>
    </div>
  </div>

  <!-- Users Table -->
  <div class="col-md-7">
    <div class="card shadow-sm card-lift users-card">
      <div class="card-body">
        <h5 class="card-title mb-3">User Accounts</h5>
        <div class="table-responsive">
          <table class="table table-hover align-middle users-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Password</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @if ($users->count())
                @foreach ($users as $row)
                  @php
                    $uid = (int) $row->uid;
                    $uname = $row->username;
                    $urole = $row->role;
                  @endphp
                  <tr>
                    <td>{{ $uid }}</td>
                    <td>{{ e($uname) }}</td>
                    <td>
                      <!-- Change role form -->
                      <form action="{{ route('users.changeRole') }}" method="POST" class="d-flex gap-2 align-items-center role-form">
                        @csrf
                        <input type="hidden" name="uid" value="{{ $uid }}">
                        <select name="role" class="form-select form-select-sm">
                          <option value="user" {{ $urole==='user' ? 'selected' : '' }}>User</option>
                          <option value="admin" {{ $urole==='admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        <button type="submit" name="changeRole" class="btn btn-sm btn-success px-3">Save</button>
                      </form>
                    </td>
                    <td>
                      <!-- Change password form -->
                      <form id="passForm-{{ $uid }}" action="{{ route('users.changePass') }}" method="POST" class="password-wrap password-toggle d-flex align-items-center gap-2">
                        @csrf
                        <input type="hidden" name="uid" value="{{ $uid }}">
                        <input type="password" name="new" class="form-control form-control-sm" placeholder="New password" data-toggle="password">
                        <button type="button" class="btn btn-outline-secondary btn-sm toggle-visibility" aria-label="Show password">
                          <i class="fa-solid fa-eye"></i>
                        </button>
                      </form>
                    </td>
                    <td class="text-end">
                      <div class="action-stack">
                        <!-- Submit password form -->
                        <button type="submit" form="passForm-{{ $uid }}" name="changePass" value="1" class="btn btn-sm btn-update-blue">Update</button>

                        <!-- Delete user -->
                        <form action="{{ route('users.delete') }}" method="POST" onsubmit="return confirm('Delete user {{ e($uname) }}?');" class="d-inline">
                          @csrf
                          <input type="hidden" name="uid" value="{{ $uid }}">
                          <button type="submit" name="delete" class="btn btn-sm btn-danger w-100">Delete</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @endforeach
              @else
                <tr><td colspan="5" class="text-center text-muted">No users found.</td></tr>
              @endif
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection