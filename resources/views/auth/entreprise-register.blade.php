@extends('layouts.guest')
@section('title', 'Inscription Entreprise')
@section('content')
  <main class="flex-grow pt-32 flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-lg">

      <div class="card-glow rounded-[2rem] p-8 md:p-10">

        <div class="text-center mb-8">
          <h1 class="text-3xl font-bold font-serif text-primary mb-2">Créer un compte Entreprise</h1>
          <p class="text-on-surface-variant text-sm">Remplissez les informations ci-dessous</p>
        </div>

        <!-- Switch Rôle -->
        <div class="flex bg-surface-container rounded-xl p-1.5 mb-8">
          <a href="{{ route('register') }}" class="flex-1 py-3 px-4 rounded-lg text-sm font-semibold transition-all duration-300 text-on-surface-variant hover:text-primary text-center">Chercheur d'emploi</a>
          <span class="flex-1 py-3 px-4 rounded-lg text-sm font-semibold transition-all duration-300 bg-white text-primary shadow-sm text-center">Entreprise</span>
        </div>

        <form class="space-y-5" action="{{ route('register.entreprise') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <!-- Section: Info responsable -->
          <div class="pb-4 border-b border-outline-variant/20">
            <h3 class="font-bold text-primary text-sm flex items-center gap-2 mb-5">
              <span class="material-symbols-outlined text-secondary-container text-lg">person</span>
              Informations du responsable
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="name" class="block text-sm font-semibold text-primary mb-2">Nom *</label>
                <input type="text" id="name" name="name" placeholder="Nom" required
                  class="w-full px-4 py-3.5 bg-white/80 backdrop-blur-sm border border-outline-variant/50 rounded-xl text-sm text-primary placeholder:text-outline" />
              </div>
              <div>
                <label for="prenom" class="block text-sm font-semibold text-primary mb-2">Prénoms *</label>
                <input type="text" id="prenom" name="prenom" placeholder="Prénoms" required
                  class="w-full px-4 py-3.5 bg-white/80 backdrop-blur-sm border border-outline-variant/50 rounded-xl text-sm text-primary placeholder:text-outline" />
              </div>
            </div>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="email" class="block text-sm font-semibold text-primary mb-2">Adresse e-mail *</label>
                <input type="email" id="email" name="email" placeholder="votre@email.com" required
                  class="w-full px-4 py-3.5 bg-white/80 backdrop-blur-sm border border-outline-variant/50 rounded-xl text-sm text-primary placeholder:text-outline" />
              </div>
              <div>
                <label for="telephone" class="block text-sm font-semibold text-primary mb-2">Téléphone *</label>
                <input type="tel" id="telephone" name="telephone" placeholder="+1 XX XX XX XX"
                  class="w-full px-4 py-3.5 bg-white/80 backdrop-blur-sm border border-outline-variant/50 rounded-xl text-sm text-primary placeholder:text-outline" />
              </div>
            </div>
          </div>

          <!-- Section: Info entreprise -->
          <div class="pb-4 border-b border-outline-variant/20">
            <h3 class="font-bold text-primary text-sm flex items-center gap-2 mb-5">
              <span class="material-symbols-outlined text-secondary-container text-lg">business</span>
              Informations de l'entreprise
            </h3>

            <div>
              <label for="company_name" class="block text-sm font-semibold text-primary mb-2">Nom de l'entreprise *</label>
              <input type="text" id="company_name" name="company_name" placeholder="Nom de votre entreprise" required
                class="w-full px-4 py-3.5 bg-white/80 backdrop-blur-sm border border-outline-variant/50 rounded-xl text-sm text-primary placeholder:text-outline" />
            </div>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="rccm" class="block text-sm font-semibold text-primary mb-2">RCCM</label>
                <input type="text" id="rccm" name="rccm" placeholder="N° RCCM (optionnel)"
                  class="w-full px-4 py-3.5 bg-white/80 backdrop-blur-sm border border-outline-variant/50 rounded-xl text-sm text-primary placeholder:text-outline" />
              </div>
              <div>
                <label for="neq" class="block text-sm font-semibold text-primary mb-2">NEQ *</label>
                <input type="text" id="neq" name="neq" placeholder="N° NEQ" required
                  class="w-full px-4 py-3.5 bg-white/80 backdrop-blur-sm border border-outline-variant/50 rounded-xl text-sm text-primary placeholder:text-outline" />
              </div>
            </div>

            <div class="mt-4">
              <label for="adresse" class="block text-sm font-semibold text-primary mb-2">Adresse *</label>
              <input type="text" id="adresse" name="adresse" placeholder="Adresse de l'entreprise"
                class="w-full px-4 py-3.5 bg-white/80 backdrop-blur-sm border border-outline-variant/50 rounded-xl text-sm text-primary placeholder:text-outline" />
            </div>

            <div class="mt-4">
              <label for="rccm_document" class="block text-sm font-semibold text-primary mb-2">Document RCCM</label>
              <div class="relative border-2 border-dashed border-outline-variant/50 rounded-xl p-6 text-center bg-white/50 hover:border-secondary-container/30 transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-3xl text-outline mb-2">upload_file</span>
                <p class="text-sm text-outline font-medium">Cliquez pour telecharger le document si vous le souhaitez</p>
                <p class="text-xs text-outline mt-1">PDF, PNG ou JPG (max 5 Mo)</p>
                <input type="file" id="rccm_document" name="rccm_document" accept=".pdf,.png,.jpg,.jpeg" class="absolute inset-0 opacity-0 cursor-pointer" />
              </div>
            </div>
          </div>

          <!-- Section: Sécurité -->
          <div>
            <h3 class="font-bold text-primary text-sm flex items-center gap-2 mb-5">
              <span class="material-symbols-outlined text-secondary-container text-lg">lock</span>
              Sécurité
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="password" class="block text-sm font-semibold text-primary mb-2">Mot de passe *</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required
                  class="w-full px-4 py-3.5 bg-white/80 backdrop-blur-sm border border-outline-variant/50 rounded-xl text-sm text-primary placeholder:text-outline" />
              </div>
              <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-primary mb-2">Confirmer *</label>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required
                  class="w-full px-4 py-3.5 bg-white/80 backdrop-blur-sm border border-outline-variant/50 rounded-xl text-sm text-primary placeholder:text-outline" />
              </div>
            </div>
          </div>

          <!-- Conditions -->
          <div class="flex items-start gap-3 p-4 bg-surface-container-low/50 rounded-xl">
            <input type="checkbox" id="terms" name="terms" required class="mt-0.5 rounded border-outline-variant/50 text-secondary-container focus:ring-secondary-container/30" />
            <label for="terms" class="text-xs text-on-surface-variant leading-relaxed">
              J'accepte les <a href="{{ route('terms') }}" class="text-secondary-container font-semibold hover:underline">conditions d'utilisation</a> et la <a href="{{ route('policy') }}" class="text-secondary-container font-semibold hover:underline">politique de confidentialité</a>
            </label>
          </div>

          <button type="submit" class="w-full py-3.5 bg-secondary-container text-white font-bold rounded-xl hover:bg-secondary transition-all shadow-lg shadow-secondary-container/20">
            Créer mon compte entreprise
          </button>
        </form>

        <p class="text-center text-sm text-on-surface-variant mt-8">
          Vous avez déjà un compte ? <a href="{{ route('login') }}" class="text-secondary-container font-bold hover:underline">Se connecter</a>
        </p>

      </div>
    </div>
  </main>
@endsection
