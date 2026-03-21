@extends('layouts.app')

@section('title', 'Activity Logs | Product Inventory System')

@section('nav')
<nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="{{ route('products.index') }}">
      <img src="{{ asset('assets/img/prism-logo.png') }}" class="prism-logo" alt="Prism Logo">
      <span class="brand-text">PRISM</span>
    </a>
    <div class="ms-auto d-flex align-items-center">
      <a href="{{ route('products.index') }}" class="btn btn-outline-light btn-sm me-2">Dashboard</a>
      <a href="{{ route('users.index') }}" class="btn btn-outline-light btn-sm me-2">Users</a>
      <a href="{{ route('app.logout') }}" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
  </div>
</nav>
@endsection

@section('content')
  <div class="card shadow-sm card-lift mb-3 logs-card">
    <div class="card-body">
      <h5 class="card-title mb-3">Filter Logs</h5>
      <form method="POST" action="{{ route('logs.index') }}" class="row g-2">
        @csrf
        <div class="col-md-5">
          <label class="form-label">Username contains</label>
          <input type="text" name="user" class="form-control" value="{{ old('user', $filterUser) }}">
        </div>
        <div class="col-md-5">
          <label class="form-label">Action</label>
          <select name="action" class="form-select">
            <option value="">All</option>
            @php $actions = ['login','logout','create','update','delete','archive','restore']; @endphp
            @foreach ($actions as $act)
              <option value="{{ $act }}" {{ $filterAction === $act ? 'selected' : '' }}>{{ $act }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
          <button type="submit" name="filter" class="btn btn-secondary w-100">Apply</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow-sm card-lift logs-card">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="card-title mb-0">Recent Activity</h5>

  <div class="d-flex align-items-center gap-2">
    <small class="text-muted">
      Page {{ $current_page }} of {{ $total_pages }} ({{ number_format($total_records) }} total)
    </small>

    @php
      // Build export URL preserving the current filters from the controller
      // $filterUser and $filterAction are already set for the rendered view
      $logsExportUrl = route('logs.exportCsv') . '?' . http_build_query([
        'u'      => (string) ($filterUser ?? ''),
        'action' => (string) ($filterAction ?? ''),
        // Add date filters if/when you include them in your form:
        // 'from'   => (string) ($filterFrom ?? ''),
        // 'to'     => (string) ($filterTo ?? ''),
      ]);
    @endphp

    <a href="{{ $logsExportUrl }}" class="btn btn-outline-secondary btn-sm">
      Export CSV
    </a>
  </div>
</div>

      <div class="table-responsive">
        <table class="table-logs table table-hover align-middle">
          <thead>
            <tr>
              <th style="width:6%;">ID</th>
              <th style="width:18%;">User</th>
              <th style="width:12%;">Action</th>
              <th style="width:10%;">Product ID</th>
              <th style="width:36%;">Details</th>
              <th style="width:18%;">When</th>
            </tr>
          </thead>
          <tbody>
            @if (!empty($rows))
              @foreach ($rows as $row)
                @php
                  $rawAction = strtolower(trim($row->action ?? ''));
                  $map = [
                    'create'  => 'badge-action badge-create',
                    'update'  => 'badge-action badge-update',
                    'delete'  => 'badge-action badge-delete',
                    'login'   => 'badge-action badge-login',
                    'logout'  => 'badge-action badge-logout',
                    'archive' => 'badge-action badge-archive',
                    'restore' => 'badge-action badge-restore',
                  ];
                  $badgeClass = $map[$rawAction] ?? 'badge-action badge-generic';
                @endphp
                <tr>
                  <td>{{ (int) $row->log_id }}</td>
                  <td>{{ e($row->username) }}</td>
                  <td><span class="{{ $badgeClass }}">{{ ucfirst($rawAction) }}</span></td>
                  <td class="text-center">{{ $row->product_id !== null ? (int) $row->product_id : '-' }}</td>
                  <td class="details" title="{{ e($row->details) }}">{!! nl2br(e($row->details)) !!}</td>
                  <td>{{ e($row->created_at) }}</td>
                </tr>
              @endforeach
            @else
              <tr><td colspan="6" class="text-center text-muted">No logs found.</td></tr>
            @endif
          </tbody>
        </table>
      </div>

      @if ($total_pages > 1)
        <nav class="logs-pagination mt-4" aria-label="Page navigation">
          <ul class="pagination pagination-theme justify-content-center">
            <li class="page-item {{ $current_page <= 1 ? 'disabled' : '' }}">
              <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $current_page - 1]) }}" tabindex="{{ $current_page <= 1 ? '-1' : '' }}">Previous</a>
            </li>
            @for ($i = 1; $i <= $total_pages; $i++)
              <li class="page-item {{ $current_page == $i ? 'active' : '' }}">
                <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $i]) }}" aria-current="{{ $current_page == $i ? 'page' : '' }}">{{ $i }}</a>
              </li>
            @endfor
            <li class="page-item {{ $current_page >= $total_pages ? 'disabled' : '' }}">
              <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $current_page + 1]) }}" tabindex="{{ $current_page >= $total_pages ? '-1' : '' }}">Next</a>
            </li>
          </ul>
        </nav>
      @endif
    </div>
  </div>
@endsection