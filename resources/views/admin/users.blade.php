@extends('layouts.admin')
@section('title', __('Utilisateurs'))
@section('page-title', __('Utilisateurs'))
@section('content')
  <div class="space-y-6">
    @if(session('success'))<div class="rounded-2xl bg-green-50 px-5 py-4 text-sm font-semibold text-green-800">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="rounded-2xl bg-red-50 px-5 py-4 text-sm text-red-700">{{ $errors->first() }}</div>@endif
    <details class="card-glow rounded-2xl" @if($errors->any()) open @endif>
      <summary class="flex cursor-pointer list-none items-center justify-between p-5 font-bold text-primary"><span class="flex items-center gap-2"><span class="material-symbols-outlined text-secondary-container">person_add</span> {{ __('Créer un compte') }}</span><span class="material-symbols-outlined">expand_more</span></summary>
      <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data" class="grid gap-4 border-t border-primary/5 p-5 md:grid-cols-2" x-data="{ role: '{{ old('role','candidat') }}' }">@csrf
        <label class="text-xs font-bold text-primary">{{ __('Nom') }}<input name="name" value="{{ old('name') }}" required class="mt-2 w-full rounded-xl border-outline-variant/30 text-sm"></label>
        <label class="text-xs font-bold text-primary">{{ __('Prénom') }}<input name="prenom" value="{{ old('prenom') }}" required class="mt-2 w-full rounded-xl border-outline-variant/30 text-sm"></label>
        <label class="text-xs font-bold text-primary">{{ __('Courriel') }}<input name="email" value="{{ old('email') }}" type="email" required class="mt-2 w-full rounded-xl border-outline-variant/30 text-sm"></label>
        <label class="text-xs font-bold text-primary">{{ __('Rôle') }}<select name="role" x-model="role" class="mt-2 w-full rounded-xl border-outline-variant/30 text-sm"><option value="candidat">{{ __('Candidat') }}</option><option value="entreprise">{{ __('Entreprise') }}</option><option value="Marketing">{{ __('Marketing') }}</option><option value="admin">Administrateur</option></select></label>
        <label class="text-xs font-bold text-primary">{{ __('Mot de passe') }}<input name="password" type="password" minlength="8" required class="mt-2 w-full rounded-xl border-outline-variant/30 text-sm"></label>
        <label class="text-xs font-bold text-primary">{{ __('Confirmation') }}<input name="password_confirmation" type="password" minlength="8" required class="mt-2 w-full rounded-xl border-outline-variant/30 text-sm"></label>
        <label class="text-xs font-bold text-primary">{{ __('Téléphone') }}<input name="telephone" value="{{ old('telephone') }}" class="mt-2 w-full rounded-xl border-outline-variant/30 text-sm"></label>
        <label class="text-xs font-bold text-primary">{{ __('Adresse') }}<input name="adresse" value="{{ old('adresse') }}" class="mt-2 w-full rounded-xl border-outline-variant/30 text-sm"></label>
        <div x-show="role === 'entreprise'" class="contents"><label class="text-xs font-bold text-primary">{{ __('Nom de l’entreprise') }}<input name="company_name" value="{{ old('company_name') }}" :required="role === 'entreprise'" class="mt-2 w-full rounded-xl border-outline-variant/30 text-sm"></label><label class="text-xs font-bold text-primary">{{ __('NEQ') }}<input name="neq" value="{{ old('neq') }}" :required="role === 'entreprise'" class="mt-2 w-full rounded-xl border-outline-variant/30 text-sm"></label><label class="text-xs font-bold text-primary">{{ __('Site Web') }}<input name="website" value="{{ old('website') }}" type="url" class="mt-2 w-full rounded-xl border-outline-variant/30 text-sm"></label><label class="text-xs font-bold text-primary">{{ __('Description') }}<textarea name="description" rows="2" class="mt-2 w-full rounded-xl border-outline-variant/30 text-sm">{{ old('description') }}</textarea></label></div>
        <button class="w-fit rounded-xl bg-primary-container px-5 py-2.5 text-sm font-bold text-white">{{ __('Créer le compte') }}</button>
      </form>
    </details>
    <form method="GET" action="{{ route('admin.users') }}" class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
        <div class="relative flex-1 sm:max-w-sm">
          <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">search</span>
          <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            class="w-full rounded-xl border border-outline-variant/20 bg-white/70 py-2.5 pl-10 pr-4 text-sm transition-all focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30"
            placeholder="{{ __('Rechercher un utilisateur...') }}"
          />
        </div>
        <select name="role" class="rounded-xl border border-outline-variant/20 bg-white/70 px-3 py-2.5 text-sm transition-all focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30">
          <option value="">{{ __('Tous les rôles') }}</option>
          <option value="candidat" @selected(request('role') === 'candidat')>{{ __('Candidat') }}</option>
          <option value="entreprise" @selected(request('role') === 'entreprise')>{{ __('Entreprise') }}</option>
          <option value="admin" @selected(request('role') === 'admin')>{{ __('Admin') }}</option>
          <option value="Marketing" @selected(request('role') === 'Marketing')>{{ __('Marketing') }}</option>
        </select>
        <select name="status" class="rounded-xl border border-outline-variant/20 bg-white/70 px-3 py-2.5 text-sm transition-all focus:border-secondary-container/50 focus:ring-2 focus:ring-accent/30">
          <option value="">{{ __('Tous les statuts') }}</option>
          <option value="Actif" @selected(request('status') === 'Actif')>{{ __('Actif') }}</option>
          <option value="Suspendu" @selected(request('status') === 'Suspendu')>{{ __('Suspendu') }}</option>
          <option value="approved" @selected(request('status') === 'approved')>{{ __('Approuvé') }}</option>
          <option value="pending" @selected(request('status') === 'pending')>{{ __('En attente') }}</option>
        </select>
      </div>
      <div class="flex items-center gap-2">
        <button type="submit" class="rounded-xl bg-secondary-container px-4 py-2.5 text-sm font-bold text-white transition-colors hover:bg-secondary">{{ __('Filtrer') }}</button>
        @if (request()->hasAny(['search', 'role', 'status']))
          <a href="{{ route('admin.users') }}" class="rounded-xl border border-outline-variant/20 px-4 py-2.5 text-sm font-semibold transition-colors hover:bg-surface-container-low">{{ __('Réinitialiser') }}</a>
        @endif
      </div>
    </form>

    <div class="card-glow overflow-hidden rounded-2xl">
      <div class="overflow-x-auto max-sm:[mask-image:linear-gradient(to_right,black_93%,transparent_100%)] max-sm:[-webkit-mask-image:linear-gradient(to_right,black_93%,transparent_100%)]">
        <table class="w-full text-left text-sm">
          <thead>
            <tr class="bg-surface-container-low/50">
              <th class="px-3 py-3 sm:px-6 text-xs font-bold uppercase tracking-wider text-outline">{{ __('Utilisateur') }}</th>
              <th class="hidden px-3 py-3 sm:px-6 text-xs font-bold uppercase tracking-wider text-outline md:table-cell">{{ __('Email') }}</th>
              <th class="px-3 py-3 sm:px-6 text-xs font-bold uppercase tracking-wider text-outline">{{ __('Rôle') }}</th>
              <th class="hidden px-3 py-3 sm:px-6 text-xs font-bold uppercase tracking-wider text-outline sm:table-cell">{{ __('Date') }}</th>
              <th class="px-3 py-3 sm:px-6 text-xs font-bold uppercase tracking-wider text-outline">{{ __('Statut') }}</th>
              <th class="px-3 py-3 sm:px-6 text-right text-xs font-bold uppercase tracking-wider text-outline">{{ __('Actions') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/5">
            @forelse ($users as $user)
              <tr class="transition-colors hover:bg-surface-container-low/30">
                <td class="px-3 py-3 sm:px-6">
                  <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-secondary-container/20 text-xs font-bold text-secondary-container">
                      {{ $user->initials }}
                    </div>
                    <div>
                      <p class="font-semibold text-primary">
                        {{ trim($user->name . ' ' . ($user->prenom ?? '')) ?: ($user->entreprise->company_name ?? __('Utilisateur')) }}
                      </p>
                      @if ($user->entreprise?->company_name)
                        <p class="text-xs text-outline">{{ $user->entreprise->company_name }}</p>
                      @endif
                    </div>
                  </div>
                </td>
                <td class="hidden px-3 py-3 sm:px-6 text-outline md:table-cell">{{ $user->email }}</td>
                <td class="px-3 py-3 sm:px-6">
                  <x-admin.status-badge :label="$user->admin_role_label" :color="$user->admin_role_color" />
                </td>
                <td class="hidden px-3 py-3 sm:px-6 text-outline sm:table-cell">{{ optional($user->created_at)->format('d/m/Y') }}</td>
                <td class="px-3 py-3 sm:px-6">
                  <x-admin.status-badge :label="$user->display_status_label" :color="$user->display_status_color" />
                </td>
                <td class="px-3 py-3 sm:px-6">
                  <div class="flex items-center justify-end gap-1">
                    <x-admin.icon-action tag="a" :href="route('admin.users.show', $user)" icon="visibility" :label="__('Voir')" />
                    @if ($user->status === 'Suspendu')
                      <x-admin.icon-action
                        type="button"
                        icon="restart_alt"
                        :label="__('Réactiver')"
                        color="text-success"
                        hover="hover:bg-success-light"
                        class="js-admin-user-action"
                        data-url="{{ route('admin.users.reactivate', $user->id) }}"
                        data-label="{{ __('réactiver') }}"
                      />
                    @else
                      <x-admin.icon-action
                        type="button"
                        icon="block"
                        :label="__('Suspendre')"
                        color="text-warning"
                        hover="hover:bg-warning-light"
                        class="js-admin-user-action"
                        data-url="{{ route('admin.users.suspend', $user->id) }}"
                        data-label="{{ __('suspendre') }}"
                      />
                    @endif
                    <x-admin.icon-action
                      type="button"
                      icon="delete"
                      :label="__('Supprimer')"
                      color="text-error/80"
                      hover="hover:bg-error-light"
                      class="js-admin-user-action"
                      data-url="{{ route('admin.users.delete', $user->id) }}"
                      data-label="{{ __('supprimer') }}"
                    />
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="px-6 py-10 text-center text-sm text-outline">{{ __('Aucun utilisateur ne correspond aux filtres actuels.') }}</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="flex flex-col gap-4 border-t border-outline-variant/10 px-6 py-4 text-sm sm:flex-row sm:items-center sm:justify-between">
        <span class="text-outline">
          {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} {{ __('sur') }} {{ $users->total() }} {{ __('utilisateurs') }}
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
          const actionLabel = button.dataset.label || '{{ __('mettre à jour') }}';
          const password = await window.ProximaUI.prompt(`{{ __('Entrez votre mot de passe administrateur pour') }} ${actionLabel} {{ __('cet utilisateur.') }}`, {
            title: '{{ __('Vérification administrateur') }}',
            inputLabel: '{{ __('Mot de passe administrateur') }}',
            confirmLabel: '{{ __('Continuer') }}',
            tone: actionLabel === '{{ __('réactiver') }}' ? 'default' : 'danger',
          });

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
              window.ProximaUI.notify(payload.message || '{{ __('Action impossible pour le moment.') }}', 'error');
              return;
            }

            window.location.reload();
          } catch (error) {
            window.ProximaUI.notify('{{ __('Une erreur est survenue pendant l’action administrateur.') }}', 'error');
          }
        });
      });
    })();
  </script>
@endsection
