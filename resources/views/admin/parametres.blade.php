@extends('layouts.admin')
@section('title', 'Paramètres')
@section('page-title', 'Paramètres')
@section('content')
  @php
    $currentLogo = $settings->logo_url;
    $currentFavicon = $settings->favicon_url;
  @endphp
  <div class="space-y-6">
    <x-admin.flash />

    @if ($errors->any())
      <div class="rounded-2xl border border-error-light bg-error-light px-4 py-3 text-sm text-error-dark">
        <p class="font-semibold">Certains champs doivent etre corriges.</p>
        <ul class="mt-2 list-disc pl-5">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="flex items-center gap-0 overflow-x-auto border-b border-outline-variant/10">
      <button class="tab-btn active whitespace-nowrap px-5 py-3 text-sm font-bold text-primary transition-colors" data-tab="general">Général</button>
      <button class="tab-btn whitespace-nowrap px-5 py-3 text-sm font-semibold text-outline transition-colors hover:text-primary" data-tab="appearance">Apparence</button>
      <button class="tab-btn whitespace-nowrap px-5 py-3 text-sm font-semibold text-outline transition-colors hover:text-primary" data-tab="contact">Contact</button>
      <button class="tab-btn whitespace-nowrap px-5 py-3 text-sm font-semibold text-outline transition-colors hover:text-primary" data-tab="delivery">Livraison</button>
    </div>

    <div class="tab-panel active space-y-6" data-tab="general">
      <x-admin.panel title="Informations du site" body-class="space-y-5">
        <form action="{{ route('parametres.update-general') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
          @csrf
          <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <x-admin.form-field label="Nom du site" name="site_nom">
              <input id="site_nom" name="site_nom" type="text" class="w-full rounded-xl border border-outline-variant/20 bg-white/70 px-4 py-3 text-sm transition-all focus:border-secondary-container/50 focus:ring-0" value="{{ old('site_nom', $settings->site_nom) }}" />
            </x-admin.form-field>
            <x-admin.form-field label="Fuseau horaire" name="timezone">
              <select id="timezone" name="timezone" class="w-full rounded-xl border border-outline-variant/20 bg-white/70 px-4 py-3 text-sm transition-all focus:border-secondary-container/50 focus:ring-0">
                @foreach ($timezones as $value => $label)
                  <option value="{{ $value }}" @selected(old('timezone', $settings->timezone) === $value)>{{ $label }}</option>
                @endforeach
              </select>
            </x-admin.form-field>
          </div>
          <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <x-admin.form-field label="Email de contact" name="email">
              <input id="email" name="email" type="email" class="w-full rounded-xl border border-outline-variant/20 bg-white/70 px-4 py-3 text-sm transition-all focus:border-secondary-container/50 focus:ring-0" value="{{ old('email', $settings->email) }}" />
            </x-admin.form-field>
            <x-admin.form-field label="Téléphone" name="tel">
              <input id="tel" name="tel" type="text" class="w-full rounded-xl border border-outline-variant/20 bg-white/70 px-4 py-3 text-sm transition-all focus:border-secondary-container/50 focus:ring-0" value="{{ old('tel', $settings->tel) }}" />
            </x-admin.form-field>
          </div>
          <button class="rounded-xl bg-secondary-container px-6 py-3 text-sm font-bold text-white transition-colors hover:bg-secondary">Enregistrer</button>
        </form>
      </x-admin.panel>
    </div>

    <div class="tab-panel space-y-6" data-tab="appearance">
      <x-admin.panel title="Logo & Favicon" body-class="space-y-5">
        <form action="{{ route('parametres.update-general') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
          @csrf
          <input type="hidden" name="site_nom" value="{{ old('site_nom', $settings->site_nom) }}">
          <input type="hidden" name="email" value="{{ old('email', $settings->email) }}">
          <input type="hidden" name="tel" value="{{ old('tel', $settings->tel) }}">
          <input type="hidden" name="localisation" value="{{ old('localisation', $settings->localisation) }}">
          <input type="hidden" name="timezone" value="{{ old('timezone', $settings->timezone) }}">
          <input type="hidden" name="map_embed_url" value="{{ old('map_embed_url', $settings->map_embed_url) }}">
          <input type="hidden" name="map_zoom" value="{{ old('map_zoom', $settings->map_zoom) }}">

          <div class="flex flex-col gap-4 rounded-2xl border border-outline-variant/20 bg-surface-container-low p-4 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-4">
              <div class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-2xl border border-outline-variant/20 bg-white">
                @if ($currentLogo)
                  <img src="{{ $currentLogo }}" alt="Logo actuel" class="h-full w-full object-contain p-2">
                @else
                  <span class="material-symbols-outlined text-4xl text-outline">image</span>
                @endif
              </div>
              <div>
                <p class="text-sm font-semibold text-primary">Logo du site</p>
                <p class="mt-1 text-xs text-outline">PNG ou SVG, utilise sur la navigation et le footer.</p>
                <input id="logo" name="logo" type="file" accept=".png,.jpg,.jpeg,.svg" class="mt-3 block text-sm text-primary file:mr-4 file:rounded-xl file:border-0 file:bg-secondary-container file:px-4 file:py-2 file:font-semibold file:text-white">
              </div>
            </div>
            @if ($currentLogo)
              <button form="remove-logo-form" class="rounded-xl border border-error-light px-4 py-2 text-sm font-semibold text-error transition-colors hover:bg-error-light">Supprimer</button>
            @endif
          </div>

          <div class="flex flex-col gap-4 rounded-2xl border border-outline-variant/20 bg-surface-container-low p-4 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-4">
              <div class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-xl border border-outline-variant/20 bg-white">
                @if ($currentFavicon)
                  <img src="{{ $currentFavicon }}" alt="Favicon actuel" class="h-full w-full object-contain p-2">
                @else
                  <span class="material-symbols-outlined text-2xl text-outline">image</span>
                @endif
              </div>
              <div>
                <p class="text-sm font-semibold text-primary">Favicon</p>
                <p class="mt-1 text-xs text-outline">ICO ou PNG, affiche dans l’onglet du navigateur.</p>
                <input id="favicon" name="favicon" type="file" accept=".ico,.png,.jpg,.jpeg" class="mt-3 block text-sm text-primary file:mr-4 file:rounded-xl file:border-0 file:bg-secondary-container file:px-4 file:py-2 file:font-semibold file:text-white">
              </div>
            </div>
            @if ($currentFavicon)
              <button form="remove-favicon-form" class="rounded-xl border border-error-light px-4 py-2 text-sm font-semibold text-error transition-colors hover:bg-error-light">Supprimer</button>
            @endif
          </div>

          <button class="rounded-xl bg-secondary-container px-6 py-3 text-sm font-bold text-white transition-colors hover:bg-secondary">Mettre a jour le branding</button>
        </form>

        @if ($currentLogo)
          <form id="remove-logo-form" action="{{ route('parametres.remove-logo') }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
          </form>
        @endif

        @if ($currentFavicon)
          <form id="remove-favicon-form" action="{{ route('parametres.remove-favicon') }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
          </form>
        @endif
      </x-admin.panel>
    </div>

    <div class="tab-panel space-y-6" data-tab="contact">
      <x-admin.panel title="Contact & localisation" body-class="space-y-5">
        <form action="{{ route('parametres.update-general') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
          @csrf
          <input type="hidden" name="site_nom" value="{{ old('site_nom', $settings->site_nom) }}">
          <input type="hidden" name="email" value="{{ old('email', $settings->email) }}">
          <input type="hidden" name="tel" value="{{ old('tel', $settings->tel) }}">
          <input type="hidden" name="timezone" value="{{ old('timezone', $settings->timezone) }}">

          <x-admin.form-field label="Adresse ou localisation" name="localisation">
            <input id="localisation" name="localisation" type="text" class="w-full rounded-xl border border-outline-variant/20 bg-white/70 px-4 py-3 text-sm transition-all focus:border-secondary-container/50 focus:ring-0" value="{{ old('localisation', $settings->localisation) }}" />
          </x-admin.form-field>

          <x-admin.form-field label="Lien d’integration Google Maps" name="map_embed_url">
            <textarea id="map_embed_url" name="map_embed_url" rows="4" class="w-full resize-none rounded-xl border border-outline-variant/20 bg-white/70 px-4 py-3 text-sm transition-all focus:border-secondary-container/50 focus:ring-0">{{ old('map_embed_url', $settings->map_embed_url) }}</textarea>
          </x-admin.form-field>

          <x-admin.form-field label="Zoom de la carte" name="map_zoom">
            <input id="map_zoom" name="map_zoom" type="number" min="1" max="20" class="w-full rounded-xl border border-outline-variant/20 bg-white/70 px-4 py-3 text-sm transition-all focus:border-secondary-container/50 focus:ring-0" value="{{ old('map_zoom', $settings->map_zoom ?? 15) }}" />
          </x-admin.form-field>

          <button class="rounded-xl bg-secondary-container px-6 py-3 text-sm font-bold text-white transition-colors hover:bg-secondary">Mettre a jour le contact</button>
        </form>
      </x-admin.panel>
    </div>

    <div class="tab-panel space-y-6" data-tab="delivery">
      <x-admin.panel title="Livraison & suite" body-class="space-y-5">
        <div class="rounded-2xl border border-outline-variant/20 bg-surface-container-low p-5">
          <p class="text-sm font-semibold text-primary">Cette brique centralise maintenant le branding et le contact publics.</p>
          <p class="mt-2 text-sm text-on-surface-variant">Etapes logiques suivantes: textes homepage, footer avance, reseaux sociaux, configuration SMTP et options de moderation.</p>
        </div>
        <div class="flex items-center justify-between rounded-xl bg-surface-container-low p-4">
          <div>
            <p class="text-sm font-bold text-primary">Authentification à deux facteurs</p>
            <p class="text-xs text-on-surface-variant">Option conservee en vitrine pour la prochaine phase admin.</p>
          </div>
          <label class="relative inline-flex cursor-pointer items-center">
            <input type="checkbox" class="peer sr-only" checked disabled />
            <div class="h-6 w-10 rounded-full bg-surface-container transition-colors peer-checked:bg-secondary-container"></div>
            <div class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white transition-transform peer-checked:translate-x-4"></div>
          </label>
        </div>
        <div class="flex items-center justify-between rounded-xl bg-surface-container-low p-4">
          <div>
            <p class="text-sm font-bold text-primary">Vérification email obligatoire</p>
            <p class="text-xs text-on-surface-variant">Les nouveaux comptes doivent vérifier leur email.</p>
          </div>
          <label class="relative inline-flex cursor-pointer items-center">
            <input type="checkbox" class="peer sr-only" checked disabled />
            <div class="h-6 w-10 rounded-full bg-surface-container transition-colors peer-checked:bg-secondary-container"></div>
            <div class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white transition-transform peer-checked:translate-x-4"></div>
          </label>
        </div>
        <div class="flex items-center justify-between rounded-xl bg-surface-container-low p-4">
          <div>
            <p class="text-sm font-bold text-primary">Validation manuelle des entreprises</p>
            <p class="text-xs text-on-surface-variant">Un admin doit approuver les nouveaux comptes entreprise.</p>
          </div>
          <label class="relative inline-flex cursor-pointer items-center">
            <input type="checkbox" class="peer sr-only" disabled />
            <div class="h-6 w-10 rounded-full bg-surface-container transition-colors peer-checked:bg-secondary-container"></div>
            <div class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white transition-transform peer-checked:translate-x-4"></div>
          </label>
        </div>
      </x-admin.panel>
    </div>
  </div>
@endsection

@section('scripts')
  <script>
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanels = document.querySelectorAll('.tab-panel');

    tabButtons.forEach((button) => {
      button.addEventListener('click', () => {
        const target = button.dataset.tab;

        tabButtons.forEach((item) => {
          item.classList.remove('active', 'text-primary', 'font-bold');
          item.classList.add('text-outline', 'font-semibold');
        });

        tabPanels.forEach((panel) => panel.classList.remove('active'));

        button.classList.add('active', 'text-primary', 'font-bold');
        button.classList.remove('text-outline', 'font-semibold');

        document.querySelector(`.tab-panel[data-tab="${target}"]`)?.classList.add('active');
      });
    });

    tabPanels.forEach((panel, index) => {
      if (index > 0) {
        panel.classList.remove('active');
        panel.classList.add('hidden');
      }
    });

    const syncPanelVisibility = () => {
      tabPanels.forEach((panel) => {
        panel.classList.toggle('hidden', !panel.classList.contains('active'));
      });
    };

    syncPanelVisibility();
    tabButtons.forEach((button) => button.addEventListener('click', syncPanelVisibility));
  </script>
@endsection
