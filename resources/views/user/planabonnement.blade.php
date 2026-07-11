@extends('layouts.candidat')
@section('title', 'Plan')
@section('content')
  <main class="flex-grow pt-32">

    <!-- Hero -->
    <section class="pt-16 pb-8 px-4 md:px-10 text-center">
      <div class="max-w-3xl mx-auto">
        <a href="{{ route('user.home') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-outline hover:text-primary transition-colors mb-4">&larr; Retour au tableau de bord</a>
        <h1 class="text-4xl md:text-5xl font-bold font-serif text-primary leading-tight mb-4">Choisissez votre Plan</h1>
        <p class="text-lg text-on-surface-variant mb-10">Trouvez l'abonnement parfait pour vos besoins</p>
      </div>
    </section>

    <!-- Pricing · Cartes 3D dynamiques -->
    <section class="pb-20 px-4 md:px-10">
      <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">

        @foreach ($abonnements as $abo)
          @php
            $gradients = [
              'default'   => 'linear-gradient(135deg, #f5f0eb, #e8e0d5)',
              'orange'    => 'linear-gradient(135deg, #fff5eb, #f5e0cc)',
              'blue'      => 'linear-gradient(135deg, #f0f4ff, #dce6f5)',
              'vert'      => 'linear-gradient(135deg, #edf7f0, #d0e8d5)',
              'violet'    => 'linear-gradient(135deg, #f5f0ff, #e5dcf5)',
            ];
            $gradient = $gradients[$abo->couleur] ?? $gradients['default'];
            $isPopular = !empty($abo->populaire);
            $fonctionnalites = $abo->fonctionnalites()->orderBy('ordre')->get();
          @endphp
          <div class="card-3d-parent">
            <div class="card-3d-inner" style="--card-grad: {{ $gradient }};">
              <div class="card-3d-logo" aria-hidden="true">
                <span class="card-3d-orbit card-3d-orbit--1" @if($isPopular) style="background: rgba(255,255,255,0.35);" @endif></span>
                <span class="card-3d-orbit card-3d-orbit--2" @if($isPopular) style="background: rgba(255,255,255,0.45);" @endif></span>
                <span class="card-3d-orbit card-3d-orbit--3" @if($isPopular) style="background: rgba(255,255,255,0.58);" @endif></span>
              </div>
              <div class="card-3d-glass"></div>
              @if ($isPopular)
                <span class="absolute -top-3 left-1/2 -translate-x-1/2 z-20 px-4 py-1 rounded-full text-xs font-bold text-white bg-secondary-container shadow-lg">Le plus populaire</span>
              @endif
              <div class="card-3d-content text-center">
                <p class="text-xs font-bold {{ $isPopular ? 'text-secondary-container' : 'text-outline' }} uppercase tracking-wide mb-1">{{ $abo->nom }}</p>
                <p class="text-4xl font-bold text-primary">{{ rtrim(rtrim(number_format((float) $abo->montant, 2, ',', ' '), '0'), ',') }}<span class="text-lg text-outline">$</span></p>
                <p class="text-xs text-outline mt-1">/{{ $abo->duree }} jour(s)</p>
                @if ($abo->description)
                  <p class="text-2xs text-outline/60 mt-1">{{ $abo->description }}</p>
                @endif
                <ul class="mt-4 space-y-2 text-xs text-outline text-left">
                  @foreach ($fonctionnalites as $fct)
                    <li class="flex items-center gap-2">
                      <span class="material-symbols-outlined text-sm {{ $fct->actif ? 'text-secondary-container' : 'text-outline-variant' }}">
                        {{ $fct->actif ? 'check' : 'close' }}
                      </span>
                      {{ $fct->nom }}
                    </li>
                  @endforeach
                </ul>
                <button
                  class="btn-subscribe w-full mt-5 py-2.5 rounded-full text-sm font-semibold transition-all {{ $isPopular ? 'btn-accent-shadow text-white hover:shadow-xl' : 'border border-outline-variant/40 text-primary hover:bg-black/5' }}"
                  @if($isPopular) style="background: rgba(var(--pj-accent-rgb),0.88);" @endif
                  data-plan-id="{{ $abo->id }}"
                  data-plan-name="{{ $abo->nom }}"
                >Choisir {{ $abo->nom }}</button>
              </div>
            </div>
          </div>
        @endforeach

      </div>
    </section>

  </main>

  <script>
    // Souscrire à un plan
    document.querySelectorAll('.btn-subscribe').forEach(button => {
      button.addEventListener('click', async () => {
        const planId = button.dataset.planId;
        const planName = button.dataset.planName;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        if (!csrfToken) return;

        button.disabled = true;
        button.textContent = 'Patientez...';

        try {
          const response = await fetch('{{ route('abonnements.souscrire') }}', {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({ abonnement_id: planId, plan: planName }),
          });

          const data = await response.json();

          if (data.success) {
            window.location.href = '{{ route('user.abonnement') }}';
          } else {
            alert(data.message || "Erreur lors de la souscription.");
            button.disabled = false;
            button.textContent = 'Choisir ' + planName;
          }
        } catch (error) {
          alert("Impossible de finaliser la souscription. Veuillez réessayer.");
          button.disabled = false;
          button.textContent = 'Choisir ' + planName;
        }
      });
    });
  </script>
@endsection
