@extends('layouts.guest')
@section('title', 'Reinitialiser le mot de passe')
@section('content')
  <main class="flex-grow pt-32 flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
      <div class="card-glow rounded-[2rem] p-8 md:p-10">
        <div class="text-center mb-8">
          <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-secondary-container/12 text-secondary-container">
            <span class="material-symbols-outlined text-2xl">password</span>
          </div>
          <h1 class="text-3xl font-bold font-serif text-primary mb-2">Choisissez un nouveau mot de passe</h1>
          <p class="text-sm leading-relaxed text-on-surface-variant">
            Saisissez votre nouveau mot de passe pour finaliser la reinitialisation de votre acces.
          </p>
        </div>

        @if ($errors->any())
          <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
            <p class="font-semibold">Reinitialisation impossible :</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
          @csrf
          <input type="hidden" name="token" value="{{ $token }}">

          <div>
            <label for="email" class="block text-sm font-semibold text-primary mb-2">Adresse e-mail</label>
            <input
              type="email"
              id="email"
              name="email"
              value="{{ $email ?? old('email') }}"
              required
              readonly
              class="w-full rounded-xl border border-outline-variant/50 bg-surface-container-low px-4 py-3.5 text-sm text-primary focus:outline-none"
            />
          </div>

          <div>
            <label for="password" class="block text-sm font-semibold text-primary mb-2">Nouveau mot de passe</label>
            <input
              type="password"
              id="password"
              name="password"
              placeholder="••••••••"
              required
              class="w-full rounded-xl border border-outline-variant/50 bg-white/80 px-4 py-3.5 text-sm text-primary placeholder:text-outline focus:outline-none"
            />
          </div>

          <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-primary mb-2">Confirmer le mot de passe</label>
            <input
              type="password"
              id="password_confirmation"
              name="password_confirmation"
              placeholder="••••••••"
              required
              class="w-full rounded-xl border border-outline-variant/50 bg-white/80 px-4 py-3.5 text-sm text-primary placeholder:text-outline focus:outline-none"
            />
          </div>

          <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-secondary-container px-5 py-3.5 text-sm font-bold text-white shadow-lg shadow-secondary-container/20 transition-all hover:bg-secondary">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            Reinitialiser le mot de passe
          </button>
        </form>

        <p class="mt-8 text-center text-sm text-on-surface-variant">
          <a href="{{ route('login') }}" class="font-bold text-secondary-container hover:underline">Retour a la connexion</a>
        </p>
      </div>
    </div>
  </main>
@endsection
