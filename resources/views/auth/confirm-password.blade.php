@extends('layouts.guest')
@section('title', 'Confirmation')
@section('content')
  <main class="flex-grow pt-32 flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-sm">

      <div class="card-glow rounded-[2rem] p-8 md:p-10">

        <div class="text-center mb-8">
          <div class="w-14 h-14 rounded-2xl bg-secondary-fixed flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-2xl text-on-secondary-fixed-variant">shield_person</span>
          </div>
          <h1 class="text-2xl font-bold font-serif text-primary mb-2">Confirmez votre mot de passe</h1>
          <p class="text-on-surface-variant text-sm leading-relaxed">
            Il s'agit d'une zone sécurisée de l'application. Veuillez confirmer votre mot de passe avant de continuer.
          </p>
        </div>

        <form class="space-y-5" action="{{ route('login') }}" method="POST">
          <div>
            <label for="password" class="block text-sm font-semibold text-primary mb-2">Mot de passe</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required
              class="w-full px-4 py-3.5 bg-white/80 backdrop-blur-sm border border-outline-variant/50 rounded-xl text-sm text-primary placeholder:text-outline" />
          </div>

          <button type="submit" class="w-full py-3.5 bg-primary text-white font-bold rounded-xl hover:bg-slate-800 transition-all shadow-xl flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-lg">lock</span>
            Confirmer
          </button>
        </form>

      </div>
    </div>
  </main>
@csrf
@endsection
