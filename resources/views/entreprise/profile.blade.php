@extends('layouts.entreprise')

@section('title', 'Profil de l’entreprise')

@section('content')
  @php
    $initials = strtoupper(substr($entreprise->company_name ?: $user->name, 0, 2));
    $logoUrl = match (true) {
      !$entreprise->logo => null,
      \Illuminate\Support\Str::startsWith($entreprise->logo, ['http://', 'https://', '/']) => $entreprise->logo,
      \Illuminate\Support\Str::startsWith($entreprise->logo, 'assets/') => asset($entreprise->logo),
      default => asset('storage/' . $entreprise->logo),
    };
  @endphp

  <section class="px-4 md:px-10 py-8">
    <div class="max-w-6xl mx-auto">
      <div class="flex flex-col md:flex-row md:items-end justify-between gap-5 mb-8">
        <div>
          <p class="text-xs font-black uppercase tracking-[0.2em] text-secondary-container mb-2">Paramètres</p>
          <h1 class="text-3xl md:text-4xl font-bold font-serif text-primary">Profil de l’entreprise</h1>
          <p class="mt-2 text-on-surface-variant max-w-2xl">Gardez vos informations publiques et vos coordonnées de recrutement à jour.</p>
        </div>
        <a href="{{ route('entreprise.profil', $entreprise) }}" target="_blank" class="inline-flex items-center justify-center gap-2 px-5 py-3 border border-primary/15 bg-white text-primary text-sm font-bold rounded-xl hover:bg-surface-container-low transition-colors">
          <span class="material-symbols-outlined text-lg">visibility</span>
          Voir le profil public
        </a>
      </div>

      @if (session('status') === 'enterprise-profile-updated')
        <div class="mb-6 rounded-2xl border border-success-light bg-success-light px-5 py-4 text-sm text-success-dark">
          Le profil de l’entreprise a été mis à jour.
        </div>
      @endif

      @if ($errors->any())
        <div class="mb-6 rounded-2xl border border-error-light bg-error-light px-5 py-4 text-sm text-error-dark">
          <p class="font-bold mb-1">Certaines informations doivent être corrigées.</p>
          <p>{{ $errors->first() }}</p>
        </div>
      @endif

      <div class="grid grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] gap-6">
        <aside class="space-y-6">
          <div class="card-glow rounded-2xl p-6 text-center">
            @if ($logoUrl)
              <img src="{{ $logoUrl }}" alt="Logo de {{ $entreprise->company_name }}" class="w-24 h-24 mx-auto rounded-2xl object-contain bg-white border border-outline-variant/20 p-2">
            @else
              <div class="w-24 h-24 mx-auto rounded-2xl bg-primary-container text-white flex items-center justify-center text-3xl font-bold">{{ $initials }}</div>
            @endif
            <h2 class="mt-4 text-lg font-bold font-serif text-primary">{{ $entreprise->company_name }}</h2>
            <p class="mt-1 text-sm text-on-surface-variant">{{ $user->email }}</p>
            <span class="mt-4 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold {{ $entreprise->verified_at ? 'bg-success-light text-success-dark' : 'bg-secondary-container/10 text-secondary-container' }}">
              <span class="material-symbols-outlined text-sm">{{ $entreprise->verified_at ? 'verified' : 'schedule' }}</span>
              {{ $entreprise->verified_at ? 'Entreprise vérifiée' : 'Validation en cours' }}
            </span>
          </div>

          <nav class="card-glow rounded-2xl p-3 space-y-1 text-sm">
            <a href="#identity" class="flex items-center gap-3 px-4 py-3 rounded-xl text-primary hover:bg-surface-container-low"><span class="material-symbols-outlined text-lg text-secondary-container">domain</span> Identité publique</a>
            <a href="#contact" class="flex items-center gap-3 px-4 py-3 rounded-xl text-primary hover:bg-surface-container-low"><span class="material-symbols-outlined text-lg text-secondary-container">contact_mail</span> Contact</a>
            <a href="#security" class="flex items-center gap-3 px-4 py-3 rounded-xl text-primary hover:bg-surface-container-low"><span class="material-symbols-outlined text-lg text-secondary-container">lock</span> Sécurité</a>
          </nav>
        </aside>

        <div class="space-y-6">
          <form method="POST" action="{{ route('entreprise.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PATCH')

            <section id="identity" class="card-glow rounded-2xl overflow-hidden scroll-mt-32">
              <div class="px-6 md:px-8 py-5 border-b border-outline-variant/10">
                <h2 class="text-xl font-bold font-serif text-primary">Identité publique</h2>
                <p class="text-sm text-on-surface-variant mt-1">Ces informations sont visibles par les candidats.</p>
              </div>
              <div class="p-6 md:p-8 space-y-5">
                <div>
                  <label for="company_name" class="block text-sm font-semibold text-primary mb-1.5">Nom de l’entreprise</label>
                  <input id="company_name" name="company_name" type="text" value="{{ old('company_name', $entreprise->company_name) }}" required class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0">
                </div>
                <div>
                  <label for="description" class="block text-sm font-semibold text-primary mb-1.5">Présentation</label>
                  <textarea id="description" name="description" rows="7" maxlength="3000" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0" placeholder="Présentez votre mission, votre culture et ce qui distingue votre entreprise.">{{ old('description', $entreprise->description) }}</textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                  <div>
                    <label for="website" class="block text-sm font-semibold text-primary mb-1.5">Site Internet</label>
                    <input id="website" name="website" type="url" value="{{ old('website', $entreprise->website) }}" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0" placeholder="https://entreprise.ca">
                  </div>
                  <div>
                    <label for="neq" class="block text-sm font-semibold text-primary mb-1.5">NEQ</label>
                    <input id="neq" name="neq" type="text" value="{{ old('neq', $entreprise->neq) }}" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0">
                  </div>
                </div>
                <div>
                  <label for="logo" class="block text-sm font-semibold text-primary mb-1.5">Logo</label>
                  <input id="logo" name="logo" type="file" accept=".jpg,.jpeg,.png,.webp" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary file:mr-4 file:border-0 file:bg-surface-container file:px-4 file:py-2 file:rounded-lg file:text-primary file:font-semibold">
                  <p class="text-xs text-outline mt-1.5">JPG, PNG ou WebP, maximum 2 Mo.</p>
                </div>
              </div>
            </section>

            <section id="contact" class="card-glow rounded-2xl overflow-hidden scroll-mt-32">
              <div class="px-6 md:px-8 py-5 border-b border-outline-variant/10">
                <h2 class="text-xl font-bold font-serif text-primary">Contact de recrutement</h2>
                <p class="text-sm text-on-surface-variant mt-1">Informations utilisées pour gérer le compte et les candidatures.</p>
              </div>
              <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                  <label for="name" class="block text-sm font-semibold text-primary mb-1.5">Nom du responsable</label>
                  <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0">
                </div>
                <div>
                  <label for="email" class="block text-sm font-semibold text-primary mb-1.5">Adresse courriel</label>
                  <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0">
                </div>
                <div>
                  <label for="telephone" class="block text-sm font-semibold text-primary mb-1.5">Téléphone</label>
                  <input id="telephone" name="telephone" type="text" value="{{ old('telephone', $user->telephone) }}" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0">
                </div>
                <div>
                  <label for="adresse" class="block text-sm font-semibold text-primary mb-1.5">Adresse</label>
                  <input id="adresse" name="adresse" type="text" value="{{ old('adresse', $user->adresse) }}" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0">
                </div>
              </div>
            </section>

            <div class="flex justify-end">
              <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-colors shadow-lg shadow-secondary-container/15">
                <span class="material-symbols-outlined text-lg">save</span>
                Enregistrer le profil
              </button>
            </div>
          </form>

          <section id="security" class="card-glow rounded-2xl overflow-hidden scroll-mt-32">
            <div class="px-6 md:px-8 py-5 border-b border-outline-variant/10">
              <h2 class="text-xl font-bold font-serif text-primary">Sécurité</h2>
              <p class="text-sm text-on-surface-variant mt-1">Utilisez un mot de passe unique et difficile à deviner.</p>
            </div>
            <form method="POST" action="{{ route('profile.change-password') }}" class="p-6 md:p-8 space-y-5">
              @csrf
              <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                  <label for="current_password" class="block text-sm font-semibold text-primary mb-1.5">Mot de passe actuel</label>
                  <input id="current_password" name="current_password" type="password" autocomplete="current-password" required class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0">
                </div>
                <div>
                  <label for="new_password" class="block text-sm font-semibold text-primary mb-1.5">Nouveau mot de passe</label>
                  <input id="new_password" name="new_password" type="password" autocomplete="new-password" required class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0">
                </div>
                <div>
                  <label for="new_password_confirmation" class="block text-sm font-semibold text-primary mb-1.5">Confirmation</label>
                  <input id="new_password_confirmation" name="new_password_confirmation" type="password" autocomplete="new-password" required class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0">
                </div>
              </div>
              <button type="submit" class="px-5 py-3 bg-primary-container text-white text-sm font-bold rounded-xl hover:bg-primary transition-colors">Mettre à jour le mot de passe</button>
            </form>
          </section>

          <section id="donnees" class="card-glow rounded-2xl overflow-hidden scroll-mt-32">
            <div class="px-6 md:px-8 py-5 border-b border-outline-variant/10">
              <h2 class="text-xl font-bold font-serif text-primary">Mes données personnelles</h2>
              <p class="text-sm text-on-surface-variant mt-1">Téléchargez une copie des données que ProximaJob détient sur votre compte : profil, entreprise, offres publiées, abonnements et notifications (format JSON).</p>
            </div>
            <div class="p-6 md:p-8">
              <a href="{{ route('profile.export-donnees') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-container text-white text-sm font-bold rounded-xl hover:bg-primary transition-colors">
                <span class="material-symbols-outlined text-lg">download</span> Exporter mes données
              </a>
            </div>
          </section>
        </div>
      </div>
    </div>
  </section>
@endsection
