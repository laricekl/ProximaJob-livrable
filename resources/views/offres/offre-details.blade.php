@extends('layouts.guest')
@section('title', 'Détail')
@section('styles')
  <style>
    .premium-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .premium-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1); }
    @media (max-width: 767px) {
      .text-2xl { font-size: 1.4rem !important; }
      .text-xl { font-size: 1.1rem !important; }

      /* Breadcrumb */
      nav .text-\[10px\] { font-size: 8px; }
      nav.mb-8 { margin-bottom: 1rem; }

      /* Main layout */
      .flex.flex-col.lg\\:flex-row.gap-8 { gap: 14px; }

      /* Job Header card */
      .bg-white.rounded-2xl.p-6 { padding: 14px !important; border-radius: 14px; }
      .bg-white.rounded-2xl.p-6.mb-6 { margin-bottom: 10px; }
      .w-16.h-16 { width: 42px; height: 42px; border-radius: 10px; }
      .w-16.h-16 .text-3xl { font-size: 22px; }
      .flex.items-start.gap-5 { gap: 12px; }
      h1.text-2xl { font-size: 18px !important; }
      .px-4.py-1\\.5.bg-green-50 { padding: 2px 7px; font-size: 7px; }
      .flex.flex-wrap.items-center.gap-4.mt-4 { gap: 8px; margin-top: 8px; }
      .flex.flex-wrap.items-center.gap-4.mt-4 .text-sm { font-size: 10px; }
      .flex.flex-wrap.items-center.gap-4.mt-4 .text-lg { font-size: 16px; }

      /* Description card */
      .bg-white.rounded-2xl.p-6.md\\:p-8 { padding: 14px !important; border-radius: 14px; }
      .leading-relaxed { font-size: 13px !important; line-height: 1.6 !important; }
      .leading-relaxed.mb-6 { margin-bottom: 12px; }
      h3.font-bold.mt-8 { margin-top: 16px; font-size: 14px; }
      h3.font-bold.mb-3 { margin-bottom: 6px; }
      ul.space-y-3 { gap: 6px; }
      ul.space-y-3 li .text-on-surface-variant { font-size: 12px; }
      ul.space-y-3 li .text-lg { font-size: 16px; }
      .flex.flex-wrap.gap-2 { gap: 6px; }
      .flex.flex-wrap.gap-2 span { padding: 4px 10px; font-size: 10px; border-radius: 8px; }

      /* Sidebar */
      aside .space-y-4 { gap: 10px; }
      aside .bg-white.rounded-2xl.p-6 { padding: 14px !important; border-radius: 14px; }
      aside .text-2xl { font-size: 1.25rem !important; }
      aside .text-xs.mt-1 { font-size: 9px; }
      aside a.w-full.px-8.py-4 { padding: 10px 20px !important; font-size: 13px; border-radius: 12px; }
      aside button.py-3 { padding: 8px 0; font-size: 13px; border-radius: 12px; }
      aside h3.text-sm { font-size: 11px; }
      aside .w-12.h-12 { width: 36px; height: 36px; border-radius: 8px; }
      aside dl.space-y-4 { gap: 10px; }
      aside dl .text-sm { font-size: 11px; }
      aside .text-sm.leading-relaxed { font-size: 12px; line-height: 1.5; }
      aside .text-sm.leading-relaxed.mb-4 { margin-bottom: 8px; }

      /* Footer - déjà compact, ajustements mineurs */
      footer nav.gap-4 { gap: 12px; }
      footer .text-\[9px\] { font-size: 8px; }
      footer .text-\[8px\] { font-size: 7px; }
      footer .text-\[10px\] { font-size: 9px; }
    }
  </style>
@endsection
@section('content')
  <main class="flex-grow pt-32 pb-20">
    <div class="max-w-5xl mx-auto px-4 md:px-10">

      <!-- Breadcrumb -->
      <nav class="flex items-center gap-3 text-[10px] font-black uppercase tracking-[0.2em] text-primary/30 mb-8">
        <a href="{{ route('offres') }}" class="hover:text-secondary-container transition-colors">Accueil</a>
        <span class="material-symbols-outlined text-sm opacity-30">chevron_right</span>
        <a href="{{ route('offres') }}" class="hover:text-secondary-container transition-colors">Offres</a>
        <span class="material-symbols-outlined text-sm opacity-30">chevron_right</span>
        <span class="text-primary/60">Ingénieur Cloud Senior</span>
      </nav>

      <div class="flex flex-col lg:flex-row gap-8">

        <!-- CONTENU PRINCIPAL -->
        <div class="flex-1">

          <!-- Job Header -->
          <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-primary/5 mb-6">
            <div class="flex items-start gap-5">
              <div class="w-16 h-16 rounded-xl bg-secondary-container/10 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-secondary-container text-3xl">cloud_done</span>
              </div>
              <div class="flex-1">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                  <div>
                    <h1 class="text-2xl md:text-3xl font-bold font-serif text-primary leading-tight">Ingénieur Cloud Senior</h1>
                    <p class="text-on-surface-variant mt-1 font-medium">DataSphere Inc.</p>
                  </div>
                  <span class="px-4 py-1.5 bg-green-50 text-green-700 text-[10px] font-black uppercase tracking-widest rounded-full w-fit">Temps plein</span>
                </div>

                <div class="flex flex-wrap items-center gap-4 mt-4 text-sm text-on-surface-variant">
                  <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-outline text-lg">location_on</span> Montréal, QC</span>
                  <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-outline text-lg">apartment</span> Hybride</span>
                  <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-outline text-lg">payments</span> 85 000 $ - 110 000 $ / an</span>
                  <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-outline text-lg">calendar_today</span> Publié le 10 mai 2025</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Description -->
          <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-primary/5 mb-6">
            <h2 class="text-xl font-bold font-serif text-primary mb-4">Description du poste</h2>
            <p class="text-on-surface-variant leading-relaxed mb-6">
              Nous recherchons un Ingénieur Cloud Senior passionné pour rejoindre notre équipe d'infrastructure. Vous serez responsable de la conception, du déploiement et de la maintenance de notre architecture cloud sur AWS et GCP. Vous travaillerez en étroite collaboration avec les équipes DevOps et les développeurs pour assurer une scalabilité optimale de nos services.
            </p>

            <h3 class="font-bold text-primary mt-8 mb-3">Responsabilités</h3>
            <ul class="space-y-3">
              <li class="flex items-start gap-3">
                <span class="material-symbols-outlined text-green-500 mt-0.5 text-lg">check_circle</span>
                <span class="text-on-surface-variant">Concevoir et implémenter des solutions cloud scalables sur AWS et GCP</span>
              </li>
              <li class="flex items-start gap-3">
                <span class="material-symbols-outlined text-green-500 mt-0.5 text-lg">check_circle</span>
                <span class="text-on-surface-variant">Automatiser les déploiements avec Terraform et Ansible</span>
              </li>
              <li class="flex items-start gap-3">
                <span class="material-symbols-outlined text-green-500 mt-0.5 text-lg">check_circle</span>
                <span class="text-on-surface-variant">Mettre en place des pipelines CI/CD avec GitHub Actions</span>
              </li>
              <li class="flex items-start gap-3">
                <span class="material-symbols-outlined text-green-500 mt-0.5 text-lg">check_circle</span>
                <span class="text-on-surface-variant">Superviser la sécurité et la conformité des infrastructures cloud</span>
              </li>
              <li class="flex items-start gap-3">
                <span class="material-symbols-outlined text-green-500 mt-0.5 text-lg">check_circle</span>
                <span class="text-on-surface-variant">Mentorer les ingénieurs juniors et participer aux revues de code</span>
              </li>
            </ul>

            <h3 class="font-bold text-primary mt-8 mb-3">Compétences requises</h3>
            <div class="flex flex-wrap gap-2">
              <span class="px-4 py-2 bg-secondary-container/10 text-secondary-container text-xs font-semibold rounded-xl">AWS</span>
              <span class="px-4 py-2 bg-secondary-container/10 text-secondary-container text-xs font-semibold rounded-xl">GCP</span>
              <span class="px-4 py-2 bg-secondary-container/10 text-secondary-container text-xs font-semibold rounded-xl">Terraform</span>
              <span class="px-4 py-2 bg-secondary-container/10 text-secondary-container text-xs font-semibold rounded-xl">Kubernetes</span>
              <span class="px-4 py-2 bg-secondary-container/10 text-secondary-container text-xs font-semibold rounded-xl">Docker</span>
              <span class="px-4 py-2 bg-secondary-container/10 text-secondary-container text-xs font-semibold rounded-xl">Python</span>
              <span class="px-4 py-2 bg-secondary-container/10 text-secondary-container text-xs font-semibold rounded-xl">CI/CD</span>
              <span class="px-4 py-2 bg-secondary-container/10 text-secondary-container text-xs font-semibold rounded-xl">Linux</span>
            </div>

            <h3 class="font-bold text-primary mt-8 mb-3">Avantages</h3>
            <ul class="space-y-3">
              <li class="flex items-start gap-3">
                <span class="material-symbols-outlined text-secondary-container mt-0.5 text-lg">stars</span>
                <span class="text-on-surface-variant">Assurance santé complète (médicale, dentaire, vision)</span>
              </li>
              <li class="flex items-start gap-3">
                <span class="material-symbols-outlined text-secondary-container mt-0.5 text-lg">stars</span>
                <span class="text-on-surface-variant">Programme de bonus annuel jusqu'à 15%</span>
              </li>
              <li class="flex items-start gap-3">
                <span class="material-symbols-outlined text-secondary-container mt-0.5 text-lg">stars</span>
                <span class="text-on-surface-variant">Horaires flexibles et télétravail hybride</span>
              </li>
              <li class="flex items-start gap-3">
                <span class="material-symbols-outlined text-secondary-container mt-0.5 text-lg">stars</span>
                <span class="text-on-surface-variant">Budget formation de 3 000 $ / an</span>
              </li>
            </ul>
          </div>
        </div>

        <!-- SIDEBAR -->
        <aside class="w-full lg:w-80 flex-shrink-0">
          <div class="lg:sticky lg:top-28 space-y-4">

            <!-- CTA Card -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-secondary-container/20">
              <div class="text-center mb-4">
                <p class="text-2xl font-bold text-primary">85k - 110k $</p>
                <p class="text-xs text-on-surface-variant mt-1">par année</p>
              </div>
              <a href="{{ route('offres') }}" class="w-full bg-secondary-container text-white font-bold px-8 py-4 rounded-xl hover:bg-secondary transition-all shadow flex items-center justify-center gap-2 mb-3">
                Postuler maintenant <span class="material-symbols-outlined">arrow_forward</span>
              </a>
              <button class="w-full py-3 rounded-xl border border-outline-variant text-on-surface-variant font-medium hover:bg-slate-50 transition-colors flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-lg">bookmark</span> Sauvegarder
              </button>
            </div>

            <!-- Company Info -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-primary/5">
              <h3 class="font-bold text-primary text-sm uppercase tracking-wider mb-4">À propos de l'entreprise</h3>
              <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-xl bg-secondary-container/10 flex items-center justify-center">
                  <span class="material-symbols-outlined text-secondary-container">cloud_done</span>
                </div>
                <div>
                  <p class="font-bold text-primary">DataSphere Inc.</p>
                  <p class="text-xs text-on-surface-variant">Technologies & Services</p>
                </div>
              </div>
              <p class="text-sm text-on-surface-variant leading-relaxed mb-4">DataSphere est un leader en solutions d'infrastructure cloud, servant des clients Fortune 500 à travers l'Amérique du Nord.</p>
              <div class="flex items-center gap-4 text-xs text-on-surface-variant">
                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">group</span> 200-500</span>
                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">language</span> data-sphere.io</span>
              </div>
            </div>

            <!-- Job Summary -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-primary/5">
              <h3 class="font-bold text-primary text-sm uppercase tracking-wider mb-4">Récapitulatif</h3>
              <dl class="space-y-4">
                <div class="flex justify-between items-center">
                  <dt class="text-sm text-on-surface-variant">Type de contrat</dt>
                  <dd class="text-sm font-semibold text-primary">Temps plein</dd>
                </div>
                <div class="flex justify-between items-center">
                  <dt class="text-sm text-on-surface-variant">Expérience</dt>
                  <dd class="text-sm font-semibold text-primary">5+ ans</dd>
                </div>
                <div class="flex justify-between items-center">
                  <dt class="text-sm text-on-surface-variant">Niveau d'études</dt>
                  <dd class="text-sm font-semibold text-primary">Baccalauréat</dd>
                </div>
                <div class="flex justify-between items-center">
                  <dt class="text-sm text-on-surface-variant">Langues</dt>
                  <dd class="text-sm font-semibold text-primary">Français, Anglais</dd>
                </div>
                <div class="flex justify-between items-center">
                  <dt class="text-sm text-on-surface-variant">Date limite</dt>
                  <dd class="text-sm font-semibold text-primary">30 juin 2025</dd>
                </div>
                <div class="flex justify-between items-center">
                  <dt class="text-sm text-on-surface-variant">Entrée en poste</dt>
                  <dd class="text-sm font-semibold text-primary">Dès que possible</dd>
                </div>
              </dl>
            </div>

          </div>
        </aside>

      </div>
    </div>
  </main>
@endsection
