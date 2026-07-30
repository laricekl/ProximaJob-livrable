@extends('layouts.guest')
@section('title', 'Entreprises partenaires')

@section('content')
<main class="flex-grow">
  <section class="px-4 pb-20 pt-8 md:px-10">
    <div class="mx-auto max-w-7xl">
      <div class="mx-auto mb-10 max-w-3xl text-center">
        <p class="text-xs font-black uppercase tracking-[0.18em] text-secondary-container">Entreprises partenaires</p>
        <h1 class="mt-3 text-3xl font-bold font-serif text-primary md:text-5xl">Découvrez les entreprises qui recrutent</h1>
        <p class="mt-4 text-sm leading-6 text-on-surface-variant md:text-base">
          Explorez les entreprises sélectionnées par ProximaJob, consultez leur présentation et découvrez leurs offres actives.
        </p>
      </div>

      @if ($entreprises->isEmpty())
        <div class="card-glow rounded-2xl px-6 py-16 text-center">
          <span class="material-symbols-outlined text-5xl text-outline">domain_disabled</span>
          <h2 class="mt-4 text-xl font-bold font-serif text-primary">Aucune entreprise à afficher</h2>
          <p class="mt-2 text-sm text-on-surface-variant">Revenez bientôt pour découvrir nos nouveaux partenaires.</p>
        </div>
      @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          @foreach ($entreprises as $entreprise)
            @php
              $logoUrl = $entreprise->logo ? asset($entreprise->logo) : null;
              $initials = strtoupper(substr($entreprise->company_name, 0, 2));
            @endphp
            <a href="{{ route('entreprise.profil', $entreprise) }}" class="card-glow group flex h-full flex-col rounded-2xl p-6 transition-all hover:-translate-y-1 hover:shadow-xl">
              <div class="flex items-start gap-4">
                @if ($logoUrl)
                  <img src="{{ $logoUrl }}" alt="" class="h-16 w-16 shrink-0 rounded-2xl border border-outline-variant/10 object-cover">
                @else
                  <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-primary-container text-lg font-bold text-white">
                    {{ $initials }}
                  </div>
                @endif
                <div class="min-w-0 flex-1">
                  <div class="flex items-start gap-2">
                    <h2 class="text-lg font-bold font-serif leading-tight text-primary transition-colors group-hover:text-secondary-container">{{ $entreprise->company_name }}</h2>
                    <span class="material-symbols-outlined mt-0.5 shrink-0 text-base text-success">verified</span>
                  </div>
                  <p class="mt-1 text-xs font-semibold text-secondary-container">
                    {{ $entreprise->active_offers_count }} offre{{ $entreprise->active_offers_count > 1 ? 's' : '' }} active{{ $entreprise->active_offers_count > 1 ? 's' : '' }}
                  </p>
                </div>
              </div>

              <p class="mt-5 line-clamp-4 flex-1 text-sm leading-6 text-on-surface-variant">
                {{ $entreprise->description ?: 'Découvrez cette entreprise et les opportunités professionnelles qu’elle propose.' }}
              </p>

              <span class="mt-6 inline-flex items-center gap-2 text-sm font-bold text-secondary-container">
                Voir l’entreprise
                <span class="material-symbols-outlined text-lg transition-transform group-hover:translate-x-1">arrow_forward</span>
              </span>
            </a>
          @endforeach
        </div>

        @if ($entreprises->hasPages())
          <div class="mt-10">
            {{ $entreprises->links('components.pagination.public-pagination') }}
          </div>
        @endif
      @endif
    </div>
  </section>
</main>
@endsection
