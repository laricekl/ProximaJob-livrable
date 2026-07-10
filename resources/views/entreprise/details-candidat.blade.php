@extends('layouts.entreprise')
@section('title', 'Détail candidat')
@section('content')
  @php
    $fullName = trim(($candidat->prenom ?? '') . ' ' . ($candidat->name ?? ''));
    $initials = strtoupper(substr($candidat->prenom ?? '', 0, 1) . substr($candidat->name ?? '', 0, 1));
    $cvUrl = $postulation?->cv ? route('preview.cv-ia.ep', $postulation) : null;
    $letterUrl = $postulation?->cover_letter
      ? route('preview.letter-ia', $postulation)
      : ($postulation?->lettre_motivation ? route('entreprise.candidature.preview', ['postulationId' => $postulation->id, 'type' => 'motivation']) : null);
  @endphp

  <main class="flex-grow pt-32 pb-16">
    <section class="py-8 px-4 md:px-10">
      <div class="max-w-6xl mx-auto">
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 text-sm text-secondary-container font-semibold hover:underline mb-6">
          <span class="material-symbols-outlined text-lg">arrow_back</span> Retour à la liste
        </a>

        <h1 class="text-2xl font-bold font-serif text-primary mb-8">Détails du candidat</h1>

        <div class="bg-secondary-container/10 rounded-2xl p-6 md:p-8 mb-8 flex flex-col sm:flex-row items-start gap-6">
          <div class="w-24 h-24 md:w-28 md:h-28 rounded-full bg-primary-container flex items-center justify-center text-white text-4xl font-bold flex-shrink-0">{{ $initials ?: 'CA' }}</div>
          <div class="flex-1">
            <h2 class="text-2xl font-bold font-serif text-primary mb-1">{{ $fullName ?: 'Candidat' }}</h2>
            <p class="text-on-surface-variant mb-4">{{ $candidat->candidateSector?->sector?->name ?? 'Profil candidat' }}</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-on-surface-variant mb-4">
              <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">mail</span> {{ $candidat->email }}</div>
              <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">call</span> {{ $candidat->telephone ?? 'À confirmer' }}</div>
              <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">location_on</span> {{ $candidat->adresse ?? 'À confirmer' }}</div>
              <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">work</span> {{ $candidat->experience_years }} ans d'expérience</div>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
              @if ($postulation)
                <span class="px-3 py-1.5 bg-secondary-container/10 text-secondary-container text-sm font-semibold rounded-full flex items-center gap-1.5"><span class="material-symbols-outlined text-sm">work</span> Offre : {{ $postulation->offre?->titre ?? 'Indisponible' }}</span>
              @endif
              @if ($candidat->candidateSector?->diplome?->nom_diplome)
                <span class="px-3 py-1.5 bg-secondary-container/10 text-secondary-container text-sm font-semibold rounded-full flex items-center gap-1.5"><span class="material-symbols-outlined text-sm">school</span> {{ $candidat->candidateSector->diplome->nom_diplome }}</span>
              @endif
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <div class="lg:col-span-2 space-y-6">
            <div class="card-glow rounded-2xl p-6">
              <h3 class="text-lg font-bold font-serif text-primary mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-secondary-container">handyman</span> Compétences principales</h3>
              <div class="flex flex-wrap gap-2">
                @forelse ($candidat->skills as $skill)
                  <span class="px-3 py-1.5 bg-secondary-container/10 text-secondary-container text-sm font-medium rounded-full">{{ $skill->name }}</span>
                @empty
                  <p class="text-sm text-on-surface-variant">Aucune compétence ajoutée.</p>
                @endforelse
              </div>
            </div>

            <div class="card-glow rounded-2xl p-6">
              <h3 class="text-lg font-bold font-serif text-primary mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-secondary-container">description</span> Lettre de présentation</h3>
              @if ($letterUrl)
                <a href="{{ $letterUrl }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-colors">
                  <span class="material-symbols-outlined text-lg">visibility</span> Ouvrir la lettre de présentation
                </a>
              @else
                <p class="text-sm text-on-surface-variant">Aucune lettre de présentation disponible pour cette candidature.</p>
              @endif
            </div>

            <div class="card-glow rounded-2xl p-6">
              <h3 class="text-lg font-bold font-serif text-primary mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-secondary-container">badge</span> Informations complémentaires</h3>
              <div class="space-y-3 text-sm text-on-surface-variant">
                <p><span class="font-semibold text-primary">Inscription :</span> {{ optional($candidat->created_at)->format('d/m/Y') }}</p>
                <p><span class="font-semibold text-primary">Secteur :</span> {{ $candidat->candidateSector?->sector?->name ?? 'À confirmer' }}</p>
                <p><span class="font-semibold text-primary">Diplôme :</span> {{ $candidat->candidateSector?->diplome?->nom_diplome ?? 'À confirmer' }}</p>
              </div>
            </div>
          </div>

          <div class="space-y-6">
            <div class="card-glow rounded-2xl p-6">
              <h3 class="text-lg font-bold font-serif text-primary mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-secondary-container">picture_as_pdf</span> Curriculum Vitae</h3>
              <div class="space-y-3">
                @if ($cvUrl)
                  <a href="{{ $cvUrl }}" class="flex items-center justify-center gap-2 w-full px-5 py-3 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-colors">
                    <span class="material-symbols-outlined text-lg">visibility</span> Voir le CV
                  </a>
                @else
                  <div class="px-5 py-3 bg-surface-container text-sm text-on-surface-variant rounded-xl text-center">Aucun CV disponible</div>
                @endif
                @if ($letterUrl)
                  <a href="{{ $letterUrl }}" class="flex items-center justify-center gap-2 w-full px-5 py-3 border-2 border-secondary-container text-secondary-container text-sm font-bold rounded-xl hover:bg-secondary-container/5 transition-colors">
                    <span class="material-symbols-outlined text-lg">description</span> Voir la lettre
                  </a>
                @endif
              </div>
            </div>

            @if ($postulation)
              <div class="card-glow rounded-2xl p-6">
                <h3 class="text-lg font-bold font-serif text-primary mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-secondary-container">settings</span> Actions</h3>
                <div class="space-y-3">
                  <form method="POST" action="{{ route('candidature.updateStatus', $postulation) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="accepted">
                    <button type="submit" class="flex items-center justify-center gap-2 w-full px-5 py-3 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-colors">
                      <span class="material-symbols-outlined text-lg">check</span> Accepter le candidat
                    </button>
                  </form>
                  <form method="POST" action="{{ route('candidature.updateStatus', $postulation) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="rejected">
                    <button type="submit" class="flex items-center justify-center gap-2 w-full px-5 py-3 border border-error-light text-error text-sm font-bold rounded-xl hover:bg-error-light transition-colors">
                      <span class="material-symbols-outlined text-lg">close</span> Refuser le candidat
                    </button>
                  </form>
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </section>
  </main>
@endsection
