<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  @include('partials.head')
</head>
<body class="bg-surface text-on-surface font-sans antialiased min-h-screen flex flex-col">

  @include('partials.nav-candidat')

  @yield('content')

  @include('partials.footer')
  @include('partials.cookie-consent')

  @yield('scripts')
</body>
</html>
