@extends('layouts.admin')
@section('title', __('Secteurs'))
@section('page-title', __('Secteurs'))
@section('content')
<x-admin.flash />
<div class="space-y-6">
  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <form method="GET" class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
      <div class="relative flex-1 sm:max-w-sm">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">search</span>
        <input type="text" name="search" value="{{ $search }}" class="w-full rounded-xl border border-outline-variant/20 bg-white/70 py-2.5 pl-10 pr-4 text-sm transition-all focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30" placeholder="{{ __('Rechercher par nom ou code SCIAN...') }}"/>
      </div>
      <button type="submit" onclick="this.disabled=true;this.innerHTML='<span class=\'material-symbols-outlined text-lg align-middle animate-spin\'>progress_activity</span> {{ __('Patientez...') }}'" class="rounded-xl bg-secondary-container px-4 py-2.5 text-sm font-bold text-white transition-colors hover:bg-secondary">{{ __('Filtrer') }}</button>
      @if ($search)<a href="{{ route('admin.secteurs.index') }}" class="rounded-xl border border-outline-variant/20 px-4 py-2.5 text-sm font-semibold transition-colors hover:bg-surface-container-low">{{ __('Réinitialiser') }}</a>@endif
    </form>
  </div>

  <div class="card-glow rounded-2xl p-5">
    <h3 class="mb-4 font-bold font-serif text-primary text-sm">{{ __('Nouveau secteur') }}</h3>
    <form method="POST" action="{{ route('admin.secteurs.store') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-3">
      @csrf
      <div>
        <label for="name" class="block text-sm font-semibold text-primary mb-1.5">{{ __('Nom') }}</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" class="w-full rounded-xl border @error('name') border-error @else border-outline-variant/20 @enderror bg-white/70 py-2.5 px-4 text-sm focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30" placeholder="{{ __("Ex: Technologies de l'information") }}" required/>
        @error('name')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
      </div>
      <div>
        <label for="scian_code" class="block text-sm font-semibold text-primary mb-1.5">{{ __('Code SCIAN') }}</label>
        <input type="text" id="scian_code" name="scian_code" value="{{ old('scian_code') }}" class="w-full rounded-xl border border-outline-variant/20 bg-white/70 py-2.5 px-4 text-sm focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30" placeholder="Ex: 5415"/>
      </div>
      <div>
        <label for="parent_id" class="block text-sm font-semibold text-primary mb-1.5">{{ __('Parent') }}</label>
        <select id="parent_id" name="parent_id" class="w-full rounded-xl border border-outline-variant/20 bg-white/70 py-2.5 px-4 text-sm focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30">
          <option value="">{{ __('— Aucun (racine) —') }}</option>
          @foreach ($parents as $parent)
            <option value="{{ $parent->id }}" @selected(old('parent_id') == $parent->id)>{{ __($parent->name) }}</option>
          @endforeach
        </select>
      </div>
      <div class="sm:col-span-3 flex justify-end">
        <button type="submit" onclick="this.disabled=true;this.innerHTML='<span class=\'material-symbols-outlined text-lg align-middle animate-spin\'>progress_activity</span> {{ __('Patientez...') }}'" class="rounded-xl bg-secondary-container text-white px-4 py-2.5 text-sm font-bold hover:bg-secondary transition-colors"><span class="material-symbols-outlined text-lg align-middle">add</span> {{ __('Créer') }}</button>
      </div>
    </form>
  </div>

  <div class="card-glow overflow-hidden rounded-2xl">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm">
        <thead><tr class="bg-surface-container-low/50">
          <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline">{{ __('Secteur') }}</th>
          <th class="hidden px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline sm:table-cell">{{ __('Code SCIAN') }}</th>
          <th class="hidden px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline md:table-cell">{{ __('Offres') }}</th>
          <th class="px-6 py-3 text-right text-xs font-bold uppercase tracking-wider text-outline">{{ __('Actions') }}</th>
        </tr></thead>
        <tbody class="divide-y divide-outline-variant/5">
          @forelse ($secteurs as $secteur)
            <tr class="transition-colors hover:bg-surface-container-low/30" x-data="{ editing: false }">
              <td class="px-6 py-3">
                <span x-show="!editing" class="font-semibold text-primary">{{ __($secteur->name) }}</span>
                <form x-show="editing" method="POST" action="{{ route('admin.secteurs.update', $secteur) }}" class="flex flex-wrap items-center gap-2" x-cloak>
                  @csrf @method('PUT')
                  <input type="text" name="name" value="{{ $secteur->name }}" class="w-40 rounded-xl border border-outline-variant/20 bg-white/70 py-1.5 px-3 text-sm focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30" required/>
                  <input type="text" name="scian_code" value="{{ $secteur->scian_code }}" class="w-20 rounded-xl border border-outline-variant/20 bg-white/70 py-1.5 px-3 text-sm focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30" placeholder="{{ __('Code') }}"/>
                  <select name="parent_id" class="w-36 rounded-xl border border-outline-variant/20 bg-white/70 py-1.5 px-2 text-sm focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30">
                    <option value="">{{ __('— Racine —') }}</option>
                    @foreach ($parents as $parent)
                      <option value="{{ $parent->id }}" @selected($secteur->parent_id == $parent->id)>{{ __($parent->name) }}</option>
                    @endforeach
                  </select>
                  <button type="submit" aria-label="{{ __('Enregistrer le secteur') }}" class="text-success hover:text-success-deep"><span class="material-symbols-outlined text-lg">check</span></button>
                  <button type="button" @click="editing = false" class="text-outline hover:text-primary"><span class="material-symbols-outlined text-lg">close</span></button>
                </form>
              </td>
              <td class="hidden px-6 py-3 sm:table-cell"><code class="text-xs bg-surface-container-low px-2 py-0.5 rounded">{{ $secteur->scian_code ?: '—' }}</code></td>
              <td class="hidden px-6 py-3 md:table-cell"><x-admin.status-badge :label="(string) $secteur->offres_count" color="bg-secondary-container/10 text-secondary-container" /></td>
              <td class="px-6 py-3 text-right">
                <div class="flex items-center justify-end gap-1">
                  <button @click="editing = !editing" title="{{ __('Modifier') }}" aria-label="{{ __('Modifier') }}" class="rounded-xl p-2.5 text-outline hover:bg-surface-container-low hover:text-primary transition-colors"><span class="material-symbols-outlined text-lg">edit</span></button>
                  <form method="POST" action="{{ route('admin.secteurs.destroy', $secteur) }}" data-proxima-confirm="{{ __('Supprimer') }} « {{ $secteur->name }} » ?" data-proxima-title="{{ __('Supprimer le secteur') }}" data-proxima-confirm-label="{{ __('Supprimer') }}" data-proxima-tone="danger">
                    @csrf @method('DELETE')
                    <button aria-label="{{ __('Supprimer le secteur') }}" class="rounded-xl p-2.5 text-outline hover:bg-error-light hover:text-error transition-colors"><span class="material-symbols-outlined text-lg">delete</span></button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="px-6 py-10 text-center text-sm text-outline">@if($search) {{ __('Aucun secteur ne correspond à') }} « {{ $search }} ». @else {{ __('Aucun secteur pour le moment. Créez le premier ci-dessus !') }} @endif</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="flex flex-col gap-4 border-t border-outline-variant/10 px-6 py-4 text-sm sm:flex-row sm:items-center sm:justify-between">
      <span class="text-outline">{{ $secteurs->firstItem() ?? 0 }}-{{ $secteurs->lastItem() ?? 0 }} {{ __('sur') }} {{ $secteurs->total() }} {{ __('secteurs') }}</span>
      {{ $secteurs->withQueryString()->links('components.pagination.public-pagination') }}
    </div>
  </div>
</div>
@endsection
