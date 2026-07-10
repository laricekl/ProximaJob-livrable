@extends('layouts.candidat')
@section('title', 'Mon CV principal')
@section('content')
  @php
    $uploadedCvPath = auth()->user()?->cv;
    $generatedCvs = $existingProfile?->cvGeneres ?? collect();
    $hasAnyCvDocument = $existingProfile || $uploadedCvPath || $generatedCvs->isNotEmpty();
    $existingCvProfilePayload = $existingProfile ? [
      'nom' => $existingProfile->nom,
      'prenom_cv' => $existingProfile->prenom,
      'email_cv' => $existingProfile->email,
      'telephone_cv' => $existingProfile->telephone,
      'adresse' => $existingProfile->adresse,
      'ville' => $existingProfile->ville,
      'code_postal' => $existingProfile->code_postal,
      'langues_competences' => $existingProfile->langues_competences,
      'logiciels' => $existingProfile->logiciels,
      'competences' => $existingProfile->competences->map(fn ($competence) => ['description' => $competence->description])->values(),
      'experiences' => $existingProfile->experiences->map(fn ($experience) => [
        'periode' => $experience->periode,
        'poste' => $experience->poste,
        'entreprise' => $experience->entreprise,
        'description' => $experience->description,
      ])->values(),
      'formations' => $existingProfile->formations->map(fn ($formation) => [
        'periode' => $formation->periode,
        'diplome' => $formation->diplome_id,
        'etablissement' => $formation->etablissement,
      ])->values(),
      'langues' => $existingProfile->langues->map(fn ($langue) => [
        'nom' => $langue->nom,
        'niveau' => $langue->niveau,
      ])->values(),
      'perfectionnements' => $existingProfile->perfectionnements->map(fn ($perfectionnement) => [
        'annee' => $perfectionnement->annee,
        'formation' => $perfectionnement->formation,
        'etablissement' => $perfectionnement->etablissement,
      ])->values(),
      'benevolats' => $existingProfile->benevolats->map(fn ($benevolat) => [
        'periode' => $benevolat->periode,
        'role' => $benevolat->role,
        'organisation' => $benevolat->organisation,
      ])->values(),
    ] : null;
    $principalContactLine = $existingProfile
      ? collect([$existingProfile->email, $existingProfile->telephone])->filter()->implode(' | ')
      : '';
  @endphp
  <main class="flex-grow pt-32 pb-16">

    <section class="py-8 px-4 md:px-10">
      <div class="max-w-7xl mx-auto">
        <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
          <div>
            <p class="text-xs font-black uppercase tracking-[0.18em] text-secondary-container">CV principal</p>
            <h1 class="mt-2 text-3xl font-bold font-serif text-primary">Construire mon CV</h1>
            <p class="mt-2 max-w-2xl text-sm text-on-surface-variant">Remplissez ici votre CV de base. La personnalisation par offre utilise ces informations pour générer une version adaptée à un poste précis.</p>
          </div>
        </div>

        <div id="cvLayout" class="mb-6 grid gap-6 lg:grid-cols-[360px_minmax(0,1fr)] lg:items-start">
        <aside class="min-w-0 rounded-2xl border border-outline-variant/10 bg-white p-4 md:p-5 lg:sticky lg:top-28">
          <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div class="min-w-0">
              <h2 class="text-lg font-bold font-serif text-primary">Mes CV</h2>
              <p class="mt-1 text-xs leading-5 text-on-surface-variant">CV principal, source et versions générées.</p>
            </div>
            <div class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-surface-container-low px-2.5 py-1 text-xs font-semibold text-outline">
              <span class="material-symbols-outlined text-base">folder_open</span>
              {{ ($existingProfile ? 1 : 0) + ($uploadedCvPath ? 1 : 0) + $generatedCvs->count() }} document(s)
            </div>
          </div>

          <div class="mt-4 space-y-3">
            @if ($existingProfile)
              <div class="min-w-0 rounded-xl border border-secondary-container/25 bg-white p-3 shadow-sm">
                <div class="flex min-w-0 items-start gap-3">
                  <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-secondary-container text-white">
                    <span class="material-symbols-outlined text-base">description</span>
                  </div>
                  <div class="min-w-0 flex-1">
                    <div class="flex min-w-0 items-start justify-between gap-2">
                      <div class="min-w-0">
                        <p class="truncate text-sm font-bold text-primary">CV principal</p>
                        <p class="truncate text-xs font-semibold text-on-surface-variant">{{ trim(($existingProfile->prenom ?? '') . ' ' . ($existingProfile->nom ?? '')) ?: 'CV candidat' }}</p>
                      </div>
                      <span class="shrink-0 rounded-full bg-secondary-container/10 px-2 py-0.5 text-2xs font-bold uppercase tracking-wide text-secondary-container">Actif</span>
                    </div>
                    <p class="mt-1 line-clamp-2 text-xs leading-4 text-on-surface-variant">{{ $existingProfile->experiences->first()?->poste ?: 'Structure CV editable depuis cette page' }}</p>
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                      <a href="{{ route('cv.personalization.form') }}" class="inline-flex items-center gap-1 text-xs font-bold text-secondary-container hover:text-secondary">
                        <span class="material-symbols-outlined text-sm">auto_awesome</span> Adapter a une offre
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            @endif

              <div class="min-w-0 rounded-xl border border-outline-variant/10 bg-white p-3 shadow-sm">
                <div class="flex min-w-0 items-start gap-3">
                  <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-surface-container-low text-outline">
                    <span class="material-symbols-outlined text-base">upload_file</span>
                  </div>
                  <div class="min-w-0 flex-1">
                    <div class="flex min-w-0 items-start justify-between gap-2">
                      <div class="min-w-0">
                        <p class="truncate text-sm font-bold text-primary">CV source</p>
                        <p class="break-all text-xs font-semibold leading-4 text-on-surface-variant">{{ $uploadedCvPath ? basename($uploadedCvPath) : 'Aucun fichier' }}</p>
                      </div>
                      @if ($uploadedCvPath)
                        <a href="{{ asset($uploadedCvPath) }}" target="_blank" rel="noopener" class="shrink-0 text-xs font-bold text-secondary-container hover:text-secondary">Ouvrir</a>
                      @endif
                    </div>
                    <p class="mt-1 line-clamp-2 text-xs leading-4 text-on-surface-variant">{{ $uploadedCvPath ? 'Source pour remplir le CV principal.' : 'PDF, DOCX, DOC ou TXT.' }}</p>
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                      <form id="sourceCvUploadForm" class="contents" enctype="multipart/form-data">
                        <label class="inline-flex cursor-pointer items-center gap-1 text-xs font-bold text-primary hover:text-secondary-container">
                          <span class="material-symbols-outlined text-sm">upload_file</span>
                          <span>{{ $uploadedCvPath ? 'Remplacer' : 'Importer' }}</span>
                          <input type="file" name="cv" accept=".pdf,.doc,.docx,.txt" class="hidden" onchange="uploadSourceCv(this.form)">
                        </label>
                      </form>
                      @if ($uploadedCvPath)
                        <button type="button" id="importUploadedCvBtn" class="inline-flex items-center gap-1 text-xs font-bold text-secondary-container hover:text-secondary" onclick="importUploadedCv()">
                          <span class="material-symbols-outlined text-sm">magic_button</span>
                          Analyser
                        </button>
                      @endif
                    </div>
                  </div>
                </div>
              </div>

            @forelse ($generatedCvs as $generatedCv)
              <div class="min-w-0 rounded-xl border border-outline-variant/10 bg-white p-3 shadow-sm">
                <div class="flex min-w-0 items-start gap-3">
                  <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-secondary-container/10 text-secondary-container">
                    <span class="material-symbols-outlined text-base">auto_awesome</span>
                  </div>
                  <div class="min-w-0 flex-1">
                    <div class="flex min-w-0 items-start justify-between gap-2">
                      <div class="min-w-0">
                        <p class="truncate text-sm font-bold text-primary">Version du CV</p>
                        <p class="truncate text-xs text-on-surface-variant">{{ $generatedCv->date_generation?->format('d/m/Y') ?: 'Version enregistrée' }}</p>
                      </div>
                      <button type="button" class="shrink-0 text-xs font-bold text-secondary-container hover:text-secondary" onclick="showGeneratedCvPreview(this)" data-title="{{ e($generatedCv->display_name) }}" data-file="Document PDF" data-preview-url="{{ route('cv.personalization.inline', ['filename' => basename($generatedCv->chemin_fichier)]) }}" data-open-url="{{ route('cv.personalization.preview', ['filename' => basename($generatedCv->chemin_fichier)]) }}">Voir</button>
                    </div>
                    <p class="mt-1 line-clamp-2 text-xs font-semibold leading-4 text-primary">{{ $generatedCv->display_name }}</p>
                    <p class="mt-1 truncate text-xs leading-4 text-on-surface-variant">Document PDF</p>
                  </div>
                </div>
              </div>
            @empty
              @if (! $existingProfile && ! $uploadedCvPath)
                <div class="rounded-2xl border border-dashed border-outline-variant/20 bg-surface-container-low p-5">
                  <p class="text-sm font-semibold text-primary">Aucun CV principal ou genere pour le moment</p>
                  <p class="text-xs text-on-surface-variant mt-1">Importez un CV source, completez le CV principal ou genere une version personnalisee pour enrichir cette liste.</p>
                </div>
              @endif
            @endforelse
          </div>

          <p class="mt-3 text-xs leading-4 text-outline">La personnalisation part toujours du CV principal.</p>
          <p id="cvImportStatus" class="mt-3 hidden rounded-xl border border-secondary-container/20 bg-secondary-container/10 px-4 py-3 text-sm font-semibold text-secondary-container"></p>
        </aside>

        <section id="cv-preview" class="rounded-2xl border border-outline-variant/10 bg-white p-5 md:p-8">
          <div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
              <p class="text-xs font-black uppercase tracking-[0.18em] text-secondary-container">Apercu</p>
              <h2 id="previewTitle" class="mt-1 text-2xl font-bold font-serif text-primary">CV principal</h2>
            </div>
            <div class="flex items-center gap-2">
              <a id="generatedCvOpenLink" href="{{ $existingProfile ? route('cv.principal.inline').'#zoom=100' : '#' }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 rounded-lg bg-white px-3 py-2 text-xs font-bold text-primary hover:bg-surface-container-low border border-outline-variant/20">
                <span class="material-symbols-outlined text-sm">open_in_new</span> Plein ecran
              </a>
              <a href="{{ $existingProfile ? route('cv.principal.download') : '#' }}" class="inline-flex items-center gap-1.5 rounded-lg bg-secondary-container px-3 py-2 text-xs font-bold text-white hover:bg-secondary transition-colors">
                <span class="material-symbols-outlined text-sm">download</span> Telecharger
              </a>
              <button type="button" onclick="toggleSidebar()" class="inline-flex items-center justify-center gap-1 rounded-lg border border-outline-variant/20 bg-white px-2.5 py-2 text-xs font-semibold text-primary hover:bg-surface-container-low">
                <span class="material-symbols-outlined text-sm" id="toggleSidebarIcon">fullscreen</span>
              </button>
            </div>
          </div>

          <div id="generatedCvPreview" class="{{ $existingProfile ? '' : 'hidden' }}">
            <div class="overflow-hidden rounded-xl border border-outline-variant/10 bg-surface-container-low">
              <iframe id="generatedCvFrame" scrolling="no" src="{{ $existingProfile ? route('cv.principal.inline').'#zoom=100' : '' }}" class="h-[600px] w-full bg-white md:h-[1120px]" style="zoom: 1.0; -moz-transform: scale(1.0); -moz-transform-origin: top left;" title="Apercu du CV"></iframe>
            </div>
          </div>

          <div id="principalCvPreview" class="{{ $existingProfile ? 'hidden' : '' }}">
          @if ($existingProfile)
            <div class="space-y-6">
              <div class="mx-auto flex max-w-[820px] items-center justify-between px-1 text-xs font-bold uppercase tracking-[0.16em] text-outline">
                <span>Page 1</span>
                <span>A4 apercu</span>
              </div>
              <div class="mx-auto min-h-[980px] max-w-[820px] rounded-lg border border-outline-variant/20 bg-white p-6 shadow-sm md:min-h-[1060px] md:p-10">
                <div class="border-b border-outline-variant/20 pb-5 text-center">
                  <h3 class="text-3xl font-bold text-primary">{{ trim(($existingProfile->prenom ?? '') . ' ' . ($existingProfile->nom ?? '')) ?: 'CV candidat' }}</h3>
                  <p class="mt-2 text-sm text-on-surface-variant">
                    {{ $principalContactLine }}
                  </p>
                </div>

                @if ($existingProfile->langues_competences || $existingProfile->logiciels)
                  <div class="mt-6">
                    <h4 class="text-sm font-black uppercase tracking-[0.16em] text-secondary-container">Competences</h4>
                    @if ($existingProfile->langues_competences)
                      <p class="mt-2 text-sm leading-6 text-on-surface-variant">{{ $existingProfile->langues_competences }}</p>
                    @endif
                    @if ($existingProfile->logiciels)
                      <p class="mt-2 text-sm leading-6 text-on-surface-variant"><span class="font-semibold text-primary">Outils:</span> {{ $existingProfile->logiciels }}</p>
                    @endif
                  </div>
                @endif

                @if ($existingProfile->experiences->isNotEmpty())
                  <div class="mt-6">
                    <h4 class="text-sm font-black uppercase tracking-[0.16em] text-secondary-container">Experience professionnelle</h4>
                    <div class="mt-3 space-y-4">
                      @foreach ($existingProfile->experiences as $experience)
                        <div>
                          <div class="flex flex-col gap-1 sm:flex-row sm:items-baseline sm:justify-between">
                            <p class="font-bold text-primary">{{ $experience->poste }}</p>
                            <p class="text-xs font-semibold text-outline">{{ $experience->periode }}</p>
                          </div>
                          @if ($experience->entreprise)
                            <p class="text-sm font-semibold text-on-surface-variant">{{ $experience->entreprise }}</p>
                          @endif
                          @if ($experience->description)
                            <p class="mt-1 text-sm leading-6 text-on-surface-variant">{{ $experience->description }}</p>
                          @endif
                        </div>
                      @endforeach
                    </div>
                  </div>
                @endif

                @if ($existingProfile->formations->isNotEmpty())
                  <div class="mt-6">
                    <h4 class="text-sm font-black uppercase tracking-[0.16em] text-secondary-container">Formation</h4>
                    <div class="mt-3 space-y-3">
                      @foreach ($existingProfile->formations as $formation)
                        <div class="flex flex-col gap-1 sm:flex-row sm:items-baseline sm:justify-between">
                          <div>
                            <p class="font-bold text-primary">{{ $formation->diplome }}</p>
                            <p class="text-sm text-on-surface-variant">{{ $formation->etablissement }}</p>
                          </div>
                          <p class="text-xs font-semibold text-outline">{{ $formation->periode }}</p>
                        </div>
                      @endforeach
                    </div>
                  </div>
                @endif

                @if ($existingProfile->langues->isNotEmpty())
                  <div class="mt-6">
                    <h4 class="text-sm font-black uppercase tracking-[0.16em] text-secondary-container">Langues</h4>
                    <p class="mt-2 text-sm text-on-surface-variant">
                      {{ $existingProfile->langues->map(fn ($langue) => trim($langue->nom . ($langue->niveau ? ': ' . $langue->niveau : '')))->implode(' | ') }}
                    </p>
                  </div>
                @endif
              </div>

              @if ($existingProfile->perfectionnements->isNotEmpty() || $existingProfile->benevolats->isNotEmpty())
                <div class="mx-auto flex max-w-[820px] items-center justify-between px-1 text-xs font-bold uppercase tracking-[0.16em] text-outline">
                  <span>Page 2</span>
                  <span>A4 apercu</span>
                </div>
                <div class="mx-auto min-h-[980px] max-w-[820px] rounded-lg border border-outline-variant/20 bg-white p-6 shadow-sm md:min-h-[1060px] md:p-10">

                  @if ($existingProfile->perfectionnements->isNotEmpty())
                    <div>
                      <h4 class="text-sm font-black uppercase tracking-[0.16em] text-secondary-container">Perfectionnement</h4>
                      <div class="mt-3 space-y-3">
                        @foreach ($existingProfile->perfectionnements as $perfectionnement)
                          <div class="flex flex-col gap-1 sm:flex-row sm:items-baseline sm:justify-between">
                            <div>
                              <p class="font-bold text-primary">{{ $perfectionnement->formation }}</p>
                              <p class="text-sm text-on-surface-variant">{{ $perfectionnement->etablissement }}</p>
                            </div>
                            <p class="text-xs font-semibold text-outline">{{ $perfectionnement->annee }}</p>
                          </div>
                        @endforeach
                      </div>
                    </div>
                  @endif

                  @if ($existingProfile->benevolats->isNotEmpty())
                    <div class="mt-6">
                      <h4 class="text-sm font-black uppercase tracking-[0.16em] text-secondary-container">Activites benevoles</h4>
                      <div class="mt-3 space-y-3">
                        @foreach ($existingProfile->benevolats as $benevolat)
                          <div class="flex flex-col gap-1 sm:flex-row sm:items-baseline sm:justify-between">
                            <div>
                              <p class="font-bold text-primary">{{ $benevolat->role }}</p>
                              <p class="text-sm text-on-surface-variant">{{ $benevolat->organisation }}</p>
                            </div>
                            <p class="text-xs font-semibold text-outline">{{ $benevolat->periode }}</p>
                          </div>
                        @endforeach
                      </div>
                    </div>
                  @endif
                </div>
              @endif
            </div>
          @else
            <div class="rounded-xl border border-dashed border-outline-variant/30 bg-surface-container-low p-8 text-center">
              <span class="material-symbols-outlined text-5xl text-outline">description</span>
              <h3 class="mt-3 text-lg font-bold text-primary">Aucun CV principal enregistré</h3>
              <p class="mx-auto mt-2 max-w-xl text-sm text-on-surface-variant">Importez un CV source pour le pre-remplir ou ouvrez le formulaire pour le creer manuellement.</p>
              <button type="button" onclick="openCvBuilder()" class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-secondary-container px-5 py-3 text-sm font-bold text-white transition-colors hover:bg-secondary">
                <span class="material-symbols-outlined text-lg">edit_note</span> Creer mon CV principal
              </button>
            </div>
          @endif
          </div>
        </section>
        </div>

        <div id="cv-builder" class="hidden flex-col md:flex-row gap-0 card-glow rounded-2xl overflow-hidden scroll-mt-32">

          <!-- Sidebar -->
          <aside class="md:w-80 flex-shrink-0 bg-gradient-to-b from-primary-container to-primary-container text-white p-8">
            <div class="mb-8">
              <div class="w-full bg-white/20 rounded-full h-2 mb-3 overflow-hidden">
                <div class="bg-white/80 h-full rounded-full transition-all duration-300" id="progressFill" style="width:12.5%"></div>
              </div>
              <p class="text-sm text-white/80" id="progressText">1/8 sections</p>
            </div>

            <nav class="space-y-2">
              <div class="cv-step active flex items-center gap-3 p-3 rounded-xl cursor-pointer" data-step="1">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold flex-shrink-0">1</div>
                <span class="text-sm font-semibold">Informations personnelles</span>
              </div>
              <div class="cv-step flex items-center gap-3 p-3 rounded-xl cursor-pointer" data-step="2">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold flex-shrink-0">2</div>
                <span class="text-sm font-semibold">Competences</span>
              </div>
              <div class="cv-step flex items-center gap-3 p-3 rounded-xl cursor-pointer" data-step="3">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold flex-shrink-0">3</div>
                <span class="text-sm font-semibold">Experience professionnelle</span>
              </div>
              <div class="cv-step flex items-center gap-3 p-3 rounded-xl cursor-pointer" data-step="4">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold flex-shrink-0">4</div>
                <span class="text-sm font-semibold">Formation</span>
              </div>
              <div class="cv-step flex items-center gap-3 p-3 rounded-xl cursor-pointer" data-step="5">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold flex-shrink-0">5</div>
                <span class="text-sm font-semibold">Perfectionnement</span>
              </div>
              <div class="cv-step flex items-center gap-3 p-3 rounded-xl cursor-pointer" data-step="6">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold flex-shrink-0">6</div>
                <span class="text-sm font-semibold">Langues</span>
              </div>
              <div class="cv-step flex items-center gap-3 p-3 rounded-xl cursor-pointer" data-step="7">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold flex-shrink-0">7</div>
                <span class="text-sm font-semibold">Activites benevoles</span>
              </div>
              <div class="cv-step flex items-center gap-3 p-3 rounded-xl cursor-pointer" data-step="8">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold flex-shrink-0">8</div>
                <span class="text-sm font-semibold">Enregistrement</span>
              </div>
            </nav>
          </aside>

          <!-- Contenu du formulaire -->
          <div class="flex-1 p-8">
            <form id="cvDataForm" method="POST" action="{{ route('cv.store') }}">
              @csrf

              <!-- Section 1: Infos personnelles -->
              <div class="cv-form-section active" id="section-1">
                <div class="mb-8 pb-4 border-b border-outline-variant/10">
                  <h2 class="text-2xl font-bold font-serif text-primary">Informations personnelles</h2>
                  <p class="text-sm text-on-surface-variant mt-1">Renseignez vos informations de base</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                  <div><label class="block text-sm font-semibold text-primary mb-1.5">Nom *</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="nom" required /></div>
                  <div><label class="block text-sm font-semibold text-primary mb-1.5">Prenom *</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="prenom_cv" required /></div>
                  <div><label class="block text-sm font-semibold text-primary mb-1.5">Courriel *</label><input type="email" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="email_cv" required /></div>
                  <div><label class="block text-sm font-semibold text-primary mb-1.5">Telephone *</label><input type="tel" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="telephone_cv" required /></div>
                  <div class="md:col-span-2"><label class="block text-sm font-semibold text-primary mb-1.5">Adresse</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="adresse" /></div>
                  <div><label class="block text-sm font-semibold text-primary mb-1.5">Ville et province</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="ville" placeholder="Montreal (Quebec)" /></div>
                  <div><label class="block text-sm font-semibold text-primary mb-1.5">Code postal</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="code_postal" placeholder="H1B 8T2" /></div>
                </div>
              </div>

              <!-- Section 2: Competences -->
              <div class="cv-form-section" id="section-2">
                <div class="mb-8 pb-4 border-b border-outline-variant/10">
                  <h2 class="text-2xl font-bold font-serif text-primary">Competences</h2>
                  <p class="text-sm text-on-surface-variant mt-1">Listez vos competences professionnelles</p>
                </div>
                <div class="space-y-5">
                  <div class="bg-white rounded-2xl border border-outline-variant/10 p-5 md:p-6 space-y-5">
                    <div>
                      <label class="block text-sm font-semibold text-primary mb-1.5">Resume des competences</label>
                      <textarea class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all resize-none" rows="2" name="langues_competences" placeholder="Gestion administrative, service client, coordination, redaction, suivi de dossiers"></textarea>
                      <p class="mt-1.5 text-xs text-outline">Les langues se gerent separement dans la section 6.</p>
                    </div>
                    <div><label class="block text-sm font-semibold text-primary mb-1.5">Logiciels et outils maitrises</label><textarea class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all resize-none" rows="2" name="logiciels" placeholder="Word, Excel, PowerPoint, Simple Comptable, CRM, outils collaboratifs"></textarea></div>
                  </div>

                  <div class="bg-white rounded-2xl border border-outline-variant/10 p-5 md:p-6">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-5">
                      <div>
                        <h3 class="text-lg font-bold text-primary">Competences specifiques</h3>
                        <p class="text-sm text-on-surface-variant">Ajoutez une carte par competence ou savoir-faire important.</p>
                      </div>
                      <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-secondary-container/10 text-secondary-container text-sm font-semibold hover:bg-secondary-container/15 transition-colors" onclick="addCompetence()"><span class="material-symbols-outlined text-lg">add_circle</span> Ajouter une competence</button>
                    </div>
                    <div id="competences-container" class="space-y-4">
                      <div class="repeatable-item bg-surface-container-low rounded-xl p-5 border border-outline-variant/10 relative" data-index="0">
                        <button type="button" class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-full bg-white hover:bg-error-light text-outline hover:text-error transition-colors" onclick="this.closest('.repeatable-item').remove()" title="Supprimer">&times;</button>
                        <label class="block text-sm font-semibold text-primary mb-1.5">Competence specifique</label>
                        <textarea class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all resize-none" rows="3" name="competences[0][description]" placeholder="Comptabilite generale : comptes clients, comptes fournisseurs, paie, facturation, conciliation bancaire"></textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Section 3: Experience -->
              <div class="cv-form-section" id="section-3">
                <div class="mb-8 pb-4 border-b border-outline-variant/10">
                  <h2 class="text-2xl font-bold font-serif text-primary">Experience professionnelle</h2>
                  <p class="text-sm text-on-surface-variant mt-1">Ajoutez vos emplois precedents</p>
                </div>
                <div class="bg-white rounded-2xl border border-outline-variant/10 p-5 md:p-6">
                  <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-5">
                    <div>
                      <h3 class="text-lg font-bold text-primary">Blocs d'experience</h3>
                      <p class="text-sm text-on-surface-variant">Une carte correspond a un poste occupe.</p>
                    </div>
                    <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-secondary-container/10 text-secondary-container text-sm font-semibold hover:bg-secondary-container/15 transition-colors" onclick="addExperience()"><span class="material-symbols-outlined text-lg">add_circle</span> Ajouter une experience</button>
                  </div>
                  <div id="experiences-container" class="space-y-5">
                    <div class="repeatable-item bg-surface-container-low rounded-xl p-5 border border-outline-variant/10 relative" data-index="0">
                      <button type="button" class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-full bg-white hover:bg-error-light text-outline hover:text-error transition-colors" onclick="this.closest('.repeatable-item').remove()" title="Supprimer">&times;</button>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div><label class="block text-sm font-semibold text-primary mb-1.5">Periode *</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="experiences[0][periode]" placeholder="2004-2017" required /></div>
                        <div><label class="block text-sm font-semibold text-primary mb-1.5">Poste *</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="experiences[0][poste]" placeholder="Adjointe administrative" required /></div>
                      </div>
                      <div class="mb-4"><label class="block text-sm font-semibold text-primary mb-1.5">Entreprise et lieu</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="experiences[0][entreprise]" placeholder="Entreprise ABC enr., Montreal (Quebec)" /></div>
                      <div><label class="block text-sm font-semibold text-primary mb-1.5">Description des taches</label><textarea class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all resize-none" rows="4" name="experiences[0][description]" placeholder="• Tache principale 1&#10;• Tache principale 2&#10;• Tache principale 3"></textarea></div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Section 4: Formation -->
              <div class="cv-form-section" id="section-4">
                <div class="mb-8 pb-4 border-b border-outline-variant/10">
                  <h2 class="text-2xl font-bold font-serif text-primary">Formation</h2>
                  <p class="text-sm text-on-surface-variant mt-1">Ajoutez vos etudes et diplomes</p>
                </div>
                <div class="bg-white rounded-2xl border border-outline-variant/10 p-5 md:p-6">
                  <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-5">
                    <div>
                      <h3 class="text-lg font-bold text-primary">Blocs de formation</h3>
                      <p class="text-sm text-on-surface-variant">Ajoutez un bloc pour chaque diplome ou parcours.</p>
                    </div>
                    <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-secondary-container/10 text-secondary-container text-sm font-semibold hover:bg-secondary-container/15 transition-colors" onclick="addFormation()"><span class="material-symbols-outlined text-lg">add_circle</span> Ajouter une formation</button>
                  </div>
                  <div id="formations-container" class="space-y-5">
                    <div class="repeatable-item bg-surface-container-low rounded-xl p-5 border border-outline-variant/10 relative" data-index="0">
                      <button type="button" class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-full bg-white hover:bg-error-light text-outline hover:text-error transition-colors" onclick="this.closest('.repeatable-item').remove()" title="Supprimer">&times;</button>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div><label class="block text-sm font-semibold text-primary mb-1.5">Periode</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="formations[0][periode]" placeholder="1995-1998" /></div>
                        <div><label class="block text-sm font-semibold text-primary mb-1.5">Diplome *</label><select class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="formations[0][diplome]" required><option value="">Selectionner un diplome</option>@foreach ($diplomes as $diplome)<option value="{{ $diplome->id }}">{{ $diplome->nom_diplome }}</option>@endforeach</select></div>
                      </div>
                      <div><label class="block text-sm font-semibold text-primary mb-1.5">Etablissement et lieu</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="formations[0][etablissement]" placeholder="Cegep Saint-Laurent, Montreal (Quebec)" /></div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Section 5: Perfectionnement -->
              <div class="cv-form-section" id="section-5">
                <div class="mb-8 pb-4 border-b border-outline-variant/10">
                  <h2 class="text-2xl font-bold font-serif text-primary">Perfectionnement</h2>
                  <p class="text-sm text-on-surface-variant mt-1">Ajoutez vos formations complementaires</p>
                </div>
                <div class="bg-white rounded-2xl border border-outline-variant/10 p-5 md:p-6">
                  <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-5">
                    <div>
                      <h3 class="text-lg font-bold text-primary">Perfectionnements</h3>
                      <p class="text-sm text-on-surface-variant">Ajoutez vos formations courtes et mises a jour de competences.</p>
                    </div>
                    <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-secondary-container/10 text-secondary-container text-sm font-semibold hover:bg-secondary-container/15 transition-colors" onclick="addPerfectionnement()"><span class="material-symbols-outlined text-lg">add_circle</span> Ajouter un perfectionnement</button>
                  </div>
                  <div id="perfectionnements-container" class="space-y-5">
                    <div class="repeatable-item bg-surface-container-low rounded-xl p-5 border border-outline-variant/10 relative" data-index="0">
                      <button type="button" class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-full bg-white hover:bg-error-light text-outline hover:text-error transition-colors" onclick="this.closest('.repeatable-item').remove()" title="Supprimer">&times;</button>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div><label class="block text-sm font-semibold text-primary mb-1.5">Annee</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="perfectionnements[0][annee]" placeholder="2003" /></div>
                        <div><label class="block text-sm font-semibold text-primary mb-1.5">Formation</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="perfectionnements[0][formation]" placeholder="Actualisation en bureautique" /></div>
                      </div>
                      <div><label class="block text-sm font-semibold text-primary mb-1.5">Etablissement et lieu</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="perfectionnements[0][etablissement]" placeholder="College Informatique de la Rive-Sud, Longueuil (Quebec)" /></div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Section 6: Langues -->
              <div class="cv-form-section" id="section-6">
                <div class="mb-8 pb-4 border-b border-outline-variant/10">
                  <h2 class="text-2xl font-bold font-serif text-primary">Langues</h2>
                  <p class="text-sm text-on-surface-variant mt-1">Indiquez les langues que vous maitrisez</p>
                </div>
                <div class="bg-white rounded-2xl border border-outline-variant/10 p-5 md:p-6">
                  <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-5">
                    <div>
                      <h3 class="text-lg font-bold text-primary">Blocs de langues</h3>
                      <p class="text-sm text-on-surface-variant">Ajoutez une carte par langue avec son niveau.</p>
                    </div>
                    <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-secondary-container/10 text-secondary-container text-sm font-semibold hover:bg-secondary-container/15 transition-colors" onclick="addLangue()"><span class="material-symbols-outlined text-lg">add_circle</span> Ajouter une langue</button>
                  </div>
                  <div id="langues-container" class="space-y-5">
                    <div class="repeatable-item bg-surface-container-low rounded-xl p-5 border border-outline-variant/10 relative" data-index="0">
                      <button type="button" class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-full bg-white hover:bg-error-light text-outline hover:text-error transition-colors" onclick="this.closest('.repeatable-item').remove()" title="Supprimer">&times;</button>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label class="block text-sm font-semibold text-primary mb-1.5">Langue</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="langues[0][nom]" placeholder="Francais" /></div>
                        <div><label class="block text-sm font-semibold text-primary mb-1.5">Niveau</label><select class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="langues[0][niveau]"><option value="">Selectionner</option><option value="Langue maternelle">Langue maternelle</option><option value="Courant">Courant</option><option value="Intermédiaire">Intermediaire</option><option value="Notions de base">Notions de base</option><option value="Connaissances de base">Connaissances de base</option></select></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Section 7: Benevolat -->
              <div class="cv-form-section" id="section-7">
                <div class="mb-8 pb-4 border-b border-outline-variant/10">
                  <h2 class="text-2xl font-bold font-serif text-primary">Activites benevoles</h2>
                  <p class="text-sm text-on-surface-variant mt-1">Ajoutez vos experiences de benevolat</p>
                </div>
                <div class="bg-white rounded-2xl border border-outline-variant/10 p-5 md:p-6">
                  <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-5">
                    <div>
                      <h3 class="text-lg font-bold text-primary">Blocs de benevolat</h3>
                      <p class="text-sm text-on-surface-variant">Ajoutez chaque implication dans une carte separee.</p>
                    </div>
                    <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-secondary-container/10 text-secondary-container text-sm font-semibold hover:bg-secondary-container/15 transition-colors" onclick="addBenevolat()"><span class="material-symbols-outlined text-lg">add_circle</span> Ajouter une activite benevole</button>
                  </div>
                  <div id="benevolats-container" class="space-y-5">
                    <div class="repeatable-item bg-surface-container-low rounded-xl p-5 border border-outline-variant/10 relative" data-index="0">
                      <button type="button" class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-full bg-white hover:bg-error-light text-outline hover:text-error transition-colors" onclick="this.closest('.repeatable-item').remove()" title="Supprimer">&times;</button>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div><label class="block text-sm font-semibold text-primary mb-1.5">Periode</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="benevolats[0][periode]" placeholder="2008-2009" /></div>
                        <div><label class="block text-sm font-semibold text-primary mb-1.5">Role / Activite</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="benevolats[0][role]" placeholder="Benevole lors d'activites-benefice au profit de Leucan" /></div>
                      </div>
                      <div><label class="block text-sm font-semibold text-primary mb-1.5">Organisation (optionnel)</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="benevolats[0][organisation]" placeholder="Leucan" /></div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Section 8: Recapitulatif & Enregistrement -->
              <div class="cv-form-section" id="section-8">
                <div class="mb-8 pb-4 border-b border-outline-variant/10">
                  <h2 class="text-2xl font-bold font-serif text-primary">Enregistrement</h2>
                  <p class="text-sm text-on-surface-variant mt-1">Confirmez l'enregistrement de vos informations</p>
                </div>

                <div id="successAlert" class="hidden bg-secondary-container/10 border border-secondary-container/20 rounded-2xl p-6 mb-6">
                  <div class="flex items-center gap-3 mb-2">
                    <span class="material-symbols-outlined text-secondary-container text-2xl">check_circle</span>
                    <h3 class="font-bold text-secondary-container">Vos informations ont ete enregistrees avec succes !</h3>
                  </div>
                  <p class="text-sm text-secondary-container ml-9">Ces informations seront utilisees pour generer votre CV ulterieurement.</p>
                </div>

                <div class="bg-white rounded-xl border border-outline-variant/10 p-6">
                  <h3 class="font-bold text-primary mb-4">Recapitulatif de vos informations</h3>
                  <div id="summary-content" class="text-sm text-on-surface-variant space-y-3">
                    <p class="text-outline italic">Naviguez jusqu'a la derniere section pour voir le recapitulatif.</p>
                  </div>
                </div>
              </div>

            </form>

            <!-- Actions -->
            <div class="flex justify-between items-center pt-6 mt-8 border-t border-outline-variant/10">
              <div class="flex items-center gap-3">
                <button type="button" id="prevBtn" class="px-6 py-3 bg-surface-container text-primary text-sm font-semibold rounded-xl hover:bg-surface-container-low transition-colors flex items-center gap-2" style="display:none" onclick="previousSection()">
                  <span class="material-symbols-outlined text-sm">arrow_back</span> Precedent
                </button>
                <button type="button" class="px-6 py-3 bg-white border border-outline-variant/10 text-primary text-sm font-semibold rounded-xl hover:bg-surface-container-low transition-colors flex items-center gap-2" onclick="closeCvBuilder()">
                  <span class="material-symbols-outlined text-sm">visibility</span> Voir l'apercu
                </button>
              </div>
              <div class="flex items-center gap-3">
                <button type="button" id="nextBtn" class="px-6 py-3 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-all flex items-center gap-2" onclick="nextSection()">
                  Suivant <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </button>
                <button type="button" id="saveBtn" class="px-6 py-3 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-all flex items-center gap-2" style="display:none" onclick="saveCVData(event)">
                  <span class="material-symbols-outlined text-sm">save</span> Enregistrer
                </button>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

  </main>
@endsection
@section('scripts')
  <script>
    let currentSection = 1;
    const totalSections = 8;
    const completedSections = new Set();
    const existingCvProfile = @json($existingCvProfilePayload);

    function openCvBuilder() {
      const builder = document.getElementById('cv-builder');
      if (!builder) return;

      builder.classList.remove('hidden');
      builder.classList.add('flex');
      builder.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function closeCvBuilder() {
      const builder = document.getElementById('cv-builder');
      const preview = document.getElementById('cv-preview');
      if (!builder) return;

      builder.classList.add('hidden');
      builder.classList.remove('flex');
      preview?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function showPrincipalCvPreview() {
      showPrincipalPdfPreview();
    }

    function freshPdfUrl(url) {
      if (!url) return '';
      const [baseUrl, rawHash = ''] = url.split('#');
      const separator = baseUrl.includes('?') ? '&' : '?';
      const hashParts = rawHash
        .split('&')
        .map((part) => part.trim())
        .filter((part) => part && !part.startsWith('zoom='));

      hashParts.unshift('zoom=100');

      return `${baseUrl}${separator}_preview=${Date.now()}#${hashParts.join('&')}`;
    }

    function showPrincipalPdfPreview() {
      document.getElementById('previewTitle').textContent = 'CV principal';
      document.getElementById('previewEditButton')?.classList.remove('hidden');
      document.getElementById('principalPreviewActions')?.classList.remove('hidden');
      document.getElementById('principalCvPreview')?.classList.add('hidden');
      document.getElementById('generatedCvPreview')?.classList.remove('hidden');
      document.getElementById('generatedCvPreviewName').textContent = 'CV principal';
      document.getElementById('generatedCvPreviewFile').textContent = 'Rendu PDF du profil';
      document.getElementById('generatedCvFrame').setAttribute('src', freshPdfUrl('{{ route('cv.principal.inline') }}'));
      document.getElementById('generatedCvOpenLink').setAttribute('href', '{{ route('cv.principal.inline') }}#zoom=100');
      document.getElementById('cv-preview')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function showGeneratedCvPreview(button) {
      const title = button.dataset.title || 'CV';
      const file = button.dataset.file || '';
      const previewUrl = button.dataset.previewUrl || '';
      const openUrl = button.dataset.openUrl || '#';

      document.getElementById('previewTitle').textContent = 'CV';
      document.getElementById('previewEditButton')?.classList.add('hidden');
      document.getElementById('principalPreviewActions')?.classList.add('hidden');
      document.getElementById('principalCvPreview')?.classList.add('hidden');
      document.getElementById('generatedCvPreview')?.classList.remove('hidden');
      document.getElementById('generatedCvPreviewName').textContent = title;
      document.getElementById('generatedCvPreviewFile').textContent = file;
      document.getElementById('generatedCvFrame').setAttribute('src', freshPdfUrl(previewUrl));
      document.getElementById('generatedCvOpenLink').setAttribute('href', openUrl);
      document.getElementById('cv-preview')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function updateWizardUI() {
      document.querySelectorAll('.cv-form-section').forEach((section, index) => {
        section.classList.toggle('active', index + 1 === currentSection);
        section.style.display = index + 1 === currentSection ? 'block' : 'none';
      });

      document.querySelectorAll('.cv-step').forEach((step, index) => {
        const stepNumber = index + 1;
        const isActive = stepNumber === currentSection;
        const isCompleted = completedSections.has(stepNumber);
        const marker = step.querySelector('div');

        step.classList.toggle('active', isActive);
        step.classList.toggle('bg-white/15', isActive);
        step.classList.toggle('text-white', isActive || isCompleted);
        step.classList.toggle('text-white/75', !isActive && !isCompleted);

        if (marker) {
          marker.classList.toggle('text-white', isCompleted);
          marker.classList.toggle('bg-white/20', !isCompleted);
          marker.style.backgroundColor = isCompleted ? '#16a34a' : '';
          marker.innerHTML = isCompleted
            ? '<span class="material-symbols-outlined text-lg leading-none">check</span>'
            : String(stepNumber);
        }
      });

      const progress = (currentSection / totalSections) * 100;
      document.getElementById('progressFill').style.width = `${progress}%`;
      document.getElementById('progressText').textContent = `${currentSection}/${totalSections} sections`;
      document.getElementById('prevBtn').style.display = currentSection === 1 ? 'none' : 'inline-flex';
      document.getElementById('nextBtn').style.display = currentSection === totalSections ? 'none' : 'inline-flex';
      document.getElementById('saveBtn').style.display = currentSection === totalSections ? 'inline-flex' : 'none';

      if (currentSection === totalSections) {
        updateSummary();
      }
    }

    function nextSection() {
      if (currentSection < totalSections) {
        markSectionComplete(currentSection);
        currentSection += 1;
        updateWizardUI();
      }
    }

    function previousSection() {
      if (currentSection > 1) {
        currentSection -= 1;
        updateWizardUI();
      }
    }

    function addRepeatableItem(containerId, html) {
      const container = document.getElementById(containerId);
      const index = container.querySelectorAll('.repeatable-item').length;
      container.insertAdjacentHTML('beforeend', html(index));
    }

    function addCompetence() {
      addRepeatableItem('competences-container', (index) => `
        <div class="repeatable-item bg-surface-container-low rounded-xl p-5 border border-outline-variant/10 relative" data-index="${index}">
          <button type="button" class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-full bg-white hover:bg-error-light text-outline hover:text-error transition-colors" onclick="this.closest('.repeatable-item').remove()" title="Supprimer">&times;</button>
          <label class="block text-sm font-semibold text-primary mb-1.5">Competence specifique</label>
          <textarea class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all resize-none" rows="3" name="competences[${index}][description]"></textarea>
        </div>
      `);
    }

    function addExperience() {
      addRepeatableItem('experiences-container', (index) => `
        <div class="repeatable-item bg-surface-container-low rounded-xl p-5 border border-outline-variant/10 relative" data-index="${index}">
          <button type="button" class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-full bg-white hover:bg-error-light text-outline hover:text-error transition-colors" onclick="this.closest('.repeatable-item').remove()" title="Supprimer">&times;</button>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div><label class="block text-sm font-semibold text-primary mb-1.5">Periode *</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="experiences[${index}][periode]" required /></div>
            <div><label class="block text-sm font-semibold text-primary mb-1.5">Poste *</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="experiences[${index}][poste]" required /></div>
          </div>
          <div class="mb-4"><label class="block text-sm font-semibold text-primary mb-1.5">Entreprise et lieu</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="experiences[${index}][entreprise]" /></div>
          <div><label class="block text-sm font-semibold text-primary mb-1.5">Description des taches</label><textarea class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all resize-none" rows="4" name="experiences[${index}][description]"></textarea></div>
        </div>
      `);
    }

    function addFormation() {
      addRepeatableItem('formations-container', (index) => `
        <div class="repeatable-item bg-surface-container-low rounded-xl p-5 border border-outline-variant/10 relative" data-index="${index}">
          <button type="button" class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-full bg-white hover:bg-error-light text-outline hover:text-error transition-colors" onclick="this.closest('.repeatable-item').remove()" title="Supprimer">&times;</button>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div><label class="block text-sm font-semibold text-primary mb-1.5">Periode</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="formations[${index}][periode]" /></div>
            <div><label class="block text-sm font-semibold text-primary mb-1.5">Diplome *</label><select class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="formations[${index}][diplome]" required><option value="">Selectionner un diplome</option>@foreach ($diplomes as $diplome)<option value="{{ $diplome->id }}">{{ $diplome->nom_diplome }}</option>@endforeach</select></div>
          </div>
          <div><label class="block text-sm font-semibold text-primary mb-1.5">Etablissement et lieu</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="formations[${index}][etablissement]" /></div>
        </div>
      `);
    }

    function addPerfectionnement() {
      addRepeatableItem('perfectionnements-container', (index) => `
        <div class="repeatable-item bg-surface-container-low rounded-xl p-5 border border-outline-variant/10 relative" data-index="${index}">
          <button type="button" class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-full bg-white hover:bg-error-light text-outline hover:text-error transition-colors" onclick="this.closest('.repeatable-item').remove()" title="Supprimer">&times;</button>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div><label class="block text-sm font-semibold text-primary mb-1.5">Annee</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="perfectionnements[${index}][annee]" /></div>
            <div><label class="block text-sm font-semibold text-primary mb-1.5">Formation</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="perfectionnements[${index}][formation]" /></div>
          </div>
          <div><label class="block text-sm font-semibold text-primary mb-1.5">Etablissement et lieu</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="perfectionnements[${index}][etablissement]" /></div>
        </div>
      `);
    }

    function addLangue() {
      addRepeatableItem('langues-container', (index) => `
        <div class="repeatable-item bg-surface-container-low rounded-xl p-5 border border-outline-variant/10 relative" data-index="${index}">
          <button type="button" class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-full bg-white hover:bg-error-light text-outline hover:text-error transition-colors" onclick="this.closest('.repeatable-item').remove()" title="Supprimer">&times;</button>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="block text-sm font-semibold text-primary mb-1.5">Langue</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="langues[${index}][nom]" /></div>
            <div><label class="block text-sm font-semibold text-primary mb-1.5">Niveau</label><select class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="langues[${index}][niveau]"><option value="">Selectionner</option><option value="Langue maternelle">Langue maternelle</option><option value="Courant">Courant</option><option value="Intermédiaire">Intermediaire</option><option value="Notions de base">Notions de base</option><option value="Connaissances de base">Connaissances de base</option></select></div>
          </div>
        </div>
      `);
    }

    function addBenevolat() {
      addRepeatableItem('benevolats-container', (index) => `
        <div class="repeatable-item bg-surface-container-low rounded-xl p-5 border border-outline-variant/10 relative" data-index="${index}">
          <button type="button" class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-full bg-white hover:bg-error-light text-outline hover:text-error transition-colors" onclick="this.closest('.repeatable-item').remove()" title="Supprimer">&times;</button>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div><label class="block text-sm font-semibold text-primary mb-1.5">Periode</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="benevolats[${index}][periode]" /></div>
            <div><label class="block text-sm font-semibold text-primary mb-1.5">Role / Activite</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="benevolats[${index}][role]" /></div>
          </div>
          <div><label class="block text-sm font-semibold text-primary mb-1.5">Organisation</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="benevolats[${index}][organisation]" /></div>
        </div>
      `);
    }

    function updateSummary() {
      const formData = new FormData(document.getElementById('cvDataForm'));
      const summary = [
        ['Nom', formData.get('nom')],
        ['Prenom', formData.get('prenom_cv')],
        ['Courriel', formData.get('email_cv')],
        ['Telephone', formData.get('telephone_cv')],
        ['Ville', formData.get('ville')],
      ].filter(([, value]) => value);

      document.getElementById('summary-content').innerHTML = summary.length
        ? summary.map(([label, value]) => `<p><span class="font-semibold text-primary">${label}:</span> ${value}</p>`).join('')
        : '<p class="text-outline italic">Remplissez au moins vos informations de base pour afficher le recapitulatif.</p>';
    }

    function setCvField(name, value) {
      if (!value) return;
      const field = document.querySelector(`[name="${name}"]`);
      if (field) field.value = value;
    }

    function setRepeatableField(containerId, index, fieldName, value) {
      if (!value) return;
      const field = document.querySelector(`#${containerId} [name="${containerId.replace('-container', '')}[${index}][${fieldName}]"]`);
      if (field) field.value = value;
    }

    function ensureRepeatableCount(containerId, count, addFn) {
      const container = document.getElementById(containerId);
      if (!container) return;

      while (container.querySelectorAll('.repeatable-item').length < count) {
        addFn();
      }
    }

    function applyCvFields(fields) {
      if (!fields) return;

      setCvField('nom', fields.nom);
      setCvField('prenom_cv', fields.prenom_cv);
      setCvField('email_cv', fields.email_cv);
      setCvField('telephone_cv', fields.telephone_cv);
      setCvField('adresse', fields.adresse);
      setCvField('ville', fields.ville);
      setCvField('code_postal', fields.code_postal);
      setCvField('langues_competences', fields.langues_competences);
      setCvField('logiciels', fields.logiciels);

      if (fields.competences?.length) {
        ensureRepeatableCount('competences-container', fields.competences.length, addCompetence);
        fields.competences.forEach((competence, index) => {
          setRepeatableField('competences-container', index, 'description', typeof competence === 'string' ? competence : competence.description);
        });
      }

      if (fields.experiences?.length) {
        ensureRepeatableCount('experiences-container', fields.experiences.length, addExperience);
        fields.experiences.forEach((experience, index) => {
          setRepeatableField('experiences-container', index, 'periode', experience.periode);
          setRepeatableField('experiences-container', index, 'poste', experience.poste);
          setRepeatableField('experiences-container', index, 'entreprise', experience.entreprise);
          setRepeatableField('experiences-container', index, 'description', experience.description);
        });
      }

      if (fields.formations?.length) {
        ensureRepeatableCount('formations-container', fields.formations.length, addFormation);
        fields.formations.forEach((formation, index) => {
          setRepeatableField('formations-container', index, 'periode', formation.periode);
          setRepeatableField('formations-container', index, 'diplome', formation.diplome || formation.diplome_id);
          setRepeatableField('formations-container', index, 'etablissement', formation.etablissement || formation.diplome_text);
        });
      }

      if (fields.langues?.length) {
        ensureRepeatableCount('langues-container', fields.langues.length, addLangue);
        fields.langues.forEach((langue, index) => {
          setRepeatableField('langues-container', index, 'nom', langue.nom);
          setRepeatableField('langues-container', index, 'niveau', langue.niveau);
        });
      }

      if (fields.perfectionnements?.length) {
        ensureRepeatableCount('perfectionnements-container', fields.perfectionnements.length, addPerfectionnement);
        fields.perfectionnements.forEach((perfectionnement, index) => {
          setRepeatableField('perfectionnements-container', index, 'annee', perfectionnement.annee);
          setRepeatableField('perfectionnements-container', index, 'formation', perfectionnement.formation);
          setRepeatableField('perfectionnements-container', index, 'etablissement', perfectionnement.etablissement);
        });
      }

      if (fields.benevolats?.length) {
        ensureRepeatableCount('benevolats-container', fields.benevolats.length, addBenevolat);
        fields.benevolats.forEach((benevolat, index) => {
          setRepeatableField('benevolats-container', index, 'periode', benevolat.periode);
          setRepeatableField('benevolats-container', index, 'role', benevolat.role);
          setRepeatableField('benevolats-container', index, 'organisation', benevolat.organisation);
        });
      }
    }

    function sectionHasData(sectionNumber) {
      const section = document.getElementById(`section-${sectionNumber}`);
      if (!section) return false;

      return Array.from(section.querySelectorAll('input, textarea, select')).some((field) => {
        if (field.type === 'hidden') return false;
        return String(field.value || '').trim() !== '';
      });
    }

    function markSectionComplete(sectionNumber) {
      if (sectionNumber < totalSections) {
        completedSections.add(sectionNumber);
      }
    }

    function markFilledSectionsComplete() {
      for (let sectionNumber = 1; sectionNumber < totalSections; sectionNumber += 1) {
        if (sectionHasData(sectionNumber)) {
          completedSections.add(sectionNumber);
        }
      }
    }

    function showCvImportStatus(message, isError = false) {
      const status = document.getElementById('cvImportStatus');
      if (!status) return;
      status.textContent = message;
      status.classList.remove('hidden', 'border-error-light', 'bg-error-light', 'text-error-dark', 'border-secondary-container/20', 'bg-secondary-container/10', 'text-secondary-container');
      status.classList.add(...(isError
        ? ['border-error-light', 'bg-error-light', 'text-error-dark']
        : ['border-secondary-container/20', 'bg-secondary-container/10', 'text-secondary-container']
      ));
      status.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    async function uploadSourceCv(form) {
      const input = form?.querySelector('input[type="file"]');
      if (!input?.files?.length) return;

      showCvImportStatus('Televersement du CV source en cours...');

      try {
        const formData = new FormData(form);
        const response = await fetch('{{ route('cv.upload-source') }}', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
          },
          body: formData
        });
        const data = await response.json();

        if (!response.ok || !data.success) {
          throw new Error(data.message || 'Impossible de televerser ce CV.');
        }

        showCvImportStatus(data.message || 'CV source televerse.');
        window.setTimeout(() => window.location.reload(), 700);
      } catch (error) {
        showCvImportStatus(error.message || 'Impossible de televerser ce CV.', true);
        input.value = '';
      }
    }

    async function importUploadedCv() {
      const button = document.getElementById('importUploadedCvBtn');
      if (button) {
        button.disabled = true;
        button.classList.add('opacity-60');
      }

      showCvImportStatus('Lecture du CV televerse en cours...');

      try {
        const response = await fetch('{{ route('cv.import-uploaded') }}', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
          }
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
          throw new Error(data.message || 'Impossible de pre-remplir le CV principal.');
        }

        applyCvFields(data.fields || {});

        markFilledSectionsComplete();
        currentSection = 1;
        openCvBuilder();
        updateWizardUI();
        showCvImportStatus(data.message || 'Champs pre-remplis. Verifiez puis enregistrez votre CV principal.');
      } catch (error) {
        showCvImportStatus(error.message || 'Impossible de pre-remplir le CV principal.', true);
      } finally {
        if (button) {
          button.disabled = false;
          button.classList.remove('opacity-60');
        }
      }
    }

    async function saveCVData(event) {
      event.preventDefault();
      const form = document.getElementById('cvDataForm');
      const formData = new FormData(form);

      try {
        const response = await fetch('{{ route('cv.store') }}', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Accept': 'application/json'
          },
          body: formData
        });

        const data = await response.json();
        const successAlert = document.getElementById('successAlert');

        if (response.ok && data.success) {
          successAlert.classList.remove('hidden');
          successAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
          window.setTimeout(() => window.location.reload(), 900);
        } else {
          alert(data.message || "Une erreur est survenue lors de l'enregistrement.");
        }
      } catch (error) {
        alert("Impossible d'enregistrer le profil CV pour le moment.");
      }
    }

    document.addEventListener('DOMContentLoaded', () => {
      applyCvFields(existingCvProfile);
      markFilledSectionsComplete();
      updateWizardUI();
      document.querySelectorAll('.cv-step').forEach((step) => {
        step.addEventListener('click', () => {
          if (Number(step.dataset.step) > currentSection) {
            markSectionComplete(currentSection);
          }

          currentSection = Number(step.dataset.step);
          updateWizardUI();
        });
      });
    });
    function toggleSidebar() {
      const grid = document.getElementById('cvLayout');
      const aside = grid?.querySelector('aside');
      const icon = document.getElementById('toggleSidebarIcon');
      const expanded = aside?.style.display === 'none';
      if (expanded) {
        aside.style.display = '';
        grid.style.gridTemplateColumns = '';
        icon.textContent = 'fullscreen';
      } else {
        aside.style.display = 'none';
        grid.style.gridTemplateColumns = '1fr';
        icon.textContent = 'fullscreen_exit';
      }
    }
  </script>
@endsection
