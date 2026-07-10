@extends('layouts.admin')
@section('title', 'Abonnements')
@section('page-title', 'Abonnements')
@section('content')
  <div class="space-y-8">
    @php
      $summaryMax = max(($abonnementSummaries->max('users_count') ?: 1), 1);
    @endphp

    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
      @forelse ($abonnementSummaries->take(3) as $abonnement)
        <div class="card-glow rounded-2xl p-6 text-center {{ $abonnement->populaire ? 'ring-2 ring-secondary-container' : '' }}">
          <p class="mb-2 text-xs font-bold uppercase tracking-widest {{ $abonnement->populaire ? 'text-secondary-container' : 'text-outline' }}">{{ $abonnement->nom }}</p>
          <p class="mb-4 text-4xl font-bold text-primary">{{ rtrim(rtrim(number_format((float) $abonnement->montant, 2, ',', ' '), '0'), ',') }}€<span class="text-sm font-normal text-outline">/{{ $abonnement->duree }}</span></p>
          <p class="mb-2 text-sm text-on-surface-variant">{{ number_format($abonnement->users_count) }} utilisateurs</p>
          <div class="mb-4 h-2 w-full rounded-full bg-surface-container">
            <div class="h-2 rounded-full bg-secondary-container" style="width: {{ max(3, ($abonnement->users_count / $summaryMax) * 100) }}%"></div>
          </div>
          <button class="w-full rounded-xl {{ $abonnement->populaire ? 'bg-secondary-container text-white hover:bg-secondary' : 'bg-secondary-container/10 text-secondary-container hover:bg-secondary-container/20' }} py-2.5 text-sm font-bold transition-colors">Modifier</button>
        </div>
      @empty
        <div class="card-glow rounded-2xl p-6 text-sm text-outline md:col-span-3">Aucun abonnement disponible pour le moment.</div>
      @endforelse
    </div>

    <form method="GET" action="{{ route('admin.abonnements') }}" class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
        <select name="status" class="rounded-xl border border-outline-variant/20 bg-white/70 px-3 py-2.5 text-sm transition-all focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30">
          <option value="">Tous les statuts</option>
          <option value="active" @selected(request('status') === 'active')>Actifs</option>
          <option value="expired" @selected(request('status') === 'expired')>Expirés</option>
          <option value="pending" @selected(request('status') === 'pending')>En attente</option>
        </select>
        <select name="plan" class="rounded-xl border border-outline-variant/20 bg-white/70 px-3 py-2.5 text-sm transition-all focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30">
          <option value="">Tous les plans</option>
          @foreach ($plans as $planId => $planName)
            <option value="{{ $planId }}" @selected((string) request('plan') === (string) $planId)>{{ $planName }}</option>
          @endforeach
        </select>
      </div>
      <div class="flex items-center gap-2">
        <button type="submit" class="rounded-xl bg-secondary-container px-4 py-2.5 text-sm font-bold text-white transition-colors hover:bg-secondary">Filtrer</button>
        @if (request()->hasAny(['status', 'plan']))
          <a href="{{ route('admin.abonnements') }}" class="rounded-xl border border-outline-variant/20 px-4 py-2.5 text-sm font-semibold transition-colors hover:bg-surface-container-low">Réinitialiser</a>
        @endif
      </div>
    </form>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <x-admin.stat-card label="Actifs" :value="number_format($activeSubscriptions)" />
      <x-admin.stat-card label="Expirés" :value="number_format($expiredSubscriptions)" />
      <x-admin.stat-card label="À renouveler" :value="number_format($toRenew)" />
      <x-admin.stat-card label="Revenu mensuel" :value="number_format((float) $monthlyRevenue, 0, ',', ' ').'€'" />

    <div class="card-glow overflow-hidden rounded-2xl">
      <div class="flex items-center justify-between border-b border-outline-variant/10 px-6 py-4">
        <h3 class="font-bold font-serif text-primary">Abonnés récents</h3>
        <span class="text-xs font-semibold text-secondary-container">{{ $activeChange >= 0 ? '+' : '' }}{{ round($activeChange, 1) }}% vs mois dernier</span>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead>
            <tr class="bg-surface-container-low/50">
              <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline">Utilisateur</th>
              <th class="hidden px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline sm:table-cell">Plan</th>
              <th class="hidden px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline md:table-cell">Début</th>
              <th class="hidden px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline sm:table-cell">Expire</th>
              <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline">Statut</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/5">
            @forelse ($abonnements as $abonnement)
              @php
                $subscriber = $abonnement->user;
                $plan = $abonnement->abonnement;
              @endphp
              <tr class="transition-colors hover:bg-surface-container-low/30">
                <td class="px-6 py-3">
                  <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-secondary-container/20 text-xs font-bold text-secondary-container">
                      {{ $subscriber?->initials ?? 'NA' }}
                    </div>
                    <div>
                      <p class="font-semibold text-primary">{{ trim(($subscriber->name ?? '') . ' ' . ($subscriber->prenom ?? '')) ?: ($subscriber->entreprise->company_name ?? 'Utilisateur') }}</p>
                      @if ($subscriber?->entreprise?->company_name)
                        <p class="text-xs text-outline">{{ $subscriber->entreprise->company_name }}</p>
                      @endif
                    </div>
                  </div>
                </td>
                <td class="hidden px-6 py-3 sm:table-cell">
                  <x-admin.status-badge :label="$plan?->nom ?? 'Plan inconnu'" color="bg-secondary-container/10 text-secondary-container" />
                </td>
                <td class="hidden px-6 py-3 text-outline md:table-cell">{{ optional($abonnement->date_debut)->format('d/m/Y') ?: '—' }}</td>
                <td class="hidden px-6 py-3 text-outline sm:table-cell">{{ optional($abonnement->date_fin)->format('d/m/Y') ?: '—' }}</td>
                <td class="px-6 py-3">
                  <x-admin.status-badge :label="$abonnement->status" :color="$abonnement->status_color" />
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-6 py-10 text-center text-sm text-outline">Aucun abonnement ne correspond aux filtres actuels.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="flex flex-col gap-4 border-t border-outline-variant/10 px-6 py-4 text-sm sm:flex-row sm:items-center sm:justify-between">
        <span class="text-outline">{{ $abonnements->firstItem() ?? 0 }}-{{ $abonnements->lastItem() ?? 0 }} sur {{ $abonnements->total() }} abonnements</span>
        {{ $abonnements->links() }}
      </div>
    </div>
  </div>
@endsection
