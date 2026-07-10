@extends('layouts.admin')
@section('title', 'Types d\'offres')
@section('page-title', 'Types d\'offres')
@section('content')
<div class="space-y-6">
  @if (session('success'))
    <div class="flex items-center gap-2 rounded-2xl border border-success-light bg-success-light px-4 py-3 text-sm text-success-deep"><span class="material-symbols-outlined text-lg">check_circle</span> {{ session('success') }}</div>
  @endif
  @if (session('error'))
    <div class="flex items-center gap-2 rounded-2xl border border-error-light bg-error-light px-4 py-3 text-sm text-error-deep"><span class="material-symbols-outlined text-lg">error</span> {{ session('error') }}</div>
  @endif

  <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <form method="GET" class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
      <div class="relative flex-1 sm:max-w-sm">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">search</span>
        <input type="text" name="search" value="{{ $search }}" class="w-full rounded-xl border border-outline-variant/20 bg-white/70 py-2.5 pl-10 pr-4 text-sm transition-all focus:border-secondary-container/50 focus:ring-0" placeholder="Rechercher..."/>
      </div>
      <button type="submit" class="rounded-xl bg-secondary-container px-4 py-2.5 text-sm font-bold text-white transition-colors hover:bg-secondary">Filtrer</button>
      @if ($search)<a href="{{ route('admin.types-offres.index') }}" class="rounded-xl border border-outline-variant/20 px-4 py-2.5 text-sm font-semibold transition-colors hover:bg-surface-container-low">Réinitialiser</a>@endif
    </form>
  </div>

  <div class="card-glow rounded-2xl p-5">
    <h3 class="mb-4 font-bold font-serif text-primary text-sm">Nouveau type d'offre</h3>
    <form method="POST" action="{{ route('admin.types-offres.store') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
      @csrf
      <div class="flex-1">
        <label for="nom" class="block text-xs font-bold uppercase tracking-wider text-outline mb-1">Nom</label>
        <input type="text" id="nom" name="nom" value="{{ old('nom') }}" class="w-full rounded-xl border @error('nom') border-error @else border-outline-variant/20 @enderror bg-white/70 py-2.5 px-4 text-sm focus:border-secondary-container/50 focus:ring-0" placeholder="Ex: CDI, CDD, Stage..." required/>
        @error('nom')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
      </div>
      <button type="submit" class="rounded-xl bg-primary text-white px-5 py-2.5 text-sm font-bold hover:bg-secondary-container transition-colors"><span class="material-symbols-outlined text-lg align-middle">add</span> Créer</button>
    </form>
  </div>

  <div class="card-glow overflow-hidden rounded-2xl">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm">
        <thead><tr class="bg-surface-container-low/50">
          <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline">Type</th>
          <th class="hidden px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline sm:table-cell">Offres</th>
          <th class="hidden px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline md:table-cell">Créé le</th>
          <th class="px-6 py-3 text-right text-xs font-bold uppercase tracking-wider text-outline">Actions</th>
        </tr></thead>
        <tbody class="divide-y divide-outline-variant/5">
          @forelse ($types as $type)
            <tr class="transition-colors hover:bg-surface-container-low/30" x-data="{ editing: false }">
              <td class="px-6 py-3">
                <span x-show="!editing" class="font-semibold text-primary">{{ $type->nom }}</span>
                <form x-show="editing" method="POST" action="{{ route('admin.types-offres.update', $type) }}" class="flex items-center gap-2" x-cloak>
                  @csrf @method('PUT')
                  <input type="text" name="nom" value="{{ $type->nom }}" class="w-48 rounded-xl border border-outline-variant/20 bg-white/70 py-1.5 px-3 text-sm focus:border-secondary-container/50 focus:ring-0" required/>
                  <button type="submit" class="text-success hover:text-success-deep"><span class="material-symbols-outlined text-lg">check</span></button>
                  <button type="button" @click="editing = false" class="text-outline hover:text-primary"><span class="material-symbols-outlined text-lg">close</span></button>
                </form>
              </td>
              <td class="hidden px-6 py-3 sm:table-cell"><x-admin.status-badge :label="(string) $type->offres_count" color="bg-secondary-container/10 text-secondary-container" /></td>
              <td class="hidden px-6 py-3 text-outline md:table-cell">{{ $type->created_at->format('d/m/Y') }}</td>
              <td class="px-6 py-3 text-right">
                <div class="flex items-center justify-end gap-1">
                  <button @click="editing = !editing" class="rounded-lg p-1.5 text-outline hover:bg-surface-container-low hover:text-primary transition-colors"><span class="material-symbols-outlined text-base">edit</span></button>
                  <form method="POST" action="{{ route('admin.types-offres.destroy', $type) }}" onsubmit="return confirm('Supprimer « {{ $type->nom }} » ?')">
                    @csrf @method('DELETE')
                    <button class="rounded-lg p-1.5 text-outline hover:bg-error-light hover:text-error transition-colors"><span class="material-symbols-outlined text-base">delete</span></button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="px-6 py-10 text-center text-sm text-outline">{{ $search ? 'Aucun type ne correspond à « '.$search.' ».' : 'Aucun type d\'offre. Créez le premier ci-dessus !' }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="flex flex-col gap-4 border-t border-outline-variant/10 px-6 py-4 text-sm sm:flex-row sm:items-center sm:justify-between">
      <span class="text-outline">{{ $types->firstItem() ?? 0 }}-{{ $types->lastItem() ?? 0 }} sur {{ $types->total() }} types</span>
      {{ $types->links() }}
    </div>
  </div>
</div>
@endsection
