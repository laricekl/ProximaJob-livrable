<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  @include('partials.head')
</head>
<body class="bg-surface text-on-surface font-sans antialiased min-h-screen flex flex-col">

  @include('partials.nav-guest')

  <div class="flex-grow pt-24 md:pt-32">
    @yield('content')
  </div>

  @include('partials.footer')
  @include('partials.cookie-consent')

  @yield('scripts')
</body>
</html>
