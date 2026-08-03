@extends('layouts.admin')
@section('title', 'Détail utilisateur')
@section('page-title', 'Détail utilisateur')
@section('content')
  @php
    $latestSubscription = $user->abonnements->first();
    $recentApplications = $user->postulations->sortByDesc('created_at')->take(5);
  @endphp

  <div class="space-y-6">
    @if(session('success'))<div class="rounded-2xl bg-green-50 px-5 py-4 text-sm font-semibold text-green-800">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="rounded-2xl bg-red-50 px-5 py-4 text-sm text-red-700">{{ $errors->first() }}</div>@endif
    <a href="{{ route('admin.users') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-secondary-container hover:underline">
      <span class="material-symbols-outlined text-lg">arrow_back</span> Retour à la liste
    </a>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
      <div class="card-glow rounded-2xl p-6 text-center">
        <div class="mx-auto mb-4 flex h-24 w-24 items-center justify-center rounded-full bg-secondary-container/20 text-4xl font-bold text-secondary-container">
          {{ $user->initials }}
        </div>
        <h2 class="text-xl font-bold font-serif text-primary">{{ trim($user->name . ' ' . ($user->prenom ?? '')) }}</h2>
        <p class="text-sm text-on-surface-variant">{{ $user->admin_role_label }}</p>
        <x-admin.status-badge class="mt-3" :label="$user->display_status_label" :color="$user->display_status_color" />

        <div class="mt-6 space-y-3 text-left text-sm">
          <div class="flex items-center gap-3"><span class="material-symbols-outlined text-lg text-outline">mail</span><span class="text-on-surface-variant">{{ $user->email }}</span></div>
          <div class="flex items-center gap-3"><span class="material-symbols-outlined text-lg text-outline">call</span><span class="text-on-surface-variant">{{ $user->telephone ?: 'Non renseigné' }}</span></div>
          <div class="flex items-center gap-3"><span class="material-symbols-outlined text-lg text-outline">location_on</span><span class="text-on-surface-variant">{{ $user->adresse ?: 'Non renseignée' }}</span></div>
          <div class="flex items-center gap-3"><span class="material-symbols-outlined text-lg text-outline">calendar_today</span><span class="text-on-surface-variant">Inscrit le {{ optional($user->created_at)->format('d/m/Y') }}</span></div>
          <div class="flex items-center gap-3"><span class="material-symbols-outlined text-lg text-outline">credit_card</span><span class="text-on-surface-variant">{{ $latestSubscription?->nom ?? 'Aucun abonnement actif' }}</span></div>
        </div>
      </div>

      <div class="space-y-6 lg:col-span-2">
        <details class="card-glow rounded-2xl" @if($errors->any()) open @endif>
          <summary class="flex cursor-pointer list-none items-center justify-between p-5 font-bold text-primary"><span class="flex items-center gap-2"><span class="material-symbols-outlined text-secondary-container">edit</span> Modifier le compte</span><span class="material-symbols-outlined">expand_more</span></summary>
          <form method="POST" action="{{ route('admin.users.update',$user) }}" class="grid gap-4 border-t border-primary/5 p-5 md:grid-cols-2">@csrf @method('PUT')
            <input type="hidden" name="role" value="{{ $user->getRoleNames()->first() }}">
            <label class="text-xs font-bold text-primary">Nom<input name="name" value="{{ old('name',$user->name) }}" required class="mt-2 w-full rounded-xl border-outline-variant/30 text-sm"></label><label class="text-xs font-bold text-primary">Prénom<input name="prenom" value="{{ old('prenom',$user->prenom) }}" required class="mt-2 w-full rounded-xl border-outline-variant/30 text-sm"></label><label class="text-xs font-bold text-primary">Courriel<input name="email" value="{{ old('email',$user->email) }}" type="email" required class="mt-2 w-full rounded-xl border-outline-variant/30 text-sm"></label><label class="text-xs font-bold text-primary">Téléphone<input name="telephone" value="{{ old('telephone',$user->telephone) }}" class="mt-2 w-full rounded-xl border-outline-variant/30 text-sm"></label><label class="text-xs font-bold text-primary md:col-span-2">Adresse<input name="adresse" value="{{ old('adresse',$user->adresse) }}" class="mt-2 w-full rounded-xl border-outline-variant/30 text-sm"></label>
            @if($user->entreprise)<label class="text-xs font-bold text-primary">Nom de l’entreprise<input name="company_name" value="{{ old('company_name',$user->entreprise->company_name) }}" required class="mt-2 w-full rounded-xl border-outline-variant/30 text-sm"></label><label class="text-xs font-bold text-primary">NEQ<input name="neq" value="{{ old('neq',$user->entreprise->neq) }}" class="mt-2 w-full rounded-xl border-outline-variant/30 text-sm"></label><label class="text-xs font-bold text-primary">Site Web<input name="website" value="{{ old('website',$user->entreprise->website) }}" type="url" class="mt-2 w-full rounded-xl border-outline-variant/30 text-sm"></label><label class="text-xs font-bold text-primary">Description<textarea name="description" rows="2" class="mt-2 w-full rounded-xl border-outline-variant/30 text-sm">{{ old('description',$user->entreprise->description) }}</textarea></label>@endif
            <label class="text-xs font-bold text-primary">Nouveau mot de passe (facultatif)<input name="password" type="password" minlength="8" class="mt-2 w-full rounded-xl border-outline-variant/30 text-sm"></label><label class="text-xs font-bold text-primary">Confirmation<input name="password_confirmation" type="password" minlength="8" class="mt-2 w-full rounded-xl border-outline-variant/30 text-sm"></label><button class="w-fit rounded-xl bg-primary-container px-5 py-2.5 text-sm font-bold text-white">Enregistrer les modifications</button>
          </form>
        </details>
        @if ($user->entreprise)
          <div class="card-glow overflow-hidden rounded-2xl">
            <div class="border-b border-outline-variant/10 px-6 py-4">
              <h3 class="font-bold font-serif text-primary">Informations entreprise</h3>
            </div>
            <div class="grid grid-cols-1 gap-4 p-6 md:grid-cols-2">
              <div>
                <p class="text-xs font-bold uppercase tracking-wider text-outline">Nom</p>
                <p class="mt-1 text-sm text-primary">{{ $user->entreprise->company_name }}</p>
              </div>
              <div>
                <p class="text-xs font-bold uppercase tracking-wider text-outline">Statut</p>
                <div class="mt-1">
                  <x-admin.status-badge :label="$user->display_status_label" :color="$user->display_status_color" />
                </div>
              </div>
              <div>
                <p class="text-xs font-bold uppercase tracking-wider text-outline">Site web</p>
                <p class="mt-1 text-sm text-primary">{{ $user->entreprise->website ?: 'Non renseigné' }}</p>
              </div>
              <div>
                <p class="text-xs font-bold uppercase tracking-wider text-outline">NEQ</p>
                <p class="mt-1 text-sm text-primary">{{ $user->entreprise->neq ?: 'Non renseigné' }}</p>
              </div>
            </div>
          </div>
        @endif

        <div class="card-glow overflow-hidden rounded-2xl">
          <div class="border-b border-outline-variant/10 px-6 py-4">
            <h3 class="font-bold font-serif text-primary">Candidatures récentes</h3>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead>
                <tr class="bg-surface-container-low/50">
                  <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline">Offre</th>
                  <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline">Entreprise</th>
                  <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline">Statut</th>
                  <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline">Date</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-outline-variant/5">
                @forelse ($recentApplications as $postulation)
                  <tr class="transition-colors hover:bg-surface-container-low/30">
                    <td class="px-6 py-3 font-semibold text-primary">
                      @if($postulation->offre)
                        <a href="{{ route('job_infos', $postulation->offre) }}" class="hover:text-secondary-container transition-colors">{{ $postulation->offre->poste }}</a>
                      @else
                        Offre indisponible
                      @endif
                    </td>
                    <td class="px-6 py-3 text-on-surface-variant">{{ $postulation->offre->entreprise->company_name ?? 'Entreprise inconnue' }}</td>
                    <td class="px-6 py-3">
                      <x-admin.status-badge :label="$postulation->status ?? 'En attente'" color="bg-info-light text-info-dark" />
                    </td>
                    <td class="px-6 py-3 text-outline">{{ optional($postulation->created_at)->format('d/m/Y') }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-sm text-outline">Aucune candidature récente pour cet utilisateur.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
