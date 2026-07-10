@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('content')
  <div class="space-y-8">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 md:gap-6">
      <div class="card-glow flex items-center gap-4 rounded-2xl p-5">
        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-secondary-container/10">
          <span class="material-symbols-outlined text-2xl text-secondary-container">business</span>
        </div>
        <div>
          <p class="text-xs text-on-surface-variant">Entreprises</p>
          <p class="text-2xl font-bold text-primary">{{ number_format($entrepriseStats['count']) }}</p>
          <p class="text-2xs font-semibold text-secondary-container">{{ $entrepriseStats['monthlyGrowth'] >= 0 ? '+' : '' }}{{ $entrepriseStats['monthlyGrowth'] }}% ce mois</p>
        </div>
      </div>
      <div class="card-glow flex items-center gap-4 rounded-2xl p-5">
        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-secondary-container/10">
          <span class="material-symbols-outlined text-2xl text-secondary-container">work</span>
        </div>
        <div>
          <p class="text-xs text-on-surface-variant">Offres d'emploi</p>
          <p class="text-2xl font-bold text-primary">{{ number_format($offreStats['count']) }}</p>
          <p class="text-2xs font-semibold text-secondary-container">{{ $offreStats['monthlyGrowth'] >= 0 ? '+' : '' }}{{ $offreStats['monthlyGrowth'] }}% ce mois</p>
        </div>
      </div>
      <div class="card-glow flex items-center gap-4 rounded-2xl p-5">
        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-secondary-container/10">
          <span class="material-symbols-outlined text-2xl text-secondary-container">group</span>
        </div>
        <div>
          <p class="text-xs text-on-surface-variant">Candidats</p>
          <p class="text-2xl font-bold text-primary">{{ number_format($candidateStats['count']) }}</p>
          <p class="text-2xs font-semibold text-secondary-container">{{ $candidateStats['monthlyGrowth'] >= 0 ? '+' : '' }}{{ $candidateStats['monthlyGrowth'] }}% ce mois</p>
        </div>
      </div>
      <div class="card-glow flex items-center gap-4 rounded-2xl p-5">
        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-secondary-container/10">
          <span class="material-symbols-outlined text-2xl text-secondary-container">auto_awesome</span>
        </div>
        <div>
          <p class="text-xs text-on-surface-variant">Candidatures</p>
          <p class="text-2xl font-bold text-primary">{{ number_format($matchStats['count']) }}</p>
          <p class="text-2xs font-semibold text-secondary-container">{{ $matchStats['monthlyGrowth'] >= 0 ? '+' : '' }}{{ $matchStats['monthlyGrowth'] }}% ce mois</p>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
      @php
        $userMax = max($userChartData['data']) ?: 1;
        $offerMax = max($offerChartData['data']) ?: 1;
      @endphp
      <div class="card-glow rounded-2xl p-6">
        <div class="mb-6 flex items-center justify-between">
          <h3 class="font-bold font-serif text-primary">Inscriptions</h3>
          <span class="text-xs font-semibold uppercase tracking-wider text-outline">{{ $userChartData['currentYear'] }}</span>
        </div>
        <div class="flex h-48 items-end gap-1 px-1">
          @foreach ($userChartData['data'] as $value)
            <div class="chart-bar flex-1 rounded-t-md bg-secondary-container/90" style="height: {{ max(8, ($value / $userMax) * 100) }}%"></div>
          @endforeach
        </div>
        <div class="mt-3 flex justify-between text-2xs font-semibold uppercase tracking-wider text-outline">
          @foreach ($userChartData['labels'] as $label)
            <span>{{ $label }}</span>
          @endforeach
        </div>
      </div>
      <div class="card-glow rounded-2xl p-6">
        <div class="mb-6 flex items-center justify-between">
          <h3 class="font-bold font-serif text-primary">Offres publiées</h3>
          <span class="text-xs font-semibold uppercase tracking-wider text-outline">{{ $offerChartData['currentYear'] }}</span>
        </div>
        <div class="flex h-48 items-end gap-1 px-1">
          @foreach ($offerChartData['data'] as $value)
            <div class="chart-bar flex-1 rounded-t-md bg-secondary-container/70" style="height: {{ max(8, ($value / $offerMax) * 100) }}%"></div>
          @endforeach
        </div>
        <div class="mt-3 flex justify-between text-2xs font-semibold uppercase tracking-wider text-outline">
          @foreach ($offerChartData['labels'] as $label)
            <span>{{ $label }}</span>
          @endforeach
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
      <div class="card-glow overflow-hidden rounded-2xl lg:col-span-2">
        <div class="flex items-center justify-between border-b border-outline-variant/10 px-6 py-4">
          <h3 class="font-bold font-serif text-primary">Derniers inscrits</h3>
          <a href="{{ route('admin.users') }}" class="text-sm font-semibold text-secondary-container hover:underline">Voir tout</a>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead>
              <tr class="bg-surface-container-low/50">
                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline">Utilisateur</th>
                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline">Rôle</th>
                <th class="hidden px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline sm:table-cell">Date</th>
                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline">Statut</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/5">
              @foreach ($users as $user)
                <tr class="transition-colors hover:bg-surface-container-low/30">
                  <td class="px-6 py-3">
                    <div class="flex items-center gap-3">
                      <div class="flex h-8 w-8 items-center justify-center rounded-full bg-secondary-container/20 text-xs font-bold text-secondary-container">{{ $user->initials }}</div>
                      <div>
                        <a href="{{ route('admin.users.show', $user) }}" class="font-semibold text-primary hover:text-secondary-container transition-colors">{{ trim($user->name . ' ' . ($user->prenom ?? '')) }}</a>
                        <p class="text-xs text-outline">{{ $user->email }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-3">
                    <x-admin.status-badge :label="$user->admin_role_label" :color="$user->admin_role_color" />
                  </td>
                  <td class="hidden px-6 py-3 text-outline sm:table-cell">{{ optional($user->created_at)->format('d/m/Y') }}</td>
                  <td class="px-6 py-3">
                    <x-admin.status-badge :label="$user->display_status_label" :color="$user->display_status_color" />
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      <div class="card-glow rounded-2xl p-6">
        <h3 class="mb-4 font-bold font-serif text-primary">Actions rapides</h3>
        <div class="space-y-3">
          <a href="{{ route('admin.users') }}" class="flex items-center gap-3 rounded-xl p-3 transition-colors hover:bg-surface-container-low">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-secondary-container/10"><span class="material-symbols-outlined text-secondary-container">person_add</span></div>
            <div><p class="text-sm font-semibold text-primary">Utilisateurs</p><p class="text-xs text-outline">Gérer les comptes</p></div>
          </a>
          <a href="{{ route('admin.offres') }}" class="flex items-center gap-3 rounded-xl p-3 transition-colors hover:bg-surface-container-low">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-secondary-container/10"><span class="material-symbols-outlined text-secondary-container">work</span></div>
            <div><p class="text-sm font-semibold text-primary">Offres</p><p class="text-xs text-outline">Modérer les offres</p></div>
          </a>
          <a href="{{ route('admin.abonnements') }}" class="flex items-center gap-3 rounded-xl p-3 transition-colors hover:bg-surface-container-low">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-secondary-container/10"><span class="material-symbols-outlined text-secondary-container">credit_card</span></div>
            <div><p class="text-sm font-semibold text-primary">Abonnements</p><p class="text-xs text-outline">Suivre les plans</p></div>
          </a>
          <a href="{{ route('admin.parametres') }}" class="flex items-center gap-3 rounded-xl p-3 transition-colors hover:bg-surface-container-low">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-surface-container"><span class="material-symbols-outlined text-outline">settings</span></div>
            <div><p class="text-sm font-semibold text-primary">Paramètres</p><p class="text-xs text-outline">Configurer le site</p></div>
          </a>
        </div>
      </div>
    </div>
  </div>
@endsection
