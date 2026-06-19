@extends('layouts.guest')
@section('title', 'Mot de passe oublie')
@section('content')
  <main class="flex-grow pt-32 flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
      <div class="card-glow rounded-[2rem] p-8 md:p-10">
        <div class="text-center mb-8">
          <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-secondary-container/12 text-secondary-container">
            <span class="material-symbols-outlined text-2xl">lock_reset</span>
          </div>
          <h1 class="text-3xl font-bold font-serif text-primary mb-2">Mot de passe oublie ?</h1>
          <p class="text-sm leading-relaxed text-on-surface-variant">
            Indiquez votre adresse e-mail. Nous vous enverrons un lien pour choisir un nouveau mot de passe.
          </p>
        </div>

        @if (session('status'))
          <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800" role="status">
            {{ session('status') }}
          </div>
        @endif

        @if ($errors->any())
          <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
            <p class="font-semibold">Envoi impossible :</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
          @csrf
          <div>
            <label for="email" class="block text-sm font-semibold text-primary mb-2">Adresse e-mail</label>
            <input
              type="email"
              id="email"
              name="email"
              value="{{ old('email') }}"
              placeholder="votre@email.com"
              required
              autofocus
              class="w-full rounded-xl border border-outline-variant/50 bg-white/80 px-4 py-3.5 text-sm text-primary placeholder:text-outline focus:outline-none"
            />
          </div>

          <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-secondary-container px-5 py-3.5 text-sm font-bold text-white shadow-lg shadow-secondary-container/20 transition-all hover:bg-secondary">
            <span class="material-symbols-outlined text-lg">send</span>
            Envoyer le lien
          </button>
        </form>

        <p class="mt-8 text-center text-sm text-on-surface-variant">
          <a href="{{ route('login') }}" class="font-bold text-secondary-container hover:underline">Retour a la connexion</a>
        </p>
      </div>
    </div>
  </main>
@endsection
