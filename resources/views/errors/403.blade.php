@extends('layouts.guest')

@section('title', 'Accès refusé')

@section('content')
  <main class="min-h-[70vh] px-4 py-16 flex items-center justify-center">
    <section class="card-glow w-full max-w-xl rounded-3xl p-8 text-center md:p-12" aria-labelledby="error-title">
      <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-error-light text-error-dark">
        <span class="material-symbols-outlined text-3xl" aria-hidden="true">lock</span>
      </div>
      <p class="text-xs font-black uppercase tracking-[0.2em] text-secondary-container">Erreur 403</p>
      <h1 id="error-title" class="mt-3 text-3xl font-bold font-serif text-primary">Accès refusé</h1>
      <p class="mx-auto mt-4 max-w-md text-sm leading-relaxed text-on-surface-variant">
        Votre compte n’a pas l’autorisation nécessaire pour consulter cette page.
      </p>
      <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
        <a href="{{ url()->previous() }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-outline-variant/30 px-5 py-2.5 text-sm font-bold text-primary hover:bg-surface-container-low">
          <span class="material-symbols-outlined text-lg" aria-hidden="true">arrow_back</span>
          Retour
        </a>
        <a href="{{ route('dashboard') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-secondary-container px-5 py-2.5 text-sm font-bold text-white hover:bg-secondary">
          <span class="material-symbols-outlined text-lg" aria-hidden="true">dashboard</span>
          Mon tableau de bord
        </a>
      </div>
    </section>
  </main>
@endsection
