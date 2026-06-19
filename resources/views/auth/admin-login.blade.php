@extends('layouts.guest')
@section('title', 'Admin')
@section('content')
  <main class="flex-grow pt-32 flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
      <div class="card-glow rounded-[2rem] p-8 md:p-10">
        <div class="text-center mb-8">
          <div class="w-14 h-14 rounded-2xl bg-primary flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-2xl text-white" style="font-variation-settings:'FILL' 1">admin_panel_settings</span>
          </div>
          <h1 class="text-2xl font-bold font-serif text-primary mb-1">Administration</h1>
          <p class="text-on-surface-variant text-sm">Accès réservé au personnel autorisé</p>
        </div>

        <form class="space-y-5" action="{{ route('admin.login') }}" method="POST">
          @csrf
          <div>
            <label for="email" class="block text-sm font-semibold text-primary mb-2">Adresse e-mail</label>
            <input type="email" id="email" name="email" placeholder="admin@proximajob.com" required
              class="w-full px-4 py-3.5 bg-white/80 backdrop-blur-sm border border-outline-variant/50 rounded-xl text-sm text-primary placeholder:text-outline" />
          </div>

          <div>
            <label for="password" class="block text-sm font-semibold text-primary mb-2">Mot de passe</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required
              class="w-full px-4 py-3.5 bg-white/80 backdrop-blur-sm border border-outline-variant/50 rounded-xl text-sm text-primary placeholder:text-outline" />
          </div>

          <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" name="remember" class="rounded border-outline-variant/50 text-secondary-container focus:ring-secondary-container/30" />
              <span class="text-xs text-on-surface-variant font-medium">Se souvenir de moi</span>
            </label>
          </div>

          <button type="submit" class="w-full py-3.5 bg-primary text-white font-bold rounded-xl hover:bg-slate-800 transition-all shadow-xl flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-lg">login</span>
            Se connecter
          </button>
        </form>
      </div>

      <p class="text-center mt-6">
        <a href="{{ route('login') }}" class="text-xs text-outline hover:text-on-surface-variant transition-colors">Retour à la connexion utilisateur</a>
      </p>
    </div>
  </main>
@endsection
