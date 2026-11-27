@extends('layouts.admin')
@section('title', __('Détail de la candidature'))
@section('page-title', __('Détail candidature'))
@section('content')
@php
  $candidateName = trim(($candidature->user?->prenom ?? '').' '.($candidature->user?->name ?? '')) ?: __('Compte supprimé');
  $statuses = ['en_attente'=>__('À examiner'),'shortlisted'=>__('Présélectionnée'),'contacted'=>__('Contactée'),'interview'=>__('Entretien'),'accepted'=>__('Acceptée'),'rejected'=>__('Refusée')];
@endphp
<div class="mx-auto max-w-7xl space-y-6">
  <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div><a href="{{ route('admin.candidatures.index') }}" class="text-sm font-semibold text-secondary-container">← {{ __('Toutes les candidatures') }}</a><h2 class="mt-2 font-serif text-3xl font-bold text-primary">{{ $candidateName }}</h2><p class="mt-1 text-sm text-outline">{{ $candidature->offre?->titre ?: $candidature->offre?->poste ?: __('Offre supprimée') }} · {{ $candidature->offre?->entreprise?->company_name }}</p></div>
    <form method="POST" action="{{ route('admin.candidatures.status', $candidature) }}" class="flex gap-2">@csrf @method('PATCH')
      <select name="status" class="rounded-xl border-outline-variant/40 text-sm">@foreach($statuses as $key=>$label)<option value="{{ $key }}" @selected($candidature->normalized_status===$key)>{{ $label }}</option>@endforeach</select>
      <button class="rounded-xl bg-primary-container px-4 py-2 text-sm font-bold text-white">{{ __('Enregistrer') }}</button>
    </form>
  </div>
  <x-admin.flash />

  <div class="grid gap-5 lg:grid-cols-3">
    <section class="rounded-2xl border border-primary/5 bg-white p-6 shadow-sm lg:col-span-2">
      <h3 class="font-serif text-xl font-bold text-primary">{{ __('Profil et compatibilité') }}</h3>
      <div class="mt-5 grid gap-4 sm:grid-cols-2">
        @foreach([[__('Courriel'),$candidature->user?->email],[__('Téléphone'),$candidature->user?->telephone],[__('Secteur'),$candidature->user?->candidateSector?->sector?->name],[__('Source'),$candidature->autopostulation ? __('Générée par IA') : __('Candidature manuelle')]] as [$label,$value])<div class="rounded-xl bg-surface-container-low p-4"><p class="text-2xs font-bold uppercase tracking-wider text-outline">{{ $label }}</p><p class="mt-1 text-sm font-semibold text-primary">{{ $value ? __($value) : __('Non renseigné') }}</p></div>@endforeach
      </div>
      <div class="mt-5 rounded-2xl bg-secondary-container/10 p-5"><p class="text-sm font-bold text-primary">{{ __('Compatibilité estimée') }}</p><p class="mt-1 text-4xl font-bold text-secondary-container">{{ $candidature->match_score === null ? '—' : round($candidature->match_score).' %' }}</p><p class="mt-1 text-xs text-outline">{{ $candidature->algorithm_version ? __('Algorithme ').$candidature->algorithm_version : __('Score non calculé') }}</p></div>
      @if(is_array($candidature->match_details) && count($candidature->match_details))<div class="mt-5"><h4 class="text-sm font-bold text-primary">{{ __('Détails du calcul') }}</h4><pre class="mt-2 overflow-x-auto rounded-xl bg-surface-container-low p-4 text-xs text-on-surface-variant">{{ json_encode($candidature->match_details, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre></div>@endif
    </section>

    <section class="rounded-2xl border border-primary/5 bg-white p-6 shadow-sm">
      <h3 class="font-serif text-xl font-bold text-primary">{{ __('Documents') }}</h3><div class="mt-4 space-y-3">
        @if($candidature->cv)<a target="_blank" href="{{ route('admin.candidatures.document', [$candidature, 'cv']) }}" class="flex items-center justify-between rounded-xl bg-surface-container-low px-4 py-3 text-sm font-bold text-primary"><span>{{ __('CV du candidat') }}</span><span class="material-symbols-outlined">open_in_new</span></a>@endif
        @if($candidature->cover_letter || $candidature->lettre_motivation)<a target="_blank" href="{{ route('admin.candidatures.document', [$candidature, 'motivation']) }}" class="flex items-center justify-between rounded-xl bg-surface-container-low px-4 py-3 text-sm font-bold text-primary"><span>{{ __('Lettre de motivation') }}</span><span class="material-symbols-outlined">open_in_new</span></a>@endif
        @foreach($candidature->autresDocs as $document)<a target="_blank" href="{{ route('admin.candidatures.other-document', [$candidature, $document]) }}" class="flex items-center justify-between rounded-xl bg-surface-container-low px-4 py-3 text-sm font-bold text-primary"><span>{{ $document->intitule ?: __('Document complémentaire') }}</span><span class="material-symbols-outlined">open_in_new</span></a>@endforeach
        @if(!$candidature->cv && !$candidature->cover_letter && !$candidature->lettre_motivation && $candidature->autresDocs->isEmpty())<p class="text-sm text-outline">{{ __('Aucun document joint.') }}</p>@endif
      </div>
    </section>
  </div>

  <div class="grid gap-5 lg:grid-cols-2">
    <form method="POST" action="{{ route('admin.candidatures.notes', $candidature) }}" class="rounded-2xl border border-primary/5 bg-white p-6 shadow-sm">@csrf @method('PATCH')<h3 class="font-serif text-xl font-bold text-primary">{{ __('Notes internes') }}</h3><p class="mt-1 text-sm text-outline">{{ __('Visibles uniquement par l’administration.') }}</p><textarea name="admin_notes" rows="7" maxlength="5000" class="mt-4 w-full rounded-xl border-outline-variant/40 text-sm" placeholder="{{ __('Ajoutez un contexte de suivi…') }}">{{ old('admin_notes', $candidature->admin_notes) }}</textarea><button class="mt-3 rounded-xl bg-primary-container px-5 py-2.5 text-sm font-bold text-white">{{ __('Enregistrer les notes') }}</button></form>
    <section class="rounded-2xl border border-primary/5 bg-white shadow-sm"><div class="border-b border-primary/5 p-6"><h3 class="font-serif text-xl font-bold text-primary">{{ __('Historique') }}</h3></div><div class="divide-y divide-primary/5">@forelse($history as $event)<div class="p-4"><p class="text-sm font-semibold text-primary">{{ $event->description }}</p><p class="mt-1 text-xs text-outline">{{ $event->admin?->email ?? __('Système') }} · {{ $event->created_at->format('d/m/Y H:i') }}</p></div>@empty<p class="p-6 text-sm text-outline">{{ __('Aucune modification enregistrée pour cette candidature.') }}</p>@endforelse</div></section>
  </div>
</div>
@endsection
