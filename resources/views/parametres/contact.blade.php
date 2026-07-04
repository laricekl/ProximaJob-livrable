@extends('layouts.guest')
@section('title', 'Contact')
@section('styles')
  <style>
    input:focus, textarea:focus {
      border-color: var(--pj-accent);
      box-shadow: 0 0 0 3px rgba(var(--pj-accent-rgb),0.1);
      outline: none;
    }
    .contact-map-frame {
      height: 20rem;
    }
    @media (min-width: 1024px) {
      .contact-map-frame {
        flex: 1 1 auto;
        height: auto;
        min-height: 20rem;
      }
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
    $mapLocation = $infos?->localisation ?: 'Montreal, QC, Canada';
    $mapSearchUrl = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($mapLocation);
  @endphp
  <main class="flex-grow pt-32" style="background: linear-gradient(180deg, rgba(176, 177, 192, 0.22) 0%, rgba(240, 242, 245, 0.36) 100%), radial-gradient(at 10% 8%, rgba(235, 132, 60, 0.055) 0, transparent 38%), radial-gradient(at 90% 88%, rgba(36, 98, 183, 0.035) 0, transparent 40%), #f7f9fb;">

    <x-public-page-hero
      title="Contactez-nous"
      subtitle="Une question, un besoin ou une demande d'accompagnement ? Notre equipe vous repond rapidement."
    />

    <!-- Contact + Form -->
    <section class="py-20 px-4 md:px-10">
      <div class="max-w-6xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:items-stretch">

          <!-- Presentation + Carte -->
          <div class="flex flex-col">
            <h2 class="text-3xl md:text-[2rem] xl:text-4xl font-bold font-serif text-primary leading-tight mb-6 lg:whitespace-nowrap">Vous grandirez, vous réussirez.</h2>
            <p class="text-on-surface-variant text-lg leading-relaxed mb-10">
              Notre algorithme intelligent analyse votre profil et vous propose automatiquement les offres d'emploi qui correspondent vraiment à vos compétences et aspirations.
            </p>

            <div class="contact-map-frame lg:flex-1 rounded-[2rem] overflow-hidden shadow-md border border-white bg-surface-container flex items-center justify-center">
              <a href="{{ $mapSearchUrl }}" target="_blank" rel="noopener" class="relative block h-full w-full group" aria-label="Ouvrir la carte de localisation {{ $infos?->site_nom ?? 'ProximaJob' }}">
                <img
                  src="{{ asset('img/contact-map-montreal.png') }}"
                  alt="Carte de localisation {{ $infos?->site_nom ?? 'ProximaJob' }}"
                  class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.03]"
                  loading="lazy"
                />
                <span class="absolute bottom-4 right-4 rounded-full bg-white/90 px-4 py-2 text-xs font-bold text-primary shadow-md backdrop-blur-sm transition-colors group-hover:text-secondary-container">
                  Ouvrir la carte
                </span>
              </a>
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
                <label for="email" class="block text-sm font-semibold text-primary mb-2">Courriel</label>
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

  </main>
@endsection
