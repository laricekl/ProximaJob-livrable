@extends('layouts.entreprise')
@section('title', 'Mes offres')
@section('content')
  @php
    $totalOffers = $offres->total();
    $activeOffers = $offres->getCollection()->where('status', 'active')->count();
    $applicationsCount = $offres->getCollection()->sum(fn ($offre) => $offre->postulations_count ?? $offre->postulations->count());
  @endphp

  <main class="flex-grow pt-32 pb-16">
    <section class="py-8 px-4 md:px-10">
      <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
          <div class="card-glow rounded-2xl p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-secondary-container/10 flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined text-secondary-container">work</span></div>
            <div><p class="text-2xl font-bold text-primary">{{ $totalOffers }}</p><p class="text-sm text-on-surface-variant">Offres publiees</p></div>
          </div>
          <div class="card-glow rounded-2xl p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-secondary-container/10 flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined text-secondary-container">check_circle</span></div>
            <div><p class="text-2xl font-bold text-primary">{{ $activeOffers }}</p><p class="text-sm text-on-surface-variant">Offres actives</p></div>
          </div>
          <div class="card-glow rounded-2xl p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-secondary-container/10 flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined text-secondary-container">people</span></div>
            <div><p class="text-2xl font-bold text-primary">{{ $applicationsCount }}</p><p class="text-sm text-on-surface-variant">Candidatures recues</p></div>
          </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
          <form method="GET" action="{{ route('offres.publies') }}" data-testid="enterprise-offers-search-form" class="relative w-full sm:w-80">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">search</span>
            <input type="text" name="search" value="{{ request('search') }}" class="w-full pl-11 pr-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary placeholder:text-outline focus:border-secondary-container/50 focus:ring-0 transition-all" placeholder="Rechercher une offre..." />
          </form>
          <a href="{{ route('entreprise.offres.create') }}" class="px-6 py-3 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-all flex items-center gap-2 shadow-lg shadow-secondary-container/20">
            <span class="material-symbols-outlined">add</span> Ajouter une offre
          </a>
        </div>

        @if (session('success'))
          <div class="mb-8 rounded-2xl border border-success-light bg-success-light px-5 py-4 text-sm text-success-dark">
            {{ session('success') }}
          </div>
        @endif

        @if ($errors->any())
          <div class="mb-8 rounded-2xl border border-error-light bg-error-light px-5 py-4 text-sm text-error-dark">
            {{ $errors->first() }}
          </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
          @forelse ($offres as $offre)
            @php
              $salary = ($offre->salaire_min || $offre->salaire_max)
                ? number_format((float) $offre->salaire_min, 0, ',', ' ') . ' - ' . number_format((float) $offre->salaire_max, 0, ',', ' ') . ' $'
                : 'Salaire à confirmer';
              $applications = $offre->postulations_count ?? $offre->postulations->count();
            @endphp
            <div class="card-glow rounded-2xl p-6 relative">
              <span class="absolute top-4 right-4 px-2.5 py-1 bg-secondary-container/10 text-secondary-container text-xs font-bold uppercase tracking-wider rounded-full">
                {{ $offre->type?->nom ?? $offre->employment_type ?? 'Offre' }}
              </span>
              <h3 class="text-lg font-bold font-serif text-primary mt-2 mb-3 pr-24">{{ $offre->titre }}</h3>
              <p class="text-sm text-on-surface-variant line-clamp-3 mb-4">{{ \Illuminate\Support\Str::limit(strip_tags($offre->description ?? $offre->missions ?? ''), 150) }}</p>
              <div class="space-y-2 mb-5 text-sm text-on-surface-variant">
                <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">location_on</span> {{ $offre->localisation ?? 'À confirmer' }}</div>
                <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">payments</span> {{ $salary }}</div>
                <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">schedule</span> Publiee le {{ optional($offre->created_at)->format('d/m/Y') }}</div>
                <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">groups</span> {{ $applications }} candidature{{ $applications > 1 ? 's' : '' }}</div>
              </div>
              <div class="flex justify-between items-center pt-4 border-t border-outline-variant/10">
                <a href="{{ route('entreprise.offres.candidatures', $offre) }}" class="px-3 py-1 bg-secondary-container/10 text-secondary-container text-xs font-semibold rounded-full hover:bg-secondary-container/20 transition-colors">
                  {{ ucfirst($offre->status ?? 'active') }}
                </a>
                <div class="flex gap-2">
                  <a href="{{ route('entreprise.offres.candidatures', $offre) }}" class="w-9 h-9 rounded-lg bg-surface-container-low hover:bg-primary-container/10 transition-colors flex items-center justify-center text-outline hover:text-primary" title="Voir candidatures"><span class="material-symbols-outlined text-lg">visibility</span></a>
                  <a href="{{ route('edit.offres', $offre->id) }}" class="w-9 h-9 rounded-lg bg-surface-container-low hover:bg-primary-container/10 transition-colors flex items-center justify-center text-outline hover:text-primary" title="Modifier"><span class="material-symbols-outlined text-lg">edit</span></a>
                  <form method="POST" action="{{ route('offres.destroy', $offre->id) }}" onsubmit="return confirm('Supprimer cette offre ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-9 h-9 rounded-lg bg-surface-container-low hover:bg-error-light transition-colors flex items-center justify-center text-outline hover:text-error" title="Supprimer"><span class="material-symbols-outlined text-lg">delete</span></button>
                  </form>
                </div>
              </div>
            </div>
          @empty
            <div class="md:col-span-2 lg:col-span-3 card-glow rounded-2xl p-10 text-center">
              <span class="material-symbols-outlined text-4xl text-outline mb-3">work_off</span>
              <h3 class="font-bold text-primary mb-2">Aucune offre publiee</h3>
              <p class="text-sm text-on-surface-variant mb-5">Creez votre premiere offre pour commencer a recevoir des candidatures.</p>
              <a href="{{ route('entreprise.offres.create') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-secondary-container text-white rounded-xl text-sm font-bold hover:bg-secondary transition-colors">
                <span class="material-symbols-outlined">add</span> Ajouter une offre
              </a>
            </div>
          @endforelse
        </div>

        <div class="flex justify-center items-center gap-3">
          {{ $offres->withQueryString()->links('components.pagination.public-pagination') }}
        </div>
      </div>
    </section>
  </main>
@endsection
