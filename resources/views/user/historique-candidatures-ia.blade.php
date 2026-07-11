@extends('layouts.candidat')
@section('title', 'Candidatures IA')
@section('content')
  @php
    $statusStyles = [
      'en_attente' => 'bg-secondary-container/10 text-secondary-container',
      'accepted' => 'bg-secondary-fixed text-on-secondary-fixed-variant',
      'rejected' => 'bg-error-light text-error',
    ];

    $statusLabels = [
      'en_attente' => 'En attente',
      'accepted' => 'Acceptée',
      'rejected' => 'Refusée',
    ];

    $newAiPostulationsCount = isset($newAiPostulationIds) ? $newAiPostulationIds->count() : 0;
  @endphp
  <main class="flex-grow pt-32 bg-surface-container-low/40">

    <section class="px-4 md:px-10 pt-6 md:pt-8 pb-8">
      <div class="max-w-7xl mx-auto">
        <div class="rounded-[1.75rem] bg-white/95 border border-outline-variant/10 shadow-sm overflow-hidden">
          <div class="p-6 md:p-8">
            <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6">
              <div>
                <h1 class="text-3xl md:text-4xl font-bold font-serif text-primary leading-tight mb-2">Candidatures IA</h1>
                <p class="text-on-surface-variant max-w-2xl">Suivez les candidatures préparées avec l'aide de l'IA et consultez chaque dossier envoyé.</p>

                <div class="flex gap-1 mt-6 bg-surface-container rounded-xl p-1.5 w-fit">
                  <a href="{{ route('user.historiques') }}" class="px-5 py-2.5 rounded-lg text-sm font-semibold text-on-surface-variant hover:text-primary transition-all">Candidatures manuelles</a>
                  <a href="{{ route('user.historiques_ia') }}" class="px-5 py-2.5 rounded-lg text-sm font-semibold bg-white text-primary shadow-sm transition-all">Candidatures IA</a>
                </div>
              </div>

              <div class="rounded-2xl bg-secondary-container/5 border border-secondary-container/10 p-5 max-w-lg">
                <div class="flex items-start gap-4">
                  <div class="w-11 h-11 rounded-xl bg-secondary-container flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-white text-2xl">auto_awesome</span>
                  </div>
                  <div class="flex-1">
                    <h3 class="font-bold text-primary">Candidature assistée par IA</h3>
                    <p class="text-sm text-on-surface-variant mt-1">L'IA repère les offres pertinentes selon votre profil et prépare vos documents pour accélérer vos candidatures.</p>
                  </div>
                </div>
                <div class="mt-4 flex justify-end">
                  <a href="{{ route('user.profil-public') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-secondary-container hover:underline whitespace-nowrap">
                    Améliorer mon profil <span class="material-symbols-outlined text-sm">arrow_forward</span>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-2 md:grid-cols-4 gap-3 border-t border-outline-variant/10 bg-surface-container-low/30 p-4 md:p-5">
            <div class="rounded-2xl bg-white border border-outline-variant/10 p-4 shadow-sm">
              <div class="flex items-center justify-between gap-3">
                <div>
                  <div class="text-2xl font-bold text-primary">{{ $postulations->total() }}</div>
                  <div class="text-xs font-semibold text-on-surface-variant mt-1">Candidatures IA</div>
                </div>
                <span class="material-symbols-outlined flex h-10 w-10 items-center justify-center rounded-xl bg-secondary-container/10 text-secondary-container">auto_awesome</span>
              </div>
            </div>
            <div class="rounded-2xl bg-white border border-outline-variant/10 p-4 shadow-sm">
              <div class="flex items-center justify-between gap-3">
                <div>
                  <div class="text-2xl font-bold text-primary">{{ $postulations->getCollection()->where('status', 'en_attente')->count() }}</div>
                  <div class="text-xs font-semibold text-on-surface-variant mt-1">En attente</div>
                </div>
                <span class="material-symbols-outlined flex h-10 w-10 items-center justify-center rounded-xl bg-secondary-container/10 text-secondary-container">hourglass_empty</span>
              </div>
            </div>
            <div class="rounded-2xl bg-white border border-outline-variant/10 p-4 shadow-sm">
              <div class="flex items-center justify-between gap-3">
                <div>
                  <div class="text-2xl font-bold text-primary">{{ $postulations->getCollection()->where('status', 'accepted')->count() }}</div>
                  <div class="text-xs font-semibold text-on-surface-variant mt-1">Acceptées</div>
                </div>
                <span class="material-symbols-outlined flex h-10 w-10 items-center justify-center rounded-xl bg-secondary-container/10 text-secondary-container">check_circle</span>
              </div>
            </div>
            <div class="rounded-2xl bg-white border border-outline-variant/10 p-4 shadow-sm">
              <div class="flex items-center justify-between gap-3">
                <div>
                  <div class="text-2xl font-bold text-primary">{{ $newAiPostulationsCount }}</div>
                  <div class="text-xs font-semibold text-on-surface-variant mt-1">Nouvelles</div>
                </div>
                <span class="material-symbols-outlined flex h-10 w-10 items-center justify-center rounded-xl bg-secondary-container/10 text-secondary-container">notifications</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="px-4 md:px-10 pb-6">
      <div class="max-w-7xl mx-auto">
        <form method="GET" action="{{ route('user.historiques_ia') }}" data-testid="candidate-ai-history-filter-form" class="flex flex-wrap items-end gap-3 rounded-2xl bg-white/90 border border-outline-variant/10 shadow-sm p-4">
          <div>
            <label for="status" class="block text-xs font-semibold text-primary mb-1.5">Statut</label>
            <select id="status" name="status" style="min-width: 13rem;" class="px-3 py-2.5 bg-white rounded-xl border border-outline-variant/30 text-sm text-primary focus:border-secondary-container/50 transition-all">
              <option value="" {{ request('status') === null ? 'selected' : '' }}>Tous statuts</option>
              <option value="En attente" {{ request('status') === 'En attente' ? 'selected' : '' }}>En attente</option>
              <option value="Accepté" {{ request('status') === 'Accepté' ? 'selected' : '' }}>Accepté</option>
              <option value="Rejeté" {{ request('status') === 'Rejeté' ? 'selected' : '' }}>Rejeté</option>
            </select>
          </div>
          <div>
            <label for="date" class="block text-xs font-semibold text-primary mb-1.5">Date</label>
            <select id="date" name="date" style="min-width: 13rem;" class="px-3 py-2.5 bg-white rounded-xl border border-outline-variant/30 text-sm text-primary focus:border-secondary-container/50 transition-all">
              <option value="">Toutes</option>
              <option value="Cette semaine" {{ request('date') === 'Cette semaine' ? 'selected' : '' }}>Cette semaine</option>
              <option value="Ce mois" {{ request('date') === 'Ce mois' ? 'selected' : '' }}>Ce mois</option>
              <option value="Cette année" {{ request('date') === 'Cette année' ? 'selected' : '' }}>Cette année</option>
            </select>
          </div>
          <div class="flex-1 relative">
            <label for="keyword" class="block text-xs font-semibold text-primary mb-1.5">Recherche</label>
            <span class="material-symbols-outlined absolute left-3 top-[2.15rem] text-outline text-sm">search</span>
            <input id="keyword" name="keyword" value="{{ request('keyword') }}" type="text" placeholder="Poste ou entreprise..." class="w-full pl-10 pr-4 py-2.5 bg-white rounded-xl border border-outline-variant/30 text-sm text-primary placeholder:text-outline focus:border-secondary-container/50 transition-all" />
          </div>
          <button type="submit" class="px-4 py-2.5 bg-secondary-container text-white rounded-xl text-sm font-bold hover:bg-secondary transition-colors">
            Filtrer
          </button>
          <a href="{{ route('user.historiques_ia') }}" class="text-sm font-semibold text-outline hover:text-primary flex items-center gap-1 py-2.5 transition-colors">
            <span class="material-symbols-outlined text-sm">refresh</span> Réinitialiser
          </a>
        </form>
      </div>
    </section>

    <!-- Liste candidatures IA -->
    <section class="py-10 px-4 md:px-10">
      <div class="max-w-7xl mx-auto">
        <div class="card-glow rounded-2xl overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead>
                <tr class="border-b border-outline-variant/10 text-left">
                  <th class="px-6 py-4 text-xs font-bold text-outline uppercase tracking-wider">Poste</th>
                  <th class="px-6 py-4 text-xs font-bold text-outline uppercase tracking-wider">Entreprise</th>
                  <th class="px-4 py-4 text-center text-xs font-bold text-outline uppercase tracking-wider hidden md:table-cell">Date</th>
                  <th class="w-20 px-4 py-4 text-center text-xs font-bold text-outline uppercase tracking-wider">Statut</th>
                  <th class="w-20 px-4 py-4 text-center text-xs font-bold text-outline uppercase tracking-wider">Voir</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-outline-variant/5">
                @forelse ($postulations as $postulation)
                  @php($isNewAiApplication = isset($newAiPostulationIds) && $newAiPostulationIds->contains($postulation->id))
                  <tr class="{{ $isNewAiApplication ? 'bg-secondary-container/5' : '' }} hover:bg-surface-container-low/30 transition-colors">
                    <td class="px-6 py-4 font-semibold text-primary align-middle">
                      <div class="flex flex-wrap items-center gap-2">
                        <span>{{ $postulation->offre?->titre ?? 'Offre supprimée' }}</span>
                        @if ($isNewAiApplication)
                          <span class="px-2 py-0.5 rounded-full bg-secondary-container text-white text-2xs font-black uppercase tracking-wider">Nouveau</span>
                        @endif
                      </div>
                    </td>
                    <td class="px-6 py-4 align-middle text-on-surface-variant">{{ $postulation->offre?->entreprise?->company_name ?? 'Entreprise indisponible' }}</td>
                    <td class="px-4 py-4 align-middle text-center text-on-surface-variant hidden md:table-cell">{{ optional($postulation->created_at)->translatedFormat('d M Y') }}</td>
                    <td class="px-4 py-4 align-middle text-center">
                      <span class="mx-auto inline-flex h-8 w-8 items-center justify-center {{ $statusStyles[$postulation->status] ?? 'bg-surface-container text-primary' }} rounded-full shadow-sm ring-1 ring-black/5" title="{{ $statusLabels[$postulation->status] ?? ucfirst(str_replace('_', ' ', $postulation->status ?? '')) }}" aria-label="{{ $statusLabels[$postulation->status] ?? ucfirst(str_replace('_', ' ', $postulation->status ?? '')) }}">
                        <span class="material-symbols-outlined text-base">{{ $postulation->status === 'accepted' ? 'check_circle' : ($postulation->status === 'rejected' ? 'cancel' : 'hourglass_empty') }}</span>
                      </span>
                    </td>
                    <td class="px-4 py-4 align-middle text-center">
                      <a href="{{ route('user.detail-candidature', ['id' => $postulation->id]) }}" class="mx-auto inline-flex h-9 w-9 items-center justify-center rounded-full border border-secondary-container/20 bg-white text-secondary-container shadow-sm hover:bg-secondary-container/10 hover:border-secondary-container/40 transition-colors" title="Voir les détails" aria-label="Voir les détails">
                        <span class="material-symbols-outlined text-lg">visibility</span>
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-on-surface-variant">
                      Aucune candidature IA ne correspond a vos criteres.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between px-6 py-4 border-t border-outline-variant/10">
            <p class="text-xs text-on-surface-variant">
              Affichage de {{ $postulations->firstItem() ?? 0 }} à {{ $postulations->lastItem() ?? 0 }} sur {{ $postulations->total() }} candidatures IA
            </p>
            <div>
              {{ $postulations->withQueryString()->links('components.pagination.public-pagination') }}
            </div>
          </div>
        </div>
      </div>
    </section>

  </main>
@endsection
