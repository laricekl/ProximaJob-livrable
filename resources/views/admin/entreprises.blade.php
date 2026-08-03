@extends('layouts.admin')

@section('title', 'Gestion des entreprises')
@section('page-title', 'Entreprises')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
  <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
    <div><p class="text-xs font-bold uppercase tracking-[.22em] text-secondary-container">Contrôle des comptes</p><h2 class="mt-1 text-3xl font-serif font-bold text-primary">Validation des entreprises</h2><p class="mt-2 text-sm text-outline">Approuvez les inscriptions, documentez les refus et choisissez les entreprises visibles publiquement.</p></div>
    <a href="{{ route('admin.users') }}" class="inline-flex items-center gap-2 rounded-xl border border-primary/10 bg-white px-4 py-2.5 text-sm font-semibold text-primary"><span class="material-symbols-outlined text-lg">person_add</span> Gérer les comptes</a>
  </div>

  @if(session('success'))<div class="rounded-2xl bg-green-50 px-5 py-4 text-sm font-semibold text-green-800">{{ session('success') }}</div>@endif
  @if($errors->any())<div class="rounded-2xl bg-red-50 px-5 py-4 text-sm text-red-700">{{ $errors->first() }}</div>@endif

  <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
    @foreach([['Total',$stats['total'],'domain'],['À valider',$stats['pending'],'pending_actions'],['Approuvées',$stats['approved'],'verified'],['En vedette',$stats['featured'],'star']] as [$label,$value,$icon])
    <div class="rounded-2xl border border-primary/5 bg-white p-5 shadow-sm"><span class="material-symbols-outlined text-secondary-container">{{ $icon }}</span><p class="mt-3 text-2xl font-bold text-primary">{{ number_format($value) }}</p><p class="text-xs text-outline">{{ $label }}</p></div>
    @endforeach
  </div>

  <form method="GET" class="flex flex-col gap-3 rounded-2xl border border-primary/5 bg-white p-4 shadow-sm md:flex-row">
    <input name="search" value="{{ request('search') }}" class="flex-1 rounded-xl border-outline-variant/40 text-sm" placeholder="Nom, NEQ ou courriel">
    <select name="status" class="rounded-xl border-outline-variant/40 text-sm"><option value="">Tous les statuts</option>@foreach(['pending'=>'À valider','approved'=>'Approuvée','rejected'=>'Refusée'] as $key=>$label)<option value="{{ $key }}" @selected(request('status')===$key)>{{ $label }}</option>@endforeach</select>
    <button class="rounded-xl bg-primary-container px-5 py-2.5 text-sm font-bold text-white">Filtrer</button>
  </form>

  <div class="space-y-3">
    @forelse($entreprises as $entreprise)
    <article class="rounded-2xl border border-primary/5 bg-white p-5 shadow-sm">
      <div class="flex flex-col gap-5 xl:flex-row xl:items-center">
        <div class="flex min-w-0 flex-1 items-center gap-4">
          <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-primary-container text-lg font-bold text-white">{{ mb_strtoupper(mb_substr($entreprise->company_name,0,1)) }}</div>
          <div class="min-w-0"><div class="flex flex-wrap items-center gap-2"><h3 class="font-bold text-primary">{{ $entreprise->company_name }}</h3>@if($entreprise->is_featured)<span class="rounded-full bg-amber-100 px-2 py-1 text-2xs font-bold text-amber-800">EN VEDETTE</span>@endif</div><p class="truncate text-sm text-outline">{{ $entreprise->user?->email }} · NEQ {{ $entreprise->neq ?: 'non renseigné' }}</p><p class="mt-1 text-xs text-outline">{{ $entreprise->offres_count }} offre(s) · {{ $entreprise->candidatures_count }} candidature(s)</p></div>
        </div>
        <span class="w-fit rounded-full px-3 py-1 text-xs font-bold {{ $entreprise->normalized_status === 'approved' ? 'bg-green-100 text-green-800' : ($entreprise->normalized_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">{{ ['approved'=>'Approuvée','rejected'=>'Refusée','pending'=>'À valider'][$entreprise->normalized_status] }}</span>
        <div class="flex flex-wrap gap-2">
          @if($entreprise->status !== 'approved')<form method="POST" action="{{ route('admin.entreprises.status',$entreprise) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="approved"><button class="rounded-xl bg-green-600 px-4 py-2 text-xs font-bold text-white">Approuver</button></form>@endif
          @if($entreprise->status === 'approved')<form method="POST" action="{{ route('admin.entreprises.featured',$entreprise) }}">@csrf @method('PATCH')<button class="rounded-xl border border-amber-300 px-4 py-2 text-xs font-bold text-amber-800">{{ $entreprise->is_featured ? 'Retirer la vedette' : 'Mettre en vedette' }}</button></form>@endif
          <details class="relative"><summary class="cursor-pointer list-none rounded-xl border border-red-200 px-4 py-2 text-xs font-bold text-red-700">Refuser</summary><form method="POST" action="{{ route('admin.entreprises.status',$entreprise) }}" class="absolute right-0 z-10 mt-2 w-80 rounded-2xl border bg-white p-4 shadow-xl">@csrf @method('PATCH')<input type="hidden" name="status" value="rejected"><label class="text-xs font-bold text-primary">Motif communiqué au dossier</label><textarea name="reason" required rows="3" class="mt-2 w-full rounded-xl border-outline-variant/40 text-sm" placeholder="Expliquez précisément le refus"></textarea><button class="mt-3 w-full rounded-xl bg-red-600 px-4 py-2 text-xs font-bold text-white">Confirmer le refus</button></form></details>
        </div>
      </div>
      @if($entreprise->rejection_reason)<p class="mt-4 rounded-xl bg-red-50 p-3 text-xs text-red-700"><strong>Motif :</strong> {{ $entreprise->rejection_reason }}</p>@endif
    </article>
    @empty<div class="rounded-2xl bg-white p-10 text-center text-sm text-outline">Aucune entreprise ne correspond aux filtres.</div>@endforelse
  </div>
  {{ $entreprises->links() }}
</div>
@endsection
