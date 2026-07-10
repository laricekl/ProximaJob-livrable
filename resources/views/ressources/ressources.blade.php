@extends('layouts.guest')
@section('title', 'Ressources')
@section('styles')
  <style>
    .premium-card {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .premium-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
    }
    .resource-item--outline {
      border-color: rgba(var(--pj-accent-rgb), 0.24) !important;
    }
    .resource-item--outline:hover {
      border-color: rgba(var(--pj-accent-rgb), 0.72) !important;
    }
    .resource-cta-outline {
      border-color: rgba(var(--pj-accent-rgb), 0.34) !important;
      color: var(--pj-accent);
    }
    .resource-cta-outline:hover {
      background: var(--pj-accent);
      color: #fff;
    }
    input:focus { outline: none; }

    @media (max-width: 767px) {

      /* Hero */
      section.py-20 { padding-top: 2rem; padding-bottom: 2rem; }
      .text-5xl, .text-6xl { font-size: 1.6rem !important; }

      /* Filter section */
      .py-20.px-4.bg-white { padding-top: 1.25rem; padding-bottom: 1.5rem; }
      .flex-wrap.justify-center.gap-3.mb-10 { gap: 6px; margin-bottom: 1rem; }
      .filter-tab { padding: 8px 14px !important; font-size: 11px !important; border-radius: 9999px; }
      .filter-tab .material-symbols-outlined { font-size: 13px !important; }

      /* Search bar */
      .relative.max-w-xl.mx-auto.mb-16 { margin-bottom: 1.25rem; }
      .relative.max-w-xl.mx-auto.mb-16 input { padding: 10px 10px 10px 40px !important; font-size: 12px !important; }
      .relative.max-w-xl.mx-auto.mb-16 .material-symbols-outlined { font-size: 18px !important; left: 12px; }

      /* Cards grid */
      .grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-3.gap-6 { grid-template-columns: 1fr !important; gap: 8px; }
      .resource-item { padding: 12px !important; border-radius: 12px; }
      .resource-item .mb-4 { margin-bottom: 8px; }
      .resource-item .text-lg { font-size: 14px !important; }
      .resource-item .text-sm.leading-relaxed { font-size: 11px !important; margin-bottom: 10px; }
      .resource-item .text-\\[10px\\] { font-size: 8px !important; }
      .resource-item .text-xs { font-size: 9px !important; }
      .resource-item a.inline-flex { font-size: 11px !important; padding: 6px 14px !important; }

      /* Footer */
      footer nav.gap-8 { gap: 12px; }
      footer .text-\\[11px\\] { font-size: 9px; }
      footer .text-sm { font-size: 10px !important; }
    }
  </style>
@endsection
@section('content')
  <main class="flex-grow pt-32" style="background: linear-gradient(180deg, rgba(176, 177, 192, 0.22) 0%, rgba(240, 242, 245, 0.36) 100%), radial-gradient(at 10% 8%, rgba(235, 132, 60, 0.055) 0, transparent 38%), radial-gradient(at 90% 88%, rgba(36, 98, 183, 0.035) 0, transparent 40%), #f7f9fb;">

    <x-public-page-hero
      title="Ressources"
      subtitle="Guides, documents et contenus utiles pour mieux avancer dans votre recherche d'emploi ou votre recrutement."
    />

    <!-- Filtres + Recherche -->
    <section class="py-20 px-4 md:px-10">
      <div class="max-w-6xl mx-auto">

        <!-- Tabs -->
        <div class="flex flex-wrap justify-center gap-3 mb-10">
          <button class="filter-tab active px-6 py-3 rounded-full text-sm font-semibold bg-primary text-white transition-all" data-filter="all">
            <span class="material-symbols-outlined text-sm align-middle mr-1">grid_view</span> Toutes les ressources
          </button>
          <button class="filter-tab px-6 py-3 rounded-full text-sm font-semibold bg-surface-container text-on-surface-variant hover:bg-surface-container-low transition-all" data-filter="document">
            <span class="material-symbols-outlined text-sm align-middle mr-1">description</span> Documents
          </button>
          <button class="filter-tab px-6 py-3 rounded-full text-sm font-semibold bg-surface-container text-on-surface-variant hover:bg-surface-container-low transition-all" data-filter="video">
            <span class="material-symbols-outlined text-sm align-middle mr-1">play_circle</span> Vidéos
          </button>
          <button class="filter-tab px-6 py-3 rounded-full text-sm font-semibold bg-surface-container text-on-surface-variant hover:bg-surface-container-low transition-all" data-filter="lien">
            <span class="material-symbols-outlined text-sm align-middle mr-1">link</span> Liens utiles
          </button>
        </div>

        <!-- Barre de recherche -->
        <div class="relative max-w-xl mx-auto mb-16">
          <input type="text" id="searchInput" placeholder="Rechercher une ressource..."
            class="focus-accent-field w-full px-6 py-4 pl-14 bg-white border border-outline-variant/50 rounded-full text-sm text-primary placeholder:text-outline transition-all" />
          <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-outline">search</span>
        </div>

        <!-- Grille de ressources -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="resourcesGrid">

          <!-- Ressource 1 - Document -->
          <div class="resource-item resource-item--outline bg-white rounded-2xl p-6 shadow-md border border-secondary-container/20 hover:border-secondary-container premium-card flex flex-col items-center text-center" data-type="document" data-title="Guide de rédaction de CV" data-description="Apprenez à créer un CV qui attire l'attention des employeurs">
            <div class="flex flex-wrap items-center justify-center gap-3 mb-4">
              <span class="px-3 py-1 bg-secondary-container/10 text-secondary-container text-2xs font-bold uppercase tracking-wide rounded-full flex items-center gap-1">
                <span class="material-symbols-outlined text-xs">description</span> Document
              </span>
              <span class="text-xs text-outline">Ajouté le 12 Mai 2025</span>
            </div>
            <h3 class="font-bold text-lg text-primary mb-2">Guide de rédaction de CV</h3>
            <p class="text-on-surface-variant text-sm leading-relaxed mb-6 flex-grow">Apprenez à créer un CV qui attire l'attention des employeurs et maximise vos chances d'être sélectionné.</p>
            <a href="{{ route('register') }}" class="inline-flex resource-cta-outline w-full items-center justify-center gap-2 bg-white border font-bold px-6 py-2.5 rounded-xl transition-all text-sm">
              <span class="material-symbols-outlined text-lg">download</span> Télécharger
            </a>
          </div>

          <!-- Ressource 2 - Vidéo -->
          <div class="resource-item resource-item--outline bg-white rounded-2xl p-6 shadow-md border border-secondary-container/20 hover:border-secondary-container premium-card flex flex-col items-center text-center" data-type="video" data-title="Préparer son entretien d'embauche" data-description="Les conseils pour réussir votre entretien et décrocher le poste">
            <div class="flex flex-wrap items-center justify-center gap-3 mb-4">
              <span class="px-3 py-1 bg-secondary-container/10 text-secondary-container text-2xs font-bold uppercase tracking-wide rounded-full flex items-center gap-1">
                <span class="material-symbols-outlined text-xs">play_circle</span> Vidéo
              </span>
              <span class="text-xs text-outline">Ajouté le 10 Mai 2025</span>
            </div>
            <h3 class="font-bold text-lg text-primary mb-2">Préparer son entretien d'embauche</h3>
            <p class="text-on-surface-variant text-sm leading-relaxed mb-6 flex-grow">Les conseils essentiels pour réussir votre entretien et décrocher le poste de vos rêves.</p>
            <a href="{{ route('contact') }}" class="inline-flex resource-cta-outline w-full items-center justify-center gap-2 bg-white border font-bold px-6 py-2.5 rounded-xl transition-all text-sm">
              <span class="material-symbols-outlined text-lg">play_arrow</span> Regarder
            </a>
          </div>

          <!-- Ressource 3 - Lien -->
          <div class="resource-item resource-item--outline bg-white rounded-2xl p-6 shadow-md border border-secondary-container/20 hover:border-secondary-container premium-card flex flex-col items-center text-center" data-type="lien" data-title="Portail officiel de l'emploi" data-description="Accédez au portail gouvernemental pour plus d'opportunités">
            <div class="flex flex-wrap items-center justify-center gap-3 mb-4">
              <span class="px-3 py-1 bg-secondary-container/10 text-secondary-container text-2xs font-bold uppercase tracking-wide rounded-full flex items-center gap-1">
                <span class="material-symbols-outlined text-xs">link</span> Lien utile
              </span>
              <span class="text-xs text-outline">Ajouté le 08 Mai 2025</span>
            </div>
            <h3 class="font-bold text-lg text-primary mb-2">Portail officiel de l'emploi</h3>
            <p class="text-on-surface-variant text-sm leading-relaxed mb-6 flex-grow">Accédez au portail gouvernemental pour plus d'opportunités et de ressources.</p>
            <a href="{{ route('offres') }}" class="inline-flex resource-cta-outline w-full items-center justify-center gap-2 bg-white border font-bold px-6 py-2.5 rounded-xl transition-all text-sm">
              <span class="material-symbols-outlined text-lg">open_in_new</span> Visiter
            </a>
          </div>

          <!-- Ressource 4 - Document -->
          <div class="resource-item resource-item--outline bg-white rounded-2xl p-6 shadow-md border border-secondary-container/20 hover:border-secondary-container premium-card flex flex-col items-center text-center" data-type="document" data-title="Modèle de lettre de présentation" data-description="Des modèles prêts à l'emploi pour votre candidature">
            <div class="flex flex-wrap items-center justify-center gap-3 mb-4">
              <span class="px-3 py-1 bg-secondary-container/10 text-secondary-container text-2xs font-bold uppercase tracking-wide rounded-full flex items-center gap-1">
                <span class="material-symbols-outlined text-xs">description</span> Document
              </span>
              <span class="text-xs text-outline">Ajouté le 05 Mai 2025</span>
            </div>
            <h3 class="font-bold text-lg text-primary mb-2">Modèle de lettre de présentation</h3>
            <p class="text-on-surface-variant text-sm leading-relaxed mb-6 flex-grow">Des modèles prêts à l'emploi pour accompagner votre CV et renforcer votre candidature.</p>
            <a href="{{ route('register') }}" class="inline-flex resource-cta-outline w-full items-center justify-center gap-2 bg-white border font-bold px-6 py-2.5 rounded-xl transition-all text-sm">
              <span class="material-symbols-outlined text-lg">download</span> Télécharger
            </a>
          </div>

          <!-- Ressource 5 - Vidéo -->
          <div class="resource-item resource-item--outline bg-white rounded-2xl p-6 shadow-md border border-secondary-container/20 hover:border-secondary-container premium-card flex flex-col items-center text-center" data-type="video" data-title="Optimiser son profil LinkedIn" data-description="Comment mettre en valeur votre profil professionnel en ligne">
            <div class="flex flex-wrap items-center justify-center gap-3 mb-4">
              <span class="px-3 py-1 bg-secondary-container/10 text-secondary-container text-2xs font-bold uppercase tracking-wide rounded-full flex items-center gap-1">
                <span class="material-symbols-outlined text-xs">play_circle</span> Vidéo
              </span>
              <span class="text-xs text-outline">Ajouté le 02 Mai 2025</span>
            </div>
            <h3 class="font-bold text-lg text-primary mb-2">Optimiser son profil LinkedIn</h3>
            <p class="text-on-surface-variant text-sm leading-relaxed mb-6 flex-grow">Comment mettre en valeur votre profil professionnel en ligne pour attirer les employeurs.</p>
            <a href="{{ route('contact') }}" class="inline-flex resource-cta-outline w-full items-center justify-center gap-2 bg-white border font-bold px-6 py-2.5 rounded-xl transition-all text-sm">
              <span class="material-symbols-outlined text-lg">play_arrow</span> Regarder
            </a>
          </div>

          <!-- Ressource 6 - Lien -->
          <div class="resource-item resource-item--outline bg-white rounded-2xl p-6 shadow-md border border-secondary-container/20 hover:border-secondary-container premium-card flex flex-col items-center text-center" data-type="lien" data-title="Calculateur de salaire" data-description="Estimez votre salaire selon votre secteur et expérience">
            <div class="flex flex-wrap items-center justify-center gap-3 mb-4">
              <span class="px-3 py-1 bg-secondary-container/10 text-secondary-container text-2xs font-bold uppercase tracking-wide rounded-full flex items-center gap-1">
                <span class="material-symbols-outlined text-xs">link</span> Lien utile
              </span>
              <span class="text-xs text-outline">Ajouté le 28 Avr 2025</span>
            </div>
            <h3 class="font-bold text-lg text-primary mb-2">Calculateur de salaire</h3>
            <p class="text-on-surface-variant text-sm leading-relaxed mb-6 flex-grow">Estimez votre salaire selon votre secteur d'activité et votre niveau d'expérience.</p>
            <a href="{{ route('abonnement') }}" class="inline-flex resource-cta-outline w-full items-center justify-center gap-2 bg-white border font-bold px-6 py-2.5 rounded-xl transition-all text-sm">
              <span class="material-symbols-outlined text-lg">open_in_new</span> Visiter
            </a>
          </div>

        </div>

        <!-- Message aucun résultat -->
        <div id="noResults" class="text-center py-16 hidden">
          <span class="material-symbols-outlined text-5xl text-outline mb-4">search_off</span>
          <p class="text-on-surface-variant font-medium">Aucune ressource ne correspond à votre recherche.</p>
        </div>
      </div>
    </section>

    <!-- Statistiques -->
    <section class="py-20 px-4 md:px-10 bg-white/55">
      <div class="max-w-4xl mx-auto">
        <div class="card-glow p-10 md:p-16 text-center">
          <h2 class="text-3xl md:text-4xl font-bold font-serif text-primary leading-tight mb-12">Nos ressources en chiffres</h2>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-10">
            <div class="stat-item">
              <div class="text-5xl font-bold text-secondary-container mb-2">24</div>
              <div class="text-on-surface-variant font-medium">Documents disponibles</div>
            </div>
            <div class="stat-item">
              <div class="text-5xl font-bold text-secondary-container mb-2">18</div>
              <div class="text-on-surface-variant font-medium">Vidéos tutorielles</div>
            </div>
            <div class="stat-item">
              <div class="text-5xl font-bold text-secondary-container mb-2">12</div>
              <div class="text-on-surface-variant font-medium">Liens utiles</div>
            </div>
          </div>
        </div>
      </div>
    </section>

  </main>
@endsection
