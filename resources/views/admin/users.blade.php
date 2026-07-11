@extends('layouts.admin')
@section('title', 'Utilisateurs')
@section('page-title', 'Utilisateurs')
@section('content')
  <div class="space-y-6">
    <form method="GET" action="{{ route('admin.users') }}" class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
        <div class="relative flex-1 sm:max-w-sm">
          <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">search</span>
          <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            class="w-full rounded-xl border border-outline-variant/20 bg-white/70 py-2.5 pl-10 pr-4 text-sm transition-all focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30"
            placeholder="Rechercher un utilisateur..."
          />
        </div>
        <select name="role" class="rounded-xl border border-outline-variant/20 bg-white/70 px-3 py-2.5 text-sm transition-all focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30">
          <option value="">Tous les rôles</option>
          <option value="candidat" @selected(request('role') === 'candidat')>Candidat</option>
          <option value="entreprise" @selected(request('role') === 'entreprise')>Entreprise</option>
          <option value="admin" @selected(request('role') === 'admin')>Admin</option>
          <option value="Marketing" @selected(request('role') === 'Marketing')>Marketing</option>
        </select>
        <select name="status" class="rounded-xl border border-outline-variant/20 bg-white/70 px-3 py-2.5 text-sm transition-all focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30">
          <option value="">Tous les statuts</option>
          <option value="Actif" @selected(request('status') === 'Actif')>Actif</option>
          <option value="Suspendu" @selected(request('status') === 'Suspendu')>Suspendu</option>
          <option value="approved" @selected(request('status') === 'approved')>Approuvé</option>
          <option value="pending" @selected(request('status') === 'pending')>En attente</option>
        </select>
      </div>
      <div class="flex items-center gap-2">
        <button type="submit" class="rounded-xl bg-secondary-container px-4 py-2.5 text-sm font-bold text-white transition-colors hover:bg-secondary">Filtrer</button>
        @if (request()->hasAny(['search', 'role', 'status']))
          <a href="{{ route('admin.users') }}" class="rounded-xl border border-outline-variant/20 px-4 py-2.5 text-sm font-semibold transition-colors hover:bg-surface-container-low">Réinitialiser</a>
        @endif
      </div>
    </form>

    <div class="card-glow overflow-hidden rounded-2xl">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead>
            <tr class="bg-surface-container-low/50">
              <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline">Utilisateur</th>
              <th class="hidden px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline md:table-cell">Email</th>
              <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline">Rôle</th>
              <th class="hidden px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline sm:table-cell">Date</th>
              <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-outline">Statut</th>
              <th class="px-6 py-3 text-right text-xs font-bold uppercase tracking-wider text-outline">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/5">
            @forelse ($users as $user)
              <tr class="transition-colors hover:bg-surface-container-low/30">
                <td class="px-6 py-3">
                  <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-secondary-container/20 text-xs font-bold text-secondary-container">
                      {{ $user->initials }}
                    </div>
                    <div>
                      <p class="font-semibold text-primary">
                        {{ trim($user->name . ' ' . ($user->prenom ?? '')) ?: ($user->entreprise->company_name ?? 'Utilisateur') }}
                      </p>
                      @if ($user->entreprise?->company_name)
                        <p class="text-xs text-outline">{{ $user->entreprise->company_name }}</p>
                      @endif
                    </div>
                  </div>
                </td>
                <td class="hidden px-6 py-3 text-outline md:table-cell">{{ $user->email }}</td>
                <td class="px-6 py-3">
                  <x-admin.status-badge :label="$user->admin_role_label" :color="$user->admin_role_color" />
                </td>
                <td class="hidden px-6 py-3 text-outline sm:table-cell">{{ optional($user->created_at)->format('d/m/Y') }}</td>
                <td class="px-6 py-3">
                  <x-admin.status-badge :label="$user->display_status_label" :color="$user->display_status_color" />
                </td>
                <td class="px-6 py-3">
                  <div class="flex items-center justify-end gap-1">
                    <x-admin.icon-action tag="a" :href="route('admin.users.show', $user)" icon="visibility" label="Voir" />
                    @if ($user->status === 'Suspendu')
                      <x-admin.icon-action
                        type="button"
                        icon="restart_alt"
                        label="Réactiver"
                        color="text-success"
                        hover="hover:bg-success-light"
                        class="js-admin-user-action"
                        data-url="{{ route('admin.users.reactivate', $user->id) }}"
                        data-label="réactiver"
                      />
                    @else
                      <x-admin.icon-action
                        type="button"
                        icon="block"
                        label="Suspendre"
                        color="text-warning"
                        hover="hover:bg-warning-light"
                        class="js-admin-user-action"
                        data-url="{{ route('admin.users.suspend', $user->id) }}"
                        data-label="suspendre"
                      />
                    @endif
                    <x-admin.icon-action
                      type="button"
                      icon="delete"
                      label="Supprimer"
                      color="text-error/80"
                      hover="hover:bg-error-light"
                      class="js-admin-user-action"
                      data-url="{{ route('admin.users.delete', $user->id) }}"
                      data-label="supprimer"
                    />
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="px-6 py-10 text-center text-sm text-outline">Aucun utilisateur ne correspond aux filtres actuels.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="flex flex-col gap-4 border-t border-outline-variant/10 px-6 py-4 text-sm sm:flex-row sm:items-center sm:justify-between">
        <span class="text-outline">
          {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} sur {{ $users->total() }} utilisateurs
        </span>
        {{ $users->withQueryString()->links('components.pagination.public-pagination') }}
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script>
    (() => {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

      document.querySelectorAll('.js-admin-user-action').forEach((button) => {
        button.addEventListener('click', async () => {
          const actionLabel = button.dataset.label || 'mettre à jour';
          const password = window.prompt(`Entrez votre mot de passe admin pour ${actionLabel} cet utilisateur :`);

          if (!password) return;

          try {
            const response = await fetch(button.dataset.url, {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
              },
              body: JSON.stringify({ password }),
            });

            const payload = await response.json();

            if (!response.ok || payload.success === false) {
              window.alert(payload.message || 'Action impossible pour le moment.');
              return;
            }

            window.location.reload();
          } catch (error) {
            window.alert('Une erreur est survenue pendant l’action admin.');
          }
        });
      });
    })();
  </script>
@endsection
