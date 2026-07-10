@extends('layouts.entreprise')
@section('title', 'Abonnements')
@section('content')
  @php
    $activeSubscriptions = $userAbonnements->getCollection()->where('status', 'Actif')->count();
    $expiredSubscriptions = $userAbonnements->getCollection()->where('status', 'Expiré')->count();
  @endphp

  <div>
    <section class="py-8 px-4 md:px-10">
      <div class="max-w-7xl mx-auto">
        <div class="mb-8">
          <h1 class="text-2xl font-bold font-serif text-primary mb-2">Mes abonnements</h1>
          <p class="text-sm text-on-surface-variant">Consultez vos forfaits actifs, leur durée et l'historique associé à votre compte entreprise.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
          <div class="card-glow rounded-2xl p-6">
            <p class="text-2xl font-bold text-primary">{{ $userAbonnements->total() }}</p>
            <p class="text-sm text-on-surface-variant mt-1">Abonnements trouvés</p>
          </div>
          <div class="card-glow rounded-2xl p-6">
            <p class="text-2xl font-bold text-secondary-container">{{ $activeSubscriptions }}</p>
            <p class="text-sm text-on-surface-variant mt-1">Abonnements actifs</p>
          </div>
          <div class="card-glow rounded-2xl p-6">
            <p class="text-2xl font-bold text-primary">{{ $expiredSubscriptions }}</p>
            <p class="text-sm text-on-surface-variant mt-1">Abonnements expirés</p>
          </div>
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
              <h3 class="font-bold text-primary mb-2">Aucun abonnement enregistre</h3>
              <p class="text-sm text-on-surface-variant">Votre entreprise n'a pas encore de forfait actif ou archivé à afficher.</p>
            </div>
          @endforelse
        </div>

        <div class="flex justify-center items-center gap-3">
          {{ $userAbonnements->withQueryString()->links() }}
        </div>
      </div>
    </section>
  </div>
@endsection
