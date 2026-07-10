@extends('layouts.admin')
@section('title', 'Offres')
@section('page-title', 'Offres')
@section('content')
  <div class="space-y-6">
    <form method="GET" action="{{ route('admin.offres') }}" class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
        <div class="relative flex-1 sm:max-w-sm">
          <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">search</span>
          <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            class="w-full rounded-xl border border-outline-variant/20 bg-white/70 py-2.5 pl-10 pr-4 text-sm transition-all focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30"
            placeholder="Rechercher une offre..."
          />
        </div>
        <select name="status" class="rounded-xl border border-outline-variant/20 bg-white/70 px-3 py-2.5 text-sm transition-all focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30">
          <option value="">Tous les statuts</option>
          <option value="active" @selected(request('status') === 'active')>Publiée</option>
          <option value="desactive" @selected(request('status') === 'desactive')>Désactivée</option>
          <option value="brouillon" @selected(request('status') === 'brouillon')>Brouillon</option>
          <option value="expire" @selected(request('status') === 'expire')>Expirée</option>
        </select>
        <select name="entreprise_id" class="rounded-xl border border-outline-variant/20 bg-white/70 px-3 py-2.5 text-sm transition-all focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30">
          <option value="">Toutes les entreprises</option>
          @foreach ($entreprises as $entreprise)
            <option value="{{ $entreprise->id }}" @selected((string) request('entreprise_id') === (string) $entreprise->id)>{{ $entreprise->company_name }}</option>
          @endforeach
        </select>
      </div>
      <div class="flex items-center gap-2">
        <button type="submit" class="rounded-xl bg-secondary-container px-4 py-2.5 text-sm font-bold text-white transition-colors hover:bg-secondary">Filtrer</button>
        @if (request()->hasAny(['search', 'status', 'entreprise_id']))
          <a href="{{ route('admin.offres') }}" class="rounded-xl border border-outline-variant/20 px-4 py-2.5 text-sm font-semibold transition-colors hover:bg-surface-container-low">Réinitialiser</a>
        @endif
      </div>
    </form>

    <div class="card-glow overflow-hidden rounded-2xl">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead>
            <tr class="bg-surface-container-low/50">
              <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline">Offre</th>
              <th class="hidden px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline md:table-cell">Entreprise</th>
              <th class="hidden px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline sm:table-cell">Catégorie</th>
              <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline">Statut</th>
              <th class="px-6 py-3 text-right text-xs font-bold uppercase tracking-wider text-outline">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/5">
            @forelse ($offers as $offer)
              <tr class="transition-colors hover:bg-surface-container-low/30">
                <td class="px-6 py-3">
                  <p class="font-semibold text-primary">{{ $offer->poste ?: $offer->titre }}</p>
                  <p class="text-xs text-outline">
                    {{ $offer->localisation ?: 'Localisation non renseignée' }}
                    @if ($offer->salaire_min || $offer->salaire_max)
                      • {{ (int) $offer->salaire_min }}-{{ (int) $offer->salaire_max }}€
                    @endif
                  </p>
                </td>
                <td class="hidden px-6 py-3 text-outline md:table-cell">
                  @if($offer->entreprise)
                    <a href="{{ route('admin.users.show', $offer->entreprise->user_id) }}" class="hover:text-secondary-container transition-colors">{{ $offer->entreprise->company_name }}</a>
                  @else
                    Entreprise inconnue
                  @endif
                </td>
                <td class="hidden px-6 py-3 sm:table-cell">
                  <x-admin.status-badge
                    :label="$offer->categorie->nom ?? 'Non classée'"
                    color="bg-surface-container-low text-on-surface-variant"
                  />
                </td>
                <td class="px-6 py-3">
                  <x-admin.status-badge :label="$offer->admin_status_label" :color="$offer->admin_status_color" />
                </td>
                <td class="px-6 py-3">
                  <div class="flex items-center justify-end gap-1">
                    @if ($offer->slug)
                      <x-admin.icon-action tag="a" :href="route('job_infos', $offer)" icon="visibility" label="Voir" />
                    @endif
                    @if ($offer->admin_status === 'desactive' || $offer->admin_status === 'expire')
                      <x-admin.icon-action
                        type="button"
                        icon="restart_alt"
                        label="Réactiver"
                        color="text-success"
                        hover="hover:bg-success-light"
                        class="js-admin-offer-action"
                        data-url="{{ route('offres.reactivate', $offer->id) }}"
                        data-method="PATCH"
                      />
                    @else
                      <x-admin.icon-action
                        type="button"
                        icon="block"
                        label="Désactiver"
                        color="text-warning"
                        hover="hover:bg-warning-light"
                        class="js-admin-offer-action"
                        data-url="{{ route('offres.deactivate', $offer->id) }}"
                        data-method="PATCH"
                      />
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-6 py-10 text-center text-sm text-outline">Aucune offre ne correspond aux filtres actuels.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="flex flex-col gap-4 border-t border-outline-variant/10 px-6 py-4 text-sm sm:flex-row sm:items-center sm:justify-between">
        <span class="text-outline">
          {{ $offers->firstItem() ?? 0 }}-{{ $offers->lastItem() ?? 0 }} sur {{ $offers->total() }} offres
        </span>
        {{ $offers->withQueryString()->links('components.pagination.admin-pagination') }}
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script>
    (() => {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

      document.querySelectorAll('.js-admin-offer-action').forEach((button) => {
        button.addEventListener('click', async () => { const action = button.dataset.url.includes('deactivate') ? 'désactiver' : 'réactiver'; if (!confirm('Confirmer la ' + action + ' de cette offre ?')) return;
          try {
            const response = await fetch(button.dataset.url, {
              method: button.dataset.method || 'POST',
              headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
              },
            });

            const payload = await response.json();

            if (!response.ok || payload.success === false) {
              window.alert(payload.message || 'Action impossible pour le moment.');
              return;
            }

            window.location.reload();
          } catch (error) {
            window.alert('Une erreur est survenue pendant la mise à jour de l’offre.');
          }
        });
      });
    })();
  </script>
@endsection
