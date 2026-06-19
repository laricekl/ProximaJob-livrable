@extends('layouts.admin')
@section('title', 'Newsletter')
@section('page-title', 'Newsletter')
@section('content')
  @php
    $campaigns = [
      ['title' => 'Les offres de la semaine', 'date' => '09/06/2026', 'sent' => '3,245', 'open_rate' => '68%'],
      ['title' => 'Nouveautés IA — Juin 2026', 'date' => '02/06/2026', 'sent' => '3,180', 'open_rate' => '72%'],
      ['title' => 'Conseils carrière — Mai 2026', 'date' => '26/05/2026', 'sent' => '2,980', 'open_rate' => '65%'],
    ];
  @endphp

  <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <x-admin.panel class="lg:col-span-2" title="Nouvelle campagne" body-class="space-y-5">
      <x-admin.form-field label="Sujet" name="newsletter-subject" :required="true">
        <input id="newsletter-subject" type="text" class="w-full rounded-xl border border-outline-variant/20 bg-white/70 px-4 py-3 text-sm transition-all focus:border-secondary-container/50 focus:ring-0" placeholder="Ex: Les offres de la semaine — 16 juin 2026" />
      </x-admin.form-field>

      <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <x-admin.form-field label="Destinataires" name="newsletter-audience">
          <select id="newsletter-audience" class="w-full rounded-xl border border-outline-variant/20 bg-white/70 px-4 py-3 text-sm transition-all focus:border-secondary-container/50 focus:ring-0">
            <option>Tous les utilisateurs</option>
            <option>Candidats uniquement</option>
            <option>Entreprises uniquement</option>
            <option>Abonnés Premium</option>
          </select>
        </x-admin.form-field>

        <x-admin.form-field label="Programmer" name="newsletter-schedule">
          <input id="newsletter-schedule" type="datetime-local" class="w-full rounded-xl border border-outline-variant/20 bg-white/70 px-4 py-3 text-sm transition-all focus:border-secondary-container/50 focus:ring-0" />
        </x-admin.form-field>
      </div>

      <x-admin.form-field label="Contenu" name="newsletter-content" :required="true">
        <textarea id="newsletter-content" rows="10" class="w-full resize-none rounded-xl border border-outline-variant/20 bg-white/70 px-4 py-3 text-sm transition-all focus:border-secondary-container/50 focus:ring-0" placeholder="Rédigez votre newsletter..."></textarea>
      </x-admin.form-field>

      <div class="flex flex-wrap items-center gap-3">
        <button class="flex items-center gap-2 rounded-xl bg-secondary-container px-6 py-3 text-sm font-bold text-white transition-colors hover:bg-secondary">
          <span class="material-symbols-outlined text-lg">send</span> Envoyer
        </button>
        <button class="flex items-center gap-2 rounded-xl border border-outline-variant/30 px-6 py-3 text-sm font-semibold transition-colors hover:bg-surface-container-low">
          <span class="material-symbols-outlined text-lg">draft</span> Brouillon
        </button>
        <button class="flex items-center gap-2 px-2 py-3 text-sm font-semibold text-secondary-container transition-colors hover:underline">
          <span class="material-symbols-outlined text-lg">visibility</span> Prévisualiser
        </button>
      </div>
    </x-admin.panel>

    <x-admin.panel title="Campagnes précédentes" padding="p-0">
      <div class="divide-y divide-outline-variant/5">
        @foreach ($campaigns as $campaign)
          <div class="cursor-pointer p-4 transition-colors hover:bg-surface-container-low/30">
            <p class="text-sm font-semibold text-primary">{{ $campaign['title'] }}</p>
            <p class="mt-0.5 text-xs text-outline">Envoyé le {{ $campaign['date'] }}</p>
            <div class="mt-2 flex items-center gap-3 text-xs text-outline">
              <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">mail</span> {{ $campaign['sent'] }}</span>
              <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">visibility</span> {{ $campaign['open_rate'] }}</span>
            </div>
          </div>
        @endforeach
      </div>
    </x-admin.panel>
  </div>
@endsection
