@php
  $isCandidateRoute = fn (...$names) => request()->routeIs(...$names);
  $candidateInitials = strtoupper(substr(auth()->user()->name ?? 'J', 0, 1)) . strtoupper(substr(auth()->user()->prenom ?? 'D', 0, 1));
  $candidateFullName = trim((auth()->user()->name ?? 'Jean') . ' ' . (auth()->user()->prenom ?? 'Dupont'));
@endphp

<header class="fixed top-0 w-full z-50 flex justify-center py-3 md:py-5 px-4 md:px-10">
  <nav class="flex justify-between items-center max-w-7xl w-full nav-glass rounded-full px-3 md:px-6 h-12 md:h-16 transition-all duration-300">
    <a href="{{ route('welcome') }}" class="group flex-shrink-0">
      <x-brand-logo icon-class="w-11 md:w-14 h-auto" text-class="text-[15px] md:text-[18px] font-bold font-serif text-primary tracking-tight" />
    </a>
    <ul class="hidden lg:flex items-center gap-8">
      <li><a class="nav-link relative text-sm font-semibold {{ $isCandidateRoute('user.home') ? 'text-primary font-bold' : 'text-slate-500' }} hover:text-secondary-container transition-colors" href="{{ route('user.home') }}">Tableau de bord</a></li>
      <li><a class="nav-link relative text-sm font-semibold {{ $isCandidateRoute('offres', 'job_details', 'job_infos') ? 'text-primary font-bold' : 'text-slate-500' }} hover:text-secondary-container transition-colors" href="{{ route('offres') }}">Offres</a></li>
      <li><a class="nav-link relative text-sm font-semibold {{ $isCandidateRoute('user.historiques', 'user.historiques_ia') ? 'text-primary font-bold' : 'text-slate-500' }} hover:text-secondary-container transition-colors" href="{{ route('user.historiques') }}">Candidatures</a></li>
      <li><a class="nav-link relative text-sm font-semibold {{ $isCandidateRoute('infos.cv', 'cv.personalization.form', 'cv.personalization.preview', 'preview.cv-ia', 'preview.letter-ia', 'profile.edit') ? 'text-primary font-bold' : 'text-slate-500' }} hover:text-secondary-container transition-colors" href="{{ route('infos.cv') }}">Mon CV</a></li>
    </ul>
    <div class="flex items-center gap-4 relative">
      <div class="hidden md:block">
        @include('components.language-selector')
      </div>
      <a href="{{ route('notifications.index') }}" class="relative transition-colors {{ $isCandidateRoute('notifications.index') ? 'text-secondary-container' : 'text-slate-500 hover:text-primary' }}" aria-label="Notifications">
        <span class="material-symbols-outlined" @if($isCandidateRoute('notifications.index')) style="font-variation-settings:'FILL' 1" @endif>notifications</span>
        <span class="absolute -top-1 -right-1 w-2 h-2 bg-secondary-container rounded-full"></span>
      </a>
      <button id="userBtn" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
        <div class="w-9 h-9 rounded-full bg-primary-container flex items-center justify-center text-white text-xs font-bold">{{ $candidateInitials }}</div>
        <span class="hidden md:block text-sm font-bold text-primary">{{ $candidateFullName }}</span>
        <span class="material-symbols-outlined text-sm text-outline">expand_more</span>
      </button>
      <div id="userDropdown" class="user-dropdown absolute top-full right-0 mt-2 bg-white rounded-2xl shadow-xl border border-outline-variant/20 p-2 min-w-[220px] z-50">
        <div class="px-4 py-3 border-b border-outline-variant/10 mb-2"><p class="text-sm font-bold text-primary">{{ $candidateFullName }}</p><p class="text-xs text-outline">{{ auth()->user()->email ?? '' }}</p></div>
        <a href="{{ route('user.profil-public') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm {{ $isCandidateRoute('user.profil-public') ? 'text-secondary-container bg-secondary-container/5 font-semibold' : 'text-primary hover:bg-surface-container-low' }} transition-colors"><span class="material-symbols-outlined text-lg {{ $isCandidateRoute('user.profil-public') ? '' : 'text-outline' }}">account_circle</span> Mon profil candidat</a>
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm {{ $isCandidateRoute('profile.edit') ? 'text-secondary-container bg-secondary-container/5 font-semibold' : 'text-primary hover:bg-surface-container-low' }} transition-colors"><span class="material-symbols-outlined text-lg {{ $isCandidateRoute('profile.edit') ? '' : 'text-outline' }}">settings</span> Paramètres</a>
        <a href="{{ route('user.abonnement') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm text-primary hover:bg-surface-container-low transition-colors"><span class="material-symbols-outlined text-lg text-outline">workspace_premium</span> Mon abonnement</a>
        <form method="POST" action="{{ route('logout') }}" class="mt-1 border-t border-outline-variant/10 pt-2">
          @csrf
          <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm text-secondary-container hover:bg-secondary-container/10 transition-colors"><span class="material-symbols-outlined text-lg">logout</span> Déconnexion</button>
        </form>
      </div>
    </div>
    <button id="menu-toggle" class="lg:hidden flex flex-col gap-1.5 p-2 z-50 relative" aria-label="Menu">
      <span class="block w-5 h-0.5 bg-primary rounded-full transition-all duration-300" id="bar1"></span>
      <span class="block w-5 h-0.5 bg-primary rounded-full transition-all duration-300" id="bar2"></span>
      <span class="block w-5 h-0.5 bg-primary rounded-full transition-all duration-300" id="bar3"></span>
    </button>
  </nav>
  <div id="mobile-menu" class="fixed inset-0 z-40 lg:hidden pointer-events-none">
    <div id="menu-overlay" class="absolute inset-0 bg-black/30 opacity-0 transition-opacity duration-300"></div>
    <div id="menu-panel" class="absolute top-0 right-0 w-72 max-w-[85vw] h-full nav-glass rounded-l-3xl shadow-2xl transform translate-x-full transition-transform duration-400 ease-[cubic-bezier(0.22,1,0.36,1)] flex flex-col gap-8 pt-20 px-8">
      <button onclick="document.getElementById('menu-overlay').click()" class="absolute top-6 right-6 w-11 h-11 flex items-center justify-center rounded-full bg-surface-container-low text-primary hover:bg-surface-container transition-colors" aria-label="Fermer le menu">
        <span class="material-symbols-outlined text-xl">close</span>
      </button>
      <a href="{{ route('user.home') }}" class="text-lg font-bold {{ $isCandidateRoute('user.home') ? 'text-primary' : 'text-slate-500' }}">Tableau de bord</a>
      <a href="{{ route('offres') }}" class="text-lg font-semibold {{ $isCandidateRoute('offres', 'job_details', 'job_infos') ? 'text-primary' : 'text-slate-500' }}">Offres</a>
      <a href="{{ route('user.historiques') }}" class="text-lg font-semibold {{ $isCandidateRoute('user.historiques', 'user.historiques_ia') ? 'text-primary' : 'text-slate-500' }}">Candidatures</a>
      <a href="{{ route('user.profil-public') }}" class="text-lg font-semibold {{ $isCandidateRoute('user.profil-public') ? 'text-primary' : 'text-slate-500' }}">Profil candidat</a>
      <a href="{{ route('infos.cv') }}" class="text-lg font-semibold {{ $isCandidateRoute('infos.cv', 'cv.personalization.form', 'cv.personalization.preview', 'preview.cv-ia', 'preview.letter-ia', 'profile.edit') ? 'text-primary' : 'text-slate-500' }}">Mon CV</a>
      <a href="{{ route('notifications.index') }}" class="text-lg font-semibold {{ $isCandidateRoute('notifications.index') ? 'text-primary' : 'text-slate-500' }}">Notifications</a>
      <div class="flex items-center justify-between gap-4">
        <span class="text-xs font-black uppercase tracking-widest text-outline">Langue</span>
        @include('components.language-selector')
      </div>
      <hr class="border-primary/10" />
      <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="text-lg font-bold text-secondary-container">Déconnexion</button></form>
    </div>
  </div>
</header>
<script>
(function(){const t=document.getElementById('menu-toggle'),p=document.getElementById('menu-panel'),o=document.getElementById('menu-overlay'),m=document.getElementById('mobile-menu'),b1=document.getElementById('bar1'),b2=document.getElementById('bar2'),b3=document.getElementById('bar3');if(!t||!p)return;let open=false;function show(){open=true;m.style.pointerEvents='auto';p.style.transform='translateX(0)';o.style.opacity='1';b1.style.transform='translateY(7px) rotate(45deg)';b2.style.opacity='0';b3.style.transform='translateY(-7px) rotate(-45deg)'}function hide(){open=false;m.style.pointerEvents='none';p.style.transform='translateX(100%)';o.style.opacity='0';b1.style.transform='translateY(0) rotate(0)';b2.style.opacity='1';b3.style.transform='translateY(0) rotate(0)'}t.addEventListener('click',()=>open?hide():show());o.addEventListener('click',hide);p.querySelectorAll('a,button').forEach(l=>l.addEventListener('click',hide))})();
document.getElementById('userBtn').addEventListener('click',()=>document.getElementById('userDropdown').classList.toggle('active'));
document.addEventListener('click',(e)=>{if(!e.target.closest('#userBtn')&&!e.target.closest('#userDropdown'))document.getElementById('userDropdown').classList.remove('active')});
</script>
