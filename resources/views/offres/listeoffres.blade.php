@extends(auth()->check() ? (auth()->user()->hasRole('entreprise') ? 'layouts.entreprise' : 'layouts.candidat') : 'layouts.guest')
@section('title', 'Offres')
@section('styles')
  <style>
    .premium-card {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .premium-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 767px) {
      .text-4xl {
        font-size: 1.75rem !important;
      }
      .text-lg {
        font-size: 0.9rem !important;
      }
      .mb-10 {
        margin-bottom: 1.5rem !important;
      }
      main.pt-32 {
        padding-top: 6rem;
      }
      main.pb-20 {
        padding-bottom: 2rem;
      }

      /* Search filter panel */
      #search-filter-panel {
        overflow: hidden;
        transition: max-height 0.35s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.3s ease, margin 0.35s cubic-bezier(0.22, 1, 0.36, 1);
      }
      #search-filter-panel.hidden {
        max-height: 0;
        opacity: 0;
        margin-top: 0;
        pointer-events: none;
      }
      #search-filter-panel:not(.hidden) {
        max-height: 200px;
        opacity: 1;
        margin-top: 0.5rem;
      }

      /* Sidebar - caché par défaut en mobile, toggle via JS */
      aside {
        display: none;
      }
      aside.filters-open {
        display: block;
      }
      aside .bg-white\/80 {
        padding: 14px !important;
        border-radius: 14px;
        margin-bottom: 12px;
      }
      aside .text-sm {
        font-size: 12px;
      }
      aside .text-xs {
        font-size: 10px;
      }
      aside .space-y-2 {
        gap: 4px;
      }
      aside .mb-6 {
        margin-bottom: 12px;
      }
      aside .pt-6 {
        padding-top: 12px;
      }
      .flex.flex-col.lg\\:flex-row.gap-8 {
        gap: 14px;
      }

      /* Job cards */
      .offres-jobs {
        gap: 6px;
      }
      .offres-jobs > a {
        padding: 10px 12px !important;
        border-radius: 12px;
        flex-direction: row !important;
        align-items: center !important;
        gap: 8px;
      }
      .offres-jobs .w-14 {
        width: 34px;
        height: 34px;
        border-radius: 8px;
      }
      .offres-jobs .text-2xl {
        font-size: 17px;
      }
      .offres-jobs h4 {
        font-size: 13px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }
      .offres-jobs .text-sm {
        font-size: 10px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }
      .offres-jobs .mt-4 {
        margin-top: 0;
      }
      .offres-jobs .px-4 {
        padding: 2px 7px;
        font-size: 7px;
      }
      .offres-jobs .opacity-0 {
        display: none;
      }
      .offres-jobs .absolute.inset-0 {
        display: none;
      }
      .offres-jobs .flex.items-center.gap-5 {
        gap: 8px;
      }

      /* Sort bar */
      .flex.flex-col.sm\\:flex-row.justify-between.gap-4.mb-6 {
        gap: 8px;
        margin-bottom: 12px;
      }

      /* Pagination */
      nav.mt-10 {
        margin-top: 16px;
      }
      nav .w-10 {
        width: 34px;
        height: 34px;
        font-size: 12px;
        border-radius: 8px;
      }

      /* Footer - déjà compact, juste ajuster si besoin */
      footer nav.gap-4 {
        gap: 12px;
      }
      footer .text-\[9px\] {
        font-size: 8px;
      }
      footer .text-\[8px\] {
        font-size: 7px;
      }
      footer .text-\[10px\] {
        font-size: 9px;
      }
    }
  </style>
@endsection
@section('content')
  <main class="flex-grow pt-32 pb-20" style="background: linear-gradient(180deg, rgba(176, 177, 192, 0.22) 0%, rgba(240, 242, 245, 0.36) 100%), radial-gradient(at 10% 8%, rgba(235, 132, 60, 0.055) 0, transparent 38%), radial-gradient(at 90% 88%, rgba(36, 98, 183, 0.035) 0, transparent 40%), #f7f9fb;">
    <x-public-page-hero
      title="Opportunités d'emploi"
      subtitle="Explorez les offres disponibles et repérez rapidement celles qui correspondent à votre profil."
    />

    <div class="max-w-7xl mx-auto px-4 md:px-10 mt-8">

      <!-- Search Bar -->
      <div class="search-glass relative z-10 mb-8">

        <!-- MOBILE: Compact single-row -->
        <form method="GET" action="{{ route('offres') }}" data-testid="offers-mobile-search-form" class="flex md:hidden items-center gap-1 bg-white backdrop-blur-xl rounded-2xl shadow-lg p-1.5 border border-white">
          <div class="flex-1 flex items-center min-w-0 px-3 py-1.5">
            <span class="material-symbols-outlined text-outline mr-2 text-sm flex-shrink-0">search</span>
            <input name="search" value="{{ $search }}" class="w-full bg-transparent border-none focus:ring-0 text-on-surface placeholder-outline text-sm" placeholder="Titre du poste, mots-clés..." type="text" />
          </div>
          <button id="search-filter-toggle" type="button" class="w-10 h-10 flex items-center justify-center rounded-xl text-outline hover:text-secondary-container hover:bg-secondary-container/5 transition-colors flex-shrink-0 min-w-[44px] min-h-[44px]" aria-label="Filtres">
            <span class="material-symbols-outlined text-xl">tune</span>
          </button>
          <button type="submit" class="bg-secondary-container text-white font-bold px-4 py-2.5 rounded-xl hover:bg-secondary transition-all shadow flex items-center justify-center gap-1 cta-pulse text-sm flex-shrink-0 min-w-[44px] min-h-[44px]">
            <span class="material-symbols-outlined text-lg">arrow_forward</span>
          </button>
        </form>

        <!-- MOBILE: Filter panel -->
        <form method="GET" action="{{ route('offres') }}" id="search-filter-panel" data-testid="offers-mobile-filter-form" class="md:hidden bg-white backdrop-blur-xl rounded-2xl shadow-lg p-3 border border-white hidden">
          <input type="hidden" name="search" value="{{ $search }}">
          <div class="flex items-center px-3 py-2 mb-2 border-b border-outline-variant/20">
            <span class="material-symbols-outlined text-outline mr-2 text-sm">location_on</span>
            <input name="localisation" value="{{ $localisation }}" class="w-full bg-transparent border-none focus:ring-0 text-on-surface placeholder-outline text-sm" placeholder="Localisation" type="text" />
          </div>
          <div class="flex items-center px-3 py-2">
            <span class="material-symbols-outlined text-outline mr-2 text-sm">category</span>
            <select name="categories[]" class="w-full bg-transparent border-none focus:ring-0 text-on-surface text-sm">
              <option value="">Toutes les catégories</option>
              @foreach ($categoriesWithCount as $category)
                <option value="{{ $category->id }}" @selected(in_array($category->id, $categories))>{{ $category->nom }}</option>
              @endforeach
            </select>
          </div>
          <button type="submit" class="mt-3 w-full bg-secondary-container text-white font-bold px-4 py-3 rounded-xl hover:bg-secondary transition-all shadow text-sm">Appliquer</button>
        </form>

        <!-- DESKTOP: Full multi-field row -->
        <form method="GET" action="{{ route('offres') }}" data-testid="offers-desktop-search-form" class="hidden md:flex items-center gap-2 bg-white backdrop-blur-xl rounded-2xl shadow-lg p-2 border border-white">
          <div class="flex-1 flex items-center px-4 py-2 border-r border-outline-variant/30">
            <span class="material-symbols-outlined text-outline mr-3 text-base">search</span>
            <input name="search" value="{{ $search }}" class="w-full bg-transparent border-none focus:ring-0 text-on-surface placeholder-outline text-base" placeholder="Titre du poste, mots-clés..." type="text" />
          </div>
          <div class="flex-1 flex items-center px-4 py-2 border-r border-outline-variant/30">
            <span class="material-symbols-outlined text-outline mr-3 text-base">location_on</span>
            <input name="localisation" value="{{ $localisation }}" class="w-full bg-transparent border-none focus:ring-0 text-on-surface placeholder-outline text-base" placeholder="Localisation" type="text" />
          </div>
          <div class="flex-1 flex items-center px-4 py-2">
            <span class="material-symbols-outlined text-outline mr-3 text-base">category</span>
            <select name="categories[]" class="w-full bg-transparent border-none focus:ring-0 text-on-surface text-base">
              <option value="">Toutes les catégories</option>
              @foreach ($categoriesWithCount as $category)
                <option value="{{ $category->id }}" @selected(in_array($category->id, $categories))>{{ $category->nom }}</option>
              @endforeach
            </select>
          </div>
          <button type="submit" class="bg-secondary-container text-white font-bold px-8 py-4 rounded-xl hover:bg-secondary transition-all shadow flex items-center justify-center gap-2 cta-pulse text-base flex-shrink-0">
            Rechercher <span class="material-symbols-outlined text-lg">arrow_forward</span>
          </button>
        </form>
      </div>

      <div class="flex flex-col lg:flex-row gap-8">

        <!-- SIDEBAR FILTRES -->
        <aside class="w-full lg:w-72 flex-shrink-0">
          <form method="GET" action="{{ route('offres') }}" data-testid="offers-filter-form" class="bg-white backdrop-blur-xl border border-white rounded-2xl p-6 shadow-lg lg:sticky lg:top-28">
            <input type="hidden" name="search" value="{{ $search }}">
            <input type="hidden" name="localisation" value="{{ $localisation }}">
            <input type="hidden" name="sort" value="{{ $sort }}">

            <div class="flex items-center justify-between mb-6">
              <h3 class="font-bold text-primary text-sm uppercase tracking-wide">Filtres</h3>
              <a href="{{ route('offres') }}" class="text-[10px] font-bold uppercase tracking-wider text-secondary-container hover:underline">Réinitialiser</a>
            </div>

            <!-- Type de contrat -->
            <div class="mb-6">
              <h4 class="text-xs font-bold uppercase tracking-wider text-primary/50 mb-3">Type de contrat</h4>
              <div class="space-y-2">
                @foreach ($typesWithCount as $contractType)
                <label class="flex items-center gap-3 cursor-pointer group">
                  <input name="type[]" value="{{ $contractType->id }}" type="checkbox" class="rounded border-outline-variant text-secondary-container focus:ring-secondary-container" @checked(in_array($contractType->id, $types)) />
                  <span class="text-sm text-on-surface group-hover:text-primary transition-colors">{{ $contractType->nom }} <span class="text-primary/35">({{ $contractType->offres_count }})</span></span>
                </label>
                @endforeach
              </div>
            </div>

            <!-- Catégories -->
            <div class="mb-6 pt-6 border-t border-primary/5">
              <h4 class="text-xs font-bold uppercase tracking-wider text-primary/50 mb-3">Catégorie</h4>
              <div class="space-y-2">
                @foreach ($categoriesWithCount as $category)
                <label class="flex items-center gap-3 cursor-pointer group">
                  <input name="categories[]" value="{{ $category->id }}" type="checkbox" class="rounded border-outline-variant text-secondary-container focus:ring-secondary-container" @checked(in_array($category->id, $categories)) />
                  <span class="text-sm text-on-surface group-hover:text-primary transition-colors">{{ $category->nom }} <span class="text-primary/35">({{ $category->offres_count }})</span></span>
                </label>
                @endforeach
              </div>
            </div>

            <!-- Mode de travail -->
            <div class="mb-6 pt-6 border-t border-primary/5">
              <h4 class="text-xs font-bold uppercase tracking-wider text-primary/50 mb-3">Mode de travail</h4>
              <div class="space-y-2">
                <label class="flex items-center gap-3 cursor-pointer group">
                  <input name="remote_work[]" value="Présentiel" type="checkbox" class="rounded border-outline-variant text-secondary-container focus:ring-secondary-container" @checked(in_array('Présentiel', $remoteWork)) />
                  <span class="text-sm text-on-surface group-hover:text-primary transition-colors">Présentiel</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer group">
                  <input name="remote_work[]" value="Hybride" type="checkbox" class="rounded border-outline-variant text-secondary-container focus:ring-secondary-container" @checked(in_array('Hybride', $remoteWork)) />
                  <span class="text-sm text-on-surface group-hover:text-primary transition-colors">Hybride</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer group">
                  <input name="remote_work[]" value="Télétravail" type="checkbox" class="rounded border-outline-variant text-secondary-container focus:ring-secondary-container" @checked(in_array('Télétravail', $remoteWork)) />
                  <span class="text-sm text-on-surface group-hover:text-primary transition-colors">Télétravail</span>
                </label>
              </div>
            </div>

            <!-- Salaire -->
            <div class="mb-6 pt-6 border-t border-primary/5">
              <h4 class="text-xs font-bold uppercase tracking-wider text-primary/50 mb-3">Salaire minimum</h4>
              <select name="salaire_min" class="w-full rounded-xl border-outline-variant/30 text-sm text-on-surface focus:ring-secondary-container focus:border-secondary-container">
                <option value="">Aucun minimum</option>
                @foreach ([30000, 50000, 70000, 90000, 110000] as $salaryOption)
                  <option value="{{ $salaryOption }}" @selected((string) $salaire_min === (string) $salaryOption)>{{ number_format($salaryOption, 0, ',', ' ') }} $</option>
                @endforeach
              </select>
            </div>

            <!-- Niveau d'expérience -->
            <div class="pt-6 border-t border-primary/5">
              <h4 class="text-xs font-bold uppercase tracking-wider text-primary/50 mb-3">Expérience</h4>
              <div class="space-y-2">
                <label class="flex items-center gap-3 cursor-pointer group">
                  <input name="experience[]" value="junior" type="checkbox" class="rounded border-outline-variant text-secondary-container focus:ring-secondary-container" @checked(in_array('junior', $experience)) />
                  <span class="text-sm text-on-surface group-hover:text-primary transition-colors">Junior (0-2 ans)</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer group">
                  <input name="experience[]" value="intermediaire" type="checkbox" class="rounded border-outline-variant text-secondary-container focus:ring-secondary-container" @checked(in_array('intermediaire', $experience)) />
                  <span class="text-sm text-on-surface group-hover:text-primary transition-colors">Intermédiaire (2-5 ans)</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer group">
                  <input name="experience[]" value="senior" type="checkbox" class="rounded border-outline-variant text-secondary-container focus:ring-secondary-container" @checked(in_array('senior', $experience)) />
                  <span class="text-sm text-on-surface group-hover:text-primary transition-colors">Senior (5+ ans)</span>
                </label>
              </div>
            </div>

            <button type="submit" class="mt-6 w-full bg-primary text-white font-bold px-5 py-3 rounded-xl hover:bg-secondary-container transition-all shadow">
              Appliquer les filtres
            </button>
          </form>
        </aside>

        <!-- LISTE OFFRES -->
        <div class="flex-1 min-w-0">

          <!-- Barre de tri + compteur -->
          <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 rounded-2xl bg-white border border-white shadow-md px-5 py-4">
            <p class="text-sm text-on-surface-variant flex items-center gap-2">
              <span class="material-symbols-outlined text-base text-secondary-container">manage_search</span>
              <span><span class="font-bold text-primary">{{ $offres->total() }} offres</span> correspondent à vos critères</span>
            </p>
            <div class="flex items-center gap-3">
              <button id="filter-toggle" class="lg:hidden flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-secondary-container bg-secondary-container/5 border border-secondary-container/20 rounded-xl px-4 py-2 hover:bg-secondary-container/10 transition-colors">
                <span class="material-symbols-outlined text-base">tune</span> Filtres
              </button>
              <form method="GET" action="{{ route('offres') }}" class="flex items-center gap-3">
                <input type="hidden" name="search" value="{{ $search }}">
                <input type="hidden" name="localisation" value="{{ $localisation }}">
                @foreach ($categories as $categoryId)
                  <input type="hidden" name="categories[]" value="{{ $categoryId }}">
                @endforeach
                @foreach ($types as $typeId)
                  <input type="hidden" name="type[]" value="{{ $typeId }}">
                @endforeach
                @foreach ($remoteWork as $mode)
                  <input type="hidden" name="remote_work[]" value="{{ $mode }}">
                @endforeach
                @foreach ($experience as $level)
                  <input type="hidden" name="experience[]" value="{{ $level }}">
                @endforeach
                <input type="hidden" name="salaire_min" value="{{ $salaire_min }}">
                <input type="hidden" name="salaire_max" value="{{ $salaire_max }}">
                <span class="text-xs text-on-surface-variant font-medium">Trier par :</span>
                <select name="sort" onchange="this.form.submit()" class="text-sm font-semibold bg-white border border-outline-variant/30 rounded-xl px-4 py-2 focus:ring-secondary-container focus:border-secondary-container">
                  <option value="random" @selected($sort === 'random')>Pertinence</option>
                  <option value="latest" @selected($sort === 'latest')>Plus récentes</option>
                  <option value="salary_asc" @selected(in_array($sort, ['salary_asc', 'salaire_asc']))>Salaire (croissant)</option>
                  <option value="salary_desc" @selected(in_array($sort, ['salary_desc', 'salaire_desc']))>Salaire (décroissant)</option>
                </select>
              </form>
            </div>
          </div>

          <!-- Job Cards -->
          <div class="offres-jobs space-y-4">
            @forelse ($offres as $offre)
            <a href="{{ route('job_infos', $offre) }}" class="group flex flex-col sm:flex-row sm:items-center justify-between gap-5 p-5 bg-white rounded-2xl border border-white shadow-md hover:border-secondary-container/30 hover:shadow-lg transition-all duration-300 cursor-pointer relative overflow-hidden" aria-label="Voir les détails de l'offre {{ $offre->poste ?: $offre->titre }}">
              <div class="absolute inset-y-0 left-0 w-1 bg-secondary-container opacity-0 group-hover:opacity-100 transition-opacity"></div>
              <div class="flex items-center gap-4 relative z-10 min-w-0">
                <div class="w-12 h-12 rounded-xl bg-secondary-container/10 flex items-center justify-center group-hover:scale-105 transition-transform duration-300 flex-shrink-0 shadow-sm ring-1 ring-secondary-container/10">
                  <span class="material-symbols-outlined text-secondary-container text-xl">work</span>
                </div>
                <div class="min-w-0">
                  <h4 class="font-bold text-base md:text-lg text-primary group-hover:text-secondary-container transition-colors truncate">{{ $offre->poste ?: $offre->titre }}</h4>
                  <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-on-surface-variant/75 font-medium">
                    <span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-sm text-outline">business</span>{{ $offre->entreprise->company_name ?? 'Entreprise' }}</span>
                    <span class="inline-flex items-center gap-1"><span class="material-symbols-outlined text-sm text-outline">location_on</span>{{ $offre->localisation ?: 'Localisation à confirmer' }}</span>
                  </div>
                </div>
              </div>
              <div class="mt-2 sm:mt-0 flex items-center gap-2 relative z-10 sm:flex-shrink-0">
                <span class="px-2.5 py-1 bg-secondary-container/10 text-secondary-container text-[10px] font-bold uppercase tracking-wide rounded-full whitespace-nowrap">{{ $offre->type->nom ?? 'Offre' }}</span>
                <span class="text-xs font-bold text-primary/70 whitespace-nowrap">
                  @if ($offre->salaire_min || $offre->salaire_max)
                    {{ $offre->salaire_min ? floor($offre->salaire_min / 1000) . 'k' : '?' }}$ – {{ $offre->salaire_max ? floor($offre->salaire_max / 1000) . 'k' : '?' }}$
                  @else
                    Salaire à confirmer
                  @endif
                </span>
                <span class="text-[10px] text-outline whitespace-nowrap">{{ $offre->salary_type ?? '' }}</span>
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-secondary-container/20 bg-white text-secondary-container shadow-sm group-hover:bg-secondary-container/10 transition-colors" title="Voir les détails" aria-label="Voir les détails">
                  <span class="material-symbols-outlined text-base">visibility</span>
                </span>
              </div>
            </a>
            @empty
            <div class="rounded-2xl border border-dashed border-outline-variant/30 bg-surface-container-low p-6 text-sm text-on-surface-variant">
              Aucune offre ne correspond à vos critères pour le moment.
            </div>
            @endforelse
          </div>

          <!-- Pagination -->
          <div class="mt-10">
            {{ $offres->links('components.pagination.public-pagination') }}
          </div>

        </div>

      </div>
    </div>
  </main>
@endsection
@section('scripts')
  <script>
    (function() {
      const btn = document.getElementById('search-filter-toggle');
      const panel = document.getElementById('search-filter-panel');
      if (!btn || !panel) return;
      let open = false;
      btn.addEventListener('click', () => {
        open = !open;
        panel.classList.toggle('hidden', !open);
        btn.querySelector('.material-symbols-outlined').textContent = open ? 'close' : 'tune';
      });
    })();
  </script>
  <script>
    (function() {
      const ft = document.getElementById('filter-toggle');
      const aside = document.querySelector('aside');
      if (!ft || !aside) return;
      ft.addEventListener('click', () => {
        aside.classList.toggle('filters-open');
        const icon = ft.querySelector('.material-symbols-outlined');
        icon.textContent = aside.classList.contains('filters-open') ? 'close' : 'tune';
      });
    })();
  </script>
@endsection
