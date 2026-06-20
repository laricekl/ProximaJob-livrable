@extends('layouts.guest')
@section('title', 'Renvoyer l\'email de verification')
@section('content')
  <main class="flex-grow pt-32 flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-2xl">
      <div class="card-glow rounded-[2rem] p-8 md:p-10">
        <div class="text-center">
          <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-[1.75rem] bg-secondary-container/12 text-secondary-container">
            <span class="material-symbols-outlined text-4xl">mail</span>
          </div>
          <h1 class="text-3xl font-bold font-serif text-primary mb-3">Renvoyer un lien de verification</h1>
          <p class="mx-auto max-w-xl text-sm leading-relaxed text-on-surface-variant">
            Saisissez l'adresse email de votre compte non verifie. Nous vous renverrons un nouveau lien si le compte existe encore en attente de confirmation.
          </p>
        </div>

        @if (session('success'))
          <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-800" role="status">
            {{ session('success') }}
          </div>
        @endif

        @if (session('error'))
          <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-semibold text-red-800" role="alert">
            {{ session('error') }}
          </div>
        @endif

        <form method="POST" action="{{ route('verification.custom.resend') }}" class="mt-8 space-y-5">
          @csrf
          <div>
            <label for="email" class="mb-2 block text-sm font-semibold text-primary">Adresse e-mail</label>
            <input
              id="email"
              name="email"
              type="email"
              value="{{ old('email', session('email')) }}"
              required
              class="w-full rounded-xl border border-outline-variant/30 bg-white px-4 py-3 text-sm text-primary outline-none transition-colors focus:border-secondary-container/50"
              placeholder="vous@entreprise.com"
            />
            @error('email')
              <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
            @enderror
          </div>

          <div class="flex flex-col gap-3 sm:flex-row">
            <button type="submit" class="flex-1 rounded-xl bg-secondary-container px-5 py-3.5 text-sm font-bold text-white shadow-lg shadow-secondary-container/20 transition-all hover:bg-secondary">
              Renvoyer l'e-mail
            </button>
            <a href="{{ route('login') }}" class="flex-1 rounded-xl border border-outline-variant/30 px-5 py-3 text-center text-sm font-semibold text-outline transition-colors hover:border-outline hover:text-on-surface-variant">
              Retour a la connexion
            </a>
          </div>
        </form>
      </div>
    </div>
  </main>
@endsection
