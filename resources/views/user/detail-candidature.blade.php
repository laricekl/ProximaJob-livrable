@extends('layouts.candidat')
@section('title', 'Ma candidature')
@section('styles')
  <style>
    /* Timeline connector */
    .timeline-step:not(:last-child)::after { content: ''; position: absolute; left: 15px; top: 40px; bottom: -16px; width: 2px; background: rgba(var(--pj-accent-rgb),0.14); }
    .timeline-step.completed::after { background: var(--pj-accent); }
    .timeline-step.completed .timeline-dot { background: var(--pj-accent); border-color: var(--pj-accent); }
    .timeline-step.active .timeline-dot { background: white; border-color: var(--pj-accent); box-shadow: 0 0 0 4px rgba(var(--pj-accent-rgb),0.14); }
    /* Card expand */
    .expandable-content { max-height: 0; overflow: hidden; transition: max-height 0.4s ease; }
    .expandable-content.open { max-height: 2000px; }
    /* Copy feedback */
    .copy-feedback { opacity: 0; transition: opacity 0.2s; }
    .copy-feedback.show { opacity: 1; }
  </style>
@endsection
@section('content')
  <main class="flex-grow pt-32 pb-16">

    <!-- Top bar -->
    <section class="px-4 md:px-10 mb-6">
      <div class="max-w-6xl mx-auto">
        @php
          $offre = $postulation?->offre;
          $cvPreviewUrl = $postulation?->cv ? route('preview.cv-ia', $postulation) : null;
          $letterPreviewUrl = $postulation?->cover_letter ? route('preview.letter-ia', $postulation) : null;
          $offerUrl = $offre ? route('job_details', $offre) : route('offres');
          $candidate = $postulation?->user;
          $cvProfile = $candidate?->cvProfile;
          $statusLabels = [
            'en_attente' => 'En cours',
            'accepted' => 'Acceptée',
            'rejected' => 'Refusée',
          ];
          $statusBadge = [
            'en_attente' => 'bg-info-light text-info-dark',
            'accepted' => 'bg-success-light text-success-dark',
            'rejected' => 'bg-error-light text-error-dark',
          ];
          $applicationStatus = $postulation?->status ?? 'en_attente';
          $applicationStatusLabel = $statusLabels[$applicationStatus] ?? ucfirst(str_replace('_', ' ', $applicationStatus));
          $candidateName = trim(($cvProfile?->prenom ?? $candidate?->prenom ?? '') . ' ' . ($cvProfile?->nom ?? $candidate?->name ?? ''));
          $candidateName = $candidateName !== '' ? $candidateName : 'Candidat ProximaJob';
          $candidateHeadline = $cvProfile?->experiences->first()?->poste ?? 'Profil candidat';
          $candidateSkills = $candidate?->skills?->pluck('name')->filter()->take(5)
            ?? collect();
          if ($candidateSkills->isEmpty() && $cvProfile) {
            $candidateSkills = $cvProfile->competences->pluck('description')->filter()->take(5);
          }
          $salaryRange = $offre && ($offre->salaire_min || $offre->salaire_max)
            ? trim(($offre->salaire_min ? number_format((float) $offre->salaire_min, 0, ',', ' ') : '?') . ' - ' . ($offre->salaire_max ? number_format((float) $offre->salaire_max, 0, ',', ' ') : '?') . ' $')
            : 'Salaire non précisé';
          $historyRoute = $postulation?->autopostulation ? route('user.historiques_ia') : route('user.historiques');
          $applicationSourceLabel = $postulation?->autopostulation ? 'Candidature assistée par IA' : 'Candidature envoyée par le candidat';
          $personalizationUrl = $offre ? route('cv.personalization.form', ['offre_id' => $offre->id]) : route('cv.personalization.form');
        @endphp
        <a href="{{ $historyRoute }}" class="inline-flex items-center gap-2 text-sm text-secondary-container font-semibold hover:underline mb-4">
          <span class="material-symbols-outlined text-lg">arrow_back</span> Retour aux candidatures
        </a>

        <!-- Offer header card -->
        <div class="card-glow rounded-2xl p-6 md:p-8 flex flex-col sm:flex-row items-start gap-6">
          <div class="w-16 h-16 md:w-20 md:h-20 rounded-2xl bg-secondary-container/10 flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-secondary-container text-3xl">code</span>
          </div>
          <div class="flex-1 w-full">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
              <div>
                <h1 class="text-2xl font-bold font-serif text-primary">{{ $offre?->poste ?: 'Votre candidature' }}</h1>
                <p class="text-on-surface-variant mt-1">{{ $offre?->entreprise->company_name ?? 'Entreprise' }} • {{ $offre?->localisation ?? 'Localisation a confirmer' }} • {{ $offre?->type->nom ?? 'Offre' }}</p>
              </div>
              <span class="self-start px-4 py-1.5 {{ $statusBadge[$applicationStatus] ?? 'bg-surface-container text-primary' }} text-2xs font-black uppercase tracking-widest rounded-full whitespace-nowrap">{{ $applicationStatusLabel }}</span>
            </div>
            <div class="flex flex-wrap items-center gap-4 text-sm">
              <span class="flex items-center gap-1.5 text-secondary-container font-bold">
                <span class="material-symbols-outlined text-lg">description</span> Dossier candidat actif
              </span>
              <span class="text-outline">•</span>
              <span class="text-on-surface-variant">Postulé le {{ $postulation?->created_at?->format('d M Y') ?? 'date indisponible' }}</span>
              <span class="text-outline">•</span>
              <span class="text-on-surface-variant flex items-center gap-1"><span class="material-symbols-outlined text-sm">manage_search</span> {{ $applicationSourceLabel }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Content grid -->
    <section class="px-4 md:px-10">
      <div class="max-w-6xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

          <!-- ====== LEFT: Timeline + CV + Letter ====== -->
          <div class="lg:col-span-2 space-y-6">

            <!-- Timeline -->
            <div class="card-glow rounded-2xl p-6">
              <h2 class="text-lg font-bold font-serif text-primary mb-6 flex items-center gap-2"><span class="material-symbols-outlined text-secondary-container">timeline</span> Suivi de candidature</h2>
              <div class="space-y-1">
                <div class="timeline-step completed relative flex items-start gap-4 pb-8">
                  <div class="timeline-dot relative z-10 w-8 h-8 rounded-full bg-secondary-container border-2 border-secondary-container flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined text-white text-sm">check</span></div>
                  <div>
                    <p class="font-bold text-primary text-sm">Candidature envoyée</p>
                    <p class="text-xs text-outline mt-0.5">{{ $postulation?->created_at?->format('d M Y \\a\\ H:i') ?? 'Date indisponible' }} • {{ $applicationSourceLabel }}</p>
                  </div>
                </div>
                <div class="timeline-step {{ $applicationStatus !== 'en_attente' ? 'completed' : 'active' }} relative flex items-start gap-4 pb-8">
                  <div class="timeline-dot relative z-10 w-8 h-8 rounded-full {{ $applicationStatus !== 'en_attente' ? 'bg-secondary-container border-secondary-container' : 'bg-white border-secondary-container' }} border-2 flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined {{ $applicationStatus !== 'en_attente' ? 'text-white' : 'text-secondary-container' }} text-sm">{{ $applicationStatus !== 'en_attente' ? 'check' : 'schedule' }}</span></div>
                  <div>
                    <p class="font-bold text-primary text-sm">{{ $applicationStatus === 'accepted' ? 'Retour positif reçu' : ($applicationStatus === 'rejected' ? 'Retour employeur reçu' : "Dossier en cours d'évaluation") }}</p>
                    <p class="text-xs text-outline mt-0.5">{{ $applicationStatus === 'en_attente' ? "L'entreprise examine actuellement votre candidature." : "Le statut actuel de votre candidature est : {$applicationStatusLabel}." }}</p>
                  </div>
                </div>
                <div class="timeline-step {{ $applicationStatus === 'accepted' ? 'active' : '' }} relative flex items-start gap-4 pb-8">
                  <div class="timeline-dot relative z-10 w-8 h-8 rounded-full {{ $applicationStatus === 'accepted' ? 'bg-white border-secondary-container' : 'bg-surface-container border-outline-variant/30' }} border-2 flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined {{ $applicationStatus === 'accepted' ? 'text-secondary-container' : 'text-outline' }} text-sm">{{ $applicationStatus === 'accepted' ? 'event_available' : 'schedule' }}</span></div>
                  <div>
                    <p class="font-bold {{ $applicationStatus === 'accepted' ? 'text-primary' : 'text-outline' }} text-sm">Suite du recrutement</p>
                    <p class="text-xs text-outline mt-0.5">{{ $applicationStatus === 'accepted' ? 'Un entretien ou une prochaine étape peut maintenant être planifié.' : "Cette étape sera mise à jour dès qu'une action employeur sera enregistrée." }}</p>
                  </div>
                </div>
                <div class="timeline-step relative flex items-start gap-4">
                  <div class="timeline-dot relative z-10 w-8 h-8 rounded-full bg-surface-container border-2 border-outline-variant/30 flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined text-outline text-sm">calendar_today</span></div>
                  <div>
                    <p class="font-bold text-outline text-sm">Entretien</p>
                    <p class="text-xs text-outline mt-0.5">{{ $applicationStatus === 'accepted' ? "À confirmer avec l'entreprise" : 'En attente' }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Application CV -->
            <div class="card-glow rounded-2xl overflow-hidden">
              <div class="px-8 py-5 border-b border-outline-variant/10 flex items-center justify-between">
                <h2 class="text-lg font-bold font-serif text-primary flex items-center gap-2"><span class="material-symbols-outlined text-secondary-container">description</span> CV de candidature</h2>
                <span class="px-3 py-1 bg-secondary-container/10 text-secondary-container text-2xs font-black uppercase tracking-wider rounded-full">Document joint</span>
              </div>
              <div class="p-6">
                <div class="bg-surface-container-low rounded-xl p-6 text-sm text-on-surface-variant leading-relaxed max-h-64 overflow-y-auto">
                  <p class="font-bold text-primary text-base mb-3">{{ $candidateName }}</p>
                  <p class="mb-3">{{ $candidateHeadline }} | {{ $cvProfile?->ville ?: 'Ville à compléter' }} | {{ $candidate?->email ?: 'Courriel indisponible' }} | {{ $cvProfile?->telephone ?: ($candidate?->telephone ?: 'Téléphone à compléter') }}</p>
                  <p class="font-bold text-primary mb-2">Résumé professionnel</p>
                  <p class="mb-4">{{ $cvProfile?->experiences->first()?->description ?: "Votre CV et vos expériences seront visibles ici lorsqu'elles sont disponibles." }}</p>
                  <p class="font-bold text-primary mb-2">Expérience</p>
                  @forelse (($cvProfile?->experiences ?? collect())->take(2) as $experience)
                    <p class="mb-1"><strong>{{ $experience->poste }}</strong> — {{ $experience->entreprise ?: 'Entreprise non précisée' }}</p>
                    <p class="mb-3 text-xs">{{ $experience->periode ?: 'Période à compléter' }}</p>
                  @empty
                    <p class="mb-3 text-xs">Aucune expérience ajoutée dans le profil CV pour le moment.</p>
                  @endforelse
                  <p class="font-bold text-primary mb-2">Compétences</p>
                  <p>{{ $candidateSkills->isNotEmpty() ? $candidateSkills->implode(' • ') : 'Ajoutez des compétences dans votre profil pour enrichir ce dossier.' }}</p>
                </div>
                <div class="flex items-center gap-3 mt-4">
                  @if ($cvPreviewUrl)
                  <a href="{{ $cvPreviewUrl }}" target="_blank" rel="noopener" class="flex items-center gap-2 px-5 py-2.5 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-colors">
                    <span class="material-symbols-outlined text-lg">description</span> Ouvrir mon CV
                  </a>
                  @endif
                  @if ($letterPreviewUrl)
                  <a href="{{ $letterPreviewUrl }}" target="_blank" rel="noopener" class="flex items-center gap-2 px-5 py-2.5 border-2 border-secondary-container text-secondary-container text-sm font-bold rounded-xl hover:bg-secondary-container/5 transition-colors">
                    <span class="material-symbols-outlined text-lg">visibility</span> Voir la lettre
                  </a>
                  @else
                  <a href="{{ $offerUrl }}" class="flex items-center gap-2 px-5 py-2.5 border-2 border-secondary-container text-secondary-container text-sm font-bold rounded-xl hover:bg-secondary-container/5 transition-colors">
                    <span class="material-symbols-outlined text-lg">north_east</span> Voir l'offre
                  </a>
                  @endif
                </div>
                @if (! $cvPreviewUrl && ! $letterPreviewUrl)
                <p class="mt-3 text-xs text-outline">Aucun fichier associé à cette candidature n'est disponible en prévisualisation pour le moment.</p>
                @endif
              </div>
            </div>

            <!-- Cover Letter -->
            <div class="card-glow rounded-2xl overflow-hidden">
              <div class="px-8 py-5 border-b border-outline-variant/10 flex items-center justify-between">
                <h2 class="text-lg font-bold font-serif text-primary flex items-center gap-2"><span class="material-symbols-outlined text-secondary-container">description</span> Lettre de présentation</h2>
                @if (! $letterPreviewUrl)
                <span class="px-3 py-1 bg-surface-container text-outline text-2xs font-black uppercase tracking-wider rounded-full">Optionnelle</span>
                @endif
                @if ($letterPreviewUrl)
                <button type="button" onclick="const t=this.closest('.card-glow').querySelector('.letter-content')?.innerText;if(t){navigator.clipboard.writeText(t);this.querySelector('.copy-feedback').classList.remove('hidden');setTimeout(()=>this.querySelector('.copy-feedback').classList.add('hidden'),2000)}" class="copy-btn flex items-center gap-1 text-xs font-bold text-secondary-container hover:underline">
                  <span class="material-symbols-outlined text-sm">content_copy</span> Copier
                  <span class="copy-feedback hidden text-success text-xs">✓ Copié</span>
                </button>
                @endif
              </div>
              <div class="p-6">
                <div class="bg-surface-container-low rounded-xl p-6 text-sm text-on-surface-variant leading-relaxed max-h-48 overflow-y-auto" id="coverLetter">
                  @if ($letterPreviewUrl)
                    <p>Une lettre de présentation est associée à cette candidature.</p>
                    <p class="mt-3">Utilisez le bouton de prévisualisation pour ouvrir le document complet dans un nouvel onglet.</p>
                  @else
                    <p>Aucune lettre de présentation n'a été jointe à cette candidature.</p>
                    <p class="mt-3">Cette étape est optionnelle. Vous pouvez toujours préparer un autre CV ou consulter l'offre.</p>
                  @endif
                </div>
              </div>
            </div>

          </div>

          <!-- ====== RIGHT: Sidebar ====== -->
          <div class="space-y-6">

            <!-- Application status card -->
            <div class="card-glow rounded-2xl p-6 text-center">
              <p class="text-xs font-bold text-outline uppercase tracking-widest mb-3">Etat du dossier</p>
              <div class="relative w-28 h-28 mx-auto mb-4">
                <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                  <circle cx="18" cy="18" r="15.5" fill="none" stroke="#eceef0" stroke-width="3" />
                  <circle cx="18" cy="18" r="15.5" fill="none" stroke="var(--pj-accent)" stroke-width="3" stroke-dasharray="{{ $applicationStatus === 'accepted' ? '100 100' : ($applicationStatus === 'rejected' ? '45 100' : '70 100') }}" stroke-linecap="round" />
                </svg>
                <span class="absolute inset-0 flex items-center justify-center text-base font-bold text-primary">{{ $applicationStatusLabel }}</span>
              </div>
              <p class="text-sm text-on-surface-variant">{{ $applicationStatus === 'accepted' ? "L'entreprise a donné une suite positive à votre candidature." : ($applicationStatus === 'rejected' ? 'Le dossier a reçu une réponse négative.' : 'Votre candidature est toujours en cours de traitement.') }}</p>
            </div>

            <!-- Offer summary -->
            <div class="card-glow rounded-2xl p-6">
              <h3 class="text-sm font-bold text-primary uppercase tracking-wider mb-4">Détails de l'offre</h3>
              <div class="space-y-3 text-sm">
                <div class="flex items-center gap-3"><span class="material-symbols-outlined text-outline text-lg">business</span><span class="text-on-surface-variant">{{ $offre?->entreprise->company_name ?? 'Entreprise indisponible' }}</span></div>
                <div class="flex items-center gap-3"><span class="material-symbols-outlined text-outline text-lg">location_on</span><span class="text-on-surface-variant">{{ $offre?->localisation ?? 'Localisation non précisée' }}</span></div>
                <div class="flex items-center gap-3"><span class="material-symbols-outlined text-outline text-lg">schedule</span><span class="text-on-surface-variant">{{ $offre?->type->nom ?? 'Type non précisé' }}</span></div>
                <div class="flex items-center gap-3"><span class="material-symbols-outlined text-outline text-lg">payments</span><span class="text-on-surface-variant">{{ $salaryRange }}</span></div>
                <div class="flex items-center gap-3"><span class="material-symbols-outlined text-outline text-lg">calendar_today</span><span class="text-on-surface-variant">Publiée le {{ $offre?->created_at?->format('d M Y') ?? 'date indisponible' }}</span></div>
              </div>
              <a href="{{ $offerUrl }}" class="flex items-center justify-center gap-2 w-full mt-4 py-2.5 border border-outline-variant/30 text-sm font-semibold rounded-xl hover:bg-surface-container-low transition-colors">Voir l'offre complète <span class="material-symbols-outlined text-sm">north_east</span></a>
            </div>

            <!-- Skills shared -->
            <div class="card-glow rounded-2xl p-6">
              <h3 class="text-sm font-bold text-primary uppercase tracking-wider mb-4">Compétences du dossier</h3>
              <div class="flex flex-wrap gap-2">
                @forelse ($candidateSkills as $skill)
                  <span class="rounded-full bg-secondary-container/10 px-3 py-1.5 text-xs font-semibold text-secondary-container">{{ $skill }}</span>
                @empty
                  <p class="text-sm text-on-surface-variant">Aucune compétence n'est encore rattachée au profil candidat.</p>
                @endforelse
              </div>
            </div>

            <!-- Actions -->
            <div class="card-glow rounded-2xl p-6 space-y-3">
              <a href="{{ $personalizationUrl }}" class="flex items-center justify-center gap-2 w-full py-3 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-colors">
                <span class="material-symbols-outlined text-lg">auto_awesome</span> Préparer un autre CV
              </a>
              <a href="{{ $historyRoute }}" class="flex items-center justify-center gap-2 w-full py-3 border-2 border-outline-variant/30 text-primary text-sm font-bold rounded-xl hover:bg-surface-container-low transition-colors">
                <span class="material-symbols-outlined text-lg">list_alt</span> Retour à l'historique
              </a>
            </div>

          </div>
        </div>
      </div>
    </section>
  </main>
@endsection
