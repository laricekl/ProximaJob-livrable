@extends('layouts.guest')
@section('title', 'Abonnements')
@section('styles')
<style>
    .premium-card {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .premium-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }
    .faq-answer { display: none; }
    .faq-item.active .faq-answer { display: block; }
    .faq-item.active .faq-icon { transform: rotate(45deg); }
    @media (max-width: 767px) {
      main.pt-32 { padding-top: 5rem !important; }

      /* Hero */
      section.py-24 { padding: 2rem 1rem !important; }
      .text-5xl { font-size: 1.75rem !important; }
      .text-white\/70.text-lg { font-size: 0.8rem; }

      /* Features */
      section.py-20 { padding: 1.5rem 1rem !important; }
      .text-3xl { font-size: 1.35rem !important; }

      .premium-card { padding: 14px !important; border-radius: 12px; }
      .premium-card .w-16.h-16 { width: 36px; height: 36px; border-radius: 8px; margin-bottom: 8px; }
      .premium-card .text-3xl { font-size: 20px; }
      .premium-card h3 { font-size: 15px; margin-bottom: 2px; }
      .premium-card .text-sm.leading-relaxed { font-size: 11px; }

      /* Pricing - 3D cards */
      .pricing-mobile { gap: 10px; }
      .pricing-mobile .card-3d-parent { height: auto !important; filter: none; }
      .pricing-mobile .card-3d-inner { border-radius: 20px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); }
      .pricing-mobile .card-3d-logo, .pricing-mobile .card-3d-glass { display: none; }
      .pricing-mobile .card-3d-content { padding: 18px !important; }
      .pricing-mobile .card-3d-content .text-4xl { font-size: 1.75rem !important; }
      .pricing-mobile .card-3d-content .text-lg { font-size: 0.9rem !important; }
      .pricing-mobile .card-3d-content ul.mt-4 { margin-top: 8px; gap: 4px; }
      .pricing-mobile .card-3d-content ul li { font-size: 10px; gap: 6px; }
      .pricing-mobile .card-3d-content a.mt-5 { margin-top: 10px; padding: 8px 0; font-size: 12px; }
      .pricing-mobile .absolute.-top-3 { top: -8px; font-size: 9px; padding: 2px 10px; }
      .pricing-mobile .text-center.text-sm.mt-8 { font-size: 10px; margin-top: 12px; }

      /* FAQ */
      .faq-item .faq-question { padding: 12px !important; }
      .faq-item .faq-question span { font-size: 11px; }
      .faq-item .faq-answer { padding: 0 12px 12px 46px !important; font-size: 11px; }
      .faq-item .w-8.h-8 { width: 26px; height: 26px; font-size: 10px; }
      .faq-question .gap-4 { gap: 8px; }
      section .mb-16 { margin-bottom: 1.25rem; }

      /* Footer */
      footer nav.gap-4 { gap: 12px; }
      footer .text-\[9px\] { font-size: 8px; }
      footer .text-\[8px\] { font-size: 7px; }
      footer .text-\[10px\] { font-size: 9px; }
    }
</style>
@endsection
@section('content')
  <main class="flex-grow pt-32" style="background: linear-gradient(180deg, rgba(176, 177, 192, 0.22) 0%, rgba(240, 242, 245, 0.36) 100%), radial-gradient(at 10% 8%, rgba(235, 132, 60, 0.055) 0, transparent 38%), radial-gradient(at 90% 88%, rgba(36, 98, 183, 0.035) 0, transparent 40%), #f7f9fb;">

    <x-public-page-hero
      title="Abonnements Premium"
      subtitle="Des fonctionnalités avancées, notre expertise, découvrez nos offres adaptées à vos besoins."
    />

    <!-- Features -->
    <section class="py-20 px-4 md:px-10">
      <div class="max-w-6xl mx-auto">
        <h2 class="text-3xl md:text-4xl font-bold font-serif text-primary leading-tight text-center mb-4">Pourquoi nous choisir ?</h2>
        <p class="text-on-surface-variant text-center mb-16 max-w-2xl mx-auto">Une qualité différentielle pour votre recherche d'emploi ou votre recrutement.</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-8">
          <!-- Feature 1 -->
          <div class="bg-white rounded-2xl p-8 shadow-md border border-white text-center premium-card">
            <div class="w-16 h-16 rounded-xl bg-primary-fixed flex items-center justify-center mx-auto mb-6">
              <span class="material-symbols-outlined text-3xl text-primary">trending_up</span>
            </div>
            <h3 class="font-bold text-xl text-primary mb-3">Performance Maximale</h3>
            <p class="text-on-surface-variant text-sm leading-relaxed">Profitez de notre réseau avancé de candidats pré-qualifiés pour vous proposer les meilleures opportunités.</p>
          </div>

          <!-- Feature 2 -->
          <div class="bg-white rounded-2xl p-8 shadow-md border border-white text-center premium-card">
            <div class="w-16 h-16 rounded-xl bg-secondary-fixed flex items-center justify-center mx-auto mb-6">
              <span class="material-symbols-outlined text-3xl text-on-secondary-fixed-variant">workspace_premium</span>
            </div>
            <h3 class="font-bold text-xl text-primary mb-3">Service Premium</h3>
            <p class="text-on-surface-variant text-sm leading-relaxed">Bénéficiez de services avancés sur le recrutement de votre main-d'oeuvre avec un accompagnement dédié.</p>
          </div>

          <!-- Feature 3 -->
          <div class="bg-white rounded-2xl p-8 shadow-md border border-white text-center premium-card">
            <div class="w-16 h-16 rounded-xl bg-tertiary-fixed flex items-center justify-center mx-auto mb-6">
              <span class="material-symbols-outlined text-3xl text-on-tertiary-fixed">support_agent</span>
            </div>
            <h3 class="font-bold text-xl text-primary mb-3">Support Dédié</h3>
            <p class="text-on-surface-variant text-sm leading-relaxed">Support dédié au niveau avancé pendant votre parcours pour vous accompagner à chaque étape.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing · Cartes 3D -->
    <section class="py-20 px-4 md:px-10 bg-white/55">
      <div class="max-w-5xl mx-auto">
        <h2 class="text-3xl md:text-4xl font-bold font-serif text-primary leading-tight text-center mb-4">Choisissez votre forfait</h2>
        <p class="text-on-surface-variant text-center mb-16 max-w-2xl mx-auto">Découvrez nos offres adaptées a vos besoins</p>

        <div class="pricing-mobile grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-6">

          <!-- Gratuit -->
          <div class="card-3d-parent">
            <div class="card-3d-inner" style="--card-grad: linear-gradient(135deg, #f5f0eb, #e8e0d5);">
              <div class="card-3d-logo" aria-hidden="true">
                <span class="card-3d-orbit card-3d-orbit--1"></span>
                <span class="card-3d-orbit card-3d-orbit--2"></span>
                <span class="card-3d-orbit card-3d-orbit--3"></span>
              </div>
              <div class="card-3d-glass"></div>
              <div class="card-3d-content text-center">
                <p class="text-xs font-bold text-outline uppercase tracking-wide mb-1">Gratuit</p>
                <p class="text-4xl font-bold text-primary">0<span class="text-lg text-outline">$</span></p>
                <p class="text-xs text-outline mt-1">/mois</p>
                <ul class="mt-4 space-y-2 text-xs text-outline text-left">
                  <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">check</span> Accès aux offres</li>
                  <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">check</span> Candidature simple</li>
                  <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline-variant">close</span> CV personnalisé IA</li>
                </ul>
                <a href="{{ route('register') }}" class="block mt-5 w-full py-2.5 rounded-full text-sm font-semibold border border-outline-variant/40 text-primary hover:bg-black/5 transition-colors text-center">Commencer</a>
              </div>
            </div>
          </div>

          <!-- Premium (recommande) -->
          <div class="card-3d-parent">
            <div class="card-3d-inner" style="--card-grad: linear-gradient(135deg, #fff5eb, #f5e0cc);">
              <div class="card-3d-logo" aria-hidden="true">
                <span class="card-3d-orbit card-3d-orbit--1" style="background: rgba(255,255,255,0.35);"></span>
                <span class="card-3d-orbit card-3d-orbit--2" style="background: rgba(255,255,255,0.45);"></span>
                <span class="card-3d-orbit card-3d-orbit--3" style="background: rgba(255,255,255,0.58);"></span>
              </div>
              <div class="card-3d-glass"></div>
              <span class="absolute -top-3 left-1/2 -translate-x-1/2 z-20 px-4 py-1 rounded-full text-[11px] font-bold text-white bg-secondary-container shadow-lg">Recommandé</span>
              <div class="card-3d-content text-center">
                <p class="text-xs font-bold text-secondary-container uppercase tracking-wide mb-1">Premium</p>
                <p class="text-4xl font-bold text-primary">29<span class="text-lg text-outline">$</span></p>
                <p class="text-xs text-outline mt-1">/mois</p>
                <ul class="mt-4 space-y-2 text-xs text-outline text-left">
                  <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">check</span> Candidatures illimitées</li>
                  <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">check</span> CV personnalisé par IA</li>
                  <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">check</span> Mise en avant du profil</li>
                </ul>
                <a href="{{ route('register') }}" class="block mt-5 w-full py-2.5 rounded-full text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:shadow-xl text-center" style="background: rgba(var(--pj-accent-rgb),0.88);">Essai gratuit 7 jours</a>
              </div>
            </div>
          </div>

          <!-- Entreprise -->
          <div class="card-3d-parent">
            <div class="card-3d-inner" style="--card-grad: linear-gradient(135deg, #f0f4ff, #dce6f5);">
              <div class="card-3d-logo" aria-hidden="true">
                <span class="card-3d-orbit card-3d-orbit--1" style="background: rgba(255,255,255,0.32);"></span>
                <span class="card-3d-orbit card-3d-orbit--2" style="background: rgba(255,255,255,0.42);"></span>
                <span class="card-3d-orbit card-3d-orbit--3" style="background: rgba(255,255,255,0.55);"></span>
              </div>
              <div class="card-3d-glass"></div>
              <div class="card-3d-content text-center">
                <p class="text-xs font-bold text-secondary-container uppercase tracking-wide mb-1">Entreprise</p>
                <p class="text-4xl font-bold text-primary">99<span class="text-lg text-outline">$</span></p>
                <p class="text-xs text-outline mt-1">/mois</p>
                <ul class="mt-4 space-y-2 text-xs text-outline text-left">
                  <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">check</span> Tout Premium</li>
                  <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">check</span> API dediee</li>
                  <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">check</span> Multi-employeurs</li>
                </ul>
                <a href="{{ route('contact') }}" class="block mt-5 w-full py-2.5 rounded-full text-sm font-semibold text-white bg-primary-container hover:bg-primary-container/90 transition-colors shadow-lg text-center">Contacter</a>
              </div>
            </div>
          </div>

        </div>

        <p class="text-center text-sm text-outline mt-8">Annulation possible à tout moment. Essai gratuit de 7 jours pour le forfait Premium.</p>
      </div>
    </section>

    <!-- FAQ -->
    <section class="py-20 px-4 md:px-10">
      <div class="max-w-3xl mx-auto">
        <h2 class="text-3xl md:text-4xl font-bold font-serif text-primary leading-tight text-center mb-16">FAQ</h2>

        <div class="space-y-4">
          <!-- FAQ 1 -->
          <div class="faq-item bg-white rounded-2xl border border-white shadow-md overflow-hidden">
            <button class="faq-question w-full flex items-center justify-between p-6 text-left font-semibold text-primary hover:bg-surface-container-low/50 transition-colors">
              <div class="flex items-center gap-4">
                <span class="w-8 h-8 rounded-full bg-secondary-container text-white flex items-center justify-center text-xs font-bold flex-shrink-0">01</span>
                <span>Comment fonctionne l'abonnement Premium ?</span>
              </div>
              <span class="faq-icon w-8 h-8 rounded-full bg-surface-container flex items-center justify-center transition-transform flex-shrink-0">
                <span class="material-symbols-outlined text-sm">add</span>
              </span>
            </button>
            <div class="faq-answer px-6 pb-6 pl-20 text-on-surface-variant text-sm leading-relaxed">
              Notre abonnement Premium vous donne accès à toutes les fonctionnalités avancées de ProximaJob, y compris la personnalisation de CV par IA, les candidatures illimitées, et la mise en avant de votre profil auprès des employeurs.
            </div>
          </div>

          <!-- FAQ 2 -->
          <div class="faq-item bg-white rounded-2xl border border-white shadow-md overflow-hidden">
            <button class="faq-question w-full flex items-center justify-between p-6 text-left font-semibold text-primary hover:bg-surface-container-low/50 transition-colors">
              <div class="flex items-center gap-4">
                <span class="w-8 h-8 rounded-full bg-secondary-container text-white flex items-center justify-center text-xs font-bold flex-shrink-0">02</span>
                <span>Puis-je annuler à tout moment ?</span>
              </div>
              <span class="faq-icon w-8 h-8 rounded-full bg-surface-container flex items-center justify-center transition-transform flex-shrink-0">
                <span class="material-symbols-outlined text-sm">add</span>
              </span>
            </button>
            <div class="faq-answer px-6 pb-6 pl-20 text-on-surface-variant text-sm leading-relaxed">
              Oui, vous pouvez annuler votre abonnement à tout moment. Après annulation, vous conservez l'accès aux fonctionnalités Premium jusqu'à la fin de votre période de facturation en cours.
            </div>
          </div>

          <!-- FAQ 3 -->
          <div class="faq-item bg-white rounded-2xl border border-white shadow-md overflow-hidden">
            <button class="faq-question w-full flex items-center justify-between p-6 text-left font-semibold text-primary hover:bg-surface-container-low/50 transition-colors">
              <div class="flex items-center gap-4">
                <span class="w-8 h-8 rounded-full bg-secondary-container text-white flex items-center justify-center text-xs font-bold flex-shrink-0">03</span>
                <span>Comment fonctionne l'essai gratuit de 7 jours ?</span>
              </div>
              <span class="faq-icon w-8 h-8 rounded-full bg-surface-container flex items-center justify-center transition-transform flex-shrink-0">
                <span class="material-symbols-outlined text-sm">add</span>
              </span>
            </button>
            <div class="faq-answer px-6 pb-6 pl-20 text-on-surface-variant text-sm leading-relaxed">
              L'essai gratuit vous donne accès à toutes les fonctionnalités Premium pendant 7 jours. Aucun frais ne vous sera facturé pendant cette période. Vous pouvez annuler avant la fin de l'essai sans être débité.
            </div>
          </div>

          <!-- FAQ 4 -->
          <div class="faq-item bg-white rounded-2xl border border-white shadow-md overflow-hidden">
            <button class="faq-question w-full flex items-center justify-between p-6 text-left font-semibold text-primary hover:bg-surface-container-low/50 transition-colors">
              <div class="flex items-center gap-4">
                <span class="w-8 h-8 rounded-full bg-secondary-container text-white flex items-center justify-center text-xs font-bold flex-shrink-0">04</span>
                <span>Quels moyens de paiement acceptez-vous ?</span>
              </div>
              <span class="faq-icon w-8 h-8 rounded-full bg-surface-container flex items-center justify-center transition-transform flex-shrink-0">
                <span class="material-symbols-outlined text-sm">add</span>
              </span>
            </button>
            <div class="faq-answer px-6 pb-6 pl-20 text-on-surface-variant text-sm leading-relaxed">
              Nous acceptons les cartes de crédit (Visa, Mastercard), les cartes de débit, ainsi que les paiements via PayPal et les services de mobile money selon votre région.
            </div>
          </div>

          <!-- FAQ 5 -->
          <div class="faq-item bg-white rounded-2xl border border-white shadow-md overflow-hidden">
            <button class="faq-question w-full flex items-center justify-between p-6 text-left font-semibold text-primary hover:bg-surface-container-low/50 transition-colors">
              <div class="flex items-center gap-4">
                <span class="w-8 h-8 rounded-full bg-secondary-container text-white flex items-center justify-center text-xs font-bold flex-shrink-0">05</span>
                <span>Le compte Basique est-il vraiment gratuit ?</span>
              </div>
              <span class="faq-icon w-8 h-8 rounded-full bg-surface-container flex items-center justify-center transition-transform flex-shrink-0">
                <span class="material-symbols-outlined text-sm">add</span>
              </span>
            </button>
            <div class="faq-answer px-6 pb-6 pl-20 text-on-surface-variant text-sm leading-relaxed">
              Oui, le compte Basique est entièrement gratuit et le restera. Il vous permet d'accéder aux offres d'emploi, de postuler et de créer votre profil sans aucun frais.
            </div>
          </div>
        </div>
      </div>
    </section>

  </main>
@endsection
@section('scripts')
  <script>
    document.querySelectorAll('.faq-question').forEach(btn => {
      btn.addEventListener('click', () => {
        const item = btn.closest('.faq-item');
        const isActive = item.classList.contains('active');
        document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('active'));
        if (!isActive) item.classList.add('active');
      });
    });
  </script>
@endsection
