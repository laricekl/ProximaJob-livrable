@extends('layouts.admin')
@section('title', 'Newsletter')
@section('page-title', 'Newsletter')
@section('content')
<x-admin.flash />

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

  {{-- Formulaire nouvelle campagne --}}
  <x-admin.panel class="lg:col-span-2" title="Nouvelle campagne" body-class="space-y-5">
    <form method="POST" action="{{ route('admin.newsletters.store') }}">
      @csrf
      <div class="space-y-5">
        <x-admin.form-field label="Sujet" name="sujet" :required="true">
          <input id="sujet" name="sujet" type="text" value="{{ old('sujet') }}" class="w-full rounded-xl border @error('sujet') border-error @else border-outline-variant/20 @enderror bg-white/70 px-4 py-3 text-sm transition-all focus:border-secondary-container/50 focus:ring-0" placeholder="Ex: Les offres de la semaine — {{ now()->format('d/m/Y') }}" />
          @error('sujet')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
        </x-admin.form-field>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <x-admin.form-field label="Audience" name="audience">
            <select id="audience" name="audience" class="w-full rounded-xl border border-outline-variant/20 bg-white/70 px-4 py-3 text-sm transition-all focus:border-secondary-container/50 focus:ring-0">
              <option value="tous">Tous les utilisateurs</option>
              <option value="candidats" @selected(old('audience') == 'candidats')>Candidats uniquement</option>
              <option value="entreprises" @selected(old('audience') == 'entreprises')>Entreprises uniquement</option>
              <option value="premium" @selected(old('audience') == 'premium')>Abonnés Premium</option>
            </select>
          </x-admin.form-field>
        </div>

        <x-admin.form-field label="Contenu" name="contenu" :required="true">
          <textarea id="contenu" name="contenu" rows="10" class="w-full resize-none rounded-xl border @error('contenu') border-error @else border-outline-variant/20 @enderror bg-white/70 px-4 py-3 text-sm transition-all focus:border-secondary-container/50 focus:ring-0" placeholder="Rédigez votre newsletter...">{{ old('contenu') }}</textarea>
          @error('contenu')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
        </x-admin.form-field>

        <div class="flex flex-wrap items-center gap-3">
          <button type="submit" name="action" value="envoyer" class="flex items-center gap-2 rounded-xl bg-secondary-container px-6 py-3 text-sm font-bold text-white transition-colors hover:bg-secondary">
            <span class="material-symbols-outlined text-lg">send</span> Envoyer
          </button>
          <button type="submit" name="action" value="brouillon" class="flex items-center gap-2 rounded-xl border border-outline-variant/30 px-6 py-3 text-sm font-semibold transition-colors hover:bg-surface-container-low">
            <span class="material-symbols-outlined text-lg">draft</span> Brouillon
          </button>
        </div>
      </div>
    </form>
  </x-admin.panel>

  {{-- Campagnes précédentes --}}
  <x-admin.panel title="Campagnes précédentes" padding="p-0">
    <div class="divide-y divide-outline-variant/5">
      @forelse ($campagnes as $campagne)
        <div class="p-4 transition-colors hover:bg-surface-container-low/30">
          <div class="flex items-center justify-between">
            <p class="text-sm font-semibold text-primary">{{ $campagne->sujet }}</p>
            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-2xs font-bold
              {{ $campagne->statut === 'envoyee' ? 'bg-success-light text-success-dark' : 'bg-warning-light text-warning-dark' }}">
              {{ $campagne->statut === 'envoyee' ? 'Envoyée' : 'Brouillon' }}
            </span>
          </div>
          <p class="mt-0.5 text-xs text-outline">
            @if ($campagne->statut === 'envoyee')
              Envoyée le {{ $campagne->envoyee_le->format('d/m/Y') }}
            @else
              Créée le {{ $campagne->created_at->format('d/m/Y') }}
            @endif
          </p>
          <div class="mt-2 flex items-center gap-3 text-xs text-outline">
            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">group</span> {{ $campagne->audience }}</span>
            @if ($campagne->statut === 'envoyee')
              <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">mail</span> {{ number_format($campagne->destinataires_count) }}</span>
            @endif
          </div>
        </div>
      @empty
        <div class="p-6 text-center text-sm text-outline">Aucune campagne pour le moment.</div>
      @endforelse
    </div>
  </x-admin.panel>

</div>
@endsection
