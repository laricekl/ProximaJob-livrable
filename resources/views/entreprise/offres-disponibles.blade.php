@extends('layouts.entreprise')

@section('title', 'Tableau de bord')

@section('content')
  @php
    $enterpriseName = auth()->user()->entreprise->company_name ?? auth()->user()->name;
    $statusLabels = ['active' => 'Active', 'closed' => 'Fermée', 'draft' => 'Brouillon', 'inactive' => 'Inactive'];
    $statusStyles = [
      'active' => 'bg-success-light text-success-dark',
      'closed' => 'bg-surface-container text-on-surface-variant',
      'draft' => 'bg-secondary-container/10 text-secondary-container',
      'inactive' => 'bg-error-light text-error-dark',
    ];
  @endphp

  <section class="px-4 md:px-10 py-8">
    <div class="max-w-7xl mx-auto">
      <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-8">
        <div>
          <p class="text-xs font-black uppercase tracking-[0.2em] text-secondary-container mb-2">Espace entreprise</p>
          <h1 class="text-3xl md:text-4xl font-bold font-serif text-primary">Bonjour, {{ $enterpriseName }}</h1>
          <p class="mt-2 text-on-surface-variant max-w-2xl">Pilotez vos offres et traitez les candidatures depuis un seul espace.</p>
        </div>
        @if ($canCreateOffer)
          <a href="{{ route('entreprise.offres.create') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-colors shadow-lg shadow-secondary-container/20">
            <span class="material-symbols-outlined">add</span>
            Publier une offre
          </a>
        @else
          <a href="{{ route('entreprise.abonnements') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 border border-secondary-container/30 text-secondary-container text-sm font-bold rounded-xl hover:bg-secondary-container/5 transition-colors">
            <span class="material-symbols-outlined">workspace_premium</span>
            Débloquer les offres illimitées
          </a>
        @endif
      </div>

      @if (session('success'))
        <div class="mb-6 rounded-2xl border border-success-light bg-success-light px-5 py-4 text-sm text-success-dark">{{ session('success') }}</div>
      @endif

      @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-error-light bg-error-light px-5 py-4 text-sm text-error-dark">{{ $errors->first() }}</div>
      @endif

      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
        @foreach ([
          ['label' => 'Offres actives', 'value' => $stats['active_offers'] ?? 0, 'icon' => 'work', 'tone' => 'bg-primary-container/10 text-primary-container', 'url' => '#vos-offres'],
          ['label' => 'À examiner', 'value' => $stats['pending_applications'] ?? 0, 'icon' => 'inbox', 'tone' => 'bg-secondary-container/10 text-secondary-container', 'url' => route('entreprise.candidatures.index', ['status' => 'en_attente'])],
          ['label' => 'Candidatures', 'value' => $stats['total_applications'] ?? 0, 'icon' => 'groups', 'tone' => 'bg-tertiary-container/30 text-primary', 'url' => route('entreprise.candidatures.index')],
          ['label' => 'Acceptées', 'value' => $stats['accepted_applications'] ?? 0, 'icon' => 'task_alt', 'tone' => 'bg-success-light text-success-dark', 'url' => route('entreprise.candidatures.index', ['status' => 'accepted'])],
        ] as $stat)
          @if ($stat['url'])
            <a href="{{ $stat['url'] }}" class="card-glow rounded-2xl p-5 md:p-6 transition-transform hover:-translate-y-0.5">
          @else
            <div class="card-glow rounded-2xl p-5 md:p-6">
          @endif
            <div class="w-11 h-11 rounded-xl {{ $stat['tone'] }} flex items-center justify-center mb-4">
              <span class="material-symbols-outlined">{{ $stat['icon'] }}</span>
            </div>
            <p class="text-3xl font-bold text-primary">{{ $stat['value'] }}</p>
            <p class="mt-1 text-sm text-on-surface-variant">{{ $stat['label'] }}</p>
          @if ($stat['url'])
            </a>
          @else
            </div>
          @endif
        @endforeach
      </div>

      <div id="vos-offres" class="flex scroll-mt-32 flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
          <h2 class="text-2xl font-bold font-serif text-primary">Vos offres</h2>
          <p class="text-sm text-on-surface-variant mt-1">{{ $stats['total_offers'] ?? $offres->total() }} offre{{ ($stats['total_offers'] ?? $offres->total()) > 1 ? 's' : '' }} au total</p>
        </div>
        <form method="GET" action="{{ route('offres.publies') }}" data-testid="enterprise-offers-search-form" class="relative w-full md:w-96">
          <label for="offer-search" class="sr-only">Rechercher une offre</label>
          <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">search</span>
          <input id="offer-search" type="search" name="search" value="{{ request('search') }}" class="w-full pl-11 pr-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary placeholder:text-outline focus:border-secondary-container/50 focus:ring-0" placeholder="Titre, lieu ou mot-clé…">
        </form>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 mb-10">
        @forelse ($offres as $offre)
          @php
            $applications = $offre->postulations_count ?? 0;
            $salaryParts = array_filter([$offre->salaire_min, $offre->salaire_max]);
            $salary = count($salaryParts)
              ? collect($salaryParts)->map(fn ($value) => number_format((float) $value, 0, ',', ' ') . ' $')->join(' – ')
              : 'Salaire à confirmer';
            $status = $offre->status ?? 'active';
          @endphp
          <article class="card-glow rounded-2xl p-6 flex flex-col min-h-[330px]">
            <div class="flex items-start justify-between gap-4 mb-4">
              <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusStyles[$status] ?? 'bg-surface-container text-primary' }}">
                {{ $statusLabels[$status] ?? ucfirst($status) }}
              </span>
              <span class="px-3 py-1 bg-secondary-container/10 text-secondary-container text-xs font-bold rounded-full">
                {{ $offre->type?->nom ?? 'Offre' }}
              </span>
            </div>

            <h3 class="text-xl font-bold font-serif text-primary leading-snug">{{ $offre->titre }}</h3>
            <p class="mt-3 text-sm text-on-surface-variant line-clamp-2">{{ \Illuminate\Support\Str::limit(strip_tags($offre->description ?? $offre->missions ?? ''), 130) }}</p>

            <div class="mt-5 space-y-2.5 text-sm text-on-surface-variant">
              <p class="flex items-center gap-2"><span class="material-symbols-outlined text-lg text-outline">location_on</span>{{ $offre->localisation ?? 'Lieu à confirmer' }}</p>
              <p class="flex items-center gap-2"><span class="material-symbols-outlined text-lg text-outline">payments</span>{{ $salary }}</p>
              <p class="flex items-center gap-2"><span class="material-symbols-outlined text-lg text-outline">calendar_today</span>Publiée le {{ optional($offre->created_at)->format('d/m/Y') }}</p>
            </div>

            <div class="mt-auto pt-5">
              <a href="{{ route('entreprise.offres.candidatures', $offre) }}" class="flex items-center justify-between rounded-xl bg-primary-container px-4 py-3 text-white hover:bg-primary transition-colors">
                <span class="font-bold text-sm">Voir les candidatures</span>
                <span class="inline-flex items-center gap-2">
                  <span class="min-w-7 h-7 px-2 rounded-full bg-white/15 flex items-center justify-center text-xs font-bold">{{ $applications }}</span>
                  <span class="material-symbols-outlined text-lg">arrow_forward</span>
                </span>
              </a>
              <div class="flex items-center justify-end gap-1 mt-3">
                <a href="{{ route('edit.offres', $offre->id) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-on-surface-variant hover:text-primary hover:bg-surface-container-low transition-colors">
                  <span class="material-symbols-outlined text-base">edit</span> Modifier
                </a>
                <button type="button" data-delete-offer data-action="{{ route('offres.destroy', $offre->id) }}" data-title="{{ $offre->titre }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-on-surface-variant hover:text-error hover:bg-error-light transition-colors">
                  <span class="material-symbols-outlined text-base">delete</span> Supprimer
                </button>
              </div>
            </div>
          </article>
        @empty
          <div class="md:col-span-2 xl:col-span-3 card-glow rounded-2xl p-12 text-center">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-secondary-container/10 text-secondary-container flex items-center justify-center mb-5">
              <span class="material-symbols-outlined text-3xl">work_off</span>
            </div>
            <h3 class="text-xl font-bold font-serif text-primary">{{ request('search') ? 'Aucune offre trouvée' : 'Publiez votre première offre' }}</h3>
            <p class="mt-2 text-sm text-on-surface-variant max-w-md mx-auto">{{ request('search') ? 'Essayez un autre terme de recherche.' : 'Présentez votre besoin et commencez à recevoir des candidatures qualifiées.' }}</p>
            @if (!request('search') && $canCreateOffer)
              <a href="{{ route('entreprise.offres.create') }}" class="mt-6 inline-flex items-center gap-2 px-5 py-3 bg-secondary-container text-white rounded-xl text-sm font-bold hover:bg-secondary transition-colors"><span class="material-symbols-outlined">add</span>Créer une offre</a>
            @endif
          </div>
        @endforelse
      </div>

      <div class="flex justify-center">{{ $offres->withQueryString()->links('components.pagination.public-pagination') }}</div>
    </div>
  </section>

  <div id="deleteOfferModal" class="fixed inset-0 z-[80] hidden items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="deleteOfferTitle">
    <div class="absolute inset-0 bg-primary/50 backdrop-blur-sm" data-close-delete></div>
    <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl p-7">
      <div class="w-12 h-12 rounded-2xl bg-error-light text-error flex items-center justify-center mb-5"><span class="material-symbols-outlined">delete</span></div>
      <h2 id="deleteOfferTitle" class="text-xl font-bold font-serif text-primary">Supprimer cette offre ?</h2>
      <p class="mt-2 text-sm text-on-surface-variant">L’offre <strong id="deleteOfferName" class="text-primary"></strong> et ses candidatures associées seront supprimées.</p>
      <form id="deleteOfferForm" method="POST" class="mt-7 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
        @csrf
        @method('DELETE')
        <button type="button" data-close-delete class="px-5 py-3 bg-surface-container text-primary text-sm font-bold rounded-xl hover:bg-surface-container-low">Annuler</button>
        <button type="submit" class="px-5 py-3 bg-error text-white text-sm font-bold rounded-xl hover:opacity-90">Supprimer définitivement</button>
      </form>
    </div>
  </div>
@endsection

@section('scripts')
  <script>
    (() => {
      const modal = document.getElementById('deleteOfferModal');
      const form = document.getElementById('deleteOfferForm');
      const name = document.getElementById('deleteOfferName');
      const close = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
      };

      document.querySelectorAll('[data-delete-offer]').forEach((button) => {
        button.addEventListener('click', () => {
          form.action = button.dataset.action;
          name.textContent = button.dataset.title;
          modal.classList.remove('hidden');
          modal.classList.add('flex');
          document.body.classList.add('overflow-hidden');
        });
      });
      document.querySelectorAll('[data-close-delete]').forEach((button) => button.addEventListener('click', close));
      document.addEventListener('keydown', (event) => event.key === 'Escape' && close());
    })();
  </script>
@endsection
