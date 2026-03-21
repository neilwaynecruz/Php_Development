<nav class="navbar navbar-expand-lg mb-3">
  <div class="container-fluid">

    {{-- Left: Logo + Brand --}}
    <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('products.index') }}">
      {{-- If you have a real logo image, replace this span with an <img>. --}}
      {{-- Example: <img src="{{ asset('assets/img/prism-logo.png') }}" alt="PRISM" class="prism-logo"> --}}
      <span class="prism-logo fw-bold text-white">P</span>
      <span class="brand-text">PRISM</span>
    </a>

    {{-- Right: User info + actions --}}
    <div class="d-flex align-items-center gap-2 ms-auto">

      {{-- Hello, user + role badge --}}
      <span class="text-white small">
        Hello, {{ (string) session('user', 'guest') }}
        @if (session('role') === 'admin')
          <span class="badge bg-warning text-dark ms-1">admin</span>
        @else
          <span class="badge bg-warning text-dark ms-1">user</span>
        @endif
      </span>

      {{-- ADMIN-ONLY: Low Stock button with real-time badge --}}
      @if (session('role') === 'admin')
        @php
            $lowStockCount = app(\App\Services\LowStockService::class)->count();
        @endphp
        <a href="{{ route('products.index', ['view' => 'active']) }}"
           class="btn btn-outline-light btn-sm position-relative">
          Low Stock
          <span data-role="low-stock-count"
                class="badge bg-danger position-absolute top-0 start-100 translate-middle {{ $lowStockCount ? '' : 'd-none' }}">
            {{ $lowStockCount ?: 0 }}
          </span>
        </a>
      @endif

      {{-- My Requests --}}
      <a href="{{ route('requisitions.my.index') }}" class="btn btn-outline-light btn-sm">
        My Requests
      </a>

      {{-- Account --}}
      <a href="{{ route('account.form') }}" class="btn btn-outline-light btn-sm">
        Account
      </a>

      {{-- Logout --}}
      <a href="{{ route('app.logout') }}" class="btn btn-outline-light btn-sm">
        Logout
      </a>
    </div>
  </div>
</nav>