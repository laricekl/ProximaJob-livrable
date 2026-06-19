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
        @endphp
        <a href="{{ route('user.historiques') }}" class="inline-flex items-center gap-2 text-sm text-secondary-container font-semibold hover:underline mb-4">
          <span class="material-symbols-outlined text-lg">arrow_back</span> Retour aux candidatures
        </a>

        <!-- Offer header card -->
        <div class="card-glow rounded-2xl p-6 md:p-8 flex flex-col sm:flex-row items-start gap-6">
          <div class="w-16 h-16 md:w-20 md:h-20 rounded-2xl bg-purple-50 flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-purple-600 text-3xl">code</span>
          </div>
          <div class="flex-1 w-full">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
              <div>
                <h1 class="text-2xl font-bold font-serif text-primary">{{ $offre?->poste ?: 'Votre candidature' }}</h1>
                <p class="text-on-surface-variant mt-1">{{ $offre?->entreprise->company_name ?? 'Entreprise' }} • {{ $offre?->localisation ?? 'Localisation a confirmer' }} • {{ $offre?->type->nom ?? 'Offre' }}</p>
              </div>
              <span class="self-start px-4 py-1.5 bg-blue-50 text-blue-700 text-[10px] font-black uppercase tracking-widest rounded-full whitespace-nowrap">En cours</span>
            </div>
            <div class="flex flex-wrap items-center gap-4 text-sm">
              <span class="flex items-center gap-1.5 text-secondary-container font-bold">
                <span class="material-symbols-outlined text-lg">bolt</span> Match 92%
              </span>
              <span class="text-outline">•</span>
              <span class="text-on-surface-variant">Postule le {{ $postulation?->created_at?->format('d M Y') ?? 'date indisponible' }}</span>
              <span class="text-outline">•</span>
              <span class="text-on-surface-variant flex items-center gap-1"><span class="material-symbols-outlined text-sm">visibility</span> Vue par l'employeur</span>
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
                    <p class="text-xs text-outline mt-0.5">12 juin 2025 à 14:30 • Postulation automatique IA</p>
                  </div>
                </div>
                <div class="timeline-step completed relative flex items-start gap-4 pb-8">
                  <div class="timeline-dot relative z-10 w-8 h-8 rounded-full bg-secondary-container border-2 border-secondary-container flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined text-white text-sm">check</span></div>
                  <div>
                    <p class="font-bold text-primary text-sm">CV consulté par l'employeur</p>
                    <p class="text-xs text-outline mt-0.5">14 juin 2025 à 09:15 • TechCorp a ouvert votre dossier</p>
                  </div>
                </div>
                <div class="timeline-step active relative flex items-start gap-4 pb-8">
                  <div class="timeline-dot relative z-10 w-8 h-8 rounded-full bg-white border-2 border-secondary-container flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined text-secondary-container text-sm">schedule</span></div>
                  <div>
                    <p class="font-bold text-primary text-sm">En cours d'évaluation</p>
                    <p class="text-xs text-outline mt-0.5">Votre profil est en cours d'examen par l'équipe de recrutement</p>
                  </div>
                </div>
                <div class="timeline-step relative flex items-start gap-4">
                  <div class="timeline-dot relative z-10 w-8 h-8 rounded-full bg-surface-container border-2 border-outline-variant/30 flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined text-outline text-sm">calendar_today</span></div>
                  <div>
                    <p class="font-bold text-outline text-sm">Entretien</p>
                    <p class="text-xs text-outline mt-0.5">En attente</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- AI Generated CV -->
            <div class="card-glow rounded-2xl overflow-hidden">
              <div class="px-8 py-5 border-b border-outline-variant/10 flex items-center justify-between">
                <h2 class="text-lg font-bold font-serif text-primary flex items-center gap-2"><span class="material-symbols-outlined text-secondary-container">auto_awesome</span> CV généré par l'IA</h2>
                <span class="px-3 py-1 bg-secondary-container/10 text-secondary-container text-[10px] font-black uppercase tracking-wider rounded-full">Personnalisé pour cette offre</span>
              </div>
              <div class="p-6">
                <div class="bg-surface-container-low rounded-xl p-6 text-sm text-on-surface-variant leading-relaxed max-h-64 overflow-y-auto">
                  <p class="font-bold text-primary text-base mb-3">Jean Dupont</p>
                  <p class="mb-3">Développeur Full Stack Senior | Montréal, QC | jean.dupont@email.com | +33 6 12 34 56 78</p>
                  <p class="font-bold text-primary mb-2">Résumé professionnel</p>
                  <p class="mb-4">Développeur Full Stack avec 7 ans d'expérience dans la conception et le déploiement d'applications web à fort trafic. Expertise en React, Node.js, TypeScript et PostgreSQL. Lead technique d'une équipe de 5 développeurs, j'ai piloté la refonte d'une plateforme SaaS servant plus de 10 000 utilisateurs.</p>
                  <p class="font-bold text-primary mb-2">Expérience</p>
                  <p class="mb-1"><strong>Développeur Full Stack Senior</strong> — TechInnovate, Montréal (2022-Présent)</p>
                  <p class="mb-3 text-xs">Lead technique • Architecture microservices • CI/CD • AWS • 10 000+ utilisateurs</p>
                  <p class="mb-1"><strong>Développeur Full Stack</strong> — WebAgency Pro, Lyon (2019-2022)</p>
                  <p class="mb-3 text-xs">React, Node.js, PostgreSQL • Clients grands comptes • Refonte produit phare</p>
                  <p class="font-bold text-primary mb-2">Compétences</p>
                  <p>React.js • Node.js • TypeScript • PostgreSQL • Docker • AWS • GraphQL • CI/CD</p>
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
                <p class="mt-3 text-xs text-outline">Aucun fichier associe a cette candidature n'est disponible en previsualisation pour le moment.</p>
                @endif
              </div>
            </div>

            <!-- AI Generated Cover Letter -->
            <div class="card-glow rounded-2xl overflow-hidden">
              <div class="px-8 py-5 border-b border-outline-variant/10 flex items-center justify-between">
                <h2 class="text-lg font-bold font-serif text-primary flex items-center gap-2"><span class="material-symbols-outlined text-secondary-container">description</span> Lettre de motivation générée</h2>
                <button class="copy-btn flex items-center gap-1 text-xs font-bold text-secondary-container hover:underline">
                  <span class="material-symbols-outlined text-sm">content_copy</span> Copier
                  <span class="copy-feedback text-green-600 text-xs">✓ Copié</span>
                </button>
              </div>
              <div class="p-6">
                <div class="bg-surface-container-low rounded-xl p-6 text-sm text-on-surface-variant leading-relaxed max-h-48 overflow-y-auto" id="coverLetter">
                  <p>Madame, Monsieur,</p>
                  <p class="mt-3">C'est avec un grand intérêt que je vous soumets ma candidature pour le poste de Développeur Full Stack Senior au sein de TechCorp. Votre approche innovante dans le domaine des solutions SaaS et votre culture d'entreprise orientée excellence technologique résonnent particulièrement avec mon parcours professionnel.</p>
                  <p class="mt-3">Fort de 7 ans d'expérience en développement full stack, dont 3 en tant que lead technique, j'ai développé une expertise approfondie dans les technologies que vous utilisez au quotidien : React, Node.js, TypeScript et les architectures cloud (AWS). La plateforme SaaS que j'ai pilotée chez TechInnovate sert aujourd'hui plus de 10 000 utilisateurs — un défi d'échelle qui m'a appris à concevoir des systèmes robustes et maintenables.</p>
                </div>
              </div>
            </div>

          </div>

          <!-- ====== RIGHT: Sidebar ====== -->
          <div class="space-y-6">

            <!-- Match score card -->
            <div class="card-glow rounded-2xl p-6 text-center">
              <p class="text-xs font-bold text-outline uppercase tracking-widest mb-3">Score de matching</p>
              <div class="relative w-28 h-28 mx-auto mb-4">
                <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                  <circle cx="18" cy="18" r="15.5" fill="none" stroke="#eceef0" stroke-width="3" />
                  <circle cx="18" cy="18" r="15.5" fill="none" stroke="var(--pj-accent)" stroke-width="3" stroke-dasharray="97.4 100" stroke-linecap="round" />
                </svg>
                <span class="absolute inset-0 flex items-center justify-center text-2xl font-bold text-primary">92%</span>
              </div>
              <p class="text-sm text-on-surface-variant">Excellent match ! Vos compétences correspondent parfaitement à cette offre.</p>
            </div>

            <!-- Offer summary -->
            <div class="card-glow rounded-2xl p-6">
              <h3 class="text-sm font-bold text-primary uppercase tracking-wider mb-4">Détails de l'offre</h3>
              <div class="space-y-3 text-sm">
                <div class="flex items-center gap-3"><span class="material-symbols-outlined text-outline text-lg">business</span><span class="text-on-surface-variant">TechCorp</span></div>
                <div class="flex items-center gap-3"><span class="material-symbols-outlined text-outline text-lg">location_on</span><span class="text-on-surface-variant">Montréal, QC</span></div>
                <div class="flex items-center gap-3"><span class="material-symbols-outlined text-outline text-lg">schedule</span><span class="text-on-surface-variant">Temps plein</span></div>
                <div class="flex items-center gap-3"><span class="material-symbols-outlined text-outline text-lg">payments</span><span class="text-on-surface-variant">55 000 - 70 000 €/an</span></div>
                <div class="flex items-center gap-3"><span class="material-symbols-outlined text-outline text-lg">calendar_today</span><span class="text-on-surface-variant">Publiée le 10 juin 2025</span></div>
              </div>
              <a href="{{ $offerUrl }}" class="flex items-center justify-center gap-2 w-full mt-4 py-2.5 border border-outline-variant/30 text-sm font-semibold rounded-xl hover:bg-surface-container-low transition-colors">Voir l'offre complète <span class="material-symbols-outlined text-sm">north_east</span></a>
            </div>

            <!-- Skills match breakdown -->
            <div class="card-glow rounded-2xl p-6">
              <h3 class="text-sm font-bold text-primary uppercase tracking-wider mb-4">Compétences matchées</h3>
              <div class="space-y-3">
                <div>
                  <div class="flex justify-between text-xs mb-1"><span class="font-semibold">React.js</span><span class="text-secondary-container font-bold">100%</span></div>
                  <div class="h-1.5 bg-surface-container rounded-full overflow-hidden"><div class="h-full bg-secondary-container rounded-full" style="width:100%"></div></div>
                </div>
                <div>
                  <div class="flex justify-between text-xs mb-1"><span class="font-semibold">Node.js</span><span class="text-secondary-container font-bold">95%</span></div>
                  <div class="h-1.5 bg-surface-container rounded-full overflow-hidden"><div class="h-full bg-secondary-container rounded-full" style="width:95%"></div></div>
                </div>
                <div>
                  <div class="flex justify-between text-xs mb-1"><span class="font-semibold">TypeScript</span><span class="text-secondary-container font-bold">90%</span></div>
                  <div class="h-1.5 bg-surface-container rounded-full overflow-hidden"><div class="h-full bg-secondary-container rounded-full" style="width:90%"></div></div>
                </div>
                <div>
                  <div class="flex justify-between text-xs mb-1"><span class="font-semibold">AWS</span><span class="text-outline font-bold">70%</span></div>
                  <div class="h-1.5 bg-surface-container rounded-full overflow-hidden"><div class="h-full bg-outline rounded-full" style="width:70%"></div></div>
                </div>
                <div>
                  <div class="flex justify-between text-xs mb-1"><span class="font-semibold">Docker</span><span class="text-secondary-container font-bold">85%</span></div>
                  <div class="h-1.5 bg-surface-container rounded-full overflow-hidden"><div class="h-full bg-secondary-container rounded-full" style="width:85%"></div></div>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="card-glow rounded-2xl p-6 space-y-3">
              <button class="flex items-center justify-center gap-2 w-full py-3 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-colors">
                <span class="material-symbols-outlined text-lg">auto_awesome</span> Régénérer CV + Lettre
              </button>
              <button id="withdrawBtn" class="flex items-center justify-center gap-2 w-full py-3 border-2 border-red-300 text-red-500 text-sm font-bold rounded-xl hover:bg-red-50 transition-colors">
                <span class="material-symbols-outlined text-lg">close</span> Retirer ma candidature
              </button>
            </div>

          </div>
        </div>
      </div>
    </section>
  </main>
@endsection
