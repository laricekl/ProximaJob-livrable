@extends('layouts.guest')
@section('title', 'Mot de passe oublié')
@section('content')
  <main class="flex-grow pt-32 flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-sm">

      <div class="card-glow rounded-[2rem] p-8 md:p-10">

        <div class="text-center mb-8">
          <div class="w-14 h-14 rounded-2xl bg-secondary-fixed flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-2xl text-on-secondary-fixed-variant">lock_reset</span>
          </div>
          <h1 class="text-2xl font-bold font-serif text-primary mb-2">Mot de passe oublié ?</h1>
          <p class="text-on-surface-variant text-sm leading-relaxed">
            Pas de problème. Indiquez votre adresse e-mail et nous vous enverrons un lien de réinitialisation.
          </p>
        </div>

        <form class="space-y-5" action="{{ route('password.email') }}" method="POST">
          @csrf
          <div>
            <label for="email" class="block text-sm font-semibold text-primary mb-2">Adresse e-mail</label>
            <input type="email" id="email" name="email" placeholder="votre@email.com" required
              class="w-full px-4 py-3.5 bg-white/80 backdrop-blur-sm border border-outline-variant/50 rounded-xl text-sm text-primary placeholder:text-outline" />
          </div>

          <button type="submit" class="w-full py-3.5 bg-secondary-container text-white font-bold rounded-xl hover:bg-secondary transition-all shadow-lg shadow-secondary-container/20 flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-lg">send</span>
            Envoyer le lien
          </button>
        </form>

        <p class="text-center text-sm text-on-surface-variant mt-8">
          <a href="{{ route('login') }}" class="text-secondary-container font-bold hover:underline">Retour à la connexion</a>
        </p>

      </div>
    </div>
  </main>
@endsection
