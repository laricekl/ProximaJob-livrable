@extends('layouts.entreprise')
@section('title', 'Candidatures IA')
@section('content')
  <main class="flex-grow pt-32 pb-16">
    <section class="py-8 px-4 md:px-10">
      <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
          <div>
            <h1 class="text-2xl font-bold font-serif text-primary mb-1 flex items-center gap-3">
              Candidatures IA
              <span class="px-3 py-1 bg-secondary-container/10 text-secondary-container text-xs font-bold rounded-full">Postulations automatiques</span>
            </h1>
            <p class="text-sm text-on-surface-variant">Les candidats sont automatiquement matchés avec vos offres par notre IA.</p>
          </div>
        </div>

        <div class="bg-secondary-container/10 rounded-2xl p-5 mb-8 flex items-start gap-3">
          <span class="material-symbols-outlined text-secondary-container flex-shrink-0 mt-0.5">info</span>
          <div>
            <p class="text-sm font-semibold text-secondary-container mb-1">Fonctionnement des candidatures automatiques</p>
            <p class="text-sm text-secondary-container">Notre IA analyse les profils compatibles avec vos offres et vous laisse ensuite décider des priorités de traitement.</p>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
          <div class="bg-white rounded-2xl p-5 text-center">
            <div class="text-3xl font-bold text-secondary-container">{{ $stats['total_applications'] ?? 0 }}</div>
            <div class="text-xs text-on-surface-variant mt-1">Candidatures IA</div>
          </div>
          <div class="bg-white rounded-2xl p-5 text-center">
            <div class="text-3xl font-bold text-secondary-container">{{ $stats['new_today'] ?? 0 }}</div>
            <div class="text-xs text-on-surface-variant mt-1">Nouvelles aujourd'hui</div>
          </div>
          <div class="bg-white rounded-2xl p-5 text-center">
            <div class="text-3xl font-bold text-secondary-container">{{ $stats['new_this_week'] ?? 0 }}</div>
            <div class="text-xs text-on-surface-variant mt-1">Nouvelles cette semaine</div>
          </div>
          <div class="bg-white rounded-2xl p-5 text-center">
            <div class="text-3xl font-bold text-secondary-container">{{ $stats['active_offers'] ?? 0 }}</div>
            <div class="text-xs text-on-surface-variant mt-1">Offres actives</div>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
          @forelse ($offres as $offre)
            <div class="card-glow rounded-2xl p-6 relative">
              <span class="absolute top-4 right-4 px-2.5 py-1 bg-secondary-container/10 text-secondary-container text-[11px] font-bold uppercase tracking-wider rounded-full">
                {{ $offre->type?->name ?? 'Offre' }}
              </span>
              <h3 class="text-lg font-bold font-serif text-primary mt-2 mb-3">{{ $offre->titre }}</h3>
              <p class="text-sm text-on-surface-variant line-clamp-3 mb-4">{{ \Illuminate\Support\Str::limit(strip_tags($offre->description ?? ''), 140) }}</p>
              <div class="space-y-2 mb-5 text-sm text-on-surface-variant">
                <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">location_on</span> {{ $offre->localisation ?? 'À confirmer' }}</div>
                <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">smart_toy</span> {{ $offre->autopostulations_count ?? 0 }} candidats matchés</div>
              </div>
              <div class="flex justify-between items-center pt-4 border-t border-outline-variant/10">
                <span class="px-3 py-1 bg-secondary-container/10 text-secondary-container text-xs font-semibold rounded-full">{{ ucfirst($offre->status ?? 'active') }}</span>
                <a href="{{ route('entreprise.offres.candidatures', $offre) }}" class="text-sm font-semibold text-secondary-container hover:underline">Voir candidatures</a>
              </div>
            </div>
          @empty
            <div class="md:col-span-2 lg:col-span-3 card-glow rounded-2xl p-10 text-center text-on-surface-variant">
              Aucune candidature IA disponible pour le moment.
            </div>
          @endforelse
        </div>

        <div class="flex justify-center items-center gap-3">
          {{ $offres->withQueryString()->links() }}
        </div>
      </div>
    </section>
  </main>
@endsection
