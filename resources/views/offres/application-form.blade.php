@extends('layouts.guest')
@section('title', 'app form')
@section('content')
  <main class="flex-grow pt-32 pb-20">
    <div class="max-w-5xl mx-auto px-4 md:px-10">

      <!-- Breadcrumb -->
      <nav class="flex items-center gap-3 text-[10px] font-black uppercase tracking-[0.2em] text-primary/30 mb-8">
        <a href="{{ route('welcome') }}" class="hover:text-secondary-container transition-colors">Accueil</a>
        <span class="material-symbols-outlined text-sm opacity-30">chevron_right</span>
        <a href="{{ route('offres') }}" class="hover:text-secondary-container transition-colors">Offres</a>
        <span class="material-symbols-outlined text-sm opacity-30">chevron_right</span>
        <a href="{{ route('details.offre') }}" class="hover:text-secondary-container transition-colors">Ingénieur Cloud Senior</a>
        <span class="material-symbols-outlined text-sm opacity-30">chevron_right</span>
        <span class="text-primary/60">Postuler</span>
      </nav>

      <div class="flex flex-col lg:flex-row gap-8">

        <!-- FORMULAIRE -->
        <div class="flex-1">
          <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-primary/5">
            <h1 class="text-2xl md:text-3xl font-bold font-serif text-primary mb-2">Postuler au poste</h1>
            <p class="text-on-surface-variant mb-8">Remplissez le formulaire ci-dessous pour soumettre votre candidature.</p>

            <form class="space-y-6">
              <!-- Informations personnelles -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-semibold text-primary mb-2">Prénom <span class="text-secondary-container">*</span></label>
                  <input type="text" class="w-full rounded-xl border-outline-variant/30 focus:ring-secondary-container focus:border-secondary-container text-on-surface placeholder-outline" placeholder="Votre prénom" />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-primary mb-2">Nom <span class="text-secondary-container">*</span></label>
                  <input type="text" class="w-full rounded-xl border-outline-variant/30 focus:ring-secondary-container focus:border-secondary-container text-on-surface placeholder-outline" placeholder="Votre nom" />
                </div>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-semibold text-primary mb-2">Email <span class="text-secondary-container">*</span></label>
                  <input type="email" class="w-full rounded-xl border-outline-variant/30 focus:ring-secondary-container focus:border-secondary-container text-on-surface placeholder-outline" placeholder="vous@email.com" />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-primary mb-2">Téléphone <span class="text-secondary-container">*</span></label>
                  <input type="tel" class="w-full rounded-xl border-outline-variant/30 focus:ring-secondary-container focus:border-secondary-container text-on-surface placeholder-outline" placeholder="+1 (514) 555-0123" />
                </div>
              </div>

              <div>
                <label class="block text-sm font-semibold text-primary mb-2">Adresse</label>
                <input type="text" class="w-full rounded-xl border-outline-variant/30 focus:ring-secondary-container focus:border-secondary-container text-on-surface placeholder-outline" placeholder="Votre adresse complète" />
              </div>

              <!-- CV -->
              <div>
                <label class="block text-sm font-semibold text-primary mb-2">Curriculum Vitae (CV) <span class="text-secondary-container">*</span></label>
                <div class="border-2 border-dashed border-outline-variant/30 rounded-2xl p-8 text-center hover:border-secondary-container/50 transition-colors cursor-pointer">
                  <span class="material-symbols-outlined text-outline text-4xl mb-3">upload_file</span>
                  <p class="text-on-surface-variant font-medium">Glissez-déposez votre CV ici</p>
                  <p class="text-on-surface-variant/60 text-sm mt-1">PDF, DOCX ou DOC — Max 5 Mo</p>
                  <input type="file" class="hidden" accept=".pdf,.docx,.doc" />
                </div>
              </div>

              <!-- Lettre de motivation -->
              <div>
                <label class="block text-sm font-semibold text-primary mb-2">Lettre de motivation</label>
                <textarea rows="5" class="w-full rounded-xl border-outline-variant/30 focus:ring-secondary-container focus:border-secondary-container text-on-surface placeholder-outline" placeholder="Expliquez pourquoi vous êtes le candidat idéal..."></textarea>
              </div>

              <!-- Disponibilité -->
              <div>
                <label class="block text-sm font-semibold text-primary mb-2">Disponibilité <span class="text-secondary-container">*</span></label>
                <select class="w-full rounded-xl border-outline-variant/30 focus:ring-secondary-container focus:border-secondary-container text-on-surface">
                  <option value="">Sélectionnez votre disponibilité</option>
                  <option>Immédiate</option>
                  <option>2 semaines</option>
                  <option>1 mois</option>
                  <option>3 mois</option>
                  <option>À discuter</option>
                </select>
              </div>

              <!-- Lien portfolio / LinkedIn -->
              <div>
                <label class="block text-sm font-semibold text-primary mb-2">Portfolio ou LinkedIn</label>
                <input type="url" class="w-full rounded-xl border-outline-variant/30 focus:ring-secondary-container focus:border-secondary-container text-on-surface placeholder-outline" placeholder="https://linkedin.com/in/votreprofil" />
              </div>

              <!-- Consentement -->
              <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" class="mt-1 rounded border-outline-variant text-secondary-container focus:ring-secondary-container" />
                <span class="text-sm text-on-surface-variant">J'accepte que mes données soient traitées conformément à la <a href="{{ route('policy') }}" class="text-secondary-container hover:underline">politique de confidentialité</a> de ProximaJob.</span>
              </label>

              <button type="submit" class="w-full bg-secondary-container text-white font-bold px-8 py-4 rounded-xl hover:bg-secondary transition-all shadow flex items-center justify-center gap-2">
                Envoyer ma candidature <span class="material-symbols-outlined">send</span>
              </button>
            </form>
          </div>
        </div>

        <!-- SIDEBAR -->
        <aside class="w-full lg:w-80 flex-shrink-0">
          <div class="lg:sticky lg:top-28 space-y-4">

            <!-- Job Summary -->
            <div class="bg-primary text-white rounded-2xl p-6">
              <span class="inline-block px-3 py-1 bg-white/20 rounded-full text-[10px] font-bold uppercase tracking-wider mb-4">Réf. #DS-2025-042</span>
              <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                  <span class="material-symbols-outlined text-white">cloud_done</span>
                </div>
                <div>
                  <h3 class="font-bold text-lg">Ingénieur Cloud Senior</h3>
                  <p class="text-white/70 text-sm">DataSphere Inc.</p>
                </div>
              </div>
              <div class="space-y-3 text-sm text-white/80">
                <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm">location_on</span> Montréal, QC</p>
                <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm">payments</span> 85k - 110k $ / an</p>
                <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm">work</span> Temps plein</p>
              </div>
            </div>

            <!-- Prérequis -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-primary/5">
              <h3 class="font-bold text-primary text-sm uppercase tracking-wider mb-4">Prérequis demandés</h3>
              <ul class="space-y-3">
                <li class="flex items-center gap-2 text-sm text-on-surface-variant">
                  <span class="material-symbols-outlined text-green-500 text-lg">check_circle</span> Baccalauréat en informatique
                </li>
                <li class="flex items-center gap-2 text-sm text-on-surface-variant">
                  <span class="material-symbols-outlined text-green-500 text-lg">check_circle</span> 5+ ans d'expérience
                </li>
                <li class="flex items-center gap-2 text-sm text-on-surface-variant">
                  <span class="material-symbols-outlined text-green-500 text-lg">check_circle</span> AWS / GCP certifié
                </li>
                <li class="flex items-center gap-2 text-sm text-on-surface-variant">
                  <span class="material-symbols-outlined text-green-500 text-lg">check_circle</span> Français & Anglais
                </li>
              </ul>
            </div>

          </div>
        </aside>

      </div>
    </div>
  </main>
@endsection
