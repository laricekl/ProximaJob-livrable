@extends('layouts.guest')
@section('title', 'Vérification email')
@section('content')
  <main class="flex-grow pt-32 flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-sm">

      <div class="card-glow rounded-[2rem] p-8 md:p-10 text-center">

        <div class="w-16 h-16 rounded-2xl bg-tertiary-fixed flex items-center justify-center mx-auto mb-6">
          <span class="material-symbols-outlined text-3xl text-on-tertiary-fixed">mark_email_read</span>
        </div>

        <h1 class="text-2xl font-bold font-serif text-primary mb-3">Vérifiez votre e-mail</h1>

        <p class="text-on-surface-variant text-sm leading-relaxed mb-8">
          Un lien de vérification vous a été envoyé par e-mail. Cliquez sur ce lien pour activer votre compte.
        </p>

        <div class="bg-surface-container-low/50 rounded-xl p-4 mb-8">
          <p class="text-xs text-on-surface-variant">
            Si vous n'avez pas reçu l'e-mail, vérifiez vos courriers indésirables ou cliquez ci-dessous pour en recevoir un nouveau.
          </p>
        </div>

        <div class="space-y-3">
          <button type="button" class="w-full py-3.5 bg-secondary-container text-white font-bold rounded-xl hover:bg-secondary transition-all shadow-lg shadow-secondary-container/20 flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-lg">refresh</span>
            Renvoyer l'e-mail
          </button>

          <form class="inline">
            <button type="submit" class="text-sm text-outline hover:text-on-surface-variant transition-colors font-medium">
              Se déconnecter
            </button>
          </form>
        </div>

      </div>
    </div>
  </main>
@csrf
@endsection
