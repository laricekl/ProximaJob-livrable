@extends('layouts.entreprise')
@section('title', 'Candidatures')

@section('content')
  @php
    $statusLabels = [
      'en_attente' => 'À examiner',
      'shortlisted' => 'Présélectionnée',
      'contacted' => 'Contactée',
      'interview' => 'Entretien planifié',
      'accepted' => 'Acceptée',
      'rejected' => 'Refusée',
    ];
    $statusStyles = [
      'en_attente' => 'bg-secondary-container/10 text-secondary-container',
      'shortlisted' => 'bg-primary-container/10 text-primary-container',
      'contacted' => 'bg-info-light text-info-dark',
      'interview' => 'bg-tertiary-container/40 text-primary',
      'accepted' => 'bg-success-light text-success-dark',
      'rejected' => 'bg-error-light text-error-dark',
    ];
    $totalApplications = array_sum($statusCounts->toArray());
  @endphp

  <section class="px-4 py-8 md:px-10">
    <div class="mx-auto max-w-7xl">
      <div class="mb-7">
        <p class="mb-2 text-xs font-black uppercase tracking-[0.2em] text-secondary-container">Recrutement</p>
        <h1 class="font-serif text-3xl font-bold text-primary">Toutes les candidatures</h1>
        <p class="mt-2 max-w-2xl text-on-surface-variant">Traitez les dossiers de toutes vos offres depuis une seule boîte de réception.</p>
      </div>

      <div class="mb-6 flex gap-2 overflow-x-auto pb-2">
        @foreach ([
          '' => ['Toutes', $totalApplications],
          'en_attente' => ['À examiner', $statusCounts['en_attente'] ?? 0],
          'shortlisted' => ['Présélectionnées', $statusCounts['shortlisted'] ?? 0],
          'contacted' => ['Contactées', $statusCounts['contacted'] ?? 0],
          'interview' => ['Entretiens', $statusCounts['interview'] ?? 0],
          'accepted' => ['Acceptées', $statusCounts['accepted'] ?? 0],
          'rejected' => ['Refusées', $statusCounts['rejected'] ?? 0],
        ] as $value => [$label, $count])
          <a href="{{ route('entreprise.candidatures.index', array_filter(['status' => $value, 'offre_id' => $filters['offre_id'] ?? null, 'source' => $filters['source'] ?? null, 'search' => $filters['search'] ?? null])) }}"
             class="inline-flex whitespace-nowrap items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-bold transition-colors {{ ($filters['status'] ?? '') === $value ? 'bg-primary-container text-white' : 'border border-outline-variant/20 bg-white text-on-surface-variant hover:text-primary' }}">
            {{ $label }}
            <span class="flex h-6 min-w-6 items-center justify-center rounded-full px-1.5 text-xs {{ ($filters['status'] ?? '') === $value ? 'bg-white/15' : 'bg-surface-container' }}">{{ $count }}</span>
          </a>
        @endforeach
      </div>

      <form method="GET" action="{{ route('entreprise.candidatures.index') }}" class="card-glow mb-8 grid grid-cols-1 gap-4 rounded-2xl p-5 md:grid-cols-4">
        @if (!empty($filters['status']))
          <input type="hidden" name="status" value="{{ $filters['status'] }}">
        @endif
        <div class="md:col-span-2">
          <label for="applications-search" class="mb-1.5 block text-sm font-semibold text-primary">Rechercher</label>
          <input id="applications-search" name="search" type="search" value="{{ $filters['search'] ?? '' }}" class="w-full rounded-xl border border-outline-variant/30 bg-white px-4 py-3 text-sm text-primary focus:border-secondary-container/50 focus:ring-0" placeholder="Candidat, courriel ou offre…">
        </div>
        <div>
          <label for="applications-offer" class="mb-1.5 block text-sm font-semibold text-primary">Offre</label>
          <select id="applications-offer" name="offre_id" class="w-full rounded-xl border border-outline-variant/30 bg-white px-4 py-3 text-sm text-primary focus:border-secondary-container/50 focus:ring-0">
            <option value="">Toutes les offres</option>
            @foreach ($offres as $offre)
              <option value="{{ $offre->id }}" @selected((string) ($filters['offre_id'] ?? '') === (string) $offre->id)>{{ $offre->titre }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label for="applications-source" class="mb-1.5 block text-sm font-semibold text-primary">Origine</label>
          <select id="applications-source" name="source" class="w-full rounded-xl border border-outline-variant/30 bg-white px-4 py-3 text-sm text-primary focus:border-secondary-container/50 focus:ring-0">
            <option value="">Toutes</option>
            <option value="directe" @selected(($filters['source'] ?? '') === 'directe')>Candidature directe</option>
            <option value="ia" @selected(($filters['source'] ?? '') === 'ia')>Sélection IA</option>
          </select>
        </div>
        <div class="flex flex-wrap gap-3 md:col-span-4">
          <button class="inline-flex items-center gap-2 rounded-xl bg-secondary-container px-5 py-2.5 text-sm font-bold text-white hover:bg-secondary"><span class="material-symbols-outlined text-lg">filter_alt</span>Appliquer</button>
          <a href="{{ route('entreprise.candidatures.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-surface-container px-5 py-2.5 text-sm font-semibold text-primary"><span class="material-symbols-outlined text-lg">close</span>Effacer</a>
        </div>
      </form>

      <div class="mb-10 space-y-4">
        @forelse ($postulations as $postulation)
          @php
            $name = trim(($postulation->user?->prenom ?? '') . ' ' . ($postulation->user?->name ?? 'Candidat'));
            $skills = $postulation->user?->skills?->take(3) ?? collect();
          @endphp
          <article class="card-glow rounded-2xl p-5 md:p-6">
            <div class="flex flex-col justify-between gap-5 lg:flex-row lg:items-center">
              <div class="min-w-0 flex-1">
                <div class="mb-2 flex flex-wrap items-center gap-2">
                  <h2 class="text-lg font-bold text-primary">{{ $name }}</h2>
                  <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusStyles[$postulation->status] ?? 'bg-surface-container text-primary' }}">{{ $statusLabels[$postulation->status] ?? ucfirst($postulation->status) }}</span>
                  <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $postulation->autopostulation ? 'bg-secondary-container/10 text-secondary-container' : 'bg-surface-container text-on-surface-variant' }}">{{ $postulation->autopostulation ? 'Sélection IA' : 'Candidature directe' }}</span>
                </div>
                <p class="font-semibold text-primary">{{ $postulation->offre?->titre }}</p>
                <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-sm text-on-surface-variant">
                  <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-base text-outline">mail</span>{{ $postulation->user?->email }}</span>
                  <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-base text-outline">work_history</span>{{ $postulation->user?->candidateSector?->experience_years ?? 0 }} an(s) d’expérience</span>
                  <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-base text-outline">calendar_today</span>Reçue le {{ optional($postulation->created_at)->format('d/m/Y') }}</span>
                  @if (!is_null($postulation->match_score))
                    <span class="flex items-center gap-1.5 font-semibold text-secondary-container"><span class="material-symbols-outlined text-base">data_usage</span>Compatibilité estimée : {{ round($postulation->match_score) }} %</span>
                  @endif
                </div>
                @if ($skills->isNotEmpty())
                  <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ($skills as $skill)
                      <span class="rounded-full bg-surface-container px-2.5 py-1 text-xs text-on-surface-variant">{{ $skill->name }}</span>
                    @endforeach
                  </div>
                @endif
              </div>
              <a href="{{ route('entreprise.candidatures.show', ['postulation' => $postulation, 'source' => $postulation->autopostulation ? 'ia' : null, 'from' => 'inbox']) }}" class="inline-flex flex-shrink-0 items-center justify-center gap-2 rounded-xl bg-primary-container px-5 py-3 text-sm font-bold text-white hover:bg-primary">
                Examiner le dossier <span class="material-symbols-outlined text-lg">arrow_forward</span>
              </a>
            </div>
          </article>
        @empty
          <div class="card-glow rounded-2xl p-12 text-center">
            <span class="material-symbols-outlined mb-3 text-4xl text-outline">inbox</span>
            <h2 class="font-serif text-xl font-bold text-primary">Aucune candidature trouvée</h2>
            <p class="mt-2 text-sm text-on-surface-variant">Modifiez les filtres ou revenez consulter cette boîte plus tard.</p>
          </div>
        @endforelse
      </div>

      {{ $postulations->links('components.pagination.public-pagination') }}
    </div>
  </section>
@endsection
