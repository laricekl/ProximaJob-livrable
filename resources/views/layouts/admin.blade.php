<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  @include('partials.head')
</head>
<body class="bg-surface text-on-surface font-sans antialiased min-h-screen">

  <!-- Sidebar -->
  <aside id="sidebar" class="sidebar fixed top-0 left-0 z-50 w-64 h-full bg-primary-container text-white flex flex-col">
    <div class="px-6 py-6 border-b border-white/10">
      <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
        <x-brand-logo
          icon-class="w-9 h-auto"
          text-class="text-lg font-bold font-serif text-white tracking-tight"
        />
      </a>
    </div>
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-6">
      <div>
        <h3 class="text-2xs font-bold text-white/40 uppercase tracking-widest mb-2 px-3">Principal</h3>
        <ul class="space-y-0.5">
          <li><a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white font-semibold' : 'text-white/60 hover:bg-white/10 hover:text-white' }} transition-colors"><span class="material-symbols-outlined text-lg">dashboard</span> Tableau de bord</a></li>
        </ul>
      </div>
      <div>
        <h3 class="text-2xs font-bold text-white/40 uppercase tracking-widest mb-2 px-3">Gestion</h3>
        <ul class="space-y-0.5">
          <li><a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.users*') ? 'bg-white/10 text-white font-semibold' : 'text-white/60 hover:bg-white/10 hover:text-white' }} transition-colors"><span class="material-symbols-outlined text-lg">group</span> Utilisateurs</a></li>
          <li><a href="{{ route('admin.offres') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.offres*') ? 'bg-white/10 text-white font-semibold' : 'text-white/60 hover:bg-white/10 hover:text-white' }} transition-colors"><span class="material-symbols-outlined text-lg">work</span> Offres</a></li>
          <li><a href="{{ route('admin.abonnements') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.abonnements*') ? 'bg-white/10 text-white font-semibold' : 'text-white/60 hover:bg-white/10 hover:text-white' }} transition-colors"><span class="material-symbols-outlined text-lg">credit_card</span> Abonnements</a></li>
        </ul>
      </div>
      <div>
        <h3 class="text-2xs font-bold text-white/40 uppercase tracking-widest mb-2 px-3">Référentiel</h3>
        <ul class="space-y-0.5">
          <li><a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.categories*') ? 'bg-white/10 text-white font-semibold' : 'text-white/60 hover:bg-white/10 hover:text-white' }} transition-colors"><span class="material-symbols-outlined text-lg">category</span> Catégories</a></li>
          <li><a href="{{ route('admin.types-offres.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.types-offres*') ? 'bg-white/10 text-white font-semibold' : 'text-white/60 hover:bg-white/10 hover:text-white' }} transition-colors"><span class="material-symbols-outlined text-lg">work_history</span> Types d'offres</a></li>
          <li><a href="{{ route('admin.secteurs.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.secteurs*') ? 'bg-white/10 text-white font-semibold' : 'text-white/60 hover:bg-white/10 hover:text-white' }} transition-colors"><span class="material-symbols-outlined text-lg">business</span> Secteurs</a></li>
          <li><a href="{{ route('admin.skills.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.skills*') ? 'bg-white/10 text-white font-semibold' : 'text-white/60 hover:bg-white/10 hover:text-white' }} transition-colors"><span class="material-symbols-outlined text-lg">bolt</span> Compétences</a></li>
          <li><a href="{{ route('admin.diplomes.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.diplomes*') ? 'bg-white/10 text-white font-semibold' : 'text-white/60 hover:bg-white/10 hover:text-white' }} transition-colors"><span class="material-symbols-outlined text-lg">school</span> Diplômes</a></li>
        </ul>
      </div>
      <div>
        <h3 class="text-2xs font-bold text-white/40 uppercase tracking-widest mb-2 px-3">Communication</h3>
        <ul class="space-y-0.5">
          <li><a href="{{ route('admin.statistiques') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.statistiques*') ? 'bg-white/10 text-white font-semibold' : 'text-white/60 hover:bg-white/10 hover:text-white' }} transition-colors"><span class="material-symbols-outlined text-lg">bar_chart</span> Statistiques</a></li>
          <li><a href="{{ route('admin.newsletters') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.newsletters*') ? 'bg-white/10 text-white font-semibold' : 'text-white/60 hover:bg-white/10 hover:text-white' }} transition-colors"><span class="material-symbols-outlined text-lg">mail</span> Newsletter</a></li>
        </ul>
      </div>
      <div>
        <h3 class="text-2xs font-bold text-white/40 uppercase tracking-widest mb-2 px-3">Configuration</h3>
        <ul class="space-y-0.5">
          <li><a href="{{ route('admin.parametres') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.parametres*') ? 'bg-white/10 text-white font-semibold' : 'text-white/60 hover:bg-white/10 hover:text-white' }} transition-colors"><span class="material-symbols-outlined text-lg">settings</span> Paramètres</a></li>
        </ul>
      </div>
    </nav>
    <div class="px-3 py-4 border-t border-white/10">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-secondary-container hover:bg-white/5 transition-colors">
          <span class="material-symbols-outlined text-lg">logout</span> Déconnexion
        </button>
      </form>
    </div>
  </aside>

  <!-- Mobile overlay -->
  <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden" onclick="closeSidebar()"></div>

  <!-- Main -->
  <div class="lg:ml-64 min-h-screen flex flex-col">
    <header class="sticky top-0 z-30 bg-white/75 backdrop-blur-xl border-b border-white/40 shadow-sm">
      <div class="flex items-center justify-between px-4 md:px-6 py-3">
        <div class="flex items-center gap-4">
          <button id="menuBtn" class="lg:hidden text-primary hover:text-secondary-container transition-colors p-1" onclick="openSidebar()">
            <span class="material-symbols-outlined">menu</span>
          </button>
          <h1 class="text-xl font-bold font-serif text-primary">@yield('page-title', 'Administration')</h1>
        </div>
        <div class="flex items-center gap-3 relative">
          <button id="userBtn" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
            <div class="w-8 h-8 rounded-full bg-secondary-container flex items-center justify-center text-white text-xs font-bold">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}{{ strtoupper(substr(auth()->user()->prenom ?? 'D', 0, 1)) }}</div>
            <span class="material-symbols-outlined text-sm text-outline">expand_more</span>
          </button>
          <div id="userDropdown" class="user-dropdown absolute top-full right-0 mt-2 bg-white rounded-2xl shadow-xl border border-outline-variant/20 p-2 min-w-[180px] z-50">
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm text-secondary-container hover:bg-secondary-container/5 transition-colors">
                <span class="material-symbols-outlined text-lg">logout</span> Déconnexion
              </button>
            </form>
          </div>
        </div>
      </div>
    </header>

    <main class="flex-1 p-4 md:p-6">
      @yield('content')
    </main>

    <footer class="border-t border-primary/5 bg-white/40 backdrop-blur-sm mt-auto">
      <div class="max-w-5xl mx-auto py-6 px-4 md:px-10 flex flex-col sm:flex-row justify-between items-center gap-4">
        <span class="text-[11px] text-primary/30 font-medium">© {{ date('Y') }} {{ $siteSettings?->site_nom ?? 'ProximaJob' }}.</span>
        <nav class="flex items-center gap-6 text-[11px] uppercase tracking-widest text-primary/30 font-bold">
          <a href="{{ route('policy') }}" class="hover:text-secondary-container transition-colors">Confidentialité</a>
          <a href="{{ route('cookies.policy') }}" class="hover:text-secondary-container transition-colors">Cookies</a>
          <a href="{{ route('terms') }}" class="hover:text-secondary-container transition-colors">Conditions</a>
        </nav>
      </div>
    </footer>
  </div>

  <script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    function openSidebar() { sidebar.classList.add('open'); overlay.classList.remove('hidden'); }
    function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.add('hidden'); }
    document.getElementById('userBtn').addEventListener('click', () => document.getElementById('userDropdown').classList.toggle('active'));
    document.addEventListener('click', (e) => { if (!e.target.closest('#userBtn') && !e.target.closest('#userDropdown')) document.getElementById('userDropdown').classList.remove('active'); });
  </script>
  @include('partials.cookie-consent')
  @yield('scripts')
</body>
</html>
