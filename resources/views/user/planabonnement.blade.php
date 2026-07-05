@extends('layouts.candidat')
@section('title', 'Plan')
@section('content')
  <main class="flex-grow pt-32">

    <!-- Hero -->
    <section class="py-16 px-4 md:px-10 text-center">
      <div class="max-w-3xl mx-auto">
        <h1 class="text-4xl md:text-5xl font-bold font-serif text-primary leading-tight mb-4">Choisissez votre Plan</h1>
        <p class="text-lg text-on-surface-variant mb-10">Trouvez l'abonnement parfait pour vos besoins</p>

        <!-- Billing Toggle -->
        <div class="inline-flex items-center gap-0 bg-surface-container rounded-2xl p-1.5">
          <button id="monthlyBtn" class="px-7 py-3 rounded-xl text-sm font-bold bg-white text-primary shadow-sm transition-all">Mensuel</button>
          <button id="yearlyBtn" class="px-7 py-3 rounded-xl text-sm font-semibold text-outline hover:text-primary transition-all flex items-center gap-2">Annuel <span class="px-2.5 py-0.5 bg-secondary-container/10 text-secondary-container text-[10px] font-black uppercase tracking-widest rounded-full">-20%</span></button>
        </div>
      </div>
    </section>

    <!-- Pricing · Cartes 3D -->
    <section class="pb-20 px-4 md:px-10">
      <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Basic -->
        <div class="card-3d-parent">
          <div class="card-3d-inner" style="--card-grad: linear-gradient(135deg, #f5f0eb, #e8e0d5);">
            <div class="card-3d-logo" aria-hidden="true">
              <span class="card-3d-orbit card-3d-orbit--1"></span>
              <span class="card-3d-orbit card-3d-orbit--2"></span>
              <span class="card-3d-orbit card-3d-orbit--3"></span>
            </div>
            <div class="card-3d-glass"></div>
            <div class="card-3d-content text-center">
              <p class="text-xs font-bold text-outline uppercase tracking-wide mb-1">Basic</p>
              <p class="text-4xl font-bold text-primary"><span class="monthly-price">9,99</span><span class="yearly-price hidden">95,90</span><span class="text-lg text-outline">$</span></p>
              <p class="text-xs text-outline mt-1"><span class="monthly-price">/mois</span><span class="yearly-price hidden">/an</span></p>
              <p class="yearly-price hidden text-[10px] text-outline line-through mt-0.5">119,88$</p>
              <ul class="mt-4 space-y-2 text-xs text-outline text-left">
                <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">check</span> Acces aux fonctionnalites de base</li>
                <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">check</span> Support par courriel</li>
                <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">check</span> 5 GB de stockage</li>
                <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-outline-variant">close</span> Fonctionnalites avancees</li>
              </ul>
              <button class="w-full mt-5 py-2.5 rounded-full text-sm font-semibold border border-outline-variant/40 text-primary hover:bg-black/5 transition-colors">Choisir Basic</button>
            </div>
          </div>
        </div>

        <!-- Standard (Recommande) -->
        <div class="card-3d-parent">
          <div class="card-3d-inner" style="--card-grad: linear-gradient(135deg, #fff5eb, #f5e0cc);">
            <div class="card-3d-logo" aria-hidden="true">
              <span class="card-3d-orbit card-3d-orbit--1" style="background: rgba(255,255,255,0.35);"></span>
              <span class="card-3d-orbit card-3d-orbit--2" style="background: rgba(255,255,255,0.45);"></span>
              <span class="card-3d-orbit card-3d-orbit--3" style="background: rgba(255,255,255,0.58);"></span>
            </div>
            <div class="card-3d-glass"></div>
            <span class="absolute -top-3 left-1/2 -translate-x-1/2 z-20 px-4 py-1 rounded-full text-[11px] font-bold text-white bg-secondary-container shadow-lg">Le plus populaire</span>
            <div class="card-3d-content text-center">
              <p class="text-xs font-bold text-secondary-container uppercase tracking-wide mb-1">Standard</p>
              <p class="text-4xl font-bold text-primary"><span class="monthly-price">19,99</span><span class="yearly-price hidden">191,90</span><span class="text-lg text-outline">$</span></p>
              <p class="text-xs text-outline mt-1"><span class="monthly-price">/mois</span><span class="yearly-price hidden">/an</span></p>
              <p class="yearly-price hidden text-[10px] text-outline line-through mt-0.5">239,88$ <span class="px-2 py-0.5 bg-secondary-container/10 text-secondary-container text-[10px] font-bold rounded-full">-20%</span></p>
              <ul class="mt-4 space-y-2 text-xs text-outline text-left">
                <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">check</span> Toutes les fonctionnalites Basic</li>
                <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">check</span> Support prioritaire</li>
                <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">check</span> 50 GB de stockage</li>
                <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">check</span> Fonctionnalites avancees</li>
              </ul>
              <button class="btn-accent-shadow w-full mt-5 py-2.5 rounded-full text-sm font-semibold text-white transition-all duration-300 hover:shadow-xl" style="background: rgba(var(--pj-accent-rgb),0.88);">Choisir Standard</button>
            </div>
          </div>
        </div>

        <!-- Premium -->
        <div class="card-3d-parent">
          <div class="card-3d-inner" style="--card-grad: linear-gradient(135deg, #f0f4ff, #dce6f5);">
            <div class="card-3d-logo" aria-hidden="true">
              <span class="card-3d-orbit card-3d-orbit--1" style="background: rgba(255,255,255,0.32);"></span>
              <span class="card-3d-orbit card-3d-orbit--2" style="background: rgba(255,255,255,0.42);"></span>
              <span class="card-3d-orbit card-3d-orbit--3" style="background: rgba(255,255,255,0.55);"></span>
            </div>
            <div class="card-3d-glass"></div>
            <div class="card-3d-content text-center">
              <p class="text-xs font-bold text-secondary-container uppercase tracking-wide mb-1">Premium</p>
              <p class="text-4xl font-bold text-primary"><span class="monthly-price">39,99</span><span class="yearly-price hidden">383,90</span><span class="text-lg text-outline">$</span></p>
              <p class="text-xs text-outline mt-1"><span class="monthly-price">/mois</span><span class="yearly-price hidden">/an</span></p>
              <p class="yearly-price hidden text-[10px] text-outline line-through mt-0.5">479,88$ <span class="px-2 py-0.5 bg-secondary-container/10 text-secondary-container text-[10px] font-bold rounded-full">-20%</span></p>
              <ul class="mt-4 space-y-2 text-xs text-outline text-left">
                <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">check</span> Toutes les fonctionnalites Standard</li>
                <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">check</span> Support 24/7</li>
                <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">check</span> Stockage illimite</li>
                <li class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-secondary-container">check</span> Acces API complet</li>
              </ul>
              <button class="w-full mt-5 py-2.5 rounded-full text-sm font-semibold border border-outline-variant/40 text-primary hover:bg-black/5 transition-colors">Choisir Premium</button>
            </div>
          </div>
        </div>

      </div>
    </section>

  </main>

  <script>
    // Toggle Mensuel / Annuel
    const monthlyBtn = document.getElementById('monthlyBtn');
    const yearlyBtn = document.getElementById('yearlyBtn');
    const monthlyPrices = document.querySelectorAll('.monthly-price');
    const yearlyPrices = document.querySelectorAll('.yearly-price');

    if (monthlyBtn && yearlyBtn) {
      monthlyBtn.addEventListener('click', () => {
        monthlyBtn.classList.add('bg-white', 'text-primary', 'shadow-sm');
        monthlyBtn.classList.remove('text-outline');
        yearlyBtn.classList.remove('bg-white', 'text-primary', 'shadow-sm');
        yearlyBtn.classList.add('text-outline');
        monthlyPrices.forEach(el => el.classList.remove('hidden'));
        yearlyPrices.forEach(el => el.classList.add('hidden'));
      });

      yearlyBtn.addEventListener('click', () => {
        yearlyBtn.classList.add('bg-white', 'text-primary', 'shadow-sm');
        yearlyBtn.classList.remove('text-outline');
        monthlyBtn.classList.remove('bg-white', 'text-primary', 'shadow-sm');
        monthlyBtn.classList.add('text-outline');
        yearlyPrices.forEach(el => el.classList.remove('hidden'));
        monthlyPrices.forEach(el => el.classList.add('hidden'));
      });
    }

    // Souscrire à un plan
    document.querySelectorAll('.card-3d-content button').forEach(button => {
      button.addEventListener('click', async () => {
        const planName = button.textContent.replace('Choisir ', '').trim();
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        if (!csrfToken) return;

        button.disabled = true;
        button.textContent = 'Patientez...';

        try {
          const response = await fetch('/user/plan-souscrire', {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json',
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({ plan: planName }),
          });

          const data = await response.json();

          if (data.success) {
            window.location.href = '/user/abonnement';
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
