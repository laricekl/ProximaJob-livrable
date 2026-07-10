@extends('layouts.entreprise')
@section('title', 'Historique des offres')
@section('content')
  <main class="flex-grow pt-32 pb-16">
    <section class="py-8 px-4 md:px-10">
      <div class="max-w-7xl mx-auto">
        <div class="mb-8">
          <h1 class="text-2xl md:text-3xl font-bold font-serif text-primary mb-2">Historique des offres</h1>
          <p class="text-on-surface-variant">Suivez la performance et l’activité de vos offres publiées.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
          <div class="card-glow rounded-2xl p-5">
            <p class="text-xs uppercase tracking-wider text-outline mb-2">Offres actives</p>
            <p class="text-3xl font-bold text-primary">{{ $stats['active_offers'] ?? $offres->where('status', 'active')->count() }}</p>
          </div>
          <div class="card-glow rounded-2xl p-5">
            <p class="text-xs uppercase tracking-wider text-outline mb-2">Candidatures</p>
            <p class="text-3xl font-bold text-primary">{{ $stats['total_applications'] ?? $offres->sum('postulations_count') }}</p>
          </div>
          <div class="card-glow rounded-2xl p-5">
            <p class="text-xs uppercase tracking-wider text-outline mb-2">Cette semaine</p>
            <p class="text-3xl font-bold text-primary">{{ $stats['new_this_week'] ?? 0 }}</p>
          </div>
          <div class="card-glow rounded-2xl p-5">
            <p class="text-xs uppercase tracking-wider text-outline mb-2">Taux de clôture</p>
            <p class="text-3xl font-bold text-primary">{{ $stats['closure_rate'] ?? 0 }}%</p>
          </div>
        </div>

        <div class="card-glow rounded-2xl overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
              <thead>
                <tr class="border-b border-outline-variant/10 bg-surface-container-low/50">
                  <th class="px-6 py-4 font-bold text-primary text-xs uppercase tracking-wider">Poste</th>
                  <th class="hidden sm:table-cell px-6 py-4 font-bold text-primary text-xs uppercase tracking-wider">Type</th>
                  <th class="hidden md:table-cell px-6 py-4 font-bold text-primary text-xs uppercase tracking-wider">Date publication</th>
                  <th class="px-6 py-4 font-bold text-primary text-xs uppercase tracking-wider">Candidatures</th>
                  <th class="px-6 py-4 font-bold text-primary text-xs uppercase tracking-wider">Statut</th>
                  <th class="px-6 py-4 font-bold text-primary text-xs uppercase tracking-wider text-right">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-outline-variant/5">
                @forelse ($offres as $offre)
                  <tr class="hover:bg-surface-container-low/30 transition-colors">
                    <td class="px-6 py-4 font-semibold text-primary">{{ $offre->titre }}</td>
                    <td class="hidden sm:table-cell px-6 py-4">
                      <span class="px-2 py-0.5 bg-secondary-container/10 text-secondary-container text-xs font-bold rounded-full">
                        {{ $offre->type?->nom ?? 'Offre' }}
                      </span>
                    </td>
                    <td class="hidden md:table-cell px-6 py-4 text-on-surface-variant">{{ optional($offre->created_at)->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">
                      <a href="{{ route('entreprise.offres.candidatures', $offre) }}" class="text-secondary-container font-semibold hover:underline">
                        {{ $offre->postulations_count ?? 0 }}
                      </a>
                    </td>
                    <td class="px-6 py-4">
                      <span class="px-2 py-0.5 bg-secondary-container/10 text-secondary-container text-xs font-semibold rounded-full">
                        {{ ucfirst($offre->status ?? 'active') }}
                      </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                      <div class="flex justify-end gap-2">
                        <a href="{{ route('entreprise.offres.candidatures', $offre) }}" class="w-8 h-8 rounded-lg bg-surface-container-low hover:bg-primary-container/10 transition-colors flex items-center justify-center text-outline hover:text-primary" title="Voir">
                          <span class="material-symbols-outlined text-base">visibility</span>
                        </a>
                        <a href="{{ route('edit.offres', $offre->id) }}" class="w-8 h-8 rounded-lg bg-surface-container-low hover:bg-secondary-container/10 transition-colors flex items-center justify-center text-outline hover:text-secondary-container" title="Modifier">
                          <span class="material-symbols-outlined text-base">edit</span>
                        </a>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-on-surface-variant">Aucune offre publiée pour le moment.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        <div class="flex justify-center items-center gap-3 mt-8">
          {{ $offres->withQueryString()->links() }}
        </div>
      </div>
    </section>
  </main>
@endsection
