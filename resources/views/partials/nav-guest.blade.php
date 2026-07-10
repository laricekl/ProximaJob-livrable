@php
  $isPublicRoute = fn (...$names) => request()->routeIs(...$names);
@endphp

<header class="fixed top-0 w-full z-50 flex justify-center py-3 md:py-5 px-4 md:px-10">
  <nav class="flex justify-between items-center max-w-7xl w-full nav-glass rounded-full px-3 md:px-6 h-12 md:h-16 transition-all duration-300">
    <a href="{{ route('welcome') }}" class="group flex-shrink-0">
      <x-brand-logo icon-class="w-11 md:w-14 h-auto" text-class="text-[15px] md:text-[18px] font-bold font-serif text-primary tracking-tight" />
    </a>
    <ul class="hidden lg:flex items-center gap-1">
      <li><a class="nav-link relative text-sm {{ $isPublicRoute('welcome') ? 'font-semibold text-secondary-container' : 'font-medium text-slate-500' }} hover:text-secondary-container transition-colors px-3 py-2" href="{{ route('welcome') }}">Accueil</a></li>
      <li><a class="nav-link relative text-sm {{ $isPublicRoute('offres', 'job_infos', 'details.offre', 'app_form') ? 'font-semibold text-secondary-container' : 'font-medium text-slate-500' }} hover:text-secondary-container transition-colors px-3 py-2" href="{{ route('offres') }}">Offres</a></li>
      <li><a class="nav-link relative text-sm {{ $isPublicRoute('ressources') ? 'font-semibold text-secondary-container' : 'font-medium text-slate-500' }} hover:text-secondary-container transition-colors px-3 py-2" href="{{ route('ressources') }}">Ressources</a></li>
      <li><a class="nav-link relative text-sm {{ $isPublicRoute('abonnement') ? 'font-semibold text-secondary-container' : 'font-medium text-slate-500' }} hover:text-secondary-container transition-colors px-3 py-2" href="{{ route('abonnement') }}">Forfaits</a></li>
      <li><a class="nav-link relative text-sm {{ $isPublicRoute('contact') ? 'font-semibold text-secondary-container' : 'font-medium text-slate-500' }} hover:text-secondary-container transition-colors px-3 py-2" href="{{ route('contact') }}">Contact</a></li>
    </ul>
    <div class="flex items-center gap-2 md:gap-3">
      <div class="hidden md:block">
        @include('components.language-selector')
      </div>
      @guest
        <a href="{{ route('login') }}" class="hidden sm:block text-sm font-medium text-slate-500 hover:text-primary transition-colors px-2 md:px-3 py-2">Se connecter</a>
        <a href="{{ route('register') }}" class="hidden sm:inline-block text-sm font-bold text-white bg-secondary-container hover:bg-secondary px-4 md:px-6 py-2 md:py-2.5 rounded-full transition-all shadow-sm hover:shadow-md">S'inscrire</a>
        <a href="{{ route('login') }}" class="sm:hidden flex items-center justify-center w-10 h-10 rounded-full text-slate-500 hover:text-primary hover:bg-black/5 transition-colors min-w-[44px] min-h-[44px]" aria-label="Compte">
          <span class="material-symbols-outlined text-xl">person</span>
        </a>
      @endguest
      @auth
        <a href="{{ route('dashboard') }}" class="hidden sm:block text-sm font-medium text-slate-500 hover:text-primary transition-colors px-2 md:px-3 py-2">Mon espace</a>
        <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
          @csrf
          <button class="text-sm font-bold text-white bg-secondary-container hover:bg-secondary px-4 md:px-6 py-2 md:py-2.5 rounded-full transition-all shadow-sm hover:shadow-md">Déconnexion</button>
        </form>
        <a href="{{ route('dashboard') }}" class="sm:hidden flex items-center justify-center w-10 h-10 rounded-full text-slate-500 hover:text-primary hover:bg-black/5 transition-colors min-w-[44px] min-h-[44px]" aria-label="Tableau de bord">
          <span class="material-symbols-outlined text-xl">person</span>
        </a>
      @endauth
      <button id="menu-toggle" class="lg:hidden flex flex-col gap-1.5 p-2 z-50 relative min-w-[44px] min-h-[44px] items-center justify-center" aria-label="Menu">
        <span class="block w-5 h-0.5 bg-primary rounded-full transition-all duration-300" id="bar1"></span>
        <span class="block w-5 h-0.5 bg-primary rounded-full transition-all duration-300" id="bar2"></span>
        <span class="block w-5 h-0.5 bg-primary rounded-full transition-all duration-300" id="bar3"></span>
      </button>
    </div>
  </nav>

  <div id="mobile-menu" class="fixed inset-0 z-40 lg:hidden pointer-events-none">
    <div id="menu-overlay" class="absolute inset-0 bg-black/30 opacity-0 transition-opacity duration-300"></div>
    <div id="menu-panel" class="absolute top-0 right-0 w-72 max-w-[85vw] h-full bg-white shadow-2xl transform translate-x-full transition-transform duration-400 ease-[cubic-bezier(0.22,1,0.36,1)] flex flex-col">
      <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <a href="{{ route('welcome') }}" class="flex items-center gap-2">
          <x-brand-logo icon-class="w-12 h-auto" text-class="text-[15px] font-bold font-serif text-primary tracking-tight" />
        </a>
        <button id="menu-close" class="w-11 h-11 flex items-center justify-center rounded-full hover:bg-slate-100 transition-colors min-w-[44px] min-h-[44px]" aria-label="Fermer">
          <span class="material-symbols-outlined text-xl">close</span>
        </button>
      </div>

      @guest
        <div class="px-5 pt-5 pb-2">
          <a href="{{ route('register') }}" class="flex items-center justify-between bg-secondary-container hover:bg-secondary text-white font-bold px-5 py-3.5 rounded-2xl transition-all shadow-sm">
            <span class="text-sm">S'inscrire</span>
            <span class="material-symbols-outlined text-lg">arrow_forward</span>
          </a>
        </div>
      @endguest

      <nav class="flex-1 px-3 pt-3 pb-4 space-y-1 overflow-y-auto">
        <a href="{{ route('welcome') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl text-sm {{ $isPublicRoute('welcome') ? 'font-semibold text-primary bg-slate-50' : 'font-medium text-slate-600 hover:bg-slate-50 hover:text-secondary-container transition-colors' }}">
          <span class="material-symbols-outlined text-xl {{ $isPublicRoute('welcome') ? 'text-secondary-container' : '' }}">home</span>
          Accueil
        </a>
        <a href="{{ route('offres') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl text-sm {{ $isPublicRoute('offres', 'job_infos', 'details.offre', 'app_form') ? 'font-semibold text-primary bg-slate-50' : 'font-medium text-slate-600 hover:bg-slate-50 hover:text-secondary-container transition-colors' }}">
          <span class="material-symbols-outlined text-xl {{ $isPublicRoute('offres', 'job_infos', 'details.offre', 'app_form') ? 'text-secondary-container' : '' }}">work</span>
          Offres
        </a>
        <a href="{{ route('ressources') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl text-sm {{ $isPublicRoute('ressources') ? 'font-semibold text-primary bg-slate-50' : 'font-medium text-slate-600 hover:bg-slate-50 hover:text-secondary-container transition-colors' }}">
          <span class="material-symbols-outlined text-xl {{ $isPublicRoute('ressources') ? 'text-secondary-container' : '' }}">menu_book</span>
          Ressources
        </a>
        <a href="{{ route('abonnement') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl text-sm {{ $isPublicRoute('abonnement') ? 'font-semibold text-primary bg-slate-50' : 'font-medium text-slate-600 hover:bg-slate-50 hover:text-secondary-container transition-colors' }}">
          <span class="material-symbols-outlined text-xl {{ $isPublicRoute('abonnement') ? 'text-secondary-container' : '' }}">sell</span>
          Forfaits
        </a>
        <a href="{{ route('contact') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl text-sm {{ $isPublicRoute('contact') ? 'font-semibold text-primary bg-slate-50' : 'font-medium text-slate-600 hover:bg-slate-50 hover:text-secondary-container transition-colors' }}">
          <span class="material-symbols-outlined text-xl {{ $isPublicRoute('contact') ? 'text-secondary-container' : '' }}">mail</span>
          Contact
        </a>
      </nav>

      <div class="px-5 py-4 border-t border-slate-100">
        <div class="mb-4 flex items-center justify-between">
          <span class="text-xs font-black uppercase tracking-widest text-outline">Langue</span>
          @include('components.language-selector')
        </div>
        @guest
          <a href="{{ route('login') }}" class="flex items-center gap-3 text-sm font-medium text-slate-500 hover:text-primary transition-colors">
            <span class="material-symbols-outlined text-xl">login</span>
            Se connecter
          </a>
        @else
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="flex items-center gap-3 text-sm font-medium text-slate-500 hover:text-primary transition-colors">
              <span class="material-symbols-outlined text-xl">logout</span>
              Déconnexion
            </button>
          </form>
        @endguest
      </div>
    </div>
  </div>
</header>
<script>
(function() {
  const toggle = document.getElementById('menu-toggle');
  const close = document.getElementById('menu-close');
  const panel = document.getElementById('menu-panel');
  const overlay = document.getElementById('menu-overlay');
  const menu = document.getElementById('mobile-menu');
  if (!toggle || !panel) return;
  let open = false;
  function show() {
    open = true; menu.style.pointerEvents = 'auto';
    panel.style.transform = 'translateX(0)'; overlay.style.opacity = '1';
    document.getElementById('bar1').style.transform = 'translateY(7px) rotate(45deg)';
    document.getElementById('bar2').style.opacity = '0';
    document.getElementById('bar3').style.transform = 'translateY(-7px) rotate(-45deg)';
  }
  function hide() {
    open = false; menu.style.pointerEvents = 'none';
    panel.style.transform = 'translateX(100%)'; overlay.style.opacity = '0';
    document.getElementById('bar1').style.transform = 'translateY(0) rotate(0)';
    document.getElementById('bar2').style.opacity = '1';
    document.getElementById('bar3').style.transform = 'translateY(0) rotate(0)';
  }
  toggle.addEventListener('click', () => open ? hide() : show());
  close?.addEventListener('click', hide);
  overlay.addEventListener('click', hide);
  panel.querySelectorAll('a').forEach(link => link.addEventListener('click', hide));
})();
</script>
