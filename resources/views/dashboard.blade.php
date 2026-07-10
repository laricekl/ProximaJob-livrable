@extends('layouts.candidat')
@section('title', 'Dashboard')
@section('content')
  <main class="flex-grow pt-32 bg-white">

    <!-- Hero Dashboard -->
    <section class="py-12 px-4 md:px-10 bg-white">
      <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
          <div>
            <h1 class="text-3xl md:text-4xl font-bold font-serif text-primary leading-tight">Bonjour, {{ auth()->user()->prenom ?? auth()->user()->name ?? 'candidat' }}</h1>
            <p class="text-on-surface-variant mt-2">Bienvenue sur votre espace ProximaJob</p>
          </div>
          <a href="{{ route('offres') }}" class="inline-flex items-center gap-2 bg-secondary-container text-white font-bold px-6 py-3 rounded-xl hover:bg-secondary transition-all shadow-lg shadow-secondary-container/20">
            <span class="material-symbols-outlined text-lg">search</span> Chercher des offres
          </a>
        </div>
      </div>
    </section>

    <!-- Stats rapides -->
    <section class="py-12 px-4 md:px-10 bg-surface-container-low/50">
      <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

          <div class="card-glow rounded-2xl p-6">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-xl bg-tertiary-fixed flex items-center justify-center">
                <span class="material-symbols-outlined text-on-tertiary-fixed">description</span>
              </div>
              <div>
                <div class="text-2xl font-bold text-primary">{{ $dashboardStats['applications_count'] }}</div>
                <div class="text-xs text-on-surface-variant">Candidatures envoyées</div>
              </div>
            </div>
          </div>

          <div class="card-glow rounded-2xl p-6">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-xl bg-secondary-container/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-secondary-container">check_circle</span>
              </div>
              <div>
                <div class="text-2xl font-bold text-primary">{{ $dashboardStats['interviews_count'] }}</div>
                <div class="text-xs text-on-surface-variant">Entretiens obtenus</div>
              </div>
            </div>
          </div>

          <div class="card-glow rounded-2xl p-6">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-xl bg-secondary-fixed flex items-center justify-center">
                <span class="material-symbols-outlined text-on-secondary-fixed-variant">auto_awesome</span>
              </div>
              <div>
                <div class="text-2xl font-bold text-primary">{{ $dashboardStats['auto_applications_count'] }}</div>
                <div class="text-xs text-on-surface-variant">CV personnalisés par IA</div>
              </div>
            </div>
          </div>

          <div class="card-glow rounded-2xl p-6">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-xl bg-secondary-container/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-secondary-container">visibility</span>
              </div>
              <div>
                <div class="text-2xl font-bold text-primary">{{ $dashboardStats['unread_notifications_count'] }}</div>
                <div class="text-xs text-on-surface-variant">Notifications non lues</div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- Dernières offres + Activité récente -->
    <section class="py-12 px-4 md:px-10 bg-white">
      <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Offres recommandées -->
        <div class="lg:col-span-2">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold font-serif text-primary">Offres recommandées</h2>
            <a href="{{ route('offres') }}" class="text-sm font-semibold text-secondary-container hover:underline flex items-center gap-1">
              Voir tout <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
          </div>
          <div class="space-y-4">
            @forelse ($recommendedOffres as $offre)
            <a href="{{ route('job_infos', $offre) }}" class="group flex flex-col sm:flex-row sm:items-center justify-between p-5 bg-white rounded-2xl border border-primary/5 hover:border-secondary-container/30 hover:shadow-[0_15px_40px_rgba(0,0,0,0.04)] transition-all duration-500 cursor-pointer relative overflow-hidden">
              <div class="absolute inset-0 bg-gradient-to-r from-secondary-container/0 to-secondary-container/[0.02] opacity-0 group-hover:opacity-100 transition-opacity"></div>
              <div class="flex items-center gap-4 relative z-10">
                <div class="w-12 h-12 rounded-xl bg-secondary-container/10 flex items-center justify-center group-hover:scale-110 transition-transform duration-500">
                  <span class="material-symbols-outlined text-secondary-container text-xl">code</span>
                </div>
                <div>
                  <h4 class="font-bold text-primary group-hover:text-secondary-container transition-colors">{{ $offre->poste ?: $offre->titre }}</h4>
                  <p class="text-on-surface-variant/70 text-xs font-medium">{{ $offre->entreprise->company_name ?? 'Entreprise' }} • {{ $offre->localisation ?: 'Localisation a confirmer' }}</p>
                </div>
              </div>
              <div class="mt-3 sm:mt-0 flex items-center gap-3 relative z-10">
                <span class="px-3 py-1 bg-success-light text-success-dark text-2xs font-black uppercase tracking-widest rounded-full">{{ $offre->type->nom ?? 'Offre' }}</span>
                <span class="text-secondary-container font-bold text-sm">
                  @if ($offre->salaire_min || $offre->salaire_max)
                    {{ $offre->salaire_min ? number_format((float) $offre->salaire_min, 0, ',', ' ') : '?' }} - {{ $offre->salaire_max ? number_format((float) $offre->salaire_max, 0, ',', ' ') : '?' }}$
                  @else
                    Salaire a confirmer
                  @endif
                </span>
              </div>
            </a>
            @empty
            <div class="rounded-2xl border border-dashed border-outline-variant/30 bg-surface-container-low p-6 text-sm text-on-surface-variant">
              Aucune offre active n'est disponible pour le moment.
            </div>
            @endforelse
          </div>
        </div>

        <!-- Activité récente -->
        <div>
          <h2 class="text-2xl font-bold font-serif text-primary mb-6">Activité récente</h2>
          <div class="card-glow rounded-2xl p-6">
            <div class="space-y-5">
              @forelse ($recentApplications as $application)
                <div class="flex gap-3">
                  <div class="w-8 h-8 rounded-full bg-secondary-container/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <span class="material-symbols-outlined text-secondary-container text-sm">{{ $application->autopostulation ? 'auto_awesome' : 'send' }}</span>
                  </div>
                  <div>
                    <p class="text-sm font-semibold text-primary">{{ $application->autopostulation ? 'Candidature IA envoyée' : 'Candidature envoyée' }}</p>
                    <p class="text-xs text-on-surface-variant">{{ $application->offre->poste ?? $application->offre->titre ?? 'Offre' }} chez {{ $application->offre->entreprise->company_name ?? 'Entreprise' }}</p>
                    <div class="mt-1 flex items-center gap-2">
                      <p class="text-2xs text-outline">{{ optional($application->created_at)->diffForHumans() }}</p>
                      <x-application-status :status="$application->status" />
                    </div>
                  </div>
                </div>
              @empty
                <div class="text-sm text-on-surface-variant">
                  Aucune activité récente pour le moment.
                </div>
              @endforelse
            </div>
          </div>
        </div>

      </div>
    </section>

  </main>
@endsection
