@extends('layouts.entreprise')
@section('title', 'Détail candidat')
@section('content')
  @php
    $fullName = trim(($candidat->prenom ?? '') . ' ' . ($candidat->name ?? ''));
    $initials = strtoupper(substr($candidat->prenom ?? '', 0, 1) . substr($candidat->name ?? '', 0, 1));
    $cvUrl = $postulation?->cv ? route('preview.cv-ia.ep', $postulation) : null;
    $letterUrl = $postulation?->cover_letter
      ? route('entreprise.candidature.preview', ['postulationId' => $postulation->id, 'type' => 'motivation'])
      : ($postulation?->lettre_motivation ? route('entreprise.candidature.preview', ['postulationId' => $postulation->id, 'type' => 'motivation']) : null);
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
    $source = request('source');
    $backUrl = request('from') === 'inbox'
      ? route('entreprise.candidatures.index', array_filter(['source' => $source]))
      : route('entreprise.offres.candidatures', array_filter(['offre' => $postulation->offre, 'source' => $source]));
  @endphp

    <section class="py-8 px-4 md:px-10">
      <div class="max-w-6xl mx-auto">
        <a href="{{ $backUrl }}" class="inline-flex items-center gap-2 text-sm text-secondary-container font-semibold hover:underline mb-6">
          <span class="material-symbols-outlined text-lg">arrow_back</span> {{ request('from') === 'inbox' ? 'Retour à toutes les candidatures' : ($source === 'ia' ? 'Retour aux profils proposés' : 'Retour à la liste') }}
        </a>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-8">
          <div>
            <p class="text-xs font-black uppercase tracking-[0.2em] text-secondary-container mb-2">Dossier de candidature</p>
            <h1 class="text-2xl md:text-3xl font-bold font-serif text-primary">{{ $postulation->offre?->titre }}</h1>
          </div>
          <span class="self-start sm:self-auto px-4 py-2 text-sm font-bold rounded-full {{ $statusStyles[$postulation->status] ?? 'bg-surface-container text-primary' }}">
            {{ $statusLabels[$postulation->status] ?? ucfirst($postulation->status) }}
          </span>
        </div>

        <div class="bg-secondary-container/10 rounded-2xl p-6 md:p-8 mb-8 flex flex-col sm:flex-row items-start gap-6">
          <div class="w-24 h-24 md:w-28 md:h-28 rounded-full bg-primary-container flex items-center justify-center text-white text-4xl font-bold flex-shrink-0">{{ $initials ?: 'CA' }}</div>
          <div class="flex-1">
            <h2 class="text-2xl font-bold font-serif text-primary mb-1">{{ $fullName ?: 'Candidat' }}</h2>
            <p class="text-on-surface-variant mb-4">{{ $candidat->candidateSector?->sector?->name ?? 'Profil candidat' }}</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-on-surface-variant mb-4">
              <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">mail</span> {{ $candidat->email }}</div>
              <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">call</span> {{ $candidat->telephone ?? 'À confirmer' }}</div>
              <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">work</span> {{ $candidat->candidateSector?->experience_years ?? 0 }} ans d’expérience</div>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
              @if ($postulation)
                <span class="px-3 py-1.5 bg-secondary-container/10 text-secondary-container text-sm font-semibold rounded-full flex items-center gap-1.5"><span class="material-symbols-outlined text-sm">calendar_today</span> Reçue le {{ optional($postulation->created_at)->format('d/m/Y') }}</span>
                @if (!is_null($postulation->match_score))
                  <span class="px-3 py-1.5 bg-primary-container/10 text-primary text-sm font-semibold rounded-full flex items-center gap-1.5"><span class="material-symbols-outlined text-sm">data_usage</span> Compatibilité estimée : {{ round($postulation->match_score) }} %</span>
                @endif
              @endif
              @if ($candidat->candidateSector?->diplome?->nom_diplome)
                <span class="px-3 py-1.5 bg-secondary-container/10 text-secondary-container text-sm font-semibold rounded-full flex items-center gap-1.5"><span class="material-symbols-outlined text-sm">school</span> {{ $candidat->candidateSector->diplome->nom_diplome }}</span>
              @endif
            </div>
            @if (!is_null($postulation->match_score))
              <p class="mt-3 text-xs text-on-surface-variant">Cette estimation compare les compétences et l’expérience du profil aux critères de l’offre. Elle aide au tri, mais ne remplace pas votre évaluation.</p>
              @if (is_array($postulation->match_details))
                <div class="mt-3 grid grid-cols-1 gap-2 text-xs sm:grid-cols-3">
                  <span class="rounded-lg bg-surface-container-low px-3 py-2">Compétences techniques : {{ round($postulation->match_details['technical_skills_match'] ?? 0) }} %</span>
                  <span class="rounded-lg bg-surface-container-low px-3 py-2">Compétences générales : {{ round($postulation->match_details['general_skills_match'] ?? 0) }} %</span>
                  <span class="rounded-lg bg-surface-container-low px-3 py-2">Expérience : {{ round($postulation->match_details['experience_match'] ?? 0) }} %</span>
                </div>
              @endif
            @endif
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
                <p><span class="font-semibold text-primary">Candidature reçue :</span> {{ optional($postulation->created_at)->format('d/m/Y à H:i') }}</p>
                <p><span class="font-semibold text-primary">Origine :</span> {{ $postulation->autopostulation ? 'Sélection IA' : 'Candidature directe' }}</p>
                <p><span class="font-semibold text-primary">Étape actuelle :</span> {{ $statusLabels[$postulation->status] ?? ucfirst($postulation->status) }}</p>
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

            @if (!in_array($postulation->status, ['accepted', 'rejected'], true))
              <div class="card-glow rounded-2xl p-6">
                <h3 class="text-lg font-bold font-serif text-primary mb-2 flex items-center gap-2"><span class="material-symbols-outlined text-secondary-container">conversion_path</span> Étape du recrutement</h3>
                <p class="mb-4 text-sm text-on-surface-variant">Choisissez la prochaine étape pour garder un suivi clair.</p>
                <form method="POST" action="{{ route('candidature.updateStatus', $postulation) }}" class="space-y-3">
                    @csrf
                    @method('PUT')
                    <label for="status" class="sr-only">Nouvelle étape</label>
                    <select id="status" name="status" class="w-full rounded-xl border border-outline-variant/30 bg-white px-4 py-3 text-sm text-primary focus:border-secondary-container/50 focus:ring-0">
                      @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected($postulation->status === $value)>{{ $label }}</option>
                      @endforeach
                    </select>
                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-secondary-container px-5 py-3 text-sm font-bold text-white transition-colors hover:bg-secondary">
                      <span class="material-symbols-outlined text-lg">save</span> Enregistrer l’étape
                    </button>
                  </form>
              </div>
            @else
              <div class="card-glow rounded-2xl p-6">
                <h3 class="text-lg font-bold font-serif text-primary mb-2">Décision enregistrée</h3>
                <p class="text-sm text-on-surface-variant">Cette candidature est {{ strtolower($statusLabels[$postulation->status] ?? $postulation->status) }}. Les actions de décision ne sont plus proposées.</p>
              </div>
            @endif
          </div>
        </div>
      </div>
    </section>
@endsection
