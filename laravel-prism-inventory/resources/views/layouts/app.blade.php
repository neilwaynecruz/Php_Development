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
<body class="bg-light">
  @yield('nav')

  <div class="container py-4">
    @if (session('message')) {!! session('message') !!} @endif
    @yield('content')
  </div>

  <!-- Floating Theme Toggle Button -->
  <button id="themeToggle" class="theme-fab" type="button" aria-label="Toggle color theme" title="Toggle theme">
    <span class="icon-sun" aria-hidden="true"><i class="fa-solid fa-sun"></i></span>
    <span class="icon-moon" aria-hidden="true"><i class="fa-solid fa-moon"></i></span>
    <span class="label">Theme</span>
  </button>

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('assets/js/password-toggle.js') }}"></script>
  <script src="{{ asset('assets/js/theme-toggle.js') }}"></script>
  <script src="{{ asset('assets/js/ui-enhance.js') }}"></script>
  @stack('scripts')
</body>
</html>