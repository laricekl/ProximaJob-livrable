@extends('layouts.guest')
@section('title', 'Inscription')
@section('styles')
  <style>
    input[type="text"], input[type="email"], input[type="tel"], input[type="password"], input[type="file"] {
      transition: all 0.3s ease;
    }
    input:focus {
      border-color: var(--pj-accent);
      box-shadow: 0 0 0 3px rgba(var(--pj-accent-rgb),0.1);
      outline: none;
    }
  </style>
@endsection
@section('content')
  <main class="flex-grow pt-32 flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-lg">

      <!-- Carte d'inscription -->
      <div class="card-glow rounded-[2rem] p-8 md:p-10">

        <!-- En-tête -->
        <div class="text-center mb-8">
          <h1 class="text-3xl font-bold font-serif text-primary mb-2">Créer un compte</h1>
          <p class="text-on-surface-variant text-sm">Remplissez les informations ci-dessous</p>
        </div>

        <!-- Switch Rôle -->
        <div class="flex bg-surface-container rounded-xl p-1.5 mb-8">
          <button id="btn-candidat" class="flex-1 py-3 px-4 rounded-lg text-sm font-semibold transition-all duration-300 bg-white text-primary shadow-sm">Chercheur d'emploi</button>
          <a href="{{ route('entreprise.register') }}" class="flex-1 py-3 px-4 rounded-lg text-sm font-semibold transition-all duration-300 text-slate-500 hover:text-primary text-center">Entreprise</a>
        </div>

        @if (session('success'))
          <div class="mb-6 rounded-2xl border border-success-light bg-success-light px-4 py-3 text-sm font-semibold text-success-deep" role="status">
            {{ session('success') }}
          </div>
        @endif

        @if (session('error'))
          <div class="mb-6 rounded-2xl border border-error-light bg-error-light px-4 py-3 text-sm font-semibold text-error-deep" role="alert">
            {{ session('error') }}
          </div>
        @endif

        @if ($errors->any())
          <div class="mb-6 rounded-2xl border border-error-light bg-error-light px-4 py-3 text-sm text-error-deep" role="alert">
            <p class="font-semibold">Veuillez corriger les champs suivants :</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <!-- Formulaire -->
        <form class="space-y-5" action="{{ route('register') }}" method="POST">
          @csrf
          <!-- Nom + Prénoms -->
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

          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-semibold text-primary mb-2">Adresse e-mail *</label>
            <input type="email" id="email" name="email" placeholder="votre@email.com" required
              class="w-full px-4 py-3.5 bg-white/80 backdrop-blur-sm border border-outline-variant/50 rounded-xl text-sm text-primary placeholder:text-outline" />
          </div>

          <!-- Téléphone + Adresse -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label for="telephone" class="block text-sm font-semibold text-primary mb-2">Téléphone *</label>
              <input type="tel" id="telephone" name="telephone" placeholder="+1 XX XX XX XX"
                class="w-full px-4 py-3.5 bg-white/80 backdrop-blur-sm border border-outline-variant/50 rounded-xl text-sm text-primary placeholder:text-outline" />
            </div>
            <div>
              <label for="adresse" class="block text-sm font-semibold text-primary mb-2">Adresse *</label>
              <input type="text" id="adresse" name="adresse" placeholder="Votre adresse"
                class="w-full px-4 py-3.5 bg-white/80 backdrop-blur-sm border border-outline-variant/50 rounded-xl text-sm text-primary placeholder:text-outline" />
            </div>
          </div>

          <!-- Password + Confirmation -->
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

          <!-- Conditions -->
          <div class="flex items-start gap-3 p-4 bg-surface-container-low/50 rounded-xl">
            <input type="checkbox" id="terms" name="terms" required class="mt-0.5 rounded border-outline-variant/50 text-secondary-container focus:ring-secondary-container/30" />
            <label for="terms" class="text-xs text-on-surface-variant leading-relaxed">
              J'accepte les <a href="{{ route('terms') }}" class="text-secondary-container font-semibold hover:underline">conditions d'utilisation</a> et la <a href="{{ route('policy') }}" class="text-secondary-container font-semibold hover:underline">politique de confidentialité</a>
            </label>
          </div>

          <!-- Newsletter -->
          <div class="flex items-start gap-3 p-4 bg-surface-container-low/50 rounded-xl">
            <input type="checkbox" id="newsletter" name="newsletter" class="mt-0.5 rounded border-outline-variant/50 text-secondary-container focus:ring-secondary-container/30" />
            <label for="newsletter" class="text-xs text-on-surface-variant leading-relaxed">
              Je souhaite recevoir les actualités et offres par e-mail
            </label>
          </div>

          <!-- Submit -->
          <button type="submit" class="w-full py-3.5 bg-secondary-container text-white font-bold rounded-xl hover:bg-secondary transition-all shadow-lg shadow-secondary-container/20">
            Créer mon compte
          </button>
        </form>

        <!-- Séparateur -->
        <div class="relative my-7 text-center">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-outline-variant/30"></div>
          </div>
          <span class="relative bg-white/70 backdrop-blur-2xl px-4 text-xs font-semibold text-outline uppercase tracking-wider">ou</span>
        </div>

        @php
          $googleEnabled = filled(config('services.google.client_id')) && filled(config('services.google.client_secret')) && filled(config('services.google.redirect'));
          $facebookEnabled = filled(config('services.facebook.client_id')) && filled(config('services.facebook.client_secret')) && filled(config('services.facebook.redirect'));
        @endphp

        <!-- Boutons sociaux -->
        <div class="space-y-3">
          @if ($googleEnabled)
          <a href="{{ route('auth.social.redirect', 'google') }}" class="flex items-center justify-center gap-3 w-full py-3.5 bg-white border border-outline-variant/30 rounded-xl text-sm font-semibold text-primary hover:bg-surface-container-low transition-all duration-300 hover:shadow-md">
            <svg width="20" height="20" viewBox="0 0 24 24">
              <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
              <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
              <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
              <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Continuer avec Google
          </a>
          @else
          <div aria-disabled="true" class="flex items-center justify-center gap-3 w-full py-3.5 bg-surface-container-low border border-outline-variant/20 rounded-xl text-sm font-semibold text-on-surface-variant/60 cursor-not-allowed">
            <svg width="20" height="20" viewBox="0 0 24 24">
              <path fill="#9CA3AF" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
              <path fill="#9CA3AF" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
              <path fill="#9CA3AF" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
              <path fill="#9CA3AF" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Google bientot disponible
          </div>
          @endif

          @if ($facebookEnabled)
          <a href="{{ route('auth.social.redirect', 'facebook') }}" class="flex items-center justify-center gap-3 w-full py-3.5 bg-[#1877F2] border border-[#1877F2] rounded-xl text-sm font-semibold text-white hover:bg-[#166fe5] transition-all duration-300 hover:shadow-md">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
              <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
            Continuer avec Facebook
          </a>
          @else
          <div aria-disabled="true" class="flex items-center justify-center gap-3 w-full py-3.5 bg-surface-container-low border border-outline-variant/20 rounded-xl text-sm font-semibold text-on-surface-variant/60 cursor-not-allowed">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
              <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
            Facebook bientot disponible
          </div>
          @endif
        </div>

        <!-- Lien connexion -->
        <p class="text-center text-sm text-on-surface-variant mt-8">
          Vous avez déjà un compte ? <a href="{{ route('login') }}" class="text-secondary-container font-bold hover:underline">Se connecter</a>
        </p>

      </div>
    </div>
  </main>
@endsection
