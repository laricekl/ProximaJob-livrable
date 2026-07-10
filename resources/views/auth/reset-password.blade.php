@extends('layouts.guest')
@section('title', 'Réinitialiser')
@section('content')
  <main class="flex-grow pt-32 flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-sm">

      <div class="card-glow rounded-[2rem] p-8 md:p-10">

        <div class="text-center mb-8">
          <div class="w-14 h-14 rounded-2xl bg-tertiary-fixed flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-2xl text-on-tertiary-fixed">password</span>
          </div>
          <h1 class="text-2xl font-bold font-serif text-primary mb-2">Nouveau mot de passe</h1>
          <p class="text-on-surface-variant text-sm">Choisissez un nouveau mot de passe sécurisé.</p>
        </div>

        <form class="space-y-5" action="{{ route('password.store') }}" method="POST">
          @csrf
          <input type="hidden" name="token" value="{{ request()->route('token') }}">
          <div>
            <label for="email" class="block text-sm font-semibold text-primary mb-2">Adresse e-mail</label>
            <input type="email" id="email" name="email" placeholder="votre@email.com" required
              class="w-full px-4 py-3.5 bg-white/80 backdrop-blur-sm border border-outline-variant/50 rounded-xl text-sm text-primary placeholder:text-outline" />
          </div>

          <div>
            <label for="password" class="block text-sm font-semibold text-primary mb-2">Nouveau mot de passe</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required
              class="w-full px-4 py-3.5 bg-white/80 backdrop-blur-sm border border-outline-variant/50 rounded-xl text-sm text-primary placeholder:text-outline" />
          </div>

          <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-primary mb-2">Confirmer le mot de passe</label>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required
              class="w-full px-4 py-3.5 bg-white/80 backdrop-blur-sm border border-outline-variant/50 rounded-xl text-sm text-primary placeholder:text-outline" />
          </div>

          <button type="submit" class="w-full py-3.5 bg-secondary-container text-white font-bold rounded-xl hover:bg-secondary transition-all shadow-lg shadow-secondary-container/20 flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            Réinitialiser le mot de passe
          </button>
        </form>

        <p class="text-center text-sm text-on-surface-variant mt-8">
          <a href="{{ route('login') }}" class="text-secondary-container font-bold hover:underline">Retour à la connexion</a>
        </p>

      </div>
    </div>
  </main>
@endsection
