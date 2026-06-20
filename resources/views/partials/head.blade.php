@php
  $brandName = $siteSettings?->site_nom ?: 'ProximaJob';
  $brandFavicon = $siteSettings?->favicon_url ?: asset('favicon.ico');
@endphp

<meta charset="utf-8" />
<meta content="width=device-width, initial-scale=1.0" name="viewport" />
<meta name="csrf-token" content="{{ csrf_token() }}" />
<title>{{ $brandName }} - @yield('title', 'IA Concierge')</title>
<link rel="icon" type="image/x-icon" href="{{ $brandFavicon }}" />

<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

<link rel="preload" as="image" href="{{ asset('img/proxi-mark.png') }}" fetchpriority="high" />
@if (request()->routeIs('welcome'))
  <link rel="preload" as="image" href="{{ asset('img/votre-hero.png') }}" fetchpriority="high" />
@endif

<!-- Polices -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Serif:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet" />

<!-- Design System -->
<link rel="stylesheet" href="/css/proxima-glass.css" />
<script src="/js/proxima-ui.js" defer></script>

@vite(['resources/css/app.css', 'resources/js/app.js'])

<!-- Styles customs -->
<style>
  :root {
    --pj-bg: #dde1e6;
    --pj-text: #191c1e;
    --pj-primary: #1f2433;
    --pj-primary-rgb: 31, 36, 51;
    --pj-accent: #eb843c;
    --pj-accent-rgb: 235, 132, 60;
    --pj-accent-strong: #d9732c;
    --pj-info-rgb: 36, 98, 183;
    --pj-wash-top-rgb: 176, 177, 192;
    --pj-wash-bottom-rgb: 240, 242, 245;
    --pj-surface: rgba(255,255,255,0.84);
    --pj-surface-soft: rgba(255,255,255,0.8);
    --pj-border: rgba(15,23,42,0.08);
    --pj-border-soft: rgba(15,23,42,0.06);
    --pj-shadow: rgba(15,23,42,0.09);
    --pj-shadow-soft: rgba(15,23,42,0.07);
    --pj-grid-dot: rgba(0,0,0,0.03);
  }
  .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
  .nav-link::after { content: ''; position: absolute; width: 0; height: 1px; bottom: -4px; left: 50%; background: currentColor; transition: all .3s; transform: translateX(-50%); }
  .nav-link:hover::after { width: 100%; }
  body { background-color: var(--pj-bg); background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n' x='0' y='0'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E"); background-size: cover; background-position: center; position: relative; color: var(--pj-text); }
  body::before { content: ""; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background: linear-gradient(rgba(var(--pj-wash-top-rgb),0.4), rgba(var(--pj-wash-bottom-rgb),0.4)), radial-gradient(at 10% 10%, rgba(var(--pj-accent-rgb),0.06) 0px, transparent 40%), radial-gradient(at 90% 90%, rgba(var(--pj-info-rgb),0.04) 0px, transparent 40%); pointer-events: none; }
  body::after { content: ""; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background-image: radial-gradient(var(--pj-grid-dot) 1px, transparent 1px); background-size: 32px 32px; pointer-events: none; }
  .user-dropdown { opacity: 0; visibility: hidden; transform: translateY(-8px); transition: all 0.2s ease; }
  .user-dropdown.active { opacity: 1; visibility: visible; transform: translateY(0); }
  .nav-glass { background: var(--pj-surface); backdrop-filter: blur(24px); border: 1px solid var(--pj-border); box-shadow: 0 12px 34px var(--pj-shadow); }
  .card-glow { background: var(--pj-surface-soft); backdrop-filter: blur(20px); border: 1px solid var(--pj-border); box-shadow: 0 10px 30px var(--pj-shadow-soft); }
  .footer-glass { background: rgba(255,255,255,0.86); backdrop-filter: blur(12px); border-top: 1px solid var(--pj-border-soft); }
  .language-switcher { border-color: var(--pj-border) !important; box-shadow: 0 8px 18px rgba(15,23,42,0.05); }
  .focus-accent-field:focus { border-color: var(--pj-accent) !important; box-shadow: 0 0 0 3px rgba(var(--pj-accent-rgb),0.1) !important; outline: none; }
  .btn-accent-shadow { box-shadow: 0 14px 30px rgba(var(--pj-accent-rgb),0.22); }
  .premium-card,
  .resource-item,
  .faq-item,
  .card-3d-inner {
    border-color: var(--pj-border) !important;
    box-shadow: 0 10px 30px rgba(15,23,42,0.06);
  }
  .sidebar { transition: transform 0.3s ease; }
  @media (max-width: 1023px) { .sidebar { transform: translateX(-100%); } .sidebar.open { transform: translateX(0); } }
</style>
@yield('styles')
