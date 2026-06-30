<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  @include('partials.head')
</head>
<body class="bg-surface text-on-surface font-sans antialiased min-h-screen flex flex-col">

  @include('partials.nav-entreprise')

  <main class="flex-grow pt-24 md:pt-32 pb-16">
    @yield('content')
  </main>

  @include('partials.footer')
  @include('partials.cookie-consent')

  @yield('scripts')
</body>
</html>
