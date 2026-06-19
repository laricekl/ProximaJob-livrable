@extends('layouts.entreprise')
@section('title', 'Promotions')
@section('content')
  <main class="flex-grow pt-32 pb-16">

    <section class="py-8 px-4 md:px-10">
      <div class="max-w-7xl mx-auto">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
          <div>
            <h1 class="text-2xl font-bold font-serif text-primary mb-2">Promotions</h1>
            <p class="text-sm text-on-surface-variant">Gérez les promotions de vos offres d'emploi pour maximiser leur visibilité.</p>
          </div>
          <button class="px-6 py-3 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-all flex items-center gap-2 shadow-lg shadow-secondary-container/20">
            <span class="material-symbols-outlined">add</span> Ajouter une promotion
          </button>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
          <div class="card-glow rounded-2xl p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-secondary-container/10 flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined text-secondary-container">trending_up</span></div>
            <div><p class="text-2xl font-bold text-primary">14</p><p class="text-sm text-on-surface-variant">Promotions actives</p></div>
          </div>
          <div class="card-glow rounded-2xl p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-secondary-container/10 flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined text-secondary-container">pending</span></div>
            <div><p class="text-2xl font-bold text-primary">2</p><p class="text-sm text-on-surface-variant">En attente</p></div>
          </div>
          <div class="card-glow rounded-2xl p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-secondary-container/10 flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined text-secondary-container">ads_click</span></div>
            <div><p class="text-2xl font-bold text-primary">72%</p><p class="text-sm text-on-surface-variant">Taux de clic moyen</p></div>
          </div>
        </div>

        <!-- Search -->
        <div class="relative w-full sm:w-80 mb-8">
          <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">search</span>
          <input type="text" class="w-full pl-11 pr-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary placeholder:text-outline focus:border-secondary-container/50 focus:ring-0 transition-all" placeholder="Rechercher une promotion..." />
        </div>

        <!-- Table -->
        <div class="card-glow rounded-2xl overflow-hidden mb-10">
          <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
              <thead>
                <tr class="border-b border-outline-variant/10 bg-surface-container-low/50">
                  <th class="px-6 py-4 font-bold text-primary text-xs uppercase tracking-wider">Nom</th>
                  <th class="px-6 py-4 font-bold text-primary text-xs uppercase tracking-wider">Type</th>
                  <th class="px-6 py-4 font-bold text-primary text-xs uppercase tracking-wider">Date début</th>
                  <th class="px-6 py-4 font-bold text-primary text-xs uppercase tracking-wider">Date fin</th>
                  <th class="px-6 py-4 font-bold text-primary text-xs uppercase tracking-wider">Statut</th>
                  <th class="px-6 py-4 font-bold text-primary text-xs uppercase tracking-wider text-right">Actions</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-outline-variant/5">
                <tr class="hover:bg-surface-container-low/30 transition-colors">
                  <td class="px-6 py-4 font-semibold text-primary">Boost Développeur Full Stack</td>
                  <td class="px-6 py-4"><span class="px-2 py-0.5 bg-secondary-container/10 text-secondary-container text-[11px] font-bold rounded-full">Journalier</span></td>
                  <td class="px-6 py-4 text-on-surface-variant">12/05/2026</td>
                  <td class="px-6 py-4 text-on-surface-variant">19/05/2026</td>
                  <td class="px-6 py-4"><span class="px-2 py-0.5 bg-secondary-container/10 text-secondary-container text-[11px] font-semibold rounded-full">Active</span></td>
                  <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                      <button class="w-8 h-8 rounded-lg bg-surface-container-low hover:bg-primary-container/10 transition-colors flex items-center justify-center text-outline hover:text-primary" title="Modifier"><span class="material-symbols-outlined text-base">edit</span></button>
                      <button class="w-8 h-8 rounded-lg bg-surface-container-low hover:bg-secondary-container/10 transition-colors flex items-center justify-center text-outline hover:text-secondary-container" title="Statistiques"><span class="material-symbols-outlined text-base">bar_chart</span></button>
                      <button class="w-8 h-8 rounded-lg bg-surface-container-low hover:bg-secondary-container/10 transition-colors flex items-center justify-center text-outline hover:text-secondary-container" title="Supprimer"><span class="material-symbols-outlined text-base">delete</span></button>
                    </div>
                  </td>
                </tr>
                <tr class="hover:bg-surface-container-low/30 transition-colors">
                  <td class="px-6 py-4 font-semibold text-primary">Visibilité Designer UX/UI</td>
                  <td class="px-6 py-4"><span class="px-2 py-0.5 bg-secondary-container/10 text-secondary-container text-[11px] font-bold rounded-full">Mensuel</span></td>
                  <td class="px-6 py-4 text-on-surface-variant">01/05/2026</td>
                  <td class="px-6 py-4 text-on-surface-variant">01/06/2026</td>
                  <td class="px-6 py-4"><span class="px-2 py-0.5 bg-secondary-container/10 text-secondary-container text-[11px] font-semibold rounded-full">Active</span></td>
                  <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                      <button class="w-8 h-8 rounded-lg bg-surface-container-low hover:bg-primary-container/10 transition-colors flex items-center justify-center text-outline hover:text-primary" title="Modifier"><span class="material-symbols-outlined text-base">edit</span></button>
                      <button class="w-8 h-8 rounded-lg bg-surface-container-low hover:bg-secondary-container/10 transition-colors flex items-center justify-center text-outline hover:text-secondary-container" title="Statistiques"><span class="material-symbols-outlined text-base">bar_chart</span></button>
                      <button class="w-8 h-8 rounded-lg bg-surface-container-low hover:bg-secondary-container/10 transition-colors flex items-center justify-center text-outline hover:text-secondary-container" title="Supprimer"><span class="material-symbols-outlined text-base">delete</span></button>
                    </div>
                  </td>
                </tr>
                <tr class="hover:bg-surface-container-low/30 transition-colors">
                  <td class="px-6 py-4 font-semibold text-primary">Urgent DevOps Engineer</td>
                  <td class="px-6 py-4"><span class="px-2 py-0.5 bg-secondary-container/10 text-secondary-container text-[11px] font-bold rounded-full">Journalier</span></td>
                  <td class="px-6 py-4 text-on-surface-variant">10/05/2026</td>
                  <td class="px-6 py-4 text-on-surface-variant">17/05/2026</td>
                  <td class="px-6 py-4"><span class="px-2 py-0.5 bg-secondary-container/10 text-secondary-container text-[11px] font-semibold rounded-full">En attente</span></td>
                  <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                      <button class="w-8 h-8 rounded-lg bg-surface-container-low hover:bg-primary-container/10 transition-colors flex items-center justify-center text-outline hover:text-primary" title="Modifier"><span class="material-symbols-outlined text-base">edit</span></button>
                      <button class="w-8 h-8 rounded-lg bg-surface-container-low hover:bg-secondary-container/10 transition-colors flex items-center justify-center text-outline hover:text-secondary-container" title="Statistiques"><span class="material-symbols-outlined text-base">bar_chart</span></button>
                      <button class="w-8 h-8 rounded-lg bg-surface-container-low hover:bg-secondary-container/10 transition-colors flex items-center justify-center text-outline hover:text-secondary-container" title="Supprimer"><span class="material-symbols-outlined text-base">delete</span></button>
                    </div>
                  </td>
                </tr>
                <tr class="hover:bg-surface-container-low/30 transition-colors">
                  <td class="px-6 py-4 font-semibold text-primary">Mise en avant Data Analyst</td>
                  <td class="px-6 py-4"><span class="px-2 py-0.5 bg-secondary-container/10 text-secondary-container text-[11px] font-bold rounded-full">Mensuel</span></td>
                  <td class="px-6 py-4 text-on-surface-variant">15/04/2026</td>
                  <td class="px-6 py-4 text-on-surface-variant">15/05/2026</td>
                  <td class="px-6 py-4"><span class="px-2 py-0.5 bg-secondary-container/10 text-secondary-container text-[11px] font-semibold rounded-full">Expirée</span></td>
                  <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                      <button class="w-8 h-8 rounded-lg bg-surface-container-low hover:bg-primary-container/10 transition-colors flex items-center justify-center text-outline hover:text-primary" title="Renouveler"><span class="material-symbols-outlined text-base">refresh</span></button>
                      <button class="w-8 h-8 rounded-lg bg-surface-container-low hover:bg-secondary-container/10 transition-colors flex items-center justify-center text-outline hover:text-secondary-container" title="Statistiques"><span class="material-symbols-outlined text-base">bar_chart</span></button>
                      <button class="w-8 h-8 rounded-lg bg-surface-container-low hover:bg-secondary-container/10 transition-colors flex items-center justify-center text-outline hover:text-secondary-container" title="Supprimer"><span class="material-symbols-outlined text-base">delete</span></button>
                    </div>
                  </td>
                </tr>
                <tr class="hover:bg-surface-container-low/30 transition-colors">
                  <td class="px-6 py-4 font-semibold text-primary">Boost Chef de Projet IT</td>
                  <td class="px-6 py-4"><span class="px-2 py-0.5 bg-secondary-container/10 text-secondary-container text-[11px] font-bold rounded-full">Journalier</span></td>
                  <td class="px-6 py-4 text-on-surface-variant">08/05/2026</td>
                  <td class="px-6 py-4 text-on-surface-variant">15/05/2026</td>
                  <td class="px-6 py-4"><span class="px-2 py-0.5 bg-secondary-container/10 text-secondary-container text-[11px] font-semibold rounded-full">Active</span></td>
                  <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                      <button class="w-8 h-8 rounded-lg bg-surface-container-low hover:bg-primary-container/10 transition-colors flex items-center justify-center text-outline hover:text-primary" title="Modifier"><span class="material-symbols-outlined text-base">edit</span></button>
                      <button class="w-8 h-8 rounded-lg bg-surface-container-low hover:bg-secondary-container/10 transition-colors flex items-center justify-center text-outline hover:text-secondary-container" title="Statistiques"><span class="material-symbols-outlined text-base">bar_chart</span></button>
                      <button class="w-8 h-8 rounded-lg bg-surface-container-low hover:bg-secondary-container/10 transition-colors flex items-center justify-center text-outline hover:text-secondary-container" title="Supprimer"><span class="material-symbols-outlined text-base">delete</span></button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center items-center gap-3">
          <button class="w-10 h-10 rounded-xl bg-white/70 border border-outline-variant/20 flex items-center justify-center text-outline hover:border-secondary-container/30 transition-colors disabled:opacity-40" disabled><span class="material-symbols-outlined text-lg">chevron_left</span></button>
          <button class="w-10 h-10 rounded-xl bg-secondary-container text-white text-sm font-bold">1</button>
          <button class="w-10 h-10 rounded-xl bg-white/70 border border-outline-variant/20 flex items-center justify-center text-sm font-semibold text-primary hover:border-secondary-container/30 transition-colors">2</button>
          <button class="w-10 h-10 rounded-xl bg-white/70 border border-outline-variant/20 flex items-center justify-center text-outline hover:border-secondary-container/30 transition-colors"><span class="material-symbols-outlined text-lg">chevron_right</span></button>
        </div>

      </div>
    </section>

  </main>
@csrf
@endsection
