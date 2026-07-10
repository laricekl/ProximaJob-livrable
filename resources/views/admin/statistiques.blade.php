@extends('layouts.admin')
@section('title', 'Statistiques')
@section('page-title', 'Statistiques')
@section('content')
  <div class="space-y-8">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 md:gap-6">
      <x-admin.stat-card label="Entreprises" :value="number_format($entrepriseStats['count'])" :hint="($entrepriseStats['monthlyGrowth'] >= 0 ? '↑ ' : '↓ ').abs($entrepriseStats['monthlyGrowth']).'% vs mois dernier'" />
      <x-admin.stat-card label="Offres" :value="number_format($offreStats['count'])" :hint="($offreStats['monthlyGrowth'] >= 0 ? '↑ ' : '↓ ').abs($offreStats['monthlyGrowth']).'% vs mois dernier'" />
      <x-admin.stat-card label="Nouveaux inscrits" :value="array_sum($userChartData['data'])" :hint="'Année '.$userChartData['currentYear']" />
      <x-admin.stat-card label="Candidatures" :value="number_format($totalPostulations)" hint="Toutes périodes confondues" />
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
      <div class="card-glow rounded-2xl p-6">
        <h3 class="mb-4 font-bold font-serif text-primary">Utilisateurs par rôle</h3>
        <div class="space-y-4">
          @foreach ($roleDistribution as $role)
            @php $percent = round(($role['count'] / $totalUsers) * 100); @endphp
            <div>
              <div class="mb-1 flex justify-between text-sm">
                <span class="font-semibold">{{ $role['label'] }}</span>
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
        <h3 class="mb-4 font-bold font-serif text-primary">Candidatures par statut</h3>
        <div class="space-y-4">
          @foreach ($applicationStatusDistribution as $status)
            @php $percent = round(($status['count'] / $totalPostulations) * 100); @endphp
            <div>
              <div class="mb-1 flex justify-between text-sm">
                <span class="font-semibold">{{ $status['label'] }}</span>
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
        <h3 class="font-bold font-serif text-primary">Top entreprises recruteuses</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead>
            <tr class="bg-surface-container-low/50">
              <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline">Entreprise</th>
              <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline">Offres</th>
              <th class="hidden px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline sm:table-cell">Candidatures</th>
              <th class="hidden px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline sm:table-cell">Indice activité</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/5">
            @forelse ($topEntreprises as $entreprise)
              <tr class="transition-colors hover:bg-surface-container-low/30">
                <td class="px-6 py-3 font-semibold text-primary">
                  @if(!empty($entreprise['user_id']))
                    <a href="{{ route('admin.users.show', $entreprise['user_id']) }}" class="hover:text-secondary-container transition-colors">{{ $entreprise['company_name'] }}</a>
                  @else
                    {{ $entreprise['company_name'] }}
                  @endif
                </td>
                <td class="px-6 py-3">{{ $entreprise['offers_count'] }}</td>
                <td class="hidden px-6 py-3 sm:table-cell">{{ $entreprise['applications_count'] }}</td>
                <td class="hidden px-6 py-3 sm:table-cell"><span class="font-bold text-secondary-container">{{ $entreprise['matching_rate'] }}%</span></td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="px-6 py-10 text-center text-sm text-outline">Aucune entreprise n’a encore suffisamment d’activité.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
