@extends('layouts.admin')
@section('title', 'Paramètres')
@section('page-title', 'Paramètres')
@section('content')
  <div class="space-y-6">
    <div class="flex items-center gap-0 overflow-x-auto border-b border-outline-variant/10">
      <button class="tab-btn active whitespace-nowrap px-5 py-3 text-sm font-bold text-primary transition-colors" data-tab="general">Général</button>
      <button class="tab-btn whitespace-nowrap px-5 py-3 text-sm font-semibold text-outline transition-colors hover:text-primary" data-tab="appearance">Apparence</button>
      <button class="tab-btn whitespace-nowrap px-5 py-3 text-sm font-semibold text-outline transition-colors hover:text-primary" data-tab="email">Email</button>
      <button class="tab-btn whitespace-nowrap px-5 py-3 text-sm font-semibold text-outline transition-colors hover:text-primary" data-tab="security">Sécurité</button>
    </div>

    <div class="tab-panel active space-y-6" data-tab="general">
      <x-admin.panel title="Informations du site" body-class="space-y-5">
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
          <x-admin.form-field label="Nom du site" name="site-name">
            <input id="site-name" type="text" class="w-full rounded-xl border border-outline-variant/20 bg-white/70 px-4 py-3 text-sm transition-all focus:border-secondary-container/50 focus:ring-0" value="ProximaJob" />
          </x-admin.form-field>
          <x-admin.form-field label="Slogan" name="site-tagline">
            <input id="site-tagline" type="text" class="w-full rounded-xl border border-outline-variant/20 bg-white/70 px-4 py-3 text-sm transition-all focus:border-secondary-container/50 focus:ring-0" value="IA Concierge pour votre carrière" />
          </x-admin.form-field>
        </div>
        <x-admin.form-field label="Description" name="site-description">
          <textarea id="site-description" rows="3" class="w-full resize-none rounded-xl border border-outline-variant/20 bg-white/70 px-4 py-3 text-sm transition-all focus:border-secondary-container/50 focus:ring-0">Plateforme de matching emploi propulsée par l'intelligence artificielle.</textarea>
        </x-admin.form-field>
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
          <x-admin.form-field label="Email de contact" name="site-email">
            <input id="site-email" type="email" class="w-full rounded-xl border border-outline-variant/20 bg-white/70 px-4 py-3 text-sm transition-all focus:border-secondary-container/50 focus:ring-0" value="infos@proximajob.com" />
          </x-admin.form-field>
          <x-admin.form-field label="Téléphone" name="site-phone">
            <input id="site-phone" type="text" class="w-full rounded-xl border border-outline-variant/20 bg-white/70 px-4 py-3 text-sm transition-all focus:border-secondary-container/50 focus:ring-0" value="+33 1 23 45 67 89" />
          </x-admin.form-field>
        </div>
        <button class="rounded-xl bg-secondary-container px-6 py-3 text-sm font-bold text-white transition-colors hover:bg-secondary">Enregistrer</button>
      </x-admin.panel>
    </div>

    <div class="tab-panel space-y-6" data-tab="appearance">
      <x-admin.panel title="Logo & Favicon" body-class="space-y-5">
        <div class="flex items-center gap-6">
          <div class="flex h-24 w-24 items-center justify-center rounded-2xl border-2 border-dashed border-outline-variant/30 bg-surface-container">
            <span class="material-symbols-outlined text-4xl text-outline">image</span>
          </div>
          <div>
            <p class="text-sm font-semibold text-primary">Logo du site</p>
            <p class="mt-1 text-xs text-outline">PNG ou SVG — recommandé 200×200px</p>
            <button class="mt-3 rounded-xl border border-outline-variant/30 px-4 py-2 text-sm font-semibold transition-colors hover:bg-surface-container-low">Téléverser</button>
          </div>
        </div>
        <div class="flex items-center gap-6">
          <div class="flex h-16 w-16 items-center justify-center rounded-xl border-2 border-dashed border-outline-variant/30 bg-surface-container">
            <span class="material-symbols-outlined text-2xl text-outline">image</span>
          </div>
          <div>
            <p class="text-sm font-semibold text-primary">Favicon</p>
            <p class="mt-1 text-xs text-outline">ICO ou PNG — 32×32px</p>
            <button class="mt-3 rounded-xl border border-outline-variant/30 px-4 py-2 text-sm font-semibold transition-colors hover:bg-surface-container-low">Téléverser</button>
          </div>
        </div>
        <button class="rounded-xl bg-secondary-container px-6 py-3 text-sm font-bold text-white transition-colors hover:bg-secondary">Enregistrer</button>
      </x-admin.panel>
    </div>

    <div class="tab-panel space-y-6" data-tab="email">
      <x-admin.panel title="Configuration SMTP" body-class="space-y-5">
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
          <x-admin.form-field label="Hôte SMTP" name="smtp-host">
            <input id="smtp-host" type="text" class="w-full rounded-xl border border-outline-variant/20 bg-white/70 px-4 py-3 text-sm transition-all focus:border-secondary-container/50 focus:ring-0" value="smtp.mailgun.org" />
          </x-admin.form-field>
          <x-admin.form-field label="Port" name="smtp-port">
            <input id="smtp-port" type="number" class="w-full rounded-xl border border-outline-variant/20 bg-white/70 px-4 py-3 text-sm transition-all focus:border-secondary-container/50 focus:ring-0" value="587" />
          </x-admin.form-field>
        </div>
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
          <x-admin.form-field label="Utilisateur" name="smtp-user">
            <input id="smtp-user" type="text" class="w-full rounded-xl border border-outline-variant/20 bg-white/70 px-4 py-3 text-sm transition-all focus:border-secondary-container/50 focus:ring-0" value="postmaster@proximajob.com" />
          </x-admin.form-field>
          <x-admin.form-field label="Mot de passe" name="smtp-password">
            <input id="smtp-password" type="password" class="w-full rounded-xl border border-outline-variant/20 bg-white/70 px-4 py-3 text-sm transition-all focus:border-secondary-container/50 focus:ring-0" value="••••••••••" />
          </x-admin.form-field>
        </div>
        <button class="rounded-xl bg-secondary-container px-6 py-3 text-sm font-bold text-white transition-colors hover:bg-secondary">Tester la connexion</button>
      </x-admin.panel>
    </div>

    <div class="tab-panel space-y-6" data-tab="security">
      <x-admin.panel title="Sécurité" body-class="space-y-5">
        <div class="flex items-center justify-between rounded-xl bg-surface-container-low p-4">
          <div>
            <p class="text-sm font-bold text-primary">Authentification à deux facteurs</p>
            <p class="text-xs text-on-surface-variant">Exiger le 2FA pour tous les comptes admin</p>
          </div>
          <label class="relative inline-flex cursor-pointer items-center">
            <input type="checkbox" class="peer sr-only" checked />
            <div class="h-6 w-10 rounded-full bg-surface-container transition-colors peer-checked:bg-secondary-container"></div>
            <div class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white transition-transform peer-checked:translate-x-4"></div>
          </label>
        </div>
        <div class="flex items-center justify-between rounded-xl bg-surface-container-low p-4">
          <div>
            <p class="text-sm font-bold text-primary">Vérification email obligatoire</p>
            <p class="text-xs text-on-surface-variant">Les nouveaux comptes doivent vérifier leur email</p>
          </div>
          <label class="relative inline-flex cursor-pointer items-center">
            <input type="checkbox" class="peer sr-only" checked />
            <div class="h-6 w-10 rounded-full bg-surface-container transition-colors peer-checked:bg-secondary-container"></div>
            <div class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white transition-transform peer-checked:translate-x-4"></div>
          </label>
        </div>
        <div class="flex items-center justify-between rounded-xl bg-surface-container-low p-4">
          <div>
            <p class="text-sm font-bold text-primary">Validation manuelle des entreprises</p>
            <p class="text-xs text-on-surface-variant">Un admin doit approuver les nouveaux comptes entreprise</p>
          </div>
          <label class="relative inline-flex cursor-pointer items-center">
            <input type="checkbox" class="peer sr-only" />
            <div class="h-6 w-10 rounded-full bg-surface-container transition-colors peer-checked:bg-secondary-container"></div>
            <div class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white transition-transform peer-checked:translate-x-4"></div>
          </label>
        </div>
        <button class="rounded-xl bg-secondary-container px-6 py-3 text-sm font-bold text-white transition-colors hover:bg-secondary">Enregistrer</button>
      </x-admin.panel>
    </div>
  </div>
@endsection
