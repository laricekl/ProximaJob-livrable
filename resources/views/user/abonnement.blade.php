@extends('layouts.candidat')
@section('title', 'Abonnement')
@section('content')
  <main class="flex-grow pt-32">

    <!-- En-tete -->
    <section class="py-12 px-4 md:px-10 bg-white">
      <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl md:text-4xl font-bold font-serif text-primary leading-tight mb-2">Historique d'Abonnements</h1>
        <p class="text-on-surface-variant">Retrouvez ici l'historique de tous vos abonnements</p>

        <div class="flex flex-wrap items-center gap-3 mt-8">
          <div class="flex gap-1 bg-surface-container rounded-xl p-1.5">
            <button class="filter-tab px-5 py-2.5 rounded-lg text-sm font-semibold bg-white text-primary shadow-sm" data-filter="all">Tous les abonnements</button>
            <button class="filter-tab px-5 py-2.5 rounded-lg text-sm font-semibold text-slate-500 hover:text-primary" data-filter="active">Actifs</button>
            <button class="filter-tab px-5 py-2.5 rounded-lg text-sm font-semibold text-slate-500 hover:text-primary" data-filter="expired">Expires</button>
          </div>
          <a href="{{ route('plan.abonnement') }}" class="px-5 py-2.5 bg-secondary-container text-white text-sm font-semibold rounded-xl hover:bg-secondary transition-colors flex items-center gap-2 shadow-sm">
            <span class="material-symbols-outlined text-lg">add</span> Nouvel Abonnement
          </a>
        </div>
      </div>
    </section>

    <!-- Grille d'abonnements -->
    <section class="py-10 px-4 md:px-10">
      <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" id="subscriptionsGrid">

          <!-- Carte Premium Active -->
          <div class="subscription-item" data-status="active">
            <div class="subscription-card card-glow border-l-4 border-l-secondary-container rounded-2xl p-6 h-full flex flex-col">
              <div class="flex justify-between items-start mb-3">
                <span class="px-3 py-1 bg-secondary-container/10 text-secondary-container text-[10px] font-black uppercase tracking-widest rounded-full">Actif</span>
                <span class="text-xl font-bold text-primary">29,99 $ CAD</span>
              </div>
              <h3 class="font-bold text-primary text-lg mb-4">Abonnement Premium</h3>
              <div class="space-y-2 text-sm text-on-surface-variant flex-grow">
                <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">calendar_today</span> Date d'activation : <strong class="text-primary ml-auto">15 Janvier 2026</strong></p>
                <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">event</span> Date d'expiration : <strong class="text-primary ml-auto">15 Janvier 2027</strong></p>
                <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">schedule</span> Duree : <strong class="text-primary ml-auto">247 jours</strong></p>
                <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">credit_card</span> Paiement : <strong class="text-primary ml-auto">Carte Visa &bull;&bull;&bull;&bull; 4242</strong></p>
              </div>
              <div class="flex items-center gap-3 mt-5 pt-4 border-t border-outline-variant/10">
                <button class="flex-1 py-2.5 bg-secondary-container text-white text-sm font-semibold rounded-xl hover:bg-secondary transition-colors">Renouveler</button>
                <button class="text-sm font-semibold text-outline hover:text-primary transition-colors underline">Voir details</button>
              </div>
            </div>
          </div>

          <!-- Carte Standard Expiree -->
          <div class="subscription-item" data-status="expired">
            <div class="subscription-card card-glow border-l-4 border-l-secondary-container/50 rounded-2xl p-6 h-full flex flex-col">
              <div class="flex justify-between items-start mb-3">
                <span class="px-3 py-1 bg-secondary-container/10 text-secondary-container text-[10px] font-black uppercase tracking-widest rounded-full">Expire</span>
                <span class="text-xl font-bold text-primary">14,99 $ CAD</span>
              </div>
              <h3 class="font-bold text-primary text-lg mb-4">Abonnement Standard</h3>
              <div class="space-y-2 text-sm text-on-surface-variant flex-grow">
                <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">calendar_today</span> Date d'activation : <strong class="text-primary ml-auto">10 Mars 2025</strong></p>
                <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">event</span> Date d'expiration : <strong class="text-primary ml-auto">10 Mars 2026</strong></p>
                <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">schedule</span> Duree : <strong class="text-primary ml-auto">1 an</strong></p>
                <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">credit_card</span> Paiement : <strong class="text-primary ml-auto">PayPal</strong></p>
              </div>
              <div class="flex items-center gap-3 mt-5 pt-4 border-t border-outline-variant/10">
                <button class="flex-1 py-2.5 bg-secondary-container text-white text-sm font-semibold rounded-xl hover:bg-secondary transition-colors">Renouveler</button>
                <button class="text-sm font-semibold text-outline hover:text-primary transition-colors underline">Voir details</button>
              </div>
            </div>
          </div>

          <!-- Carte Standard Expiree 2 -->
          <div class="subscription-item" data-status="expired">
            <div class="subscription-card card-glow border-l-4 border-l-secondary-container/50 rounded-2xl p-6 h-full flex flex-col">
              <div class="flex justify-between items-start mb-3">
                <span class="px-3 py-1 bg-secondary-container/10 text-secondary-container text-[10px] font-black uppercase tracking-widest rounded-full">Expire</span>
                <span class="text-xl font-bold text-primary">14,99 $ CAD</span>
              </div>
              <h3 class="font-bold text-primary text-lg mb-4">Abonnement Standard</h3>
              <div class="space-y-2 text-sm text-on-surface-variant flex-grow">
                <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">calendar_today</span> Date d'activation : <strong class="text-primary ml-auto">05 Janvier 2025</strong></p>
                <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">event</span> Date d'expiration : <strong class="text-primary ml-auto">05 Janvier 2026</strong></p>
                <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">schedule</span> Duree : <strong class="text-primary ml-auto">1 an</strong></p>
                <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">credit_card</span> Paiement : <strong class="text-primary ml-auto">Carte Debit</strong></p>
              </div>
              <div class="flex items-center gap-3 mt-5 pt-4 border-t border-outline-variant/10">
                <button class="flex-1 py-2.5 bg-secondary-container text-white text-sm font-semibold rounded-xl hover:bg-secondary transition-colors">Renouveler</button>
                <button class="text-sm font-semibold text-outline hover:text-primary transition-colors underline">Voir details</button>
              </div>
            </div>
          </div>

          <!-- Cartes supplementaires -->
          <div class="subscription-item" data-status="expired">
            <div class="subscription-card card-glow border-l-4 border-l-secondary-container/50 rounded-2xl p-6 h-full flex flex-col">
              <div class="flex justify-between items-start mb-3">
                <span class="px-3 py-1 bg-secondary-container/10 text-secondary-container text-[10px] font-black uppercase tracking-widest rounded-full">Expire</span>
                <span class="text-xl font-bold text-primary">14,99 $ CAD</span>
              </div>
              <h3 class="font-bold text-primary text-lg mb-4">Abonnement Standard</h3>
              <div class="space-y-2 text-sm text-on-surface-variant flex-grow">
                <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">calendar_today</span> Date d'activation : <strong class="text-primary ml-auto">02 Nov 2024</strong></p>
                <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">event</span> Date d'expiration : <strong class="text-primary ml-auto">02 Nov 2025</strong></p>
                <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">schedule</span> Duree : <strong class="text-primary ml-auto">1 an</strong></p>
                <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">credit_card</span> Paiement : <strong class="text-primary ml-auto">PayPal</strong></p>
              </div>
              <div class="flex items-center gap-3 mt-5 pt-4 border-t border-outline-variant/10">
                <button class="flex-1 py-2.5 bg-secondary-container text-white text-sm font-semibold rounded-xl hover:bg-secondary transition-colors">Renouveler</button>
                <button class="text-sm font-semibold text-outline hover:text-primary transition-colors underline">Voir details</button>
              </div>
            </div>
          </div>

          <div class="subscription-item" data-status="expired">
            <div class="subscription-card card-glow border-l-4 border-l-secondary-container/50 rounded-2xl p-6 h-full flex flex-col">
              <div class="flex justify-between items-start mb-3">
                <span class="px-3 py-1 bg-secondary-container/10 text-secondary-container text-[10px] font-black uppercase tracking-widest rounded-full">Expire</span>
                <span class="text-xl font-bold text-primary">14,99 $ CAD</span>
              </div>
              <h3 class="font-bold text-primary text-lg mb-4">Abonnement Standard</h3>
              <div class="space-y-2 text-sm text-on-surface-variant flex-grow">
                <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">calendar_today</span> Date d'activation : <strong class="text-primary ml-auto">18 Aout 2024</strong></p>
                <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">event</span> Date d'expiration : <strong class="text-primary ml-auto">18 Aout 2025</strong></p>
                <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">schedule</span> Duree : <strong class="text-primary ml-auto">1 an</strong></p>
                <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">credit_card</span> Paiement : <strong class="text-primary ml-auto">PayPal</strong></p>
              </div>
              <div class="flex items-center gap-3 mt-5 pt-4 border-t border-outline-variant/10">
                <button class="flex-1 py-2.5 bg-secondary-container text-white text-sm font-semibold rounded-xl hover:bg-secondary transition-colors">Renouveler</button>
                <button class="text-sm font-semibold text-outline hover:text-primary transition-colors underline">Voir details</button>
              </div>
            </div>
          </div>

          <div class="subscription-item" data-status="expired">
            <div class="subscription-card card-glow border-l-4 border-l-secondary-container/50 rounded-2xl p-6 h-full flex flex-col">
              <div class="flex justify-between items-start mb-3">
                <span class="px-3 py-1 bg-secondary-container/10 text-secondary-container text-[10px] font-black uppercase tracking-widest rounded-full">Expire</span>
                <span class="text-xl font-bold text-primary">14,99 $ CAD</span>
              </div>
              <h3 class="font-bold text-primary text-lg mb-4">Abonnement Standard</h3>
              <div class="space-y-2 text-sm text-on-surface-variant flex-grow">
                <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">calendar_today</span> Date d'activation : <strong class="text-primary ml-auto">03 Avril 2024</strong></p>
                <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">event</span> Date d'expiration : <strong class="text-primary ml-auto">03 Avril 2025</strong></p>
                <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">schedule</span> Duree : <strong class="text-primary ml-auto">1 an</strong></p>
                <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">credit_card</span> Paiement : <strong class="text-primary ml-auto">PayPal</strong></p>
              </div>
              <div class="flex items-center gap-3 mt-5 pt-4 border-t border-outline-variant/10">
                <button class="flex-1 py-2.5 bg-secondary-container text-white text-sm font-semibold rounded-xl hover:bg-secondary transition-colors">Renouveler</button>
                <button class="text-sm font-semibold text-outline hover:text-primary transition-colors underline">Voir details</button>
              </div>
            </div>
          </div>

        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between mt-8 card-glow rounded-2xl px-6 py-4">
          <p class="text-xs text-on-surface-variant" id="paginationInfo">Affichage de 1 a 9 sur 9 abonnements</p>
          <div class="flex items-center gap-1">
            <button class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold text-outline hover:bg-surface-container transition-colors"><span class="material-symbols-outlined text-sm">chevron_left</span></button>
            <button class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold bg-primary text-white">1</button>
            <button class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold text-outline hover:bg-surface-container transition-colors"><span class="material-symbols-outlined text-sm">chevron_right</span></button>
          </div>
        </div>

        <!-- Aide -->
        <div class="mt-8 bg-gradient-to-br from-secondary-container/10 to-secondary-container/5 rounded-2xl p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
          <div>
            <h3 class="text-lg font-bold text-secondary-container mb-1">Besoin d'aide avec vos abonnements ?</h3>
            <p class="text-sm text-secondary-container">Notre equipe est la pour vous aider avec toutes vos questions.</p>
          </div>
          <button class="px-6 py-3 bg-secondary-container text-white text-sm font-semibold rounded-xl hover:bg-secondary transition-colors shadow-sm flex-shrink-0">Contacter le support</button>
        </div>
      </div>
    </section>

  </main>
@endsection
