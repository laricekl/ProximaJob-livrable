@extends('layouts.admin')
@section('title', __('Compétences'))
@section('page-title', __('Compétences'))
@section('content')
<x-admin.flash />
<div class="space-y-6">
  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <form method="GET" class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
      <div class="relative flex-1 sm:max-w-sm">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">search</span>
        <input type="text" name="search" value="{{ $search }}" class="w-full rounded-xl border border-outline-variant/20 bg-white/70 py-2.5 pl-10 pr-4 text-sm transition-all focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30" placeholder="{{ __('Rechercher par nom ou catégorie...') }}"/>
      </div>
      <button type="submit" onclick="this.disabled=true;this.innerHTML='<span class=\'material-symbols-outlined text-lg align-middle animate-spin\'>progress_activity</span> {{ __('Patientez...') }}'" class="rounded-xl bg-secondary-container px-4 py-2.5 text-sm font-bold text-white transition-colors hover:bg-secondary">{{ __('Filtrer') }}</button>
      @if ($search)<a href="{{ route('admin.skills.index') }}" class="rounded-xl border border-outline-variant/20 px-4 py-2.5 text-sm font-semibold transition-colors hover:bg-surface-container-low">{{ __('Réinitialiser') }}</a>@endif
    </form>
  </div>

  <div class="card-glow rounded-2xl p-5">
    <h3 class="mb-4 font-bold font-serif text-primary text-sm">{{ __('Nouvelle compétence') }}</h3>
    <form method="POST" action="{{ route('admin.skills.store') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-4">
      @csrf
      <div class="sm:col-span-2">
        <label for="name" class="block text-sm font-semibold text-primary mb-1.5">{{ __('Nom') }}</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" class="w-full rounded-xl border @error('name') border-error @else border-outline-variant/20 @enderror bg-white/70 py-2.5 px-4 text-sm focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30" placeholder="{{ __('Ex: PHP, Gestion de projet...') }}" required/>
        @error('name')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
      </div>
      <div>
        <label for="category" class="block text-sm font-semibold text-primary mb-1.5">{{ __('Catégorie') }}</label>
        <select id="category" name="category" class="w-full rounded-xl border @error('category') border-error @else border-outline-variant/20 @enderror bg-white/70 py-2.5 px-4 text-sm focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30" required>
          <option value="">{{ __('— Choisir —') }}</option>
          <option value="technique" @selected(old('category') == 'technique')>{{ __('Technique') }}</option>
          <option value="transversale" @selected(old('category') == 'transversale')>{{ __('Transversale') }}</option>
          <option value="numerique" @selected(old('category') == 'numerique')>{{ __('Numérique') }}</option>
          <option value="linguistique" @selected(old('category') == 'linguistique')>{{ __('Linguistique') }}</option>
          <option value="gestion" @selected(old('category') == 'gestion')>{{ __('Gestion') }}</option>
          <option value="commercial" @selected(old('category') == 'commercial')>{{ __('Commercial') }}</option>
        </select>
        @error('category')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
      </div>
      <div>
        <label for="importance_level" class="block text-sm font-semibold text-primary mb-1.5">{{ __('Importance') }}</label>
        <select id="importance_level" name="importance_level" class="w-full rounded-xl border @error('importance_level') border-error @else border-outline-variant/20 @enderror bg-white/70 py-2.5 px-4 text-sm focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30" required>
          @foreach ([1=>__('1 - Basique'),2=>__('2 - Utile'),3=>__('3 - Important'),4=>__('4 - Très important'),5=>__('5 - Essentiel')] as $val => $label)
            <option value="{{ $val }}" @selected(old('importance_level', '3') == $val)>{{ $label }}</option>
          @endforeach
        </select>
        @error('importance_level')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
      </div>
      <div class="sm:col-span-4 flex justify-end">
        <button type="submit" onclick="this.disabled=true;this.innerHTML='<span class=\'material-symbols-outlined text-lg align-middle animate-spin\'>progress_activity</span> {{ __('Patientez...') }}'" class="rounded-xl bg-secondary-container text-white px-4 py-2.5 text-sm font-bold hover:bg-secondary transition-colors"><span class="material-symbols-outlined text-lg align-middle">add</span> {{ __('Créer') }}</button>
      </div>
    </form>
  </div>

  <div class="card-glow overflow-hidden rounded-2xl">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm">
        <thead><tr class="bg-surface-container-low/50">
          <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline">{{ __('Compétence') }}</th>
          <th class="hidden px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline sm:table-cell">{{ __('Catégorie') }}</th>
          <th class="hidden px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline md:table-cell">{{ __('Importance') }}</th>
          <th class="px-6 py-3 text-right text-xs font-bold uppercase tracking-wider text-outline">{{ __('Actions') }}</th>
        </tr></thead>
        <tbody class="divide-y divide-outline-variant/5">
          @forelse ($skills as $skill)
            <tr class="transition-colors hover:bg-surface-container-low/30" x-data="{ editing: false }">
              <td class="px-6 py-3">
                <span x-show="!editing" class="font-semibold text-primary">{{ __($skill->name) }}</span>
                <form x-show="editing" method="POST" action="{{ route('admin.skills.update', $skill) }}" class="flex flex-wrap items-center gap-2" x-cloak>
                  @csrf @method('PUT')
                  <input type="text" name="name" value="{{ $skill->name }}" class="w-36 rounded-xl border border-outline-variant/20 bg-white/70 py-1.5 px-3 text-sm focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30" required/>
                  <select name="category" class="w-28 rounded-xl border border-outline-variant/20 bg-white/70 py-1.5 px-2 text-sm focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30">
                    @foreach(['technique'=>__('Technique'),'transversale'=>__('Transversale'),'numerique'=>__('Numérique'),'linguistique'=>__('Linguistique'),'gestion'=>__('Gestion'),'commercial'=>__('Commercial')] as $val => $label)
                      <option value="{{ $val }}" @selected($skill->category == $val)>{{ $label }}</option>
                    @endforeach
                  </select>
                  <select name="importance_level" class="w-24 rounded-xl border border-outline-variant/20 bg-white/70 py-1.5 px-2 text-sm focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30">
                    @for ($i = 1; $i <= 5; $i++)<option value="{{ $i }}" @selected($skill->importance_level == $i)>{{ $i }}</option>@endfor
                  </select>
                  <button type="submit" aria-label="{{ __('Enregistrer la compétence') }}" class="text-success hover:text-success-deep"><span class="material-symbols-outlined text-lg">check</span></button>
                  <button type="button" @click="editing = false" class="text-outline hover:text-primary"><span class="material-symbols-outlined text-lg">close</span></button>
                </form>
              </td>
              <td class="hidden px-6 py-3 sm:table-cell"><x-admin.status-badge :label="$skill->category_label" color="bg-secondary-container/10 text-secondary-container" /></td>
              <td class="hidden px-6 py-3 md:table-cell">
                <div class="flex gap-0.5">
                  @for ($i = 1; $i <= 5; $i++)
                    <span class="material-symbols-outlined text-sm {{ $i <= $skill->importance_level ? 'text-secondary-container' : 'text-outline/20' }}">star</span>
                  @endfor
                </div>
              </td>
              <td class="px-6 py-3 text-right">
                <div class="flex items-center justify-end gap-1">
                  <button @click="editing = !editing" title="{{ __('Modifier') }}" aria-label="{{ __('Modifier') }}" class="rounded-xl p-2.5 text-outline hover:bg-surface-container-low hover:text-primary transition-colors"><span class="material-symbols-outlined text-lg">edit</span></button>
                  <form method="POST" action="{{ route('admin.skills.destroy', $skill) }}" data-proxima-confirm="{{ __('Supprimer') }} « {{ $skill->name }} » ?" data-proxima-title="{{ __('Supprimer la compétence') }}" data-proxima-confirm-label="{{ __('Supprimer') }}" data-proxima-tone="danger">
                    @csrf @method('DELETE')
                    <button aria-label="{{ __('Supprimer la compétence') }}" class="rounded-xl p-2.5 text-outline hover:bg-error-light hover:text-error transition-colors"><span class="material-symbols-outlined text-lg">delete</span></button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="px-6 py-10 text-center text-sm text-outline">@if($search) {{ __('Aucune compétence ne correspond à') }} « {{ $search }} ». @else {{ __('Aucune compétence pour le moment. Créez la première ci-dessus !') }} @endif</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="flex flex-col gap-4 border-t border-outline-variant/10 px-6 py-4 text-sm sm:flex-row sm:items-center sm:justify-between">
      <span class="text-outline">{{ $skills->firstItem() ?? 0 }}-{{ $skills->lastItem() ?? 0 }} {{ __('sur') }} {{ $skills->total() }} {{ __('compétences') }}</span>
      {{ $skills->withQueryString()->links('components.pagination.public-pagination') }}
    </div>
  </div>
</div>
@endsection
