@extends('layouts.admin')
@section('title', 'Détail utilisateur')
@section('page-title', 'Détail utilisateur')
@section('content')
  @php
    $latestSubscription = $user->abonnements->first();
    $recentApplications = $user->postulations->sortByDesc('created_at')->take(5);
  @endphp

  <div class="space-y-6">
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
                    <td class="px-6 py-3 font-semibold text-primary">{{ $postulation->offre->poste ?? 'Offre indisponible' }}</td>
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
