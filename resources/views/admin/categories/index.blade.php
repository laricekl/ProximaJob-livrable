@extends('layouts.admin')
@section('title', __('Catégories'))
@section('page-title', __('Catégories'))

@section('content')
<x-admin.flash />
<div class="space-y-6">

  {{-- Messages flash --}}
  {{-- Barre de recherche + bouton création --}}
  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <form method="GET" action="{{ route('admin.categories.index') }}" class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
      <div class="relative flex-1 sm:max-w-sm">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">search</span>
        <input
          type="text"
          name="search"
          value="{{ $search }}"
          class="w-full rounded-xl border border-outline-variant/20 bg-white/70 py-2.5 pl-10 pr-4 text-sm transition-all focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30"
          placeholder="{{ __('Rechercher une catégorie...') }}"
        />
      </div>
      <button type="submit" onclick="this.disabled=true;this.innerHTML='<span class=\'material-symbols-outlined text-lg align-middle animate-spin\'>progress_activity</span> {{ __('Patientez...') }}'" class="rounded-xl bg-secondary-container px-4 py-2.5 text-sm font-bold text-white transition-colors hover:bg-secondary">{{ __('Filtrer') }}</button>
      @if ($search)
        <a href="{{ route('admin.categories.index') }}" class="rounded-xl border border-outline-variant/20 px-4 py-2.5 text-sm font-semibold transition-colors hover:bg-surface-container-low">{{ __('Réinitialiser') }}</a>
      @endif
    </form>
  </div>

  {{-- Formulaire de création rapide --}}
  <div class="card-glow rounded-2xl p-5">
    <h3 class="mb-4 font-bold font-serif text-primary text-sm">{{ __('Nouvelle catégorie') }}</h3>
    <form method="POST" action="{{ route('admin.categories.store') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
      @csrf
      <div class="flex-1">
        <label for="nom" class="block text-sm font-semibold text-primary mb-1.5">{{ __('Nom') }}</label>
        <input
          type="text"
          id="nom"
          name="nom"
          value="{{ old('nom') }}"
          class="w-full rounded-xl border @error('nom') border-error @else border-outline-variant/20 @enderror bg-white/70 py-2.5 px-4 text-sm transition-all focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30"
          placeholder="{{ __('Ex: Informatique, Marketing, Santé...') }}"
          required
        />
        @error('nom')
          <p class="mt-1 text-xs text-error">{{ $message }}</p>
        @enderror
      </div>
      <button type="submit" class="rounded-xl bg-primary text-white px-5 py-2.5 text-sm font-bold transition-colors hover:bg-secondary-container">
        <span class="material-symbols-outlined text-lg align-middle">add</span> {{ __('Créer') }}
      </button>
    </form>
  </div>

  {{-- Tableau des catégories --}}
  <div class="card-glow overflow-hidden rounded-2xl">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm">
        <thead>
          <tr class="bg-surface-container-low/50">
            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline">{{ __('Catégorie') }}</th>
            <th class="hidden px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline sm:table-cell">{{ __('Offres') }}</th>
            <th class="hidden px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline md:table-cell">{{ __('Créée le') }}</th>
            <th class="px-6 py-3 text-right text-xs font-bold uppercase tracking-wider text-outline">{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/5">
          @forelse ($categories as $categorie)
            <tr class="transition-colors hover:bg-surface-container-low/30" x-data="{ editing: false }">
              <td class="px-6 py-3">
                {{-- Mode lecture --}}
                <span x-show="!editing" class="font-semibold text-primary">{{ __($categorie->nom) }}</span>
                {{-- Mode édition --}}
                <form x-show="editing" method="POST" action="{{ route('admin.categories.update', $categorie) }}" class="flex items-center gap-2" x-cloak>
                  @csrf
                  @method('PUT')
                  <input
                    type="text"
                    name="nom"
                    value="{{ $categorie->nom }}"
                    class="w-48 rounded-xl border border-outline-variant/20 bg-white/70 py-1.5 px-3 text-sm focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30"
                    required
                  />
                  <button type="submit" class="text-success hover:text-success-deep" title="{{ __('Enregistrer') }}">
                    <span class="material-symbols-outlined text-lg">check</span>
                  </button>
                  <button type="button" @click="editing = false" class="text-outline hover:text-primary" title="{{ __('Annuler') }}">
                    <span class="material-symbols-outlined text-lg">close</span>
                  </button>
                </form>
              </td>
              <td class="hidden px-6 py-3 sm:table-cell">
                <x-admin.status-badge :label="(string) $categorie->offres_count" color="bg-secondary-container/10 text-secondary-container" />
              </td>
              <td class="hidden px-6 py-3 text-outline md:table-cell">{{ $categorie->created_at->format('d/m/Y') }}</td>
              <td class="px-6 py-3 text-right">
                <div class="flex items-center justify-end gap-1">
                  <button @click="editing = !editing" title="{{ __('Modifier') }}" aria-label="{{ __('Modifier') }}" class="rounded-xl p-2.5 text-outline hover:bg-surface-container-low hover:text-primary transition-colors" title="{{ __('Modifier') }}">
                    <span class="material-symbols-outlined text-lg">edit</span>
                  </button>
                  <form method="POST" action="{{ route('admin.categories.destroy', $categorie) }}" data-proxima-confirm="{{ __('Supprimer') }} « {{ $categorie->nom }} » ?" data-proxima-title="{{ __('Supprimer la catégorie') }}" data-proxima-confirm-label="{{ __('Supprimer') }}" data-proxima-tone="danger">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-xl p-2.5 text-outline hover:bg-error-light hover:text-error transition-colors" title="{{ __('Supprimer') }}">
                      <span class="material-symbols-outlined text-lg">delete</span>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="px-6 py-10 text-center text-sm text-outline">
                @if ($search)
                  {{ __('Aucune catégorie ne correspond à') }} « {{ $search }} ».
                @else
                  {{ __('Aucune catégorie pour le moment. Créez la première ci-dessus !') }}
                @endif
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="flex flex-col gap-4 border-t border-outline-variant/10 px-6 py-4 text-sm sm:flex-row sm:items-center sm:justify-between">
      <span class="text-outline">{{ $categories->firstItem() ?? 0 }}-{{ $categories->lastItem() ?? 0 }} {{ __('sur') }} {{ $categories->total() }} {{ __('catégories') }}</span>
      {{ $categories->withQueryString()->links('components.pagination.public-pagination') }}
    </div>
  </div>

</div>
@endsection
