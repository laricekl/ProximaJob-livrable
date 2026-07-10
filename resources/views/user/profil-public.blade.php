@extends('layouts.candidat')
@section('title', 'Profil candidat')
@php
  $publicUser = $user ?? auth()->user();
  $publicFullName = trim(($cvProfile?->prenom ?? $publicUser->prenom ?? '') . ' ' . ($cvProfile?->nom ?? $publicUser->name ?? ''));
  $publicFullName = $publicFullName !== '' ? $publicFullName : 'Profil candidat';
  $publicInitials = $publicUser->initials ?: 'PJ';
  $profileHeadline = $profileData['headline'] ?? 'Profil en recherche active';
  $profileLocation = $profileData['location'] ?? 'À compléter';
  $profilePhone = $profileData['phone'] ?? 'À compléter';
  $profileExperienceYears = $profileData['experience_years'] ?? 0;
  $profileSkills = $profileData['skills'] ?? collect();
  $profileSkillsCount = $profileData['skills_count'] ?? 0;
  $profileCompletion = $profileData['completion_percentage'] ?? 0;
  $profileApplicationsCount = $profileData['applications_count'] ?? 0;
  $profilePitch = $profileData['pitch'] ?? '';
  $profileMotivation = $profileData['motivation'] ?? '';
  $latestCvLabel = $profileData['latest_cv_label'] ?? null;
  $isRecruiterPreview = request('preview') === 'employeur';
  $editableAttr = $isRecruiterPreview ? 'false' : 'true';
@endphp
@section('styles')
  <style>
    /* Inline edit */
    .editable-field { border: 2px solid transparent; border-radius: 0.75rem; padding: 0.75rem 1rem; transition: all 0.2s ease; cursor: pointer; }
    .editable-field:hover { border-color: rgba(var(--pj-accent-rgb),0.12); background: rgba(var(--pj-accent-rgb),0.03); }
    .editable-field:focus { border-color: var(--pj-accent); background: white; outline: none; cursor: text; }
    .editable-field[contenteditable]:empty:before { content: attr(data-placeholder); color: #76777d; }
    .recruiter-preview [contenteditable="false"] { cursor: default; }
    .recruiter-preview input,
    .recruiter-preview select,
    .recruiter-preview textarea { pointer-events: none; background: rgba(246,244,240,0.7); }
    /* Photo hover overlay */
    .photo-wrapper:hover .photo-overlay { opacity: 1; }
    /* Section save feedback */
    .save-feedback { opacity: 0; transform: translateY(-4px); transition: all 0.3s ease; }
    .save-feedback.show { opacity: 1; transform: translateY(0); }
    /* Character counter */
    .char-count.warn { color: var(--pj-accent); }
    /* Sidebar highlight */
    .sidebar-highlight { position: relative; }
    .sidebar-highlight::before { content: ""; position: absolute; left: -12px; top: 8px; bottom: 8px; width: 3px; background: var(--pj-accent); border-radius: 3px; opacity: 0; transition: opacity 0.3s; }
    .sidebar-highlight.active::before { opacity: 1; }
  </style>
@endsection
@section('content')
  <main class="flex-grow pt-32 pb-16 {{ $isRecruiterPreview ? 'recruiter-preview' : '' }}">

    <!-- Top bar: title + preview -->
    <section class="px-4 md:px-10 mb-6">
      <div class="max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl md:text-3xl font-bold font-serif text-primary">{{ $isRecruiterPreview ? 'Aperçu employeur' : 'Mon profil candidat' }}</h1>
          <p class="text-sm text-on-surface-variant mt-1">{{ $isRecruiterPreview ? "Voici ce que l'employeur voit quand il consulte votre profil candidat." : 'Ces informations enrichissent vos candidatures et aident les employeurs à comprendre votre profil.' }}</p>
        </div>
        <div class="flex items-center gap-3">
          <a href="{{ $isRecruiterPreview ? route('user.profil-public') : route('user.profil-public', ['preview' => 'employeur']) }}" class="flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-secondary-container border border-secondary-container/30 rounded-full hover:bg-secondary-container/5 transition-colors">
            <span class="material-symbols-outlined text-lg">{{ $isRecruiterPreview ? 'edit' : 'visibility' }}</span>
            <span class="hidden sm:inline">{{ $isRecruiterPreview ? 'Retour édition' : 'Prévisualiser comme employeur' }}</span>
          </a>
        </div>
      </div>
    </section>

    <!-- ============ CONTENT GRID ============ -->
    <section class="px-4 md:px-10">
      <div class="max-w-7xl mx-auto">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

          <!-- ========== SIDEBAR (1/3) ========== -->
          <aside class="space-y-6">

            <!-- Photo + Identity Card -->
            <div class="card-glow rounded-2xl overflow-hidden">
              <div class="p-6 flex flex-col items-center text-center">
                <!-- Photo -->
                <div class="photo-wrapper relative w-28 h-28 mb-4">
                  <div class="w-28 h-28 rounded-full bg-primary-container flex items-center justify-center text-white text-4xl font-bold">{{ $publicInitials }}</div>
                  @unless ($isRecruiterPreview)
                  <div class="photo-overlay absolute inset-0 rounded-full bg-black/50 flex items-center justify-center opacity-0 transition-opacity cursor-pointer">
                    <span class="material-symbols-outlined text-white text-2xl">photo_camera</span>
                  </div>
                  @endunless
                </div>
                <div class="w-full">
                  <h2 class="text-xl font-bold font-serif text-primary" contenteditable="{{ $editableAttr }}" data-placeholder="Votre nom complet">{{ $publicFullName }}</h2>
                  <p class="text-sm text-on-surface-variant mt-1" contenteditable="{{ $editableAttr }}" data-placeholder="Titre professionnel">{{ $profileHeadline }}</p>
                </div>
              </div>
              <div class="border-t border-outline-variant/10 px-6 py-4 space-y-3">
                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                  <span class="material-symbols-outlined text-lg text-outline">location_on</span>
                  <span contenteditable="{{ $editableAttr }}" data-placeholder="Votre ville">{{ $profileLocation }}</span>
                </div>
                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                  <span class="material-symbols-outlined text-lg text-outline">mail</span>
                  <span contenteditable="{{ $editableAttr }}" data-placeholder="Courriel">{{ $publicUser->email ?? 'jean.dupont@email.com' }}</span>
                </div>
                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                  <span class="material-symbols-outlined text-lg text-outline">call</span>
                  <span contenteditable="{{ $editableAttr }}" data-placeholder="Téléphone">{{ $profilePhone }}</span>
                </div>
                <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                  <span class="material-symbols-outlined text-lg text-outline">language</span>
                  <span contenteditable="{{ $editableAttr }}" data-placeholder="LinkedIn / portfolio">LinkedIn / portfolio</span>
                </div>
              </div>
            </div>

            <!-- Quick stats -->
            <div class="card-glow rounded-2xl p-6">
              <h3 class="text-sm font-bold text-primary uppercase tracking-wider mb-4">En résumé</h3>
              <div class="grid grid-cols-2 gap-4">
                <div class="text-center p-3 bg-surface-container-low rounded-xl">
                  <p class="text-2xl font-bold text-primary">{{ $profileExperienceYears }}</p>
                  <p class="text-2xs text-outline uppercase tracking-wider mt-1">ans d'exp.</p>
                </div>
                <div class="text-center p-3 bg-surface-container-low rounded-xl">
                  <p class="text-2xl font-bold text-primary">{{ $profileSkillsCount }}</p>
                  <p class="text-2xs text-outline uppercase tracking-wider mt-1">compétences</p>
                </div>
                <div class="text-center p-3 bg-surface-container-low rounded-xl">
                  <p class="text-2xl font-bold text-secondary-container">{{ $profileCompletion }}%</p>
                  <p class="text-2xs text-outline uppercase tracking-wider mt-1">complétude</p>
                </div>
                <div class="text-center p-3 bg-surface-container-low rounded-xl">
                  <p class="text-2xl font-bold text-primary">{{ $profileApplicationsCount }}</p>
                  <p class="text-2xs text-outline uppercase tracking-wider mt-1">candidatures</p>
                </div>
              </div>
            </div>

            <!-- Preferences -->
            <div class="card-glow rounded-2xl p-6">
              <h3 class="text-sm font-bold text-primary uppercase tracking-wider mb-4">Préférences</h3>
              <div class="space-y-4">
                <div>
                  <label class="block text-xs font-semibold text-outline mb-1.5">Salaire souhaité</label>
                  <div class="flex items-center gap-2">
                    <input type="number" class="w-full px-3 py-2.5 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" placeholder="65000" value="{{ $publicUser->salary_expectation_min ?? '' }}" @disabled($isRecruiterPreview) />
                    <span class="text-sm text-outline font-medium">CAD/an</span>
                  </div>
                </div>
                <div>
                  <label class="block text-xs font-semibold text-outline mb-1.5">Disponibilité</label>
                  <select class="w-full px-3 py-2.5 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" @disabled($isRecruiterPreview)>
                    <option>Immédiate</option>
                    <option>1 mois</option>
                    <option>3 mois</option>
                    <option>En poste</option>
                  </select>
                </div>
                <div>
                  <label class="block text-xs font-semibold text-outline mb-1.5">Type de contrat recherché</label>
                  <div class="flex flex-wrap gap-2">
                    <label class="flex items-center gap-1.5 text-sm {{ $isRecruiterPreview ? '' : 'cursor-pointer' }}"><input type="checkbox" class="rounded border-outline-variant/50 text-secondary-container focus:ring-secondary-container/30" checked @disabled($isRecruiterPreview) /> <span>Permanent</span></label>
                    <label class="flex items-center gap-1.5 text-sm {{ $isRecruiterPreview ? '' : 'cursor-pointer' }}"><input type="checkbox" class="rounded border-outline-variant/50 text-secondary-container focus:ring-secondary-container/30" @disabled($isRecruiterPreview) /> <span>Temporaire</span></label>
                    <label class="flex items-center gap-1.5 text-sm {{ $isRecruiterPreview ? '' : 'cursor-pointer' }}"><input type="checkbox" class="rounded border-outline-variant/50 text-secondary-container focus:ring-secondary-container/30" checked @disabled($isRecruiterPreview) /> <span>Contractuel</span></label>
                    <label class="flex items-center gap-1.5 text-sm {{ $isRecruiterPreview ? '' : 'cursor-pointer' }}"><input type="checkbox" class="rounded border-outline-variant/50 text-secondary-container focus:ring-secondary-container/30" @disabled($isRecruiterPreview) /> <span>Stage</span></label>
                  </div>
                </div>
                @unless ($isRecruiterPreview)
                <button class="w-full py-2.5 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-colors flex items-center justify-center gap-2">
                  <span class="material-symbols-outlined text-lg">save</span> Enregistrer
                </button>
                @endunless
              </div>
            </div>

          </aside>

          <!-- ========== MAIN COLUMN (2/3) ========== -->
          <div class="lg:col-span-2 space-y-6">

            <!-- Pitch / Bio -->
            <div class="card-glow rounded-2xl overflow-hidden">
              <div class="px-8 py-5 border-b border-outline-variant/10 flex items-center justify-between">
                <div>
                  <h2 class="text-lg font-bold font-serif text-primary flex items-center gap-2"><span class="material-symbols-outlined text-secondary-container">auto_awesome</span> Mon pitch</h2>
                  <p class="text-xs text-on-surface-variant mt-0.5">Présentez-vous en quelques phrases. C'est la première chose que les employeurs lisent.</p>
                </div>
                <span class="char-count text-xs text-outline" data-target="pitchBio" data-max="500">0/500</span>
              </div>
              <div class="p-6">
                <textarea id="pitchBio" rows="5" class="w-full px-4 py-3 bg-white border border-outline-variant/20 rounded-xl text-sm text-primary placeholder:text-outline focus:border-secondary-container/50 focus:ring-0 transition-all resize-none" placeholder="Ex. Coordonnateur administratif avec 4 ans d'expérience, reconnu pour structurer les suivis, améliorer les processus et soutenir les équipes dans leurs priorités." maxlength="500" @readonly($isRecruiterPreview)>{{ $profilePitch }}</textarea>
                @unless ($isRecruiterPreview)
                <div class="flex justify-end mt-3">
                  <button class="save-section flex items-center gap-2 px-4 py-2 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-colors">
                    <span class="material-symbols-outlined text-lg">save</span> Enregistrer
                    <span class="save-feedback text-white text-xs">✓ Sauvegardé</span>
                  </button>
                </div>
                @endunless
              </div>
            </div>

            <!-- Compétences phares -->
            <div class="card-glow rounded-2xl overflow-hidden">
              <div class="px-8 py-5 border-b border-outline-variant/10 flex items-center justify-between">
                <div>
                  <h2 class="text-lg font-bold font-serif text-primary flex items-center gap-2"><span class="material-symbols-outlined text-secondary-container">handyman</span> Compétences phares</h2>
                  <p class="text-xs text-on-surface-variant mt-0.5">Mettez en avant vos compétences les plus fortes. Elles apparaîtront en premier.</p>
                </div>
                <span class="text-xs text-outline" id="skillCount">8/15</span>
              </div>
              <div class="p-6">
                <div id="skillTags" class="flex flex-wrap gap-2 mb-4">
                  @forelse ($profileSkills as $skillLabel)
                    <span class="skill-tag inline-flex items-center gap-1.5 px-3 py-1.5 bg-secondary-container/10 text-secondary-container text-sm font-medium rounded-full {{ $isRecruiterPreview ? '' : 'cursor-pointer hover:bg-secondary-container/20' }} transition-colors">{{ $skillLabel }} @unless ($isRecruiterPreview)<span class="material-symbols-outlined text-sm">close</span>@endunless</span>
                  @empty
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-surface-container text-outline text-sm font-medium rounded-full">Ajoutez des competences depuis votre profil</span>
                  @endforelse
                  @unless ($isRecruiterPreview)
                  <!-- Add skill button -->
                  <button id="addSkillBtn" class="inline-flex items-center gap-1.5 px-4 py-1.5 border-2 border-dashed border-outline-variant/50 text-outline text-sm font-medium rounded-full hover:border-secondary-container/50 hover:text-secondary-container transition-colors">
                    <span class="material-symbols-outlined text-lg">add</span> Ajouter
                  </button>
                  @endunless
                </div>
                @unless ($isRecruiterPreview)
                <div id="skillInputRow" class="hidden flex items-center gap-2">
                  <input id="skillInput" type="text" class="flex-1 px-4 py-2.5 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" placeholder="Nouvelle compétence..." />
                  <button id="skillAddConfirm" class="px-4 py-2.5 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-colors">Ajouter</button>
                  <button id="skillAddCancel" class="px-4 py-2.5 bg-surface-container text-sm font-semibold rounded-xl hover:bg-surface-container-low transition-colors">Annuler</button>
                </div>
                @endunless
              </div>
            </div>

            <!-- Motivation letter -->
            <div class="card-glow rounded-2xl overflow-hidden">
              <div class="px-8 py-5 border-b border-outline-variant/10 flex items-center justify-between">
                <div>
                  <h2 class="text-lg font-bold font-serif text-primary flex items-center gap-2"><span class="material-symbols-outlined text-secondary-container">description</span> Lettre de présentation</h2>
                  <p class="text-xs text-on-surface-variant mt-0.5">Une lettre de base que vous pouvez joindre ou adapter selon l'offre.</p>
                </div>
                @unless ($isRecruiterPreview)
                <a href="{{ route('cv.personalization.form') }}" class="text-xs font-bold text-secondary-container hover:underline flex items-center gap-1">
                  <span class="material-symbols-outlined text-sm">auto_awesome</span> Adapter à une offre
                </a>
                @endunless
              </div>
              <div class="p-6">
                <textarea id="pitchMotivation" rows="6" class="w-full px-4 py-3 bg-white border border-outline-variant/20 rounded-xl text-sm text-primary placeholder:text-outline focus:border-secondary-container/50 focus:ring-0 transition-all resize-none" placeholder="Rédigez une lettre de base que vous pourrez adapter pour une offre précise. Cette étape reste optionnelle." @readonly($isRecruiterPreview)>{{ $profileMotivation }}</textarea>
                @unless ($isRecruiterPreview)
                <div class="flex justify-end mt-3">
                  <button class="save-section flex items-center gap-2 px-4 py-2 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-colors">
                    <span class="material-symbols-outlined text-lg">save</span> Enregistrer
                    <span class="save-feedback text-white text-xs">✓ Sauvegardé</span>
                  </button>
                </div>
                @endunless
              </div>
            </div>

            <!-- Expérience pro (read-only, link to CV builder) -->
            <div class="card-glow rounded-2xl overflow-hidden">
              <div class="px-8 py-5 border-b border-outline-variant/10 flex items-center justify-between">
                <div>
                  <h2 class="text-lg font-bold font-serif text-primary flex items-center gap-2"><span class="material-symbols-outlined text-secondary-container">work</span> Expérience professionnelle</h2>
                  <p class="text-xs text-on-surface-variant mt-0.5">
                    Gérée depuis votre CV.
                    @unless ($isRecruiterPreview)
                      <a href="{{ route('infos.cv') }}" class="text-secondary-container font-semibold hover:underline">Modifier dans le builder CV →</a>
                    @endunless
                  </p>
                </div>
              </div>
              <div class="p-6">
                <div class="space-y-6">
                  @forelse (($cvProfile?->experiences ?? collect()) as $experience)
                    <div class="border-l-2 border-secondary-container pl-5 relative">
                      <div class="absolute -left-[5px] top-1 w-2 h-2 bg-secondary-container rounded-full"></div>
                      <h4 class="font-bold text-primary">{{ $experience->poste }}</h4>
                      <p class="text-sm text-on-surface-variant">{{ $experience->entreprise ?: 'Entreprise non precisee' }}</p>
                      <p class="text-xs text-outline mb-2">{{ $experience->periode ?: 'Periode a completer' }}</p>
                      @if ($experience->description)
                        <p class="text-sm text-on-surface-variant">{{ $experience->description }}</p>
                      @endif
                    </div>
                  @empty
                    <p class="text-sm text-on-surface-variant">Aucune experience n'est encore indiquée dans votre CV.</p>
                  @endforelse
                </div>
              </div>
            </div>

            <!-- CV Upload + Generated CVs -->
            <div class="card-glow rounded-2xl overflow-hidden">
              <div class="px-8 py-5 border-b border-outline-variant/10">
                <h2 class="text-lg font-bold font-serif text-primary flex items-center gap-2"><span class="material-symbols-outlined text-secondary-container">picture_as_pdf</span> Mes CV</h2>
                <p class="text-xs text-on-surface-variant mt-0.5">{{ $isRecruiterPreview ? 'CV disponible pour la candidature.' : 'Gérez votre CV principal et vos versions enregistrées.' }}</p>
              </div>
              <div class="p-6 space-y-4">
                @unless ($isRecruiterPreview)
                <!-- Upload zone -->
                <div class="border-2 border-dashed border-outline-variant/30 rounded-2xl p-8 text-center hover:border-secondary-container/40 transition-colors cursor-pointer">
                  <span class="material-symbols-outlined text-4xl text-outline mb-3">upload_file</span>
                  <p class="text-sm font-semibold text-primary">Déposez votre CV ici</p>
                  <p class="text-xs text-outline mt-1">PDF, DOCX — max 5 Mo</p>
                  <input type="file" class="hidden" accept=".pdf,.docx" />
                </div>
                @endunless
                <!-- Existing CVs -->
                @if ($latestCvLabel)
                  <div class="flex items-center justify-between p-4 bg-surface-container-low rounded-xl">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-lg bg-secondary-container/10 flex items-center justify-center"><span class="material-symbols-outlined text-secondary-container">description</span></div>
                      <div>
                        <p class="text-sm font-semibold text-primary">{{ $latestCvLabel }}</p>
                        <p class="text-2xs text-outline">CV deja ajoute a votre compte</p>
                      </div>
                    </div>
                    <div class="flex items-center gap-2">
                      <a href="{{ route('infos.cv') }}" class="w-9 h-9 rounded-full hover:bg-surface-container transition-colors flex items-center justify-center" title="Voir"><span class="material-symbols-outlined text-lg text-outline">visibility</span></a>
                      @unless ($isRecruiterPreview)
                        <a href="{{ route('cv.personalization.form') }}" class="w-9 h-9 rounded-full hover:bg-surface-container transition-colors flex items-center justify-center" title="Adapter"><span class="material-symbols-outlined text-lg text-outline">auto_awesome</span></a>
                      @endunless
                    </div>
                  </div>
                @endif
                @unless ($isRecruiterPreview)
                <a href="{{ route('cv.personalization.form') }}" class="flex items-center justify-center gap-2 w-full py-3 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-colors">
                  <span class="material-symbols-outlined text-lg">auto_awesome</span> Adapter mon CV à une offre
                </a>
                @endunless
              </div>
            </div>

          </div><!-- end main column -->
        </div><!-- end grid -->

      </div>
    </section>

  </main>
@endsection
