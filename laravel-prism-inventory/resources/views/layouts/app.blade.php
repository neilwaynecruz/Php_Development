<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>@yield('title', 'PRISM')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Bootstrap, Font Awesome, Google Fonts, Theme CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="{{ asset('assets/css/theme.css') }}?v=1" rel="stylesheet">
</head>
<body>
  {{-- Global navbar (PRISM header) --}}
  @yield('nav')

  <div class="container py-4">
    @if (session('message')) {!! session('message') !!} @endif
    @yield('content')
  </div>

  <!-- Toast container for real-time low-stock alerts -->
  <div id="lowStockToastContainer"
       style="position:fixed; top:80px; right:16px; z-index:1050; max-width:320px;">
  </div>

  <!-- Floating Theme Toggle Button -->
  <button id="themeToggle" class="theme-fab" type="button" aria-label="Toggle color theme" title="Toggle theme">
    <span class="icon-sun" aria-hidden="true"><i class="fa-solid fa-sun"></i></span>
    <span class="icon-moon" aria-hidden="true"><i class="fa-solid fa-moon"></i></span>
    <span class="label">Theme</span>
  </button>

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

  {{-- If you are bundling Echo via Vite, include your compiled JS here, e.g.: --}}
  @vite(['resources/js/app.js'])

  <script src="{{ asset('assets/js/password-toggle.js') }}"></script>
  <script src="{{ asset('assets/js/theme-toggle.js') }}"></script>
  <script src="{{ asset('assets/js/ui-enhance.js') }}"></script>

  {{-- Real-time low-stock listener (uses window.Echo configured in your JS bundle) --}}
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // If Echo is not loaded (e.g. you haven't wired it yet), just skip
      if (!window.Echo) return;

      const toastContainer = document.getElementById('lowStockToastContainer');
      const badge = document.querySelector('[data-role="low-stock-count"]');

      function showLowStockToast(data) {
        if (!toastContainer) return;

        const toast = document.createElement('div');
        toast.className = 'alert alert-warning alert-animate mb-2';
        toast.innerHTML = `
          <div class="alert-icon">
            <i class="fa-solid fa-triangle-exclamation"></i>
          </div>
          <div class="alert-content">
            <strong>Low stock:</strong> ${data.name}
            <br>
            Qty: ${data.quantity} (threshold: ${data.threshold})
          </div>
        `;
        toastContainer.appendChild(toast);

        setTimeout(() => {
          toast.classList.add('alert-dim');
          setTimeout(() => toast.remove(), 400);
        }, 4000);
      }

      function incrementBadge() {
        if (!badge) return;
        const current = parseInt(badge.textContent || '0', 10) || 0;
        badge.textContent = String(current + 1);
        badge.classList.remove('d-none');
      }

      window.Echo.channel('products.low-stock')
        .listen('.LowStockDetected', (e) => {
          showLowStockToast(e);
          incrementBadge();
        });
    });
  </script>

  @stack('scripts')
</body>
</html>