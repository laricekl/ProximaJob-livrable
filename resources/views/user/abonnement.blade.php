@extends('layouts.candidat')
@section('title', 'Mon abonnement')
@section('content')
  <main class="flex-grow pt-32">

    <section class="py-12 px-4 md:px-10">
      <div class="max-w-5xl mx-auto">

        <div class="mb-8">
          <p class="text-xs font-black uppercase tracking-[0.18em] text-secondary-container">Abonnement</p>
          <h1 class="mt-2 text-3xl font-bold font-serif text-primary">Mon abonnement</h1>
        </div>

        @if ($userAbonnement)
          {{-- Abonnement actif --}}
          <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
              <div class="card-glow rounded-2xl overflow-hidden border-l-4 border-l-secondary-container">
                <div class="p-6 md:p-8">
                  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div>
                      <span class="inline-flex px-3 py-1 bg-green-50 text-green-700 text-[10px] font-black uppercase tracking-widest rounded-full">Actif</span>
                      <h2 class="mt-2 text-xl font-bold text-primary">{{ $userAbonnement->abonnement->nom ?? 'Abonnement' }}</h2>
                    </div>
                    <div class="text-right">
                      <p class="text-3xl font-bold text-primary">{{ number_format($userAbonnement->abonnement->montant ?? 0, 2, ',', ' ') }} $</p>
                      <p class="text-xs text-outline">/{{ $userAbonnement->abonnement->duree ?? 'mois' }}</p>
                    </div>
                  </div>

                  <div class="grid grid-cols-2 gap-4 p-4 rounded-xl bg-surface-container-low/50">
                    <div>
                      <p class="text-[10px] font-bold uppercase tracking-wide text-outline">Debut</p>
                      <p class="text-sm font-semibold text-primary">{{ $userAbonnement->date_debut ? $userAbonnement->date_debut->translatedFormat('d M Y') : '—' }}</p>
                    </div>
                    <div>
                      <p class="text-[10px] font-bold uppercase tracking-wide text-outline">Expiration</p>
                      <p class="text-sm font-semibold text-primary">{{ $userAbonnement->date_fin ? \Carbon\Carbon::parse($userAbonnement->date_fin)->translatedFormat('d M Y') : '—' }}</p>
                    </div>
                    <div>
                      <p class="text-[10px] font-bold uppercase tracking-wide text-outline">Jours restants</p>
                      @php
                        $joursRestants = $userAbonnement->date_fin ? (int) ceil(now()->diffInDays($userAbonnement->date_fin, false)) : null;
                      @endphp
                      <p class="text-sm font-semibold {{ $joursRestants !== null && $joursRestants < 0 ? 'text-red-500' : ($joursRestants !== null && $joursRestants <= 7 ? 'text-amber-600' : 'text-primary') }}">
                        @if ($joursRestants === null) —
                        @elseif ($joursRestants < 0) Expiré depuis {{ abs($joursRestants) }} jours
                        @else {{ $joursRestants }} jours
                        @endif
                      </p>
                    </div>
                    <div>
                      <p class="text-[10px] font-bold uppercase tracking-wide text-outline">Statut</p>
                      <p class="text-sm font-semibold text-green-600">{{ $userAbonnement->status }}</p>
                    </div>
                  </div>

                  <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('plan.abonnement') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-colors">
                      <span class="material-symbols-outlined text-lg">upgrade</span> Changer de plan
                    </a>
                  </div>
                </div>
              </div>
            </div>

            <div class="space-y-4">
              <div class="card-glow rounded-2xl p-6">
                <h3 class="text-sm font-bold text-primary mb-4 flex items-center gap-2">
                  <span class="material-symbols-outlined text-secondary-container text-lg">star</span> Avantages inclus
                </h3>
                <ul class="space-y-3">
                  <li class="flex items-center gap-2 text-sm text-on-surface-variant">
                    <span class="material-symbols-outlined text-green-500 text-base">check_circle</span> CV personnalises par IA
                  </li>
                  <li class="flex items-center gap-2 text-sm text-on-surface-variant">
                    <span class="material-symbols-outlined text-green-500 text-base">check_circle</span> Matching automatique
                  </li>
                  <li class="flex items-center gap-2 text-sm text-on-surface-variant">
                    <span class="material-symbols-outlined text-green-500 text-base">check_circle</span> Candidatures illimitees
                  </li>
                  <li class="flex items-center gap-2 text-sm text-on-surface-variant">
                    <span class="material-symbols-outlined text-green-500 text-base">check_circle</span> Support prioritaire
                  </li>
                  <li class="flex items-center gap-2 text-sm text-on-surface-variant">
                    <span class="material-symbols-outlined text-green-500 text-base">check_circle</span> Statistiques de profil
                  </li>
                </ul>
              </div>

              <div class="rounded-2xl bg-gradient-to-br from-secondary-container/10 to-secondary-container/5 p-6">
                <p class="text-xs font-semibold text-secondary-container mb-2">Besoin d'aide ?</p>
                <p class="text-sm text-on-surface-variant mb-3">Notre equipe est disponible pour vos questions.</p>
                <a href="{{ route('contact') }}" class="text-sm font-bold text-secondary-container hover:underline">Contacter le support →</a>
              </div>
            </div>
          </div>
        @else
          {{-- Aucun abonnement actif --}}
          <div class="card-glow rounded-2xl overflow-hidden">
            <div class="p-8 md:p-12 text-center">
              <div class="w-16 h-16 mx-auto rounded-2xl bg-surface-container-low flex items-center justify-center mb-5">
                <span class="material-symbols-outlined text-3xl text-outline">workspace_premium</span>
              </div>
              <h2 class="text-xl font-bold font-serif text-primary mb-2">Aucun abonnement actif</h2>
              <p class="text-on-surface-variant max-w-md mx-auto mb-6">Souscrivez a un plan pour acceder aux CV personnalises par IA, au matching automatique et a toutes les fonctionnalites premium.</p>
              <a href="{{ route('plan.abonnement') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-colors shadow-lg shadow-secondary-container/20">
                <span class="material-symbols-outlined text-lg">rocket_launch</span> Voir les plans
              </a>
            </div>
          </div>

          <div class="mt-8 grid gap-4 sm:grid-cols-3">
            <div class="rounded-xl bg-white border border-outline-variant/10 p-5 text-center">
              <span class="material-symbols-outlined text-2xl text-secondary-container mb-2">auto_awesome</span>
              <p class="text-sm font-semibold text-primary">CV personnalises</p>
              <p class="text-xs text-on-surface-variant mt-1">Generes par IA pour chaque offre</p>
            </div>
            <div class="rounded-xl bg-white border border-outline-variant/10 p-5 text-center">
              <span class="material-symbols-outlined text-2xl text-secondary-container mb-2">psychology</span>
              <p class="text-sm font-semibold text-primary">Matching intelligent</p>
              <p class="text-xs text-on-surface-variant mt-1">Trouvez les offres faites pour vous</p>
            </div>
            <div class="rounded-xl bg-white border border-outline-variant/10 p-5 text-center">
              <span class="material-symbols-outlined text-2xl text-secondary-container mb-2">support_agent</span>
              <p class="text-sm font-semibold text-primary">Support prioritaire</p>
              <p class="text-xs text-on-surface-variant mt-1">Assistance rapide et dedicate</p>
            </div>
          </div>
        @endif

      </div>
    </section>

  </main>
@endsection
