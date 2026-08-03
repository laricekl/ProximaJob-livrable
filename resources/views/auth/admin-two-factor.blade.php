@extends('layouts.app')

@section('title', 'Vérification de sécurité')

@section('content')
<main class="mx-auto flex min-h-[70vh] max-w-lg items-center px-4 py-12">
  <section class="w-full rounded-3xl border border-primary/10 bg-white p-7 shadow-xl sm:p-9">
    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-secondary-container/15 text-secondary-container">
      <span class="material-symbols-outlined text-3xl">verified_user</span>
    </div>
    <h1 class="mt-5 font-serif text-3xl font-bold text-primary">Vérification de sécurité</h1>
    <p class="mt-2 text-sm leading-6 text-outline">Un code à six chiffres a été envoyé à l’adresse courriel du compte administrateur. Il reste valide pendant 10 minutes.</p>

    @if ($errors->any())
      <div class="mt-5 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.2fa.verify') }}" class="mt-6 space-y-5">
      @csrf
      <label class="block text-sm font-bold text-primary" for="code">Code de sécurité</label>
      <input id="code" name="code" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required autofocus class="w-full rounded-xl border-outline-variant/40 text-center text-2xl font-bold tracking-[.4em]" placeholder="000000">
      <button class="w-full rounded-xl bg-primary-container px-5 py-3 text-sm font-bold text-white">Vérifier et continuer</button>
    </form>

    <a href="{{ route('admin.login') }}" class="mt-5 block text-center text-sm font-semibold text-secondary-container">Recommencer la connexion</a>
  </section>
</main>
@endsection
