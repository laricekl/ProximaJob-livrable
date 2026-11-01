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

        @if ($enterprisePlan && $activeSubscriptions === 0)
          <div class="card-glow rounded-2xl p-6 mb-8 flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
            <div>
              <p class="text-xs font-black uppercase tracking-widest text-secondary-container">Forfait entreprise</p>
              <h2 class="mt-1 text-xl font-bold text-primary">{{ $enterprisePlan->nom }} — {{ $enterprisePlan->prix_formatte }}</h2>
              <p class="mt-1 text-sm text-on-surface-variant">{{ $enterprisePlan->description }}</p>
            </div>
            <form method="POST" action="{{ route('entreprise.abonnements.souscrire') }}">
              @csrf
              <input type="hidden" name="abonnement_id" value="{{ $enterprisePlan->id }}">
              <button class="inline-flex items-center gap-2 px-5 py-3 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-colors">
                <span class="material-symbols-outlined text-lg">workspace_premium</span>
                Demander l’activation
              </button>
            </form>
          </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
          @forelse ($userAbonnements as $userAbonnement)
            @php
              $plan = $userAbonnement->abonnement;
              $isActive = $userAbonnement->effective_status === 'Actif';
            @endphp
            <div class="card-glow rounded-2xl overflow-hidden border-l-4 {{ $isActive ? 'border-l-success' : 'border-l-outline-variant' }}">
              <div class="p-6">
                <div class="flex justify-between items-start mb-4 gap-3">
                  <span class="px-3 py-1 {{ $userAbonnement->status_color }} text-xs font-semibold rounded-full">{{ $userAbonnement->effective_status }}</span>
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

                @if ($userAbonnement->effective_status === 'En attente' && (float) ($plan?->montant ?? 0) > 0)
                  <form method="POST" action="{{ route('paiement.checkout', $userAbonnement) }}" class="mt-5">
                    @csrf
                    <button class="inline-flex w-full items-center justify-center gap-2 px-5 py-2.5 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-colors">
                      <span class="material-symbols-outlined text-lg">payments</span> Payer maintenant
                    </button>
                  </form>
                @endif
              </div>
            </div>
          @empty
            <div class="md:col-span-2 lg:col-span-3 card-glow rounded-2xl p-10 text-center">
              <span class="material-symbols-outlined text-4xl text-outline mb-3">credit_card_off</span>
              <h3 class="font-bold text-primary mb-2">Accès gratuit</h3>
              <p class="text-sm text-on-surface-variant">Aucun forfait payant n’est associé à ce compte. L’accès découverte permet d’évaluer la plateforme avant activation.</p>
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
