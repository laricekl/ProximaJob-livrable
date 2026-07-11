@extends('layouts.admin')
@section('title', 'Compétences')
@section('page-title', 'Compétences')
@section('content')
<x-admin.flash />
<div class="space-y-6">
  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <form method="GET" class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
      <div class="relative flex-1 sm:max-w-sm">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">search</span>
        <input type="text" name="search" value="{{ $search }}" class="w-full rounded-xl border border-outline-variant/20 bg-white/70 py-2.5 pl-10 pr-4 text-sm transition-all focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30" placeholder="Rechercher par nom ou catégorie..."/>
      </div>
      <button type="submit" onclick="this.disabled=true;this.innerHTML='<span class=\'material-symbols-outlined text-lg align-middle animate-spin\'>progress_activity</span> Patientez...'" class="rounded-xl bg-secondary-container px-4 py-2.5 text-sm font-bold text-white transition-colors hover:bg-secondary">Filtrer</button>
      @if ($search)<a href="{{ route('admin.skills.index') }}" class="rounded-xl border border-outline-variant/20 px-4 py-2.5 text-sm font-semibold transition-colors hover:bg-surface-container-low">Réinitialiser</a>@endif
    </form>
  </div>

  <div class="card-glow rounded-2xl p-5">
    <h3 class="mb-4 font-bold font-serif text-primary text-sm">Nouvelle compétence</h3>
    <form method="POST" action="{{ route('admin.skills.store') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-4">
      @csrf
      <div class="sm:col-span-2">
        <label for="name" class="block text-sm font-semibold text-primary mb-1.5">Nom</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" class="w-full rounded-xl border @error('name') border-error @else border-outline-variant/20 @enderror bg-white/70 py-2.5 px-4 text-sm focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30" placeholder="Ex: PHP, Gestion de projet..." required/>
        @error('name')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
      </div>
      <div>
        <label for="category" class="block text-sm font-semibold text-primary mb-1.5">Catégorie</label>
        <select id="category" name="category" class="w-full rounded-xl border @error('category') border-error @else border-outline-variant/20 @enderror bg-white/70 py-2.5 px-4 text-sm focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30" required>
          <option value="">— Choisir —</option>
          <option value="technique" @selected(old('category') == 'technique')>Technique</option>
          <option value="transversale" @selected(old('category') == 'transversale')>Transversale</option>
          <option value="numerique" @selected(old('category') == 'numerique')>Numérique</option>
          <option value="linguistique" @selected(old('category') == 'linguistique')>Linguistique</option>
          <option value="gestion" @selected(old('category') == 'gestion')>Gestion</option>
          <option value="commercial" @selected(old('category') == 'commercial')>Commercial</option>
        </select>
        @error('category')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
      </div>
      <div>
        <label for="importance_level" class="block text-sm font-semibold text-primary mb-1.5">Importance</label>
        <select id="importance_level" name="importance_level" class="w-full rounded-xl border @error('importance_level') border-error @else border-outline-variant/20 @enderror bg-white/70 py-2.5 px-4 text-sm focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30" required>
          @foreach ([1=>'1 - Basique',2=>'2 - Utile',3=>'3 - Important',4=>'4 - Très important',5=>'5 - Essentiel'] as $val => $label)
            <option value="{{ $val }}" @selected(old('importance_level', '3') == $val)>{{ $label }}</option>
          @endforeach
        </select>
        @error('importance_level')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
      </div>
      <div class="sm:col-span-4 flex justify-end">
        <button type="submit" onclick="this.disabled=true;this.innerHTML='<span class=\'material-symbols-outlined text-lg align-middle animate-spin\'>progress_activity</span> Patientez...'" class="rounded-xl bg-secondary-container text-white px-4 py-2.5 text-sm font-bold hover:bg-secondary transition-colors"><span class="material-symbols-outlined text-lg align-middle">add</span> Créer</button>
      </div>
    </form>
  </div>

  <div class="card-glow overflow-hidden rounded-2xl">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm">
        <thead><tr class="bg-surface-container-low/50">
          <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline">Compétence</th>
          <th class="hidden px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline sm:table-cell">Catégorie</th>
          <th class="hidden px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline md:table-cell">Importance</th>
          <th class="px-6 py-3 text-right text-xs font-bold uppercase tracking-wider text-outline">Actions</th>
        </tr></thead>
        <tbody class="divide-y divide-outline-variant/5">
          @forelse ($skills as $skill)
            <tr class="transition-colors hover:bg-surface-container-low/30" x-data="{ editing: false }">
              <td class="px-6 py-3">
                <span x-show="!editing" class="font-semibold text-primary">{{ $skill->name }}</span>
                <form x-show="editing" method="POST" action="{{ route('admin.skills.update', $skill) }}" class="flex flex-wrap items-center gap-2" x-cloak>
                  @csrf @method('PUT')
                  <input type="text" name="name" value="{{ $skill->name }}" class="w-36 rounded-xl border border-outline-variant/20 bg-white/70 py-1.5 px-3 text-sm focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30" required/>
                  <select name="category" class="w-28 rounded-xl border border-outline-variant/20 bg-white/70 py-1.5 px-2 text-sm focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30">
                    @foreach(['technique'=>'Technique','transversale'=>'Transversale','numerique'=>'Numérique','linguistique'=>'Linguistique','gestion'=>'Gestion','commercial'=>'Commercial'] as $val => $label)
                      <option value="{{ $val }}" @selected($skill->category == $val)>{{ $label }}</option>
                    @endforeach
                  </select>
                  <select name="importance_level" class="w-24 rounded-xl border border-outline-variant/20 bg-white/70 py-1.5 px-2 text-sm focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30">
                    @for ($i = 1; $i <= 5; $i++)<option value="{{ $i }}" @selected($skill->importance_level == $i)>{{ $i }}</option>@endfor
                  </select>
                  <button type="submit" class="text-success hover:text-success-deep"><span class="material-symbols-outlined text-lg">check</span></button>
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
                  <button @click="editing = !editing" title="Modifier" aria-label="Modifier" class="rounded-xl p-2.5 text-outline hover:bg-surface-container-low hover:text-primary transition-colors"><span class="material-symbols-outlined text-lg">edit</span></button>
                  <form method="POST" action="{{ route('admin.skills.destroy', $skill) }}" onsubmit="return confirm('Supprimer « {{ $skill->name }} » ?')">
                    @csrf @method('DELETE')
                    <button class="rounded-xl p-2.5 text-outline hover:bg-error-light hover:text-error transition-colors"><span class="material-symbols-outlined text-lg">delete</span></button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="px-6 py-10 text-center text-sm text-outline">@if($search) Aucune compétence ne correspond à « {{ $search }} ». @else Aucune compétence pour le moment. Créez la première ci-dessus ! @endif</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="flex flex-col gap-4 border-t border-outline-variant/10 px-6 py-4 text-sm sm:flex-row sm:items-center sm:justify-between">
      <span class="text-outline">{{ $skills->firstItem() ?? 0 }}-{{ $skills->lastItem() ?? 0 }} sur {{ $skills->total() }} compétences</span>
      {{ $skills->withQueryString()->links('components.pagination.public-pagination') }}
    </div>
  </div>
</div>
@endsection
