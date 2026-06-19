@extends('layouts.entreprise')
@section('title', 'Candidatures')
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
      'rejected' => 'Rejetée',
    ];
  @endphp

  <main class="flex-grow pt-32 pb-16">
    <section class="py-8 px-4 md:px-10">
      <div class="max-w-5xl mx-auto">
        <a href="{{ route('offres.publies') }}" class="inline-flex items-center gap-2 text-sm text-secondary-container font-semibold hover:underline mb-4">
          <span class="material-symbols-outlined text-lg">arrow_back</span> Retour aux offres
        </a>

        <h1 class="text-2xl font-bold font-serif text-primary mb-2">Candidatures pour : {{ $offre->titre }}</h1>
        <div class="bg-secondary-container/10/70 backdrop-blur-md border border-secondary-container/20/50 rounded-2xl p-5 mb-8 inline-block">
          <p class="text-2xl font-bold text-primary">{{ $postulations->total() }} candidatures reçues</p>
        </div>

        <div class="card-glow rounded-2xl p-6 mb-8">
          <form method="GET" action="{{ route('entreprise.offres.candidatures', $offre) }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
              <div>
                <label class="block text-sm font-semibold text-primary mb-1.5" for="status">Statut</label>
                <select id="status" name="status" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all">
                  <option value="">Tous les statuts</option>
                  @foreach ($statuses as $status)
                    <option value="{{ $status }}" {{ ($filters['status'] ?? '') === $status ? 'selected' : '' }}>
                      {{ $statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status)) }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div>
                <label class="block text-sm font-semibold text-primary mb-1.5" for="search">Rechercher</label>
                <input type="text" id="search" name="search" value="{{ $filters['search'] ?? '' }}" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary placeholder:text-outline focus:border-secondary-container/50 focus:ring-0 transition-all" placeholder="Nom ou email..." />
              </div>
              <div>
                <label class="block text-sm font-semibold text-primary mb-1.5" for="date">Date</label>
                <input type="date" id="date" name="date" value="{{ $filters['date'] ?? '' }}" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-primary mb-1.5" for="per_page">Par page</label>
                <select id="per_page" name="per_page" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all">
                  @foreach ([10, 20, 50] as $perPage)
                    <option value="{{ $perPage }}" {{ (int) ($filters['per_page'] ?? 10) === $perPage ? 'selected' : '' }}>{{ $perPage }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <button type="submit" class="px-5 py-2.5 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">search</span> Filtrer
              </button>
              <a href="{{ route('entreprise.offres.candidatures', $offre) }}" class="px-5 py-2.5 bg-surface-container text-primary text-sm font-semibold rounded-xl hover:bg-surface-container-low transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">close</span> Effacer
              </a>
            </div>
          </form>
        </div>

        <div class="space-y-4 mb-10">
          @forelse ($postulations as $postulation)
            <div class="card-glow rounded-2xl p-6 {{ $postulation->status === 'rejected' ? 'opacity-80' : '' }}">
              <div class="flex flex-col sm:flex-row justify-between items-start gap-3 mb-4">
                <div class="flex items-center gap-3">
                  <h3 class="font-bold text-primary text-lg">{{ trim(($postulation->user?->prenom ?? '') . ' ' . ($postulation->user?->name ?? 'Candidat')) }}</h3>
                  <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusStyles[$postulation->status] ?? 'bg-surface-container text-primary' }}">
                    {{ $statusLabels[$postulation->status] ?? ucfirst(str_replace('_', ' ', $postulation->status ?? '')) }}
                  </span>
                </div>
                <span class="text-sm text-outline">Postulé le {{ optional($postulation->created_at)->format('d/m/Y à H:i') }}</span>
              </div>

              <div class="flex flex-wrap gap-4 text-sm text-on-surface-variant mb-4">
                <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-sm text-outline">mail</span> {{ $postulation->user?->email ?? 'Non renseigné' }}</span>
                <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-sm text-outline">call</span> {{ $postulation->user?->telephone ?? 'Non renseigné' }}</span>
                @if ($postulation->cv)
                  <a href="{{ route('preview.cv-ia.ep', $postulation) }}" class="flex items-center gap-1.5 text-secondary-container font-semibold hover:underline"><span class="material-symbols-outlined text-sm">picture_as_pdf</span> Voir le CV</a>
                @endif
                @if ($postulation->cover_letter || $postulation->lettre_motivation)
                  <a href="{{ $postulation->cover_letter ? route('preview.letter-ia', $postulation) : route('entreprise.candidature.preview', ['postulationId' => $postulation->id, 'type' => 'motivation']) }}" class="flex items-center gap-1.5 text-secondary-container font-semibold hover:underline"><span class="material-symbols-outlined text-sm">description</span> Voir la lettre de motivation</a>
                @endif
              </div>

              <div class="flex flex-wrap gap-3 pt-4 border-t border-outline-variant/10">
                <a href="{{ route('entreprise.connected_candidate_details', $postulation->user_id) }}" class="px-5 py-2.5 bg-primary-container text-white text-sm font-semibold rounded-xl hover:bg-primary-container/90 transition-colors flex items-center gap-2">
                  <span class="material-symbols-outlined text-lg">visibility</span> Voir le profil
                </a>

                @if ($postulation->status !== 'accepted')
                  <form method="POST" action="{{ route('candidature.updateStatus', $postulation) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="accepted">
                    <button type="submit" class="px-5 py-2.5 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-colors flex items-center gap-2">
                      <span class="material-symbols-outlined text-lg">check</span> Accepter
                    </button>
                  </form>
                @endif

                @if ($postulation->status !== 'rejected')
                  <form method="POST" action="{{ route('candidature.updateStatus', $postulation) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="rejected">
                    <button type="submit" class="px-5 py-2.5 bg-white border border-red-200 text-red-600 text-sm font-bold rounded-xl hover:bg-red-50 transition-colors flex items-center gap-2">
                      <span class="material-symbols-outlined text-lg">close</span> Rejeter
                    </button>
                  </form>
                @endif
              </div>
            </div>
          @empty
            <div class="card-glow rounded-2xl p-10 text-center text-on-surface-variant">
              Aucune candidature ne correspond aux filtres sélectionnés.
            </div>
          @endforelse
        </div>

        <div class="flex justify-center items-center gap-3">
          {{ $postulations->withQueryString()->links() }}
        </div>
      </div>
    </section>
  </main>
@endsection
