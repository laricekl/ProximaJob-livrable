@extends('layouts.guest')
@section('title', 'Vérification du courriel entreprise')
@section('content')
  <main class="flex-grow pt-32 flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-3xl">
      <div class="card-glow rounded-[2rem] p-8 md:p-10">
        <div class="text-center">
          <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-[1.75rem] bg-secondary-container/12 text-secondary-container">
            <span class="material-symbols-outlined text-4xl">outgoing_mail</span>
          </div>
          <h1 class="text-3xl font-bold font-serif text-primary mb-3">Vérifiez votre adresse courriel</h1>
          <p class="mx-auto max-w-2xl text-sm leading-relaxed text-on-surface-variant">
            Nous avons bien reçu votre demande d’inscription. Avant la validation de votre entreprise par notre équipe, confirmez votre adresse courriel.
          </p>
        </div>

        <div class="mt-8 rounded-[1.75rem] border border-secondary-container/15 bg-white/75 p-6 text-center backdrop-blur-sm">
          <p class="text-xs font-semibold uppercase tracking-[0.22em] text-outline">Adresse concernée</p>
          <p class="mt-3 text-lg font-bold text-primary sm:text-xl">{{ session('email') ?? 'votre adresse courriel' }}</p>
        </div>

        @if (session('message'))
          <div class="mt-6 rounded-2xl border border-success-light bg-success-light px-5 py-4 text-sm font-semibold text-success-deep" role="status">
            {{ session('message') }}
          </div>
        @endif

        @if (session('error'))
          <div class="mt-6 rounded-2xl border border-error-light bg-error-light px-5 py-4 text-sm font-semibold text-error-deep" role="alert">
            {{ session('error') }}
          </div>
        @endif

        <div class="mt-8 grid gap-5 lg:grid-cols-[1.3fr,0.85fr]">
          <section class="rounded-[1.75rem] border border-outline-variant/20 bg-white/70 p-6 backdrop-blur-sm">
            <h2 class="text-lg font-bold text-primary">Ce qui se passe ensuite</h2>
            <ul class="mt-4 space-y-3 text-sm text-on-surface-variant">
              <li class="flex gap-3">
                <span class="mt-0.5 flex h-6 w-6 flex-none items-center justify-center rounded-full bg-secondary-container text-xs font-bold text-white">1</span>
                <span>Ouvrez le courriel de vérification envoyé à votre adresse.</span>
              </li>
              <li class="flex gap-3">
                <span class="mt-0.5 flex h-6 w-6 flex-none items-center justify-center rounded-full bg-secondary-container text-xs font-bold text-white">2</span>
                <span>Cliquez sur le lien pour confirmer que cette adresse vous appartient.</span>
              </li>
              <li class="flex gap-3">
                <span class="mt-0.5 flex h-6 w-6 flex-none items-center justify-center rounded-full bg-secondary-container text-xs font-bold text-white">3</span>
                <span>Votre dossier entreprise sera ensuite transmis à notre équipe pour validation finale.</span>
              </li>
            </ul>

            <div class="mt-6 rounded-2xl bg-surface-container-low/70 p-5">
              <p class="text-sm font-semibold text-primary">Conseils rapides</p>
              <ul class="mt-3 space-y-2 text-sm text-on-surface-variant">
                <li>Vérifiez aussi vos dossiers de pourriels et de courrier indésirable.</li>
                <li>Le lien de vérification expire après 24 heures.</li>
                <li>Une fois l’adresse vérifiée, vous recevrez la décision d’activation de votre compte.</li>
              </ul>
            </div>
          </section>

          <aside class="rounded-[1.75rem] border border-outline-variant/20 bg-surface-container-low/70 p-6">
            <h2 class="text-lg font-bold text-primary">Vous n’avez rien reçu ?</h2>
            <p class="mt-3 text-sm leading-relaxed text-on-surface-variant">
              Nous pouvons renvoyer le lien à cette même adresse si nécessaire.
            </p>

            <form method="POST" action="{{ route('enterprise.verification.resend') }}" class="mt-6 space-y-3">
              @csrf
              <input type="hidden" name="email" value="{{ session('email') }}">
              <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-secondary-container px-5 py-3.5 text-sm font-bold text-white shadow-lg shadow-secondary-container/20 transition-all hover:bg-secondary">
                <span class="material-symbols-outlined text-lg">refresh</span>
                Renvoyer le courriel
              </button>
              <a href="{{ route('login') }}" class="block w-full rounded-xl border border-outline-variant/30 px-5 py-3 text-center text-sm font-semibold text-outline transition-colors hover:border-outline hover:text-on-surface-variant">
                Retour à la connexion
              </a>
            </form>
          </aside>
        </div>
      </div>
    </div>
  </main>
@endsection
