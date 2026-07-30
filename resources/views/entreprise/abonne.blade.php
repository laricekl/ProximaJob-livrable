@extends('layouts.entreprise')
@section('title', 'Abonnements')
@section('content')
  @php
    $activeSubscriptions = $subscriptionStats['active'] ?? 0;
    $expiredSubscriptions = $subscriptionStats['expired'] ?? 0;
  @endphp

  <div>
    <section class="py-8 px-4 md:px-10">
      <div class="max-w-7xl mx-auto">
        <div class="mb-8">
          <h1 class="text-2xl font-bold font-serif text-primary mb-2">Mes abonnements</h1>
          <p class="text-sm text-on-surface-variant">Consultez vos forfaits payants actifs et l’historique associé à votre compte entreprise.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
          <div class="card-glow rounded-2xl p-6">
            <p class="text-2xl font-bold text-primary">{{ $subscriptionStats['total'] ?? $userAbonnements->total() }}</p>
            <p class="text-sm text-on-surface-variant mt-1">Forfaits utilisés</p>
          </div>
          <div class="card-glow rounded-2xl p-6">
            <p class="text-2xl font-bold text-secondary-container">{{ $activeSubscriptions }}</p>
            <p class="text-sm text-on-surface-variant mt-1">Forfaits actifs</p>
          </div>
          <div class="card-glow rounded-2xl p-6">
            <p class="text-2xl font-bold text-primary">{{ $expiredSubscriptions }}</p>
            <p class="text-sm text-on-surface-variant mt-1">Forfaits expirés</p>
          </div>
        </div>

        <div class="flex justify-end mb-6">
          <a href="{{ route('abonnement') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-colors">
            <span class="material-symbols-outlined text-lg">workspace_premium</span>
            Voir les forfaits
          </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
          @forelse ($userAbonnements as $userAbonnement)
            @php
              $plan = $userAbonnement->abonnement;
              $isActive = $userAbonnement->status === 'Actif';
            @endphp
            <div class="card-glow rounded-2xl overflow-hidden border-l-4 {{ $isActive ? 'border-l-success' : 'border-l-outline-variant' }}">
              <div class="p-6">
                <div class="flex justify-between items-start mb-4 gap-3">
                  <span class="px-3 py-1 {{ $userAbonnement->status_color }} text-xs font-semibold rounded-full">{{ $userAbonnement->status }}</span>
                  <span class="text-xl font-bold text-primary">{{ $plan?->prix_formatte ?? 'Tarif indisponible' }}</span>
                </div>

                <h3 class="font-bold font-serif text-primary text-lg mb-3">{{ $plan?->nom ?? 'Abonnement' }}</h3>

                @if ($plan?->description)
                  <p class="mb-4 text-sm text-on-surface-variant">{{ $plan->description }}</p>
                @endif

                <div class="space-y-2 text-sm text-on-surface-variant">
                  <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">calendar_today</span> Active le {{ optional($userAbonnement->date_debut)->format('d/m/Y') ?? 'À confirmer' }}</div>
                  <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">event</span> Se termine le {{ optional($userAbonnement->date_fin)->format('d/m/Y') ?? 'À confirmer' }}</div>
                  <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">schedule</span> Durée {{ $plan?->periode_formattee ?? 'non définie' }}</div>
                </div>
              </div>
            </div>
          @empty
            <div class="md:col-span-2 lg:col-span-3 card-glow rounded-2xl p-10 text-center">
              <span class="material-symbols-outlined text-4xl text-outline mb-3">credit_card_off</span>
              <h3 class="font-bold text-primary mb-2">Accès gratuit</h3>
              <p class="text-sm text-on-surface-variant">Aucun forfait payant n’est associé à ce compte. Votre accès actuel reste gratuit.</p>
            </div>
          @endforelse
        </div>

        <div class="flex justify-center items-center gap-3">
          {{ $userAbonnements->withQueryString()->links('components.pagination.public-pagination') }}
        </div>
      </div>
    </section>
  </div>
@endsection
