@extends('layouts.admin')
@section('title', __('Statistiques'))
@section('page-title', __('Statistiques'))
@section('content')
  <div class="space-y-8">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 md:gap-6">
      <x-admin.stat-card :label="__('Entreprises')" :value="number_format($entrepriseStats['count'])" :hint="$entrepriseStats['monthlyGrowth'] === null ? __('Données antérieures insuffisantes') : (($entrepriseStats['monthlyGrowth'] >= 0 ? '↑ ' : '↓ ').abs($entrepriseStats['monthlyGrowth']).__('% vs mois dernier'))" />
      <x-admin.stat-card :label="__('Offres')" :value="number_format($offreStats['count'])" :hint="$offreStats['monthlyGrowth'] === null ? __('Données antérieures insuffisantes') : (($offreStats['monthlyGrowth'] >= 0 ? '↑ ' : '↓ ').abs($offreStats['monthlyGrowth']).__('% vs mois dernier'))" />
      <x-admin.stat-card :label="__('Nouveaux inscrits')" :value="array_sum($userChartData['data'])" :hint="__('Année ').$userChartData['currentYear']" />
      <x-admin.stat-card :label="__('Candidatures')" :value="number_format($totalPostulations)" :hint="__('Toutes périodes confondues')" />
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
      <div class="card-glow rounded-2xl p-6">
        <h3 class="mb-4 font-bold font-serif text-primary">{{ __('Utilisateurs par rôle') }}</h3>
        <div class="space-y-4">
          @foreach ($roleDistribution as $role)
            @php $percent = round(($role['count'] / $totalUsers) * 100); @endphp
            <div>
              <div class="mb-1 flex justify-between text-sm">
                <span class="font-semibold">{{ __($role['label']) }}</span>
                <span class="text-outline">{{ $percent }}%</span>
              </div>
              <div class="h-3 w-full rounded-full bg-surface-container">
                <div class="h-3 rounded-full {{ $role['color'] }}" style="width: {{ $percent }}%"></div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
      <div class="card-glow rounded-2xl p-6">
        <h3 class="mb-4 font-bold font-serif text-primary">{{ __('Candidatures par statut') }}</h3>
        <div class="space-y-4">
          @foreach ($applicationStatusDistribution as $status)
            @php $percent = round(($status['count'] / $totalPostulations) * 100); @endphp
            <div>
              <div class="mb-1 flex justify-between text-sm">
                <span class="font-semibold">{{ __($status['label']) }}</span>
                <span class="text-outline">{{ $percent }}%</span>
              </div>
              <div class="h-3 w-full rounded-full bg-surface-container">
                <div class="h-3 rounded-full {{ $status['color'] }}" style="width: {{ $percent }}%"></div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    <div class="card-glow overflow-hidden rounded-2xl">
      <div class="border-b border-outline-variant/10 px-6 py-4">
        <h3 class="font-bold font-serif text-primary">{{ __('Top entreprises recruteuses') }}</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead>
            <tr class="bg-surface-container-low/50">
              <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline">{{ __('Entreprise') }}</th>
              <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline">{{ __('Offres') }}</th>
              <th class="hidden px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline sm:table-cell">{{ __('Candidatures') }}</th>
              <th class="hidden px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline sm:table-cell">{{ __('Indice activité') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/5">
            @forelse ($topEntreprises as $entreprise)
              <tr class="transition-colors hover:bg-surface-container-low/30">
                <td class="px-6 py-3 font-semibold text-primary">
                  @role('admin')
                  @if(!empty($entreprise['user_id']))
                    <a href="{{ route('admin.users.show', $entreprise['user_id']) }}" class="hover:text-secondary-container transition-colors">{{ $entreprise['company_name'] }}</a>
                  @else
                    {{ $entreprise['company_name'] }}
                  @endif
                  @else
                    {{ $entreprise['company_name'] }}
                  @endrole
                </td>
                <td class="px-6 py-3">{{ $entreprise['offers_count'] }}</td>
                <td class="hidden px-6 py-3 sm:table-cell">{{ $entreprise['applications_count'] }}</td>
                <td class="hidden px-6 py-3 sm:table-cell"><span class="font-bold text-secondary-container">{{ $entreprise['matching_rate'] }}%</span></td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="px-6 py-10 text-center text-sm text-outline">{{ __('Aucune entreprise n’a encore suffisamment d’activité.') }}</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
