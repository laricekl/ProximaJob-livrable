@extends('layouts.entreprise')

@section('title', 'Nouvelle offre')

@section('styles')
  <style>
    .input-glass { background: rgb(255 255 255 / 0.7); backdrop-filter: blur(12px); border: 1px solid rgb(198 198 205 / 0.3); }
    .input-glass:focus { background: white; border-color: rgb(235 132 60 / 0.5); }
  </style>
@endsection

@section('content')
  <section class="px-4 md:px-10 pt-32 pb-16">
    <div class="max-w-4xl mx-auto">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
          <a href="{{ route('offres.publies') }}" class="inline-flex items-center gap-2 text-sm text-secondary-container font-semibold hover:underline mb-3">
            <span class="material-symbols-outlined text-lg">arrow_back</span> Retour aux offres
          </a>
          <h1 class="text-2xl md:text-3xl font-bold font-serif text-primary">Nouvelle offre d'emploi</h1>
        </div>
        <button type="submit" form="offerForm" class="px-6 py-2.5 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-colors flex items-center gap-2">
          <span class="material-symbols-outlined text-lg">publish</span> Publier
        </button>
      </div>

      @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-error-light bg-error-light px-5 py-4 text-sm text-error-dark">
          <p class="font-bold mb-2">Le formulaire contient des erreurs.</p>
          <ul class="space-y-1">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @if (session('error'))
        <div class="mb-6 rounded-2xl border border-error-light bg-error-light px-5 py-4 text-sm text-error-dark">
          {{ session('error') }}
        </div>
      @endif

      <form id="offerForm" method="POST" action="{{ route('offres.store') }}" class="space-y-6">
        @csrf

        <div class="card-glow rounded-2xl overflow-hidden">
          <div class="px-8 py-5 border-b border-outline-variant/10">
            <h2 class="text-lg font-bold font-serif text-primary flex items-center gap-2"><span class="w-7 h-7 rounded-full bg-secondary-container text-white text-xs font-black flex items-center justify-center">1</span> Informations principales</h2>
          </div>
          <div class="p-6 md:p-8 space-y-5">
            <div>
              <label for="jobTitle" class="block text-sm font-semibold text-primary mb-1.5">Titre du poste <span class="text-secondary-container">*</span></label>
              <input id="jobTitle" name="jobTitle" type="text" value="{{ old('jobTitle') }}" class="input-glass w-full px-4 py-3 rounded-xl text-sm text-primary placeholder:text-outline transition-all {{ $errors->has('jobTitle') ? 'border-error' : '' }}" placeholder="Ex: Développeur Full Stack Senior" required />
              @error('jobTitle') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div>
                <label for="contractType" class="block text-sm font-semibold text-primary mb-1.5">Type de contrat <span class="text-secondary-container">*</span></label>
                <select id="contractType" name="contractType" class="input-glass w-full px-4 py-3 rounded-xl text-sm text-primary transition-all {{ $errors->has('contractType') ? 'border-error' : '' }}" required>
                  <option value="">Choisir...</option>
                  @foreach ($contractTypes as $contractType)
                    <option value="{{ $contractType->id }}" @selected(old('contractType') == $contractType->id)>{{ $contractType->nom }}</option>
                  @endforeach
                </select>
                @error('contractType') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
              </div>
              <div>
                <label for="sector" class="block text-sm font-semibold text-primary mb-1.5">Secteur d'activité <span class="text-secondary-container">*</span></label>
                <select id="sector" name="sector" class="input-glass w-full px-4 py-3 rounded-xl text-sm text-primary transition-all {{ $errors->has('sector') ? 'border-error' : '' }}" required>
                  <option value="">Choisir...</option>
                  @forelse ($sectors as $sector)
                    <option value="{{ $sector->id }}" @selected(old('sector') == $sector->id)>{{ $sector->name }}</option>
                  @empty
                    <option value="" disabled>Aucun secteur disponible pour le moment</option>
                  @endforelse
                </select>
                @error('sector') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
              <div>
                <label for="location" class="block text-sm font-semibold text-primary mb-1.5">Lieu de travail <span class="text-secondary-container">*</span></label>
                <input id="location" name="location" type="text" value="{{ old('location') }}" class="input-glass w-full px-4 py-3 rounded-xl text-sm text-primary placeholder:text-outline transition-all {{ $errors->has('location') ? 'border-error' : '' }}" placeholder="Ex: Montréal, QC" required />
              @error('location') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
              </div>
              <div>
                <label for="remote_work" class="block text-sm font-semibold text-primary mb-1.5">Mode de travail <span class="text-secondary-container">*</span></label>
                <select id="remote_work" name="remote_work" class="input-glass w-full px-4 py-3 rounded-xl text-sm text-primary transition-all {{ $errors->has('remote_work') ? 'border-error' : '' }}" required>
                  <option value="Présentiel" @selected(old('remote_work') === 'Présentiel')>Présentiel</option>
                  <option value="Hybride" @selected(old('remote_work') === 'Hybride')>Hybride</option>
                  <option value="Télétravail" @selected(old('remote_work') === 'Télétravail')>Télétravail</option>
                </select>
                @error('remote_work') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
              </div>
              <div>
                <label for="job_category" class="block text-sm font-semibold text-primary mb-1.5">Catégorie <span class="text-secondary-container">*</span></label>
                <select id="job_category" name="job_category" class="input-glass w-full px-4 py-3 rounded-xl text-sm text-primary transition-all {{ $errors->has('job_category') ? 'border-error' : '' }}" required>
                  <option value="informatique" @selected(old('job_category') === 'informatique')>Informatique & Technologie</option>
                  <option value="marketing" @selected(old('job_category') === 'marketing')>Marketing & Communication</option>
                  <option value="finance" @selected(old('job_category') === 'finance')>Finance & Comptabilité</option>
                  <option value="rh" @selected(old('job_category') === 'rh')>Ressources humaines</option>
                  <option value="sante" @selected(old('job_category') === 'sante')>Santé</option>
                </select>
                @error('job_category') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div>
                <label for="salary_type" class="block text-sm font-semibold text-primary mb-1.5">Type de rémunération <span class="text-secondary-container">*</span></label>
                <select id="salary_type" name="salary_type" class="input-glass w-full px-4 py-3 rounded-xl text-sm text-primary transition-all {{ $errors->has('salary_type') ? 'border-error' : '' }}" required>
                  <option value="annuel" @selected(old('salary_type') === 'annuel')>Annuel</option>
                  <option value="mensuel" @selected(old('salary_type') === 'mensuel')>Mensuel</option>
                  <option value="journalier" @selected(old('salary_type') === 'journalier')>Journalier</option>
                  <option value="horaire" @selected(old('salary_type') === 'horaire')>Horaire</option>
                </select>
                @error('salary_type') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
              </div>
              <div>
                <label for="start_date" class="block text-sm font-semibold text-primary mb-1.5">Date d'entrée <span class="text-secondary-container">*</span></label>
                <select id="start_date" name="start_date" class="input-glass w-full px-4 py-3 rounded-xl text-sm text-primary transition-all" required>
                  <option value="Immédiate" @selected(old('start_date') === 'Immédiate')>Immédiate</option>
                  <option value="Sous 2 semaines" @selected(old('start_date') === 'Sous 2 semaines')>Sous 2 semaines</option>
                  <option value="Sous 1 mois" @selected(old('start_date') === 'Sous 1 mois')>Sous 1 mois</option>
                  <option value="Autre" @selected(old('start_date') === 'Autre')>Autre</option>
                </select>
              </div>
            </div>

            <div>
              <label for="jobDescription" class="block text-sm font-semibold text-primary mb-1.5">Description du poste <span class="text-secondary-container">*</span></label>
              <textarea id="jobDescription" name="jobDescription" rows="6" class="input-glass w-full px-4 py-3 rounded-xl text-sm text-primary placeholder:text-outline transition-all resize-none" placeholder="Décrivez le poste, les missions, l'environnement de travail..." required>{{ old('jobDescription') }}</textarea>
            </div>

            <div>
              <label for="responsibilities" class="block text-sm font-semibold text-primary mb-1.5">Responsabilités principales <span class="text-secondary-container">*</span></label>
              <textarea id="responsibilities" name="responsibilities" rows="4" class="input-glass w-full px-4 py-3 rounded-xl text-sm text-primary placeholder:text-outline transition-all resize-none" placeholder="Ex: piloter les développements, coordonner l'équipe, suivre la qualité..." required>{{ old('responsibilities') }}</textarea>
            </div>
          </div>
        </div>

        <div class="card-glow rounded-2xl overflow-hidden">
          <div class="px-8 py-5 border-b border-outline-variant/10">
            <h2 class="text-lg font-bold font-serif text-primary flex items-center gap-2"><span class="w-7 h-7 rounded-full bg-secondary-container text-white text-xs font-black flex items-center justify-center">2</span> Détails pratiques</h2>
          </div>
          <div class="p-6 md:p-8 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div>
                <label for="salary_min" class="block text-sm font-semibold text-primary mb-1.5">Salaire minimum</label>
                <input id="salary_min" name="salary_min" type="number" step="0.01" value="{{ old('salary_min') }}" class="input-glass w-full px-4 py-3 rounded-xl text-sm text-primary placeholder:text-outline transition-all" placeholder="Ex: 45000" />
              </div>
              <div>
                <label for="salary_max" class="block text-sm font-semibold text-primary mb-1.5">Salaire maximum</label>
                <input id="salary_max" name="salary_max" type="number" step="0.01" value="{{ old('salary_max') }}" class="input-glass w-full px-4 py-3 rounded-xl text-sm text-primary placeholder:text-outline transition-all" placeholder="Ex: 70000" />
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div>
                <label for="required_experience" class="block text-sm font-semibold text-primary mb-1.5">Expérience requise <span class="text-secondary-container">*</span></label>
                <select id="required_experience" name="required_experience" class="input-glass w-full px-4 py-3 rounded-xl text-sm text-primary transition-all" required>
                  <option value="Non exigée" @selected(old('required_experience') === 'Non exigée')>Non exigée</option>
                  <option value="0-1 an" @selected(old('required_experience') === '0-1 an')>0-1 an</option>
                  <option value="2-3 ans" @selected(old('required_experience') === '2-3 ans')>2-3 ans</option>
                  <option value="4-5 ans" @selected(old('required_experience') === '4-5 ans')>4-5 ans</option>
                  <option value="5 ans et plus" @selected(old('required_experience') === '5 ans et plus')>5 ans et plus</option>
                </select>
              </div>
              <div>
                <label for="endDate" class="block text-sm font-semibold text-primary mb-1.5">Date d'expiration <span class="text-secondary-container">*</span></label>
                <input id="endDate" name="endDate" type="date" value="{{ old('endDate') }}" class="input-glass w-full px-4 py-3 rounded-xl text-sm text-primary transition-all {{ $errors->has('endDate') ? 'border-error' : '' }}" required />
                @error('endDate') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
              </div>
            </div>

            <div>
              <label for="languages_data" class="block text-sm font-semibold text-primary mb-1.5">Langues demandées <span class="text-secondary-container">*</span></label>
              <input id="languages_data" name="languages_data" type="text" value="{{ old('languages_data') }}" class="input-glass w-full px-4 py-3 rounded-xl text-sm text-primary placeholder:text-outline transition-all" placeholder="Ex: Français courant, anglais professionnel" required />
            </div>

            <div>
              <label for="otherCriteria" class="block text-sm font-semibold text-primary mb-1.5">Autres critères</label>
              <textarea id="otherCriteria" name="otherCriteria" rows="3" class="input-glass w-full px-4 py-3 rounded-xl text-sm text-primary placeholder:text-outline transition-all resize-none" placeholder="Permis, disponibilité, certifications, contraintes particulières...">{{ old('otherCriteria') }}</textarea>
            </div>
          </div>
        </div>

        <div class="card-glow rounded-2xl overflow-hidden">
          <div class="px-8 py-5 border-b border-outline-variant/10">
            <h2 class="text-lg font-bold font-serif text-primary flex items-center gap-2"><span class="w-7 h-7 rounded-full bg-secondary-container text-white text-xs font-black flex items-center justify-center">3</span> Diplômes & avantages</h2>
          </div>
          <div class="p-6 md:p-8 space-y-5">
            <div>
              <label class="block text-sm font-semibold text-primary mb-2">Diplômes souhaités</label>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach ($diplomes->take(8) as $diplome)
                  <label class="flex items-center gap-3 px-4 py-3 bg-white/70 backdrop-blur-sm border border-outline-variant/20 rounded-xl text-sm cursor-pointer hover:border-secondary-container/30 transition-colors">
                    <input type="checkbox" name="diplomes[{{ $loop->index }}][id]" value="{{ $diplome->id }}" class="rounded border-outline-variant/50 text-secondary-container focus:ring-2 focus:ring-secondary-container/30" @checked(collect(old('diplomes', []))->pluck('id')->contains($diplome->id)) />
                    <input type="hidden" name="diplomes[{{ $loop->index }}][obligatoire]" value="1" />
                    <span>{{ $diplome->nom_diplome }}</span>
                  </label>
                @endforeach
              </div>
            </div>

            <div>
              <label class="block text-sm font-semibold text-primary mb-2">Avantages proposés</label>
              <div class="flex flex-wrap gap-3">
                @foreach (['Télétravail', 'Assurance collective', 'Prime', 'Budget formation', 'Horaires flexibles'] as $benefit)
                  <label class="flex items-center gap-2 px-4 py-2.5 bg-white/70 backdrop-blur-sm border border-outline-variant/20 rounded-xl text-sm cursor-pointer hover:border-secondary-container/30 transition-colors">
                    <input type="checkbox" name="benefits[]" value="{{ $benefit }}" class="rounded border-outline-variant/50 text-secondary-container focus:ring-2 focus:ring-secondary-container/30" @checked(in_array($benefit, old('benefits', []), true)) />
                    <span>{{ $benefit }}</span>
                  </label>
                @endforeach
              </div>
            </div>
          </div>
        </div>

        <input type="hidden" name="custom_benefits" value="{{ old('custom_benefits', '[]') }}" />
        <input type="hidden" name="start_date_other" value="{{ old('start_date_other') }}" />
        <input type="hidden" name="education_level" value="{{ old('education_level') }}" />

        <div class="flex justify-end">
          <button type="submit" class="px-8 py-3 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-colors flex items-center gap-2 shadow-lg shadow-secondary-container/20">
            <span class="material-symbols-outlined text-lg">publish</span> Publier l'offre
          </button>
        </div>
      </form>
    </div>
  </section>
@endsection
