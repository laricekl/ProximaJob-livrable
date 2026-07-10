@extends('layouts.guest')
@section('title', 'Verification email')
@section('content')
  <main class="flex-grow pt-32 flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-2xl">
      <div class="card-glow rounded-[2rem] p-8 md:p-10">
        <div class="text-center">
          <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-[1.75rem] bg-secondary-container/12 text-secondary-container">
            <span class="material-symbols-outlined text-4xl">mark_email_unread</span>
          </div>
          <h1 class="text-3xl font-bold font-serif text-primary mb-3">Verifiez votre adresse e-mail</h1>
          <p class="mx-auto max-w-xl text-sm leading-relaxed text-on-surface-variant">
            Votre compte a bien ete cree. Il reste une derniere etape: ouvrir l'e-mail de verification puis cliquer sur le lien pour activer votre acces.
          </p>
        </div>

        @if (session('status') === 'verification-link-sent')
          <div class="mt-8 rounded-2xl border border-success-light bg-success-light px-5 py-4 text-sm font-semibold text-success-deep" role="status">
            Un nouveau lien de verification vient d'etre envoye a votre adresse e-mail.
          </div>
        @endif

        <div class="mt-8 grid gap-5 md:grid-cols-[1.4fr,0.9fr]">
          <section class="rounded-[1.75rem] border border-outline-variant/20 bg-white/70 p-6 backdrop-blur-sm">
            <h2 class="text-lg font-bold text-primary">Prochaines etapes</h2>
            <ol class="mt-4 space-y-3 text-sm text-on-surface-variant">
              <li class="flex gap-3">
                <span class="mt-0.5 flex h-6 w-6 flex-none items-center justify-center rounded-full bg-secondary-container text-xs font-bold text-white">1</span>
                <span>Consultez votre boite de reception et vos courriers indesirables.</span>
              </li>
              <li class="flex gap-3">
                <span class="mt-0.5 flex h-6 w-6 flex-none items-center justify-center rounded-full bg-secondary-container text-xs font-bold text-white">2</span>
                <span>Cliquez sur le lien de verification envoye par ProximaJob.</span>
              </li>
              <li class="flex gap-3">
                <span class="mt-0.5 flex h-6 w-6 flex-none items-center justify-center rounded-full bg-secondary-container text-xs font-bold text-white">3</span>
                <span>Revenez ensuite vous connecter pour acceder a votre espace.</span>
              </li>
            </ol>
          </section>

          <aside class="rounded-[1.75rem] border border-outline-variant/20 bg-surface-container-low/70 p-6">
            <h2 class="text-lg font-bold text-primary">Besoin d'un nouvel e-mail ?</h2>
            <p class="mt-3 text-sm leading-relaxed text-on-surface-variant">
              Si vous ne voyez toujours rien, nous pouvons renvoyer un lien de verification.
            </p>

            <form method="POST" action="{{ route('verification.send') }}" class="mt-6">
              @csrf
              <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-secondary-container px-5 py-3.5 text-sm font-bold text-white shadow-lg shadow-secondary-container/20 transition-all hover:bg-secondary">
                <span class="material-symbols-outlined text-lg">refresh</span>
                Renvoyer l'e-mail
              </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-3">
              @csrf
              <button type="submit" class="w-full rounded-xl border border-outline-variant/30 px-5 py-3 text-sm font-semibold text-outline transition-colors hover:border-outline hover:text-on-surface-variant">
                Se deconnecter
              </button>
            </form>
          </aside>
        </div>
      </div>
    </div>
  </main>
@endsection
