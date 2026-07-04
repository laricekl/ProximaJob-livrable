@extends('layouts.entreprise')
@section('title', 'Promotions')
@section('content')
  <main class="flex-grow pt-32 pb-16">
    <section class="py-8 px-4 md:px-10">
      <div class="max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
          <div>
            <h1 class="text-2xl font-bold font-serif text-primary mb-2">Promotions</h1>
            <p class="text-sm text-on-surface-variant">Suivez vos offres actives et les candidatures associées. Le module de promotion avancée pourra ensuite se brancher ici sans réutiliser de fausses données.</p>
          </div>
          <a href="{{ route('entreprise.offres.create') }}" class="px-6 py-3 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-all flex items-center gap-2 shadow-lg shadow-secondary-container/20">
            <span class="material-symbols-outlined">add</span> Ajouter une offre
          </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
          <div class="card-glow rounded-2xl p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-secondary-container/10 flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined text-secondary-container">inventory_2</span></div>
            <div><p class="text-2xl font-bold text-primary">{{ $promotionStats['total_offers'] ?? 0 }}</p><p class="text-sm text-on-surface-variant">Offres suivies</p></div>
          </div>
          <div class="card-glow rounded-2xl p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-secondary-container/10 flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined text-secondary-container">trending_up</span></div>
            <div><p class="text-2xl font-bold text-primary">{{ $promotionStats['active_offers'] ?? 0 }}</p><p class="text-sm text-on-surface-variant">Offres actives</p></div>
          </div>
          <div class="card-glow rounded-2xl p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-secondary-container/10 flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined text-secondary-container">smart_toy</span></div>
            <div><p class="text-2xl font-bold text-primary">{{ $promotionStats['autopostulations'] ?? 0 }}</p><p class="text-sm text-on-surface-variant">Candidatures IA recues</p></div>
          </div>
        </div>

        <div class="card-glow rounded-2xl overflow-hidden mb-10">
          <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
              <thead>
                <tr class="border-b border-outline-variant/10 bg-surface-container-low/50">
                  <th class="px-6 py-4 font-bold text-primary text-xs uppercase tracking-wider">Offre</th>
                  <th class="px-6 py-4 font-bold text-primary text-xs uppercase tracking-wider">Type</th>
                  <th class="px-6 py-4 font-bold text-primary text-xs uppercase tracking-wider">Statut</th>
                  <th class="px-6 py-4 font-bold text-primary text-xs uppercase tracking-wider">Candidatures</th>
                  <th class="px-6 py-4 font-bold text-primary text-xs uppercase tracking-wider">IA</th>
                  <th class="px-6 py-4 font-bold text-primary text-xs uppercase tracking-wider text-right">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-outline-variant/5">
                @forelse ($offers as $offer)
                  <tr class="hover:bg-surface-container-low/30 transition-colors">
                    <td class="px-6 py-4">
                      <p class="font-semibold text-primary">{{ $offer->titre }}</p>
                      <p class="text-xs text-on-surface-variant mt-1">{{ $offer->localisation ?? 'Localisation à confirmer' }}</p>
                    </td>
                    <td class="px-6 py-4 text-on-surface-variant">{{ $offer->type?->nom ?? 'Offre' }}</td>
                    <td class="px-6 py-4">
                      <span class="px-2 py-0.5 bg-secondary-container/10 text-secondary-container text-[11px] font-semibold rounded-full">{{ ucfirst($offer->status ?? 'active') }}</span>
                    </td>
                    <td class="px-6 py-4 text-on-surface-variant">{{ $offer->postulations_count ?? 0 }}</td>
                    <td class="px-6 py-4 text-on-surface-variant">{{ $offer->autopostulations_count ?? 0 }}</td>
                    <td class="px-6 py-4 text-right">
                      <div class="flex justify-end gap-2">
                        <a href="{{ route('entreprise.offres.candidatures', $offer) }}" class="w-8 h-8 rounded-lg bg-surface-container-low hover:bg-primary-container/10 transition-colors flex items-center justify-center text-outline hover:text-primary" title="Voir candidatures"><span class="material-symbols-outlined text-base">visibility</span></a>
                        <a href="{{ route('edit.offres', $offer->id) }}" class="w-8 h-8 rounded-lg bg-surface-container-low hover:bg-primary-container/10 transition-colors flex items-center justify-center text-outline hover:text-primary" title="Modifier"><span class="material-symbols-outlined text-base">edit</span></a>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-on-surface-variant">
                      Aucune offre n'est encore disponible pour alimenter le suivi promotionnel.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        <div class="flex justify-center items-center gap-3">
          {{ $offers->withQueryString()->links() }}
        </div>
      </div>
    </section>
  </main>
@endsection
