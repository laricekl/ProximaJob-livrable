@extends('layouts.candidat')
@section('title', 'Offres')
@section('content')
  <main class="flex-grow pt-32">

    <!-- Barre de recherche -->
    <section class="py-8 px-4 md:px-10 bg-white border-b border-outline-variant/10">
      <div class="max-w-5xl mx-auto">
        <div class="flex flex-col sm:flex-row gap-3 bg-surface-container-low/50 rounded-2xl p-2">
          <div class="flex-1 relative">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">search</span>
            <input type="text" placeholder="Poste, mots-clés..." class="w-full pl-12 pr-4 py-3.5 bg-white rounded-xl border border-outline-variant/30 text-sm text-primary placeholder:text-outline focus:border-secondary-container/50 transition-all" />
          </div>
          <div class="relative">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">location_on</span>
            <input type="text" placeholder="Localisation" class="w-full sm:w-48 pl-12 pr-4 py-3.5 bg-white rounded-xl border border-outline-variant/30 text-sm text-primary placeholder:text-outline focus:border-secondary-container/50 transition-all" />
          </div>
          <select class="px-4 py-3.5 bg-white rounded-xl border border-outline-variant/30 text-sm text-primary focus:border-secondary-container/50 transition-all">
            <option>Type de contrat</option>
            <option>Temps plein</option>
            <option>Temps partiel</option>
            <option>CDI</option>
            <option>CDD</option>
            <option>Stage</option>
          </select>
          <button class="px-8 py-3.5 bg-secondary-container text-white font-bold rounded-xl hover:bg-secondary transition-all shadow-lg shadow-secondary-container/20 flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">search</span> Rechercher
          </button>
        </div>
      </div>
    </section>

    <!-- Contenu principal -->
    <section class="py-12 px-4 md:px-10">
      <div class="max-w-7xl mx-auto">
        <div class="flex flex-col lg:flex-row gap-8">

          <!-- Sidebar filtres -->
          <aside class="lg:w-72 flex-shrink-0">
            <div class="card-glow rounded-2xl p-6 sticky top-28">
              <h3 class="font-bold text-primary text-sm mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-secondary-container text-lg">tune</span> Filtres
              </h3>

              <div class="space-y-5">
                <div>
                  <h4 class="text-xs font-bold text-primary uppercase tracking-wider mb-3">Type de contrat</h4>
                  <div class="space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-on-surface-variant"><input type="checkbox" class="rounded border-outline-variant/50 text-secondary-container focus:ring-secondary-container/30" /> Temps plein</label>
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-on-surface-variant"><input type="checkbox" class="rounded border-outline-variant/50 text-secondary-container focus:ring-secondary-container/30" /> Temps partiel</label>
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-on-surface-variant"><input type="checkbox" class="rounded border-outline-variant/50 text-secondary-container focus:ring-secondary-container/30" /> CDI</label>
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-on-surface-variant"><input type="checkbox" class="rounded border-outline-variant/50 text-secondary-container focus:ring-secondary-container/30" /> Stage</label>
                  </div>
                </div>

                <div>
                  <h4 class="text-xs font-bold text-primary uppercase tracking-wider mb-3">Mode de travail</h4>
                  <div class="space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-on-surface-variant"><input type="checkbox" class="rounded border-outline-variant/50 text-secondary-container focus:ring-secondary-container/30" /> Présentiel</label>
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-on-surface-variant"><input type="checkbox" class="rounded border-outline-variant/50 text-secondary-container focus:ring-secondary-container/30" /> Hybride</label>
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-on-surface-variant"><input type="checkbox" class="rounded border-outline-variant/50 text-secondary-container focus:ring-secondary-container/30" /> Télétravail</label>
                  </div>
                </div>

                <div>
                  <h4 class="text-xs font-bold text-primary uppercase tracking-wider mb-3">Salaire minimum</h4>
                  <select class="w-full px-3 py-3 bg-white rounded-xl border border-outline-variant/30 text-sm text-primary focus:border-secondary-container/50 transition-all">
                    <option>Tous</option>
                    <option>40 000$+</option>
                    <option>60 000$+</option>
                    <option>80 000$+</option>
                    <option>100 000$+</option>
                  </select>
                </div>

                <button class="w-full py-2.5 text-sm font-semibold text-outline hover:text-primary transition-colors flex items-center justify-center gap-1">
                  <span class="material-symbols-outlined text-sm">refresh</span> Réinitialiser
                </button>
              </div>
            </div>
          </aside>

          <!-- Liste des offres -->
          <div class="flex-1">
            <div class="flex items-center justify-between mb-6">
              <p class="text-sm text-on-surface-variant"><span class="font-bold text-primary">24</span> offres trouvées</p>
              <select class="px-3 py-2 bg-white rounded-xl border border-outline-variant/30 text-xs text-primary focus:border-secondary-container/50 transition-all">
                <option>Plus récentes</option>
                <option>Salaire croissant</option>
                <option>Salaire décroissant</option>
              </select>
            </div>

            <div class="space-y-4">
              <a href="{{ route('job_infos', 'slug') }}" class="group flex flex-col sm:flex-row sm:items-center justify-between p-5 bg-white rounded-2xl border border-primary/5 hover:border-secondary-container/30 hover:shadow-[0_15px_40px_rgba(0,0,0,0.04)] transition-all duration-500 cursor-pointer relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-secondary-container/0 to-secondary-container/[0.02] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="flex items-center gap-4 relative z-10">
                  <div class="w-12 h-12 rounded-xl bg-secondary-container/10 flex items-center justify-center group-hover:scale-110 transition-transform duration-500">
                    <span class="material-symbols-outlined text-secondary-container text-xl">code</span>
                  </div>
                  <div>
                    <h4 class="font-bold text-primary group-hover:text-secondary-container transition-colors">Développeur Full Stack</h4>
                    <p class="text-on-surface-variant/70 text-xs font-medium">TechCorp • Montréal (Hybride)</p>
                  </div>
                </div>
                <div class="mt-3 sm:mt-0 flex items-center gap-3 relative z-10">
                  <span class="px-3 py-1 bg-green-50 text-green-700 text-[10px] font-black uppercase tracking-widest rounded-full">Temps plein</span>
                  <span class="text-secondary-container font-bold text-sm">75k-95k$</span>
                </div>
              </a>

              <a href="{{ route('job_infos', 'slug') }}" class="group flex flex-col sm:flex-row sm:items-center justify-between p-5 bg-white rounded-2xl border border-primary/5 hover:border-secondary-container/30 hover:shadow-[0_15px_40px_rgba(0,0,0,0.04)] transition-all duration-500 cursor-pointer relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-secondary-container/0 to-secondary-container/[0.02] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="flex items-center gap-4 relative z-10">
                  <div class="w-12 h-12 rounded-xl bg-secondary-container/10 flex items-center justify-center group-hover:scale-110 transition-transform duration-500">
                    <span class="material-symbols-outlined text-secondary-container text-xl">campaign</span>
                  </div>
                  <div>
                    <h4 class="font-bold text-primary group-hover:text-secondary-container transition-colors">Chef de Projet Marketing</h4>
                    <p class="text-on-surface-variant/70 text-xs font-medium">BrandCo • Québec (Télétravail)</p>
                  </div>
                </div>
                <div class="mt-3 sm:mt-0 flex items-center gap-3 relative z-10">
                  <span class="px-3 py-1 bg-secondary-container/10 text-secondary-container text-[10px] font-black uppercase tracking-widest rounded-full">CDI</span>
                  <span class="text-secondary-container font-bold text-sm">60k-80k$</span>
                </div>
              </a>

              <a href="{{ route('job_infos', 'slug') }}" class="group flex flex-col sm:flex-row sm:items-center justify-between p-5 bg-white rounded-2xl border border-primary/5 hover:border-secondary-container/30 hover:shadow-[0_15px_40px_rgba(0,0,0,0.04)] transition-all duration-500 cursor-pointer relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-secondary-container/0 to-secondary-container/[0.02] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="flex items-center gap-4 relative z-10">
                  <div class="w-12 h-12 rounded-xl bg-secondary-container/10 flex items-center justify-center group-hover:scale-110 transition-transform duration-500">
                    <span class="material-symbols-outlined text-secondary-container text-xl">analytics</span>
                  </div>
                  <div>
                    <h4 class="font-bold text-primary group-hover:text-secondary-container transition-colors">Data Analyst</h4>
                    <p class="text-on-surface-variant/70 text-xs font-medium">DataProd • Montréal (Présentiel)</p>
                  </div>
                </div>
                <div class="mt-3 sm:mt-0 flex items-center gap-3 relative z-10">
                  <span class="px-3 py-1 bg-green-50 text-green-700 text-[10px] font-black uppercase tracking-widest rounded-full">Temps plein</span>
                  <span class="text-secondary-container font-bold text-sm">65k-85k$</span>
                </div>
              </a>

              <a href="{{ route('job_infos', 'slug') }}" class="group flex flex-col sm:flex-row sm:items-center justify-between p-5 bg-white rounded-2xl border border-primary/5 hover:border-secondary-container/30 hover:shadow-[0_15px_40px_rgba(0,0,0,0.04)] transition-all duration-500 cursor-pointer relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-secondary-container/0 to-secondary-container/[0.02] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="flex items-center gap-4 relative z-10">
                  <div class="w-12 h-12 rounded-xl bg-secondary-container/10 flex items-center justify-center group-hover:scale-110 transition-transform duration-500">
                    <span class="material-symbols-outlined text-secondary-container text-xl">design_services</span>
                  </div>
                  <div>
                    <h4 class="font-bold text-primary group-hover:text-secondary-container transition-colors">UI/UX Designer</h4>
                    <p class="text-on-surface-variant/70 text-xs font-medium">DesignLab • Montréal (Hybride)</p>
                  </div>
                </div>
                <div class="mt-3 sm:mt-0 flex items-center gap-3 relative z-10">
                  <span class="px-3 py-1 bg-green-50 text-green-700 text-[10px] font-black uppercase tracking-widest rounded-full">Temps plein</span>
                  <span class="text-secondary-container font-bold text-sm">70k-90k$</span>
                </div>
              </a>

              <a href="{{ route('job_infos', 'slug') }}" class="group flex flex-col sm:flex-row sm:items-center justify-between p-5 bg-white rounded-2xl border border-primary/5 hover:border-secondary-container/30 hover:shadow-[0_15px_40px_rgba(0,0,0,0.04)] transition-all duration-500 cursor-pointer relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-secondary-container/0 to-secondary-container/[0.02] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="flex items-center gap-4 relative z-10">
                  <div class="w-12 h-12 rounded-xl bg-secondary-fixed flex items-center justify-center group-hover:scale-110 transition-transform duration-500">
                    <span class="material-symbols-outlined text-on-secondary-fixed-variant text-xl">engineering</span>
                  </div>
                  <div>
                    <h4 class="font-bold text-primary group-hover:text-secondary-container transition-colors">Ingénieur DevOps</h4>
                    <p class="text-on-surface-variant/70 text-xs font-medium">CloudSys • Québec (Télétravail)</p>
                  </div>
                </div>
                <div class="mt-3 sm:mt-0 flex items-center gap-3 relative z-10">
                  <span class="px-3 py-1 bg-secondary-container/10 text-secondary-container text-[10px] font-black uppercase tracking-widest rounded-full">CDI</span>
                  <span class="text-secondary-container font-bold text-sm">85k-110k$</span>
                </div>
              </a>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-center gap-2 mt-10">
              <button class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold text-outline hover:bg-white transition-colors"><span class="material-symbols-outlined text-sm">chevron_left</span></button>
              <button class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold bg-primary text-white">1</button>
              <button class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold text-primary hover:bg-white transition-colors">2</button>
              <button class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold text-primary hover:bg-white transition-colors">3</button>
              <button class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold text-outline hover:bg-white transition-colors"><span class="material-symbols-outlined text-sm">chevron_right</span></button>
            </div>
          </div>

        </div>
      </div>
    </section>

  </main>
@endsection
