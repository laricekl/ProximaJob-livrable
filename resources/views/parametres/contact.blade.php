@extends('layouts.guest')
@section('title', 'Contact')
@section('styles')
  <style>
    input:focus, textarea:focus {
      border-color: var(--pj-accent);
      box-shadow: 0 0 0 3px rgba(var(--pj-accent-rgb),0.1);
      outline: none;
    }
    @media (max-width: 767px) {
      main.pt-32 { padding-top: 6rem; }
      section.py-20 { padding: 2rem 1rem !important; }
      .text-5xl { font-size: 2rem !important; }
      .text-3xl { font-size: 1.5rem !important; }
      .text-lg { font-size: 0.9rem !important; }
      .grid.grid-cols-1.lg\\:grid-cols-2.gap-12 { gap: 20px; }
      .bg-white.rounded-2xl.p-6 { padding: 14px !important; border-radius: 14px; }
      .bg-white.rounded-2xl .w-12.h-12 { width: 36px; height: 36px; border-radius: 10px; }
      .bg-secondary-fixed\\/30.rounded-\\[2rem\\] { padding: 20px !important; border-radius: 20px !important; }
      form.space-y-5 { gap: 12px; }
      form .px-4.py-3\\.5 { padding: 10px 14px; font-size: 13px; border-radius: 12px; }
      form button.py-3\\.5 { padding: 12px 0; font-size: 14px; }
      .h-80 { height: 200px !important; }
      /* Footer */
      footer nav.gap-4 { gap: 12px; }
      footer .text-\[9px\] { font-size: 8px; }
      footer .text-\[8px\] { font-size: 7px; }
      footer .text-\[10px\] { font-size: 9px; }
    }
  </style>
@endsection
@section('content')
  @php
    $contactPhone = $infos?->tel ?: '+1 234 567 890';
    $contactEmail = $infos?->email ?: 'contact@proximajob.com';
    $contactAddress = $infos?->localisation ?: 'Montreal, QC, Canada';
    $mapEmbedUrl = $infos?->map_embed_url;
  @endphp
  <main class="flex-grow pt-32">

    <x-public-page-hero
      title="Contactez-nous"
      subtitle="Une question, un besoin ou une demande d'accompagnement ? Notre equipe vous repond rapidement."
    />

    <!-- Contact + Form -->
    <section class="py-20 px-4 md:px-10 bg-surface-container-low/50">
      <div class="max-w-6xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

          <!-- Infos contact -->
          <div>
            <h2 class="text-3xl md:text-4xl font-bold font-serif text-primary leading-tight mb-6">Vous grandirez, vous réussirez.</h2>
            <p class="text-on-surface-variant text-lg leading-relaxed mb-10">
              Notre algorithme intelligent analyse votre profil et vous propose automatiquement les offres d'emploi qui correspondent vraiment à vos compétences et aspirations.
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
              <!-- Téléphone -->
              <div class="bg-white rounded-2xl p-6 shadow-sm border border-outline-variant/30 flex flex-col items-start gap-3">
                <div class="w-12 h-12 rounded-xl bg-secondary-fixed flex items-center justify-center">
                  <span class="material-symbols-outlined text-on-secondary-fixed-variant">call</span>
                </div>
                <div>
                  <h4 class="font-bold text-primary text-sm">Téléphone</h4>
                  <p class="text-on-surface-variant text-sm">{{ $contactPhone }}</p>
                </div>
              </div>

              <!-- Email -->
              <div class="bg-white rounded-2xl p-6 shadow-sm border border-outline-variant/30 flex flex-col items-start gap-3">
                <div class="w-12 h-12 rounded-xl bg-tertiary-fixed flex items-center justify-center">
                  <span class="material-symbols-outlined text-on-tertiary-fixed">mail</span>
                </div>
                <div>
                  <h4 class="font-bold text-primary text-sm">Envoyez-nous un email</h4>
                  <p class="text-on-surface-variant text-sm">{{ $contactEmail }}</p>
                </div>
              </div>

              <!-- Bureau -->
              <div class="bg-white rounded-2xl p-6 shadow-sm border border-outline-variant/30 flex flex-col items-start gap-3 sm:col-span-2">
                <div class="w-12 h-12 rounded-xl bg-primary-fixed flex items-center justify-center">
                  <span class="material-symbols-outlined text-primary">location_on</span>
                </div>
                <div>
                  <h4 class="font-bold text-primary text-sm">Bureau</h4>
                  <p class="text-on-surface-variant text-sm">{{ $contactAddress }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Formulaire -->
          <div class="bg-secondary-fixed/30 rounded-[2rem] p-8 md:p-10">
            <h3 class="font-bold text-xl text-primary mb-8">Envoyez-nous un message</h3>
            @if (session('success'))
              <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                {{ session('success') }}
              </div>
            @endif
            <form class="space-y-5" action="{{ route('contact.submit') }}" method="POST">
              @csrf
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label for="name" class="block text-sm font-semibold text-primary mb-2">Nom</label>
                  <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Votre nom"
                    class="w-full px-4 py-3.5 bg-white/90 backdrop-blur-sm border {{ $errors->has('name') ? 'border-red-400' : 'border-outline-variant/50' }} rounded-xl text-sm text-primary placeholder:text-outline focus:outline-none" />
                  @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                  @enderror
                </div>
                <div>
                  <label for="firstname" class="block text-sm font-semibold text-primary mb-2">Prénom</label>
                  <input type="text" id="firstname" name="firstname" value="{{ old('firstname') }}" placeholder="Votre prénom"
                    class="w-full px-4 py-3.5 bg-white/90 backdrop-blur-sm border {{ $errors->has('firstname') ? 'border-red-400' : 'border-outline-variant/50' }} rounded-xl text-sm text-primary placeholder:text-outline focus:outline-none" />
                  @error('firstname')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                  @enderror
                </div>
              </div>
              <div>
                <label for="email" class="block text-sm font-semibold text-primary mb-2">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="votre@email.com"
                  class="w-full px-4 py-3.5 bg-white/90 backdrop-blur-sm border {{ $errors->has('email') ? 'border-red-400' : 'border-outline-variant/50' }} rounded-xl text-sm text-primary placeholder:text-outline focus:outline-none" />
                @error('email')
                  <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
              </div>
              <div>
                <label for="message" class="block text-sm font-semibold text-primary mb-2">Message</label>
                <textarea id="message" name="message" rows="5" placeholder="Votre message..."
                  class="w-full px-4 py-3.5 bg-white/90 backdrop-blur-sm border {{ $errors->has('message') ? 'border-red-400' : 'border-outline-variant/50' }} rounded-xl text-sm text-primary placeholder:text-outline focus:outline-none resize-none">{{ old('message') }}</textarea>
                @error('message')
                  <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
              </div>
              <button type="submit" class="w-full py-3.5 bg-secondary-container text-white font-bold rounded-xl hover:bg-secondary transition-all shadow-lg shadow-secondary-container/20 flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-lg">send</span>
                Envoyer un message
              </button>
            </form>
          </div>

        </div>
      </div>
    </section>

    <!-- Carte -->
    <section class="py-20 px-4 md:px-10 bg-white">
      <div class="max-w-6xl mx-auto">
        <div class="rounded-[2rem] overflow-hidden shadow-lg border border-outline-variant/20 h-80 bg-surface-container flex items-center justify-center">
          @if ($mapEmbedUrl)
            <iframe
              src="{{ $mapEmbedUrl }}"
              class="h-full w-full border-0"
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              allowfullscreen
              title="Carte de localisation {{ $infos?->site_nom ?? 'ProximaJob' }}"
            ></iframe>
          @else
            <div class="text-center text-outline">
              <span class="material-symbols-outlined text-5xl mb-4">map</span>
              <p class="text-sm font-medium">Carte intégrée ici</p>
            </div>
          @endif
        </div>
      </div>
    </section>

  </main>
@endsection
