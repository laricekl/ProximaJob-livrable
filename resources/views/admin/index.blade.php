@extends('layouts.admin')
@section('title', __('Dashboard'))
@section('page-title', __('Dashboard'))
@section('content')
  @php
    $growthLabel = static function ($growth): string {
      if ($growth === null) return __('Pas de comparaison disponible');
      if ($growth <= -100) return __('Aucune nouvelle donnée ce mois');

      return ($growth >= 0 ? '+' : '').$growth.__('% ce mois');
    };
  @endphp
  <div class="space-y-8">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 md:gap-6">
      <div class="card-glow flex items-center gap-4 rounded-2xl p-5">
        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-secondary-container/10">
          <span class="material-symbols-outlined text-2xl text-secondary-container">business</span>
        </div>
        <div>
          <p class="text-xs text-on-surface-variant">{{ __('Entreprises') }}</p>
          <p class="text-2xl font-bold text-primary">{{ number_format($entrepriseStats['count']) }}</p>
          <p class="text-2xs font-semibold text-secondary-container">{{ $growthLabel($entrepriseStats['monthlyGrowth']) }}</p>
        </div>
      </div>
      <div class="card-glow flex items-center gap-4 rounded-2xl p-5">
        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-secondary-container/10">
          <span class="material-symbols-outlined text-2xl text-secondary-container">work</span>
        </div>
        <div>
          <p class="text-xs text-on-surface-variant">{{ __("Offres d'emploi") }}</p>
          <p class="text-2xl font-bold text-primary">{{ number_format($offreStats['count']) }}</p>
          <p class="text-2xs font-semibold text-secondary-container">{{ $growthLabel($offreStats['monthlyGrowth']) }}</p>
        </div>
      </div>
      <div class="card-glow flex items-center gap-4 rounded-2xl p-5">
        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-secondary-container/10">
          <span class="material-symbols-outlined text-2xl text-secondary-container">group</span>
        </div>
        <div>
          <p class="text-xs text-on-surface-variant">{{ __('Candidats') }}</p>
          <p class="text-2xl font-bold text-primary">{{ number_format($candidateStats['count']) }}</p>
          <p class="text-2xs font-semibold text-secondary-container">{{ $growthLabel($candidateStats['monthlyGrowth']) }}</p>
        </div>
      </div>
      <div class="card-glow flex items-center gap-4 rounded-2xl p-5">
        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-secondary-container/10">
          <span class="material-symbols-outlined text-2xl text-secondary-container">auto_awesome</span>
        </div>
        <div>
          <p class="text-xs text-on-surface-variant">{{ __('Candidatures') }}</p>
          <p class="text-2xl font-bold text-primary">{{ number_format($matchStats['count']) }}</p>
          <p class="text-2xs font-semibold text-secondary-container">{{ $growthLabel($matchStats['monthlyGrowth']) }}</p>
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
          <h3 class="font-bold font-serif text-primary">{{ __('Inscriptions') }}</h3>
          <span class="text-xs font-semibold uppercase tracking-wider text-outline">{{ $userChartData['currentYear'] }}</span>
        </div>
        <div class="flex h-48 items-end gap-1 px-1">
          @foreach ($userChartData['data'] as $value)
            <div class="chart-bar flex-1 rounded-t-md bg-secondary-container/90" style="height: {{ max(8, ($value / $userMax) * 100) }}%"></div>
          @endforeach
        </div>
        <div class="mt-3 flex justify-between text-2xs font-semibold uppercase tracking-wider text-outline">
          @foreach ($userChartData['labels'] as $label)
            <span>{{ __($label) }}</span>
          @endforeach
        </div>
      </div>
      <div class="card-glow rounded-2xl p-6">
        <div class="mb-6 flex items-center justify-between">
          <h3 class="font-bold font-serif text-primary">{{ __('Offres publiées') }}</h3>
          <span class="text-xs font-semibold uppercase tracking-wider text-outline">{{ $offerChartData['currentYear'] }}</span>
        </div>
        <div class="flex h-48 items-end gap-1 px-1">
          @foreach ($offerChartData['data'] as $value)
            <div class="chart-bar flex-1 rounded-t-md bg-secondary-container/70" style="height: {{ max(8, ($value / $offerMax) * 100) }}%"></div>
          @endforeach
        </div>
        <div class="mt-3 flex justify-between text-2xs font-semibold uppercase tracking-wider text-outline">
          @foreach ($offerChartData['labels'] as $label)
            <span>{{ __($label) }}</span>
          @endforeach
        </div>
      </div>
    </div>

    @role('admin')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
      <div class="card-glow overflow-hidden rounded-2xl lg:col-span-2">
        <div class="flex items-center justify-between border-b border-outline-variant/10 px-6 py-4">
          <h3 class="font-bold font-serif text-primary">{{ __('Derniers inscrits') }}</h3>
          <a href="{{ route('admin.users') }}" class="text-sm font-semibold text-secondary-container hover:underline">{{ __('Voir tout') }}</a>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead>
              <tr class="bg-surface-container-low/50">
                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline">{{ __('Utilisateur') }}</th>
                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline">{{ __('Rôle') }}</th>
                <th class="hidden px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline sm:table-cell">{{ __('Date') }}</th>
                <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline">{{ __('Statut') }}</th>
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
        <h3 class="mb-4 font-bold font-serif text-primary">{{ __('Actions rapides') }}</h3>
        <div class="space-y-3">
          <a href="{{ route('admin.users') }}" class="flex items-center gap-3 rounded-xl p-3 transition-colors hover:bg-surface-container-low">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-secondary-container/10"><span class="material-symbols-outlined text-secondary-container">person_add</span></div>
            <div><p class="text-sm font-semibold text-primary">{{ __('Utilisateurs') }}</p><p class="text-xs text-outline">{{ __('Gérer les comptes') }}</p></div>
          </a>
          <a href="{{ route('admin.offres') }}" class="flex items-center gap-3 rounded-xl p-3 transition-colors hover:bg-surface-container-low">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-secondary-container/10"><span class="material-symbols-outlined text-secondary-container">work</span></div>
            <div><p class="text-sm font-semibold text-primary">{{ __('Offres') }}</p><p class="text-xs text-outline">{{ __('Modérer les offres') }}</p></div>
          </a>
          <a href="{{ route('admin.abonnements') }}" class="flex items-center gap-3 rounded-xl p-3 transition-colors hover:bg-surface-container-low">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-secondary-container/10"><span class="material-symbols-outlined text-secondary-container">credit_card</span></div>
            <div><p class="text-sm font-semibold text-primary">{{ __('Abonnements') }}</p><p class="text-xs text-outline">{{ __('Suivre les plans') }}</p></div>
          </a>
          <a href="{{ route('admin.parametres') }}" class="flex items-center gap-3 rounded-xl p-3 transition-colors hover:bg-surface-container-low">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-surface-container"><span class="material-symbols-outlined text-outline">settings</span></div>
            <div><p class="text-sm font-semibold text-primary">{{ __('Paramètres') }}</p><p class="text-xs text-outline">{{ __('Configurer le site') }}</p></div>
          </a>
        </div>
      </div>
    </div>
    @endrole
    @role('Marketing')
      <div class="grid gap-6 md:grid-cols-2">
        <a href="{{ route('admin.statistiques') }}" class="card-glow rounded-2xl p-6 hover:border-secondary-container/30">
          <span class="material-symbols-outlined text-secondary-container">bar_chart</span>
          <h3 class="mt-3 font-bold text-primary">{{ __('Statistiques') }}</h3>
          <p class="mt-1 text-sm text-on-surface-variant">{{ __('Analyser les tendances agrégées de la plateforme.') }}</p>
        </a>
        <a href="{{ route('admin.newsletters') }}" class="card-glow rounded-2xl p-6 hover:border-secondary-container/30">
          <span class="material-symbols-outlined text-secondary-container">mail</span>
          <h3 class="mt-3 font-bold text-primary">{{ __('Campagnes') }}</h3>
          <p class="mt-1 text-sm text-on-surface-variant">{{ __('Préparer et envoyer les communications autorisées.') }}</p>
        </a>
      </div>
    @endrole
  </div>
@endsection
