@extends('layouts.entreprise')
@section('title', 'Abonnements')
@section('content')
  <main class="flex-grow pt-32 pb-16">

    <section class="py-8 px-4 md:px-10">
      <div class="max-w-7xl mx-auto">

        <div class="mb-8">
          <h1 class="text-2xl font-bold font-serif text-primary mb-2">Mes abonnements</h1>
          <p class="text-sm text-on-surface-variant">Gérez vos abonnements et suivez l'historique de vos paiements.</p>
        </div>

        <!-- Filter Tabs -->
        <div class="flex items-center gap-2 mb-8 flex-wrap">
          <button class="filter-tab px-5 py-2.5 bg-secondary-container text-white text-sm font-semibold rounded-xl transition-colors active">Tous</button>
          <button class="filter-tab px-5 py-2.5 bg-white/70 text-primary text-sm font-semibold rounded-xl hover:bg-white transition-colors">Actifs</button>
          <button class="filter-tab px-5 py-2.5 bg-white/70 text-primary text-sm font-semibold rounded-xl hover:bg-white transition-colors">Expirés</button>
        </div>

        <!-- Subscription Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
          <!-- Active sub 1 -->
          <div class="card-glow rounded-2xl overflow-hidden border-l-4 border-l-green-500">
            <div class="p-6">
              <div class="flex justify-between items-start mb-4">
                <span class="px-3 py-1 bg-secondary-container/10 text-secondary-container text-xs font-semibold rounded-full">Actif</span>
                <span class="text-xl font-bold text-primary">49,99 $</span>
              </div>
              <h3 class="font-bold font-serif text-primary text-lg mb-3">Plan Business</h3>
              <div class="space-y-2 text-sm text-on-surface-variant">
                <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">calendar_today</span> Activé le 15/03/2026</div>
                <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">event</span> Expire le 15/06/2026</div>
                <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">schedule</span> Durée : 3 mois</div>
                <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">credit_card</span> Carte bancaire ****4242</div>
              </div>
              <div class="flex gap-3 mt-5 pt-4 border-t border-outline-variant/10">
                <button class="flex-1 px-4 py-2.5 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-colors">Renouveler</button>
                <button class="px-4 py-2.5 bg-surface-container-low text-primary text-sm font-semibold rounded-xl hover:bg-surface-container transition-colors">Détails</button>
              </div>
            </div>
          </div>

          <!-- Expired sub 1 -->
          <div class="card-glow rounded-2xl overflow-hidden border-l-4 border-l-red-400">
            <div class="p-6">
              <div class="flex justify-between items-start mb-4">
                <span class="px-3 py-1 bg-secondary-container/10 text-secondary-container text-xs font-semibold rounded-full">Expiré</span>
                <span class="text-xl font-bold text-primary">29,99 $</span>
              </div>
              <h3 class="font-bold font-serif text-primary text-lg mb-3">Plan Standard</h3>
              <div class="space-y-2 text-sm text-on-surface-variant">
                <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">calendar_today</span> Activé le 15/12/2025</div>
                <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">event</span> Expiré le 15/03/2026</div>
                <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">schedule</span> Durée : 3 mois</div>
                <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">credit_card</span> Carte bancaire ****4242</div>
              </div>
              <div class="flex gap-3 mt-5 pt-4 border-t border-outline-variant/10">
                <button class="flex-1 px-4 py-2.5 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-colors">Renouveler</button>
                <button class="px-4 py-2.5 bg-surface-container-low text-primary text-sm font-semibold rounded-xl hover:bg-surface-container transition-colors">Détails</button>
              </div>
            </div>
          </div>

          <!-- Active sub 2 -->
          <div class="card-glow rounded-2xl overflow-hidden border-l-4 border-l-green-500">
            <div class="p-6">
              <div class="flex justify-between items-start mb-4">
                <span class="px-3 py-1 bg-secondary-container/10 text-secondary-container text-xs font-semibold rounded-full">Actif</span>
                <span class="text-xl font-bold text-primary">99,99 $</span>
              </div>
              <h3 class="font-bold font-serif text-primary text-lg mb-3">Plan Enterprise</h3>
              <div class="space-y-2 text-sm text-on-surface-variant">
                <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">calendar_today</span> Activé le 01/01/2026</div>
                <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">event</span> Expire le 01/01/2027</div>
                <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">schedule</span> Durée : 12 mois</div>
                <div class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline">credit_card</span> Virement bancaire</div>
              </div>
              <div class="flex gap-3 mt-5 pt-4 border-t border-outline-variant/10">
                <button class="flex-1 px-4 py-2.5 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-colors">Renouveler</button>
                <button class="px-4 py-2.5 bg-surface-container-low text-primary text-sm font-semibold rounded-xl hover:bg-surface-container transition-colors">Détails</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center items-center gap-3">
          <button class="w-10 h-10 rounded-xl bg-white/70 border border-outline-variant/20 flex items-center justify-center text-outline hover:border-secondary-container/30 transition-colors disabled:opacity-40" disabled><span class="material-symbols-outlined text-lg">chevron_left</span></button>
          <button class="w-10 h-10 rounded-xl bg-secondary-container text-white text-sm font-bold">1</button>
          <button class="w-10 h-10 rounded-xl bg-white/70 border border-outline-variant/20 flex items-center justify-center text-outline hover:border-secondary-container/30 transition-colors"><span class="material-symbols-outlined text-lg">chevron_right</span></button>
        </div>

      </div>
    </section>

  </main>
@csrf
@endsection
