@extends('layouts.admin')
@section('title', 'Abonnements')
@section('page-title', 'Abonnements')
@section('content')
  <div class="space-y-8">
    @if(session('success'))<div class="rounded-2xl bg-green-50 px-5 py-4 text-sm font-semibold text-green-800">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="rounded-2xl bg-red-50 px-5 py-4 text-sm text-red-700">{{ $errors->first() }}</div>@endif
    <details class="card-glow rounded-2xl" @if($errors->any()) open @endif><summary class="flex cursor-pointer list-none items-center justify-between p-5 font-bold text-primary"><span class="flex items-center gap-2"><span class="material-symbols-outlined text-secondary-container">add_card</span> Créer un plan</span><span class="material-symbols-outlined">expand_more</span></summary><form method="POST" action="{{ route('admin.abonnements.store') }}" class="grid gap-4 border-t border-primary/5 p-5 md:grid-cols-2">@csrf
      <label class="text-xs font-bold text-primary">Nom<input name="nom" value="{{ old('nom') }}" required class="mt-2 w-full rounded-xl border-outline-variant/30 text-sm"></label><label class="text-xs font-bold text-primary">Prix CAD<input name="montant" value="{{ old('montant') }}" type="number" min="0" step="0.01" required class="mt-2 w-full rounded-xl border-outline-variant/30 text-sm"></label><label class="text-xs font-bold text-primary">Durée en jours<input name="duree" value="{{ old('duree',30) }}" type="number" min="1" required class="mt-2 w-full rounded-xl border-outline-variant/30 text-sm"></label><label class="text-xs font-bold text-primary">Couleur<input name="couleur" value="{{ old('couleur','#155e75') }}" class="mt-2 w-full rounded-xl border-outline-variant/30 text-sm"></label><label class="text-xs font-bold text-primary md:col-span-2">Description<textarea name="description" required rows="2" class="mt-2 w-full rounded-xl border-outline-variant/30 text-sm">{{ old('description') }}</textarea></label><fieldset class="space-y-2 md:col-span-2"><legend class="text-xs font-bold text-primary">Fonctionnalités</legend><p class="text-xs text-outline">Une fonctionnalité par champ, sans code JSON.</p>@for($i=0;$i<4;$i++)<input name="fonctionnalites[]" value="{{ old('fonctionnalites.'.$i, $i===0 ? 'Accès aux fonctionnalités du plan' : '') }}" @if($i===0) required @endif class="w-full rounded-xl border-outline-variant/30 text-sm" placeholder="{{ $i===0 ? 'Fonctionnalité principale' : 'Fonctionnalité supplémentaire (facultatif)' }}">@endfor</fieldset><div class="flex gap-4"><label class="text-sm text-primary"><input type="hidden" name="actif" value="0"><input type="checkbox" name="actif" value="1" checked class="rounded"> Actif</label><label class="text-sm text-primary"><input type="hidden" name="populaire" value="0"><input type="checkbox" name="populaire" value="1" class="rounded"> Populaire</label></div><button class="w-fit rounded-xl bg-primary-container px-5 py-2.5 text-sm font-bold text-white">Créer le plan</button>
    </form></details>
    @php
      $summaryMax = max(($abonnementSummaries->max('users_count') ?: 1), 1);
    @endphp

    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
      @forelse ($abonnementSummaries as $abonnement)
        <div class="card-glow rounded-2xl p-6 text-center {{ $abonnement->populaire ? 'ring-2 ring-secondary-container' : '' }}" x-data="{ editing: false }">
          <p class="mb-2 text-xs font-bold uppercase tracking-widest {{ $abonnement->populaire ? 'text-secondary-container' : 'text-outline' }}">{{ $abonnement->nom }}</p>
          <p class="mb-4 text-4xl font-bold text-primary">{{ rtrim(rtrim(number_format((float) $abonnement->montant, 2, ',', ' '), '0'), ',') }}$<span class="text-sm font-normal text-outline">/{{ $abonnement->duree }}</span></p>
          <p class="mb-2 text-sm text-on-surface-variant">{{ number_format($abonnement->users_count) }} utilisateurs</p>
          <div class="mb-4 h-2 w-full rounded-full bg-surface-container">
            <div class="h-2 rounded-full bg-secondary-container" style="width: {{ max(3, ($abonnement->users_count / $summaryMax) * 100) }}%"></div>
          </div>
          <button @click="editing = !editing" type="button" class="w-full rounded-xl {{ $abonnement->populaire ? 'bg-secondary-container text-white hover:bg-secondary' : 'bg-secondary-container/10 text-secondary-container hover:bg-secondary-container/20' }} py-2.5 text-sm font-bold transition-colors">Modifier</button>
          {{-- Edit form (hidden by default) --}}
          <form x-show="editing" x-cloak method="POST" action="{{ route('admin.abonnements.update', $abonnement) }}" class="mt-3 space-y-2 border-t border-outline-variant/10 pt-3">
            @csrf @method('PUT')
            <input type="text" name="nom" value="{{ $abonnement->nom }}" class="w-full rounded-lg border border-outline-variant/20 bg-white/70 px-3 py-1.5 text-xs focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30" placeholder="Nom" />
            <div class="flex gap-2">
              <input type="number" name="montant" value="{{ $abonnement->montant }}" step="0.01" min="0" class="flex-1 rounded-lg border border-outline-variant/20 bg-white/70 px-3 py-1.5 text-xs focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30" placeholder="Prix" />
              <input type="number" name="duree" value="{{ $abonnement->duree }}" min="1" class="w-20 rounded-lg border border-outline-variant/20 bg-white/70 px-3 py-1.5 text-xs focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30" placeholder="Jours" />
            </div>
            <textarea name="description" rows="2" required class="w-full rounded-lg border border-outline-variant/20 bg-white/70 px-3 py-1.5 text-xs" placeholder="Description">{{ $abonnement->description }}</textarea>
            <div class="space-y-1 text-left"><p class="text-2xs font-bold uppercase text-outline">Fonctionnalités</p>@foreach($abonnement->fonctionnalites as $feature)<input name="fonctionnalites[]" value="{{ $feature->nom }}" required class="w-full rounded-lg border border-outline-variant/20 bg-white/70 px-3 py-1.5 text-xs">@endforeach<input name="fonctionnalites[]" class="w-full rounded-lg border border-dashed border-outline-variant/30 bg-white/70 px-3 py-1.5 text-xs" placeholder="Ajouter une fonctionnalité"></div>
            <div class="flex gap-3 text-xs"><label><input type="hidden" name="actif" value="0"><input type="checkbox" name="actif" value="1" @checked($abonnement->actif) class="rounded"> Actif</label><label><input type="hidden" name="populaire" value="0"><input type="checkbox" name="populaire" value="1" @checked($abonnement->populaire) class="rounded"> Populaire</label><input type="hidden" name="couleur" value="{{ $abonnement->couleur }}"></div>
            <div class="flex gap-2">
              <button type="submit" class="flex-1 rounded-lg bg-success px-3 py-1.5 text-xs font-bold text-white hover:bg-success-dark transition-colors">Enregistrer</button>
              <button type="button" @click="editing = false" class="rounded-lg border border-outline-variant/20 px-3 py-1.5 text-xs text-outline hover:text-primary transition-colors">Annuler</button>
            </div>
          </form>
          @if($abonnement->users_count === 0)<form method="POST" action="{{ route('admin.abonnements.destroy',$abonnement) }}" class="mt-2" data-proxima-confirm="Supprimer définitivement ce plan sans historique ?" data-proxima-title="Supprimer le plan" data-proxima-confirm-label="Supprimer" data-proxima-tone="danger">@csrf @method('DELETE')<button class="text-xs font-bold text-error">Supprimer le plan</button></form>@else<p class="mt-2 text-2xs text-outline">Ce plan possède un historique : il peut être désactivé, mais pas supprimé.</p>@endif
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
      <x-admin.stat-card label="Revenu mensuel" :value="number_format((float) $monthlyRevenue, 0, ',', ' ').'$'" />
    </div>

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
              <th class="px-6 py-3 text-right text-xs font-bold uppercase tracking-wider text-outline">Actions</th>
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
                      <a href="{{ $subscriber ? route('admin.users.show', $subscriber) : '#' }}" class="font-semibold text-primary hover:text-secondary-container transition-colors">{{ trim(($subscriber->name ?? '') . ' ' . ($subscriber->prenom ?? '')) ?: ($subscriber->entreprise->company_name ?? 'Utilisateur') }}</a>
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
                  <x-admin.status-badge :label="$abonnement->effective_status" :color="$abonnement->status_color" />
                </td>
                <td class="px-6 py-3 text-right whitespace-nowrap">
                  @if ($abonnement->effective_status === 'En attente')
                    <form method="POST" action="{{ route('admin.user-subscriptions.activate', $abonnement) }}" class="inline">
                      @csrf @method('PATCH')
                      <button class="rounded-xl px-3 py-2 text-xs font-bold text-success-dark hover:bg-success-light">Activer</button>
                    </form>
                  @endif
                  @if (in_array($abonnement->effective_status, ['Actif', 'En attente'], true))
                    <form method="POST" action="{{ route('admin.user-subscriptions.cancel', $abonnement) }}" class="inline" data-proxima-confirm="Annuler cet abonnement ?" data-proxima-title="Annuler l’abonnement" data-proxima-confirm-label="Annuler l’abonnement" data-proxima-tone="danger">
                      @csrf @method('PATCH')
                      <button class="rounded-xl px-3 py-2 text-xs font-bold text-error-dark hover:bg-error-light">Annuler</button>
                    </form>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="px-6 py-10 text-center text-sm text-outline">Aucun abonnement ne correspond aux filtres actuels.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="flex flex-col gap-4 border-t border-outline-variant/10 px-6 py-4 text-sm sm:flex-row sm:items-center sm:justify-between">
        <span class="text-outline">{{ $abonnements->firstItem() ?? 0 }}-{{ $abonnements->lastItem() ?? 0 }} sur {{ $abonnements->total() }} abonnements</span>
        {{ $abonnements->withQueryString()->links('components.pagination.public-pagination') }}
      </div>
    </div>
  </div>
@endsection
