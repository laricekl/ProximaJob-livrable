@extends('layouts.candidat')
@section('title', $offre->titre ?? 'Detail offre')
@section('content')
  @php
    $companyName = $offre->entreprise?->company_name ?? $offre->entreprise?->user?->name ?? 'Entreprise';
    $contractType = $offre->type?->nom ?? $offre->employment_type ?? 'Type non renseigne';
    $salary = ($offre->salaire_min || $offre->salaire_max)
      ? trim(number_format((float) $offre->salaire_min, 0, ',', ' ') . ' - ' . number_format((float) $offre->salaire_max, 0, ',', ' ') . ' $')
      : 'Salaire non renseigne';
    $skills = collect($offre->skills ?? [])
      ->map(fn ($jobSkill) => $jobSkill->skill?->name)
      ->filter();
    $fallbackSkills = collect(explode(',', $offre->competences ?? ''))->map(fn ($skill) => trim($skill))->filter();
    $displaySkills = $skills->isNotEmpty() ? $skills : $fallbackSkills;
    $benefits = collect(json_decode($offre->avantages ?? '[]', true));
    if ($benefits->isEmpty() && $offre->avantages) {
      $benefits = collect(preg_split('/\r\n|\r|\n|,/', $offre->avantages))->map(fn ($item) => trim($item))->filter();
    }
    $responsibilities = collect(preg_split('/\r\n|\r|\n/', $offre->responsibilities ?? $offre->missions ?? ''))->map(fn ($item) => trim($item))->filter();
    $description = $offre->description ?? $offre->missions ?? 'Aucune description detaillee disponible pour cette offre.';
  @endphp

  <main class="flex-grow pt-32">
    <section class="py-6 px-4 md:px-10 bg-white border-b border-outline-variant/10">
      <div class="max-w-7xl mx-auto flex items-center gap-2 text-xs text-outline">
        <a href="{{ auth()->check() ? route('user.home') : route('welcome') }}" class="hover:text-primary transition-colors">Accueil</a>
        <span class="material-symbols-outlined text-xs">chevron_right</span>
        <a href="{{ route('offres') }}" class="hover:text-primary transition-colors">Offres</a>
        <span class="material-symbols-outlined text-xs">chevron_right</span>
        <span class="text-primary font-medium">{{ $offre->titre }}</span>
      </div>
    </section>

    <section class="py-10 px-4 md:px-10">
      <div class="max-w-7xl mx-auto">
        <div class="flex flex-col lg:flex-row gap-8">
          <div class="flex-1 space-y-6">
            <div class="card-glow rounded-2xl p-8">
              <div class="flex items-start gap-5 mb-6">
                <div class="w-16 h-16 rounded-2xl bg-secondary-container/10 flex items-center justify-center flex-shrink-0">
                  <span class="material-symbols-outlined text-secondary-container text-3xl">work</span>
                </div>
                <div>
                  <h1 class="text-2xl md:text-3xl font-bold font-serif text-primary leading-tight">{{ $offre->titre }}</h1>
                  <p class="text-on-surface-variant mt-1">{{ $companyName }} • {{ $offre->localisation ?? 'Localisation non renseignee' }}</p>
                  <div class="flex flex-wrap items-center gap-2 mt-3">
                    <span class="px-3 py-1 bg-green-50 text-green-700 text-[10px] font-black uppercase tracking-widest rounded-full">{{ $contractType }}</span>
                    @if ($offre->remote_work)
                      <span class="px-3 py-1 bg-secondary-container/10 text-secondary-container text-[10px] font-black uppercase tracking-widest rounded-full">{{ $offre->remote_work }}</span>
                    @endif
                    <span class="px-3 py-1 bg-secondary-container/10 text-secondary-container text-[10px] font-black uppercase tracking-widest rounded-full">{{ ucfirst($offre->status ?? 'active') }}</span>
                  </div>
                </div>
              </div>
              <div class="flex flex-wrap items-center gap-6 text-sm text-on-surface-variant">
                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">today</span> Publie le {{ optional($offre->created_at)->translatedFormat('d M Y') }}</span>
                @if ($offre->date_fin)
                  <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">schedule</span> Date limite : {{ \Carbon\Carbon::parse($offre->date_fin)->translatedFormat('d M Y') }}</span>
                @endif
              </div>
            </div>

            <div class="card-glow rounded-2xl p-8">
              <h2 class="text-xl font-bold font-serif text-primary mb-4">Description du poste</h2>
              <div class="text-on-surface-variant leading-relaxed text-sm space-y-3">
                {!! nl2br(e($description)) !!}
              </div>

              @if ($responsibilities->isNotEmpty())
                <h3 class="font-bold text-primary mt-8 mb-3">Responsabilites</h3>
                <ul class="space-y-2 text-sm text-on-surface-variant">
                  @foreach ($responsibilities as $responsibility)
                    <li class="flex items-start gap-2"><span class="material-symbols-outlined text-secondary-container text-sm mt-0.5">check_circle</span> {{ $responsibility }}</li>
                  @endforeach
                </ul>
              @endif

              @if ($displaySkills->isNotEmpty())
                <h3 class="font-bold text-primary mt-8 mb-3">Competences requises</h3>
                <div class="flex flex-wrap gap-2">
                  @foreach ($displaySkills as $skill)
                    <span class="px-3 py-1.5 bg-secondary-container/10 text-secondary-container text-xs font-semibold rounded-full">{{ $skill }}</span>
                  @endforeach
                </div>
              @endif

              @if ($benefits->isNotEmpty())
                <h3 class="font-bold text-primary mt-8 mb-3">Avantages</h3>
                <ul class="space-y-2 text-sm text-on-surface-variant">
                  @foreach ($benefits as $benefit)
                    <li class="flex items-start gap-2"><span class="material-symbols-outlined text-secondary-container text-sm mt-0.5">star</span> {{ is_array($benefit) ? ($benefit['label'] ?? implode(' ', $benefit)) : $benefit }}</li>
                  @endforeach
                </ul>
              @endif
            </div>
          </div>

          <aside class="lg:w-80 flex-shrink-0 space-y-5">
            <div class="card-glow rounded-2xl p-6 text-center sticky top-28">
              <div class="text-3xl font-bold text-primary mb-1">{{ $salary }}</div>
              <p class="text-xs text-on-surface-variant mb-5">{{ $offre->salary_type ?? 'Selon experience' }}</p>

              @auth
                @php
                  $applicationStatus = $existingPostulation?->status;
                  $canUpdateApplication = $existingPostulation && !in_array($applicationStatus, ['accepted', 'rejected'], true);
                  $isFinalApplication = $existingPostulation && in_array($applicationStatus, ['accepted', 'rejected'], true);
                  $applicationButtonLabel = $canUpdateApplication ? 'Mettre à jour ma candidature' : 'Postuler maintenant';
                  $applicationButtonIcon = $canUpdateApplication ? 'edit_document' : 'send';
                  $finalApplicationLabel = $applicationStatus === 'accepted' ? 'Candidature acceptée' : 'Candidature refusée';
                @endphp

                @if ($isFinalApplication)
                  <button type="button" disabled class="w-full py-3.5 bg-surface-container-low text-outline font-bold rounded-xl mb-3 flex items-center justify-center gap-2 cursor-not-allowed">
                    <span class="material-symbols-outlined text-lg">lock</span> {{ $finalApplicationLabel }}
                  </button>
                  <p class="mb-3 text-xs leading-5 text-on-surface-variant">Cette candidature a déjà reçu une décision finale.</p>
                @else
                  <button type="button" onclick="handleApplyClick({{ $offre->id }})" class="w-full py-3.5 bg-secondary-container text-white font-bold rounded-xl hover:bg-secondary transition-all shadow-lg shadow-secondary-container/20 mb-3 flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-lg">{{ $applicationButtonIcon }}</span> {{ $applicationButtonLabel }}
                  </button>
                  @if ($canUpdateApplication)
                    <p class="mb-3 text-xs leading-5 text-on-surface-variant">Vous avez déjà postulé. Les nouveaux documents remplaceront votre dossier en attente.</p>
                  @endif
                @endif
              @else
                <a href="{{ route('login') }}" class="w-full py-3.5 bg-secondary-container text-white font-bold rounded-xl hover:bg-secondary transition-all shadow-lg shadow-secondary-container/20 mb-3 flex items-center justify-center gap-2">
                  <span class="material-symbols-outlined text-lg">login</span> Se connecter pour postuler
                </a>
              @endauth

              @auth
                @if (!($isFinalApplication ?? false))
                  <a href="{{ route('cv.personalization.form', ['offre_id' => $offre->id]) }}" class="w-full py-3 bg-white border border-outline-variant/30 text-primary font-bold rounded-xl hover:bg-surface-container-low transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-lg">auto_awesome</span> Préparer mon CV
                  </a>
                @endif
              @else
                <a href="{{ route('register') }}" class="w-full py-3 bg-white border border-outline-variant/30 text-primary font-bold rounded-xl hover:bg-surface-container-low transition-all flex items-center justify-center gap-2">
                  <span class="material-symbols-outlined text-lg">auto_awesome</span> Préparer mon CV
                </a>
              @endauth

              <hr class="my-5 border-outline-variant/10" />

              <dl class="space-y-4 text-left">
                <div class="flex justify-between gap-4 text-sm">
                  <dt class="text-outline">Contrat</dt>
                  <dd class="font-semibold text-primary text-right">{{ $contractType }}</dd>
                </div>
                <div class="flex justify-between gap-4 text-sm">
                  <dt class="text-outline">Experience</dt>
                  <dd class="font-semibold text-primary text-right">{{ $offre->experience ?? $offre->required_experience ?? 'Non renseignee' }}</dd>
                </div>
                <div class="flex justify-between gap-4 text-sm">
                  <dt class="text-outline">Niveau</dt>
                  <dd class="font-semibold text-primary text-right">{{ $offre->education_level ?? $offre->diplomes->pluck('name')->first() ?? 'Non renseigne' }}</dd>
                </div>
                <div class="flex justify-between gap-4 text-sm">
                  <dt class="text-outline">Langues</dt>
                  <dd class="font-semibold text-primary text-right">{{ $offre->langues ?? 'Non renseignees' }}</dd>
                </div>
                @if ($offre->date_fin)
                  <div class="flex justify-between gap-4 text-sm">
                    <dt class="text-outline">Date limite</dt>
                    <dd class="font-semibold text-primary text-right">{{ \Carbon\Carbon::parse($offre->date_fin)->translatedFormat('d M Y') }}</dd>
                  </div>
                @endif
              </dl>
            </div>
          </aside>
        </div>
      </div>
    </section>
  </main>

  @auth
    @include('components.application-modal')
  @endauth
@endsection
