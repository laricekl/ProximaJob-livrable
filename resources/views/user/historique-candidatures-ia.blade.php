@extends('layouts.candidat')
@section('title', 'Candidatures IA')
@section('content')
  @php
    $statusStyles = [
      'en_attente' => 'bg-secondary-container/10 text-secondary-container',
      'accepted' => 'bg-secondary-fixed text-on-secondary-fixed-variant',
      'rejected' => 'bg-red-50 text-red-600',
    ];

    $statusLabels = [
      'en_attente' => 'En attente',
      'accepted' => 'Acceptée',
      'rejected' => 'Refusée',
    ];
  @endphp
  <main class="flex-grow pt-32">

    <section class="py-12 px-4 md:px-10 bg-white">
      <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl md:text-4xl font-bold font-serif text-primary leading-tight mb-2">Candidatures automatiques IA</h1>
        <p class="text-on-surface-variant">Notre IA postule automatiquement aux offres correspondant à votre profil</p>

        <div class="flex gap-1 mt-8 bg-surface-container rounded-xl p-1.5 w-fit">
          <a href="{{ route('user.historiques') }}" class="px-5 py-2.5 rounded-lg text-sm font-semibold text-slate-500 hover:text-primary transition-all">Candidatures manuelles</a>
          <a href="{{ route('user.historiques_ia') }}" class="px-5 py-2.5 rounded-lg text-sm font-semibold bg-white text-primary shadow-sm transition-all">Candidatures IA</a>
        </div>

        <!-- Info banner -->
        <div class="mt-6 bg-secondary-fixed/30 rounded-2xl p-6 flex flex-col sm:flex-row items-start sm:items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-secondary-container flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-white text-2xl">auto_awesome</span>
          </div>
          <div class="flex-1">
            <h3 class="font-bold text-primary">Mode automatique activé</h3>
            <p class="text-sm text-on-surface-variant">L'IA analyse 24 offres par jour et postule automatiquement à celles qui correspondent à au moins 75% de votre profil.</p>
          </div>
          <a href="{{ route('user.profil-public') }}" class="px-4 py-2 bg-white border border-outline-variant/30 rounded-xl text-sm font-semibold text-primary hover:bg-surface-container-low transition-colors">Configurer</a>
        </div>
      </div>
    </section>

    <!-- Stats IA -->
    <section class="py-8 px-4 md:px-10 bg-surface-container-low/50">
      <div class="max-w-7xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 text-center">
          <div class="text-3xl font-bold text-secondary-container">{{ $postulations->total() }}</div>
          <div class="text-xs text-on-surface-variant mt-1">Candidatures IA</div>
        </div>
        <div class="bg-white rounded-2xl p-5 text-center">
          <div class="text-3xl font-bold text-secondary-container">{{ $postulations->getCollection()->where('status', 'accepted')->count() }}</div>
          <div class="text-xs text-on-surface-variant mt-1">Réponses reçues</div>
        </div>
        <div class="bg-white rounded-2xl p-5 text-center">
          <div class="text-3xl font-bold text-secondary-container">{{ $postulations->getCollection()->where('status', 'accepted')->count() }}</div>
          <div class="text-xs text-on-surface-variant mt-1">Entretiens décrochés</div>
        </div>
        <div class="bg-white rounded-2xl p-5 text-center">
          <div class="text-3xl font-bold text-secondary-container">{{ $postulations->total() > 0 ? round(($postulations->getCollection()->where('status', 'accepted')->count() / max($postulations->count(), 1)) * 100) : 0 }}%</div>
          <div class="text-xs text-on-surface-variant mt-1">Taux de correspondance</div>
        </div>
      </div>
    </section>

    <section class="py-6 px-4 md:px-10 bg-white border-b border-outline-variant/10">
      <div class="max-w-7xl mx-auto">
        <form method="GET" action="{{ route('user.historiques_ia') }}" data-testid="candidate-ai-history-filter-form" class="flex flex-wrap items-end gap-4">
          <div>
            <label for="status" class="block text-xs font-semibold text-primary mb-1.5">Statut</label>
            <select id="status" name="status" class="px-3 py-2.5 bg-white rounded-xl border border-outline-variant/30 text-sm text-primary focus:border-secondary-container/50 transition-all">
              <option value="" {{ request('status') === null ? 'selected' : '' }}>Tous statuts</option>
              <option value="En attente" {{ request('status') === 'En attente' ? 'selected' : '' }}>En attente</option>
              <option value="Accepté" {{ request('status') === 'Accepté' ? 'selected' : '' }}>Accepté</option>
              <option value="Rejeté" {{ request('status') === 'Rejeté' ? 'selected' : '' }}>Rejeté</option>
            </select>
          </div>
          <div>
            <label for="date" class="block text-xs font-semibold text-primary mb-1.5">Date</label>
            <select id="date" name="date" class="px-3 py-2.5 bg-white rounded-xl border border-outline-variant/30 text-sm text-primary focus:border-secondary-container/50 transition-all">
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
                  <th class="px-6 py-4 text-xs font-bold text-outline uppercase tracking-wider hidden md:table-cell">Date</th>
                  <th class="px-6 py-4 text-xs font-bold text-outline uppercase tracking-wider">Match</th>
                  <th class="px-6 py-4 text-xs font-bold text-outline uppercase tracking-wider">Statut</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-outline-variant/5">
                @forelse ($postulations as $postulation)
                  <tr class="hover:bg-surface-container-low/30 transition-colors">
                    <td class="px-6 py-4 font-semibold text-primary">{{ $postulation->offre?->titre ?? 'Offre supprimée' }}</td>
                    <td class="px-6 py-4 text-on-surface-variant">{{ $postulation->offre?->entreprise?->company_name ?? 'Entreprise indisponible' }}</td>
                    <td class="px-6 py-4 text-on-surface-variant hidden md:table-cell">{{ optional($postulation->created_at)->translatedFormat('d M Y') }}</td>
                    <td class="px-6 py-4"><span class="px-3 py-1 bg-secondary-container/10 text-secondary-container text-[10px] font-black uppercase tracking-widest rounded-full">IA</span></td>
                    <td class="px-6 py-4"><span class="px-3 py-1 {{ $statusStyles[$postulation->status] ?? 'bg-surface-container text-primary' }} text-[10px] font-black uppercase tracking-widest rounded-full">{{ $statusLabels[$postulation->status] ?? ucfirst(str_replace('_', ' ', $postulation->status ?? '')) }}</span></td>
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
              {{ $postulations->withQueryString()->links() }}
            </div>
          </div>
        </div>
      </div>
    </section>

  </main>
@endsection
