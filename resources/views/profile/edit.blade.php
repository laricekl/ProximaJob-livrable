@extends('layouts.candidat')
@section('title', 'Mon profil')
@section('styles')
  <style>
    .delete-modal { opacity: 0; visibility: hidden; transition: all 0.2s ease; }
    .delete-modal.active { opacity: 1; visibility: visible; }
  </style>
@endsection
@section('content')
  <main class="flex-grow pt-32 pb-16">

    <section class="py-8 px-4 md:px-10">
      <div class="max-w-3xl mx-auto space-y-6">

        <!-- Informations du profil -->
        <div class="card-glow rounded-2xl overflow-hidden">
          <div class="px-8 py-5 border-b border-outline-variant/10">
            <h2 class="text-xl font-bold font-serif text-primary">Informations du profil</h2>
            <p class="text-sm text-on-surface-variant mt-0.5">Mettez a jour les informations de votre compte et votre adresse e-mail.</p>
          </div>
          <div class="p-8">
            <form class="space-y-5" method="POST" action="{{ route('profile.update') }}">
              @csrf
              @method('PATCH')
              <div>
                <label class="block text-sm font-semibold text-primary mb-1.5" for="name">Nom complet</label>
                <input type="text" id="name" name="name" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" value="{{ old('name', $user->name) }}" required />
              </div>
              <div>
                <label class="block text-sm font-semibold text-primary mb-1.5" for="email">Adresse e-mail</label>
                <input type="email" id="email" name="email" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" value="{{ old('email', $user->email) }}" required />
              </div>
              <div class="flex items-center gap-4 pt-2">
                <button type="submit" class="px-6 py-3 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-all">Enregistrer</button>
                <span id="profileSaved" class="text-sm text-secondary-container font-medium hidden">Enregistre.</span>
              </div>
            </form>
          </div>
        </div>

        <!-- Informations professionnelles -->
        <div class="card-glow rounded-2xl overflow-hidden">
          <div class="px-8 py-5 border-b border-outline-variant/10">
            <h2 class="text-xl font-bold font-serif text-primary">Informations professionnelles</h2>
            <p class="text-sm text-on-surface-variant mt-0.5">Complétez votre profil pour améliorer votre matching avec les offres.</p>
          </div>
          <div class="p-8">
            <form class="space-y-5" method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data">
              @csrf
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-semibold text-primary mb-1.5" for="prenom">Prénom</label>
                  <input type="text" id="prenom" name="prenom" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" value="{{ old('prenom', $user->prenom) }}" />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-primary mb-1.5" for="telephone">Téléphone</label>
                  <input type="text" id="telephone" name="telephone" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" value="{{ old('telephone', $user->telephone) }}" />
                </div>
                <div class="md:col-span-2">
                  <label class="block text-sm font-semibold text-primary mb-1.5" for="adresse">Adresse</label>
                  <input type="text" id="adresse" name="adresse" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" value="{{ old('adresse', $user->adresse) }}" />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-primary mb-1.5" for="salary_expectation_min">Salaire minimum souhaité</label>
                  <input type="number" id="salary_expectation_min" name="salary_expectation_min" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" value="{{ old('salary_expectation_min', $user->salary_expectation_min) }}" min="0" step="1000" />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-primary mb-1.5" for="experience_years">Années d'expérience</label>
                  <input type="number" id="experience_years" name="experience_years" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" value="{{ old('experience_years', $user->experience_years) }}" min="0" max="50" />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-primary mb-1.5" for="cv_file">CV (PDF, DOC)</label>
                  <input type="file" id="cv_file" name="cv" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" accept=".pdf,.doc,.docx" />
                  @if ($user->cv)
                    <p class="text-xs text-outline mt-1">CV actuel : {{ basename($user->cv) }}</p>
                  @endif
                </div>
              </div>
              <div class="flex items-center gap-4 pt-2">
                <button type="submit" class="px-6 py-3 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-all">Enregistrer</button>
                <span id="proSaved" class="text-sm text-secondary-container font-medium hidden">Profil professionnel mis à jour.</span>
              </div>
            </form>
          </div>
        </div>

        <!-- Mot de passe -->
        <div class="card-glow rounded-2xl overflow-hidden">
          <div class="px-8 py-5 border-b border-outline-variant/10">
            <h2 class="text-xl font-bold font-serif text-primary">Mettre a jour le mot de passe</h2>
            <p class="text-sm text-on-surface-variant mt-0.5">Assurez-vous que votre compte utilise un mot de passe long et aleatoire pour rester securise.</p>
          </div>
          <div class="p-8">
            <form class="space-y-5" method="POST" action="{{ route('profile.change-password') }}">
              @csrf
              <div>
                <label class="block text-sm font-semibold text-primary mb-1.5" for="current_password">Mot de passe actuel</label>
                <input type="password" id="current_password" name="current_password" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" autocomplete="current-password" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-primary mb-1.5" for="new_password">Nouveau mot de passe</label>
                <input type="password" id="new_password" name="new_password" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" autocomplete="new-password" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-primary mb-1.5" for="password_confirmation">Confirmer le mot de passe</label>
                <input type="password" id="password_confirmation" name="new_password_confirmation" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" autocomplete="new-password" />
              </div>
              <div class="flex items-center gap-4 pt-2">
                <button type="submit" class="px-6 py-3 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-all">Mettre a jour</button>
                <span id="passwordSaved" class="text-sm text-secondary-container font-medium hidden">Mot de passe mis a jour.</span>
              </div>
            </form>
          </div>
        </div>

        <!-- Supprimer le compte -->
        <div class="card-glow border border-secondary-container/20 rounded-2xl overflow-hidden">
          <div class="px-8 py-5 border-b border-secondary-container/10">
            <h2 class="text-xl font-bold font-serif text-secondary-container">Supprimer le compte</h2>
            <p class="text-sm text-on-surface-variant mt-0.5">Une fois votre compte supprime, toutes ses ressources et donnees seront definitivement effacees. Avant de supprimer votre compte, veuillez telecharger toutes les donnees ou informations que vous souhaitez conserver.</p>
          </div>
          <div class="p-8">
            <form method="POST" action="{{ route('profile.destroy') }}" class="flex items-end gap-4">
              @csrf
              @method('DELETE')
              <div class="flex-1">
                <label class="block text-sm font-semibold text-primary mb-1.5" for="delete_password">Mot de passe actuel</label>
                <input type="password" id="delete_password" name="password" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" required />
              </div>
              <button id="deleteAccountBtn" type="submit" class="px-6 py-3 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">warning</span> Supprimer le compte
              </button>
            </form>
          </div>
        </div>

      </div>
    </section>

  </main>
@endsection
