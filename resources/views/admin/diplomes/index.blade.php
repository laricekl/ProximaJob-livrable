@extends('layouts.admin')
@section('title', 'Diplômes')
@section('page-title', 'Diplômes')
@section('content')
<x-admin.flash />
<div class="space-y-6">
  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <form method="GET" class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
      <div class="relative flex-1 sm:max-w-sm">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">search</span>
        <input type="text" name="search" value="{{ $search }}" class="w-full rounded-xl border border-outline-variant/20 bg-white/70 py-2.5 pl-10 pr-4 text-sm transition-all focus:border-secondary-container/50 focus:ring-0" placeholder="Rechercher par nom ou sigle..."/>
      </div>
      <select name="niveau" class="rounded-xl border border-outline-variant/20 bg-white/70 px-3 py-2.5 text-sm transition-all focus:border-secondary-container/50 focus:ring-0">
        <option value="">Tous les niveaux</option>
        @foreach ($niveaux as $val => $label)
          <option value="{{ $val }}" @selected($niveau === $val)>{{ $label }}</option>
        @endforeach
      </select>
      <button type="submit" class="rounded-xl bg-secondary-container px-4 py-2.5 text-sm font-bold text-white transition-colors hover:bg-secondary">Filtrer</button>
      @if ($search || $niveau)<a href="{{ route('admin.diplomes.index') }}" class="rounded-xl border border-outline-variant/20 px-4 py-2.5 text-sm font-semibold transition-colors hover:bg-surface-container-low">Réinitialiser</a>@endif
    </form>
  </div>

  <div class="card-glow rounded-2xl p-5">
    <h3 class="mb-4 font-bold font-serif text-primary text-sm">Nouveau diplôme</h3>
    <form method="POST" action="{{ route('admin.diplomes.store') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-3">
      @csrf
      <div>
        <label for="nom_diplome" class="block text-xs font-bold uppercase tracking-wider text-outline mb-1">Nom</label>
        <input type="text" id="nom_diplome" name="nom_diplome" value="{{ old('nom_diplome') }}" class="w-full rounded-xl border @error('nom_diplome') border-error @else border-outline-variant/20 @enderror bg-white/70 py-2.5 px-4 text-sm focus:border-secondary-container/50 focus:ring-0" placeholder="Ex: Baccalauréat en informatique" required/>
        @error('nom_diplome')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
      </div>
      <div>
        <label for="sigle" class="block text-xs font-bold uppercase tracking-wider text-outline mb-1">Sigle</label>
        <input type="text" id="sigle" name="sigle" value="{{ old('sigle') }}" class="w-full rounded-xl border border-outline-variant/20 bg-white/70 py-2.5 px-4 text-sm focus:border-secondary-container/50 focus:ring-0" placeholder="Ex: B.Sc., DEC..."/>
      </div>
      <div>
        <label for="niveau_education" class="block text-xs font-bold uppercase tracking-wider text-outline mb-1">Niveau</label>
        <select id="niveau_education" name="niveau_education" class="w-full rounded-xl border @error('niveau_education') border-error @else border-outline-variant/20 @enderror bg-white/70 py-2.5 px-4 text-sm focus:border-secondary-container/50 focus:ring-0" required>
          <option value="">— Choisir —</option>
          @foreach ($niveaux as $val => $label)
            <option value="{{ $val }}" @selected(old('niveau_education') == $val)>{{ $label }}</option>
          @endforeach
        </select>
        @error('niveau_education')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
      </div>
      <div>
        <label for="duree_annees" class="block text-xs font-bold uppercase tracking-wider text-outline mb-1">Durée (années)</label>
        <input type="number" id="duree_annees" name="duree_annees" value="{{ old('duree_annees') }}" step="0.5" min="0" max="15" class="w-full rounded-xl border border-outline-variant/20 bg-white/70 py-2.5 px-4 text-sm focus:border-secondary-container/50 focus:ring-0" placeholder="Ex: 3"/>
      </div>
      <div>
        <label for="nom_anglais" class="block text-xs font-bold uppercase tracking-wider text-outline mb-1">Nom anglais</label>
        <input type="text" id="nom_anglais" name="nom_anglais" value="{{ old('nom_anglais') }}" class="w-full rounded-xl border border-outline-variant/20 bg-white/70 py-2.5 px-4 text-sm focus:border-secondary-container/50 focus:ring-0" placeholder="Ex: Bachelor of Computer Science"/>
      </div>
      <div class="sm:col-span-3 flex justify-end">
        <button type="submit" class="rounded-xl bg-primary text-white px-5 py-2.5 text-sm font-bold hover:bg-secondary-container transition-colors"><span class="material-symbols-outlined text-lg align-middle">add</span> Créer</button>
      </div>
    </form>
  </div>

  <div class="card-glow overflow-hidden rounded-2xl">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm">
        <thead><tr class="bg-surface-container-low/50">
          <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline">Diplôme</th>
          <th class="hidden px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline sm:table-cell">Sigle</th>
          <th class="hidden px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline md:table-cell">Niveau</th>
          <th class="hidden px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline lg:table-cell">Durée</th>
          <th class="px-6 py-3 text-right text-xs font-bold uppercase tracking-wider text-outline">Actions</th>
        </tr></thead>
        <tbody class="divide-y divide-outline-variant/5">
          @forelse ($diplomes as $diplome)
            <tr class="transition-colors hover:bg-surface-container-low/30" x-data="{ editing: false }">
              <td class="px-6 py-3">
                <span x-show="!editing" class="font-semibold text-primary">{{ $diplome->nom_diplome }}</span>
                <form x-show="editing" method="POST" action="{{ route('admin.diplomes.update', $diplome) }}" class="flex flex-wrap items-center gap-2" x-cloak>
                  @csrf @method('PUT')
                  <input type="text" name="nom_diplome" value="{{ $diplome->nom_diplome }}" class="w-44 rounded-xl border border-outline-variant/20 bg-white/70 py-1.5 px-3 text-sm focus:border-secondary-container/50 focus:ring-0" required/>
                  <input type="text" name="sigle" value="{{ $diplome->sigle }}" class="w-16 rounded-xl border border-outline-variant/20 bg-white/70 py-1.5 px-3 text-sm focus:border-secondary-container/50 focus:ring-0" placeholder="Sigle"/>
                  <select name="niveau_education" class="w-32 rounded-xl border border-outline-variant/20 bg-white/70 py-1.5 px-2 text-sm focus:border-secondary-container/50 focus:ring-0">
                    @foreach ($niveaux as $val => $label)<option value="{{ $val }}" @selected($diplome->niveau_education == $val)>{{ $label }}</option>@endforeach
                  </select>
                  <button type="submit" class="text-success hover:text-success-deep"><span class="material-symbols-outlined text-lg">check</span></button>
                  <button type="button" @click="editing = false" class="text-outline hover:text-primary"><span class="material-symbols-outlined text-lg">close</span></button>
                </form>
              </td>
              <td class="hidden px-6 py-3 sm:table-cell"><code class="text-xs bg-surface-container-low px-2 py-0.5 rounded">{{ $diplome->sigle ?: '—' }}</code></td>
              <td class="hidden px-6 py-3 md:table-cell"><x-admin.status-badge :label="$diplome->niveau_libelle" color="bg-secondary-container/10 text-secondary-container" /></td>
              <td class="hidden px-6 py-3 lg:table-cell text-outline">{{ $diplome->duree_annees ? $diplome->duree_annees.' an(s)' : '—' }}</td>
              <td class="px-6 py-3 text-right">
                <div class="flex items-center justify-end gap-1">
                  <button @click="editing = !editing" class="rounded-lg p-1.5 text-outline hover:bg-surface-container-low hover:text-primary transition-colors"><span class="material-symbols-outlined text-base">edit</span></button>
                  <form method="POST" action="{{ route('admin.diplomes.destroy', $diplome) }}" onsubmit="return confirm('Supprimer « {{ $diplome->nom_diplome }} » ?')">
                    @csrf @method('DELETE')
                    <button class="rounded-lg p-1.5 text-outline hover:bg-error-light hover:text-error transition-colors"><span class="material-symbols-outlined text-base">delete</span></button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="px-6 py-10 text-center text-sm text-outline">Aucun diplôme.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="flex flex-col gap-4 border-t border-outline-variant/10 px-6 py-4 text-sm sm:flex-row sm:items-center sm:justify-between">
      <span class="text-outline">{{ $diplomes->firstItem() ?? 0 }}-{{ $diplomes->lastItem() ?? 0 }} sur {{ $diplomes->total() }} diplômes</span>
      {{ $diplomes->links() }}
    </div>
  </div>
</div>
@endsection
