@extends('layouts.guest')
@section('title', $entreprise->company_name)

@php
  $logoUrl  = $entreprise->logo ? asset($entreprise->logo) : null;
  $initials = strtoupper(substr($entreprise->company_name, 0, 2));
@endphp

@section('content')
<main class="flex-grow">
  <section class="pt-8 pb-20 px-4 md:px-10">
    <div class="max-w-7xl mx-auto">

      {{-- Back --}}
      <a href="{{ url()->previous() }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-outline hover:text-primary transition-colors mb-6">&larr; Retour</a>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ========== SIDEBAR (1/3) ========== --}}
        <aside class="space-y-5">

          {{-- Logo + Identity --}}
          <div class="card-glow rounded-2xl overflow-hidden text-center p-8">
            @if ($logoUrl)
              <img src="{{ $logoUrl }}" alt="{{ $entreprise->company_name }}" class="w-24 h-24 rounded-2xl object-cover border border-outline-variant/10 mx-auto">
            @else
              <div class="w-24 h-24 rounded-2xl bg-primary-container flex items-center justify-center mx-auto">
                <span class="text-white text-2xl font-bold">{{ $initials }}</span>
              </div>
            @endif
            <h2 class="mt-4 text-xl font-bold font-serif text-primary">{{ $entreprise->company_name }}</h2>
            @if ($entreprise->verified_at)
              <span class="inline-flex items-center gap-1 mt-2 px-3 py-0.5 bg-success-light text-success-dark text-2xs font-bold rounded-full">
                <span class="material-symbols-outlined text-sm">verified</span> Vérifiée
              </span>
            @endif
            <div class="mt-5 space-y-2.5 text-left">
              @if ($entreprise->website)
                <a href="{{ $entreprise->website }}" target="_blank" rel="noopener" class="flex items-center gap-2 text-sm text-on-surface-variant hover:text-secondary-container transition-colors">
                  <span class="material-symbols-outlined text-base">language</span> {{ parse_url($entreprise->website, PHP_URL_HOST) ?: $entreprise->website }}
                </a>
              @endif
              @if ($entreprise->neq)
                <div class="flex items-center gap-2 text-sm text-on-surface-variant">
                  <span class="material-symbols-outlined text-base">badge</span> NEQ: {{ $entreprise->neq }}
                </div>
              @endif
            </div>
          </div>

          {{-- Stats --}}
          <div class="card-glow rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-outline-variant/10">
              <h3 class="text-sm font-bold font-serif text-primary flex items-center gap-2"><span class="material-symbols-outlined text-secondary-container text-lg">bar_chart</span> En chiffres</h3>
            </div>
            <div class="p-6 grid grid-cols-2 gap-4">
              <div class="text-center">
                <p class="text-2xl font-bold text-primary">{{ $offres->total() }}</p>
                <p class="text-xs text-outline mt-0.5">Offre(s) active(s)</p>
              </div>
              <div class="text-center">
                <p class="text-2xl font-bold text-primary">{{ $offres->total() }}</p>
                <p class="text-xs text-outline mt-0.5">Total publié</p>
              </div>
            </div>
          </div>

        </aside>

        {{-- ========== MAIN (2/3) ========== --}}
        <div class="lg:col-span-2 space-y-6">

          {{-- Description --}}
          @if ($entreprise->description)
            <div class="card-glow rounded-2xl overflow-hidden">
              <div class="px-8 py-5 border-b border-outline-variant/10">
                <h2 class="text-lg font-bold font-serif text-primary flex items-center gap-2"><span class="material-symbols-outlined text-secondary-container">auto_awesome</span> À propos</h2>
                <p class="text-xs text-on-surface-variant mt-0.5">Découvrez l'entreprise et sa mission.</p>
              </div>
              <div class="p-8">
                <p class="text-sm text-on-surface-variant leading-relaxed whitespace-pre-line">{{ $entreprise->description }}</p>
              </div>
            </div>
          @endif

          {{-- Offres --}}
          <div class="card-glow rounded-2xl overflow-hidden">
            <div class="px-8 py-5 border-b border-outline-variant/10 flex items-center justify-between">
              <div>
                <h2 class="text-lg font-bold font-serif text-primary flex items-center gap-2"><span class="material-symbols-outlined text-secondary-container">work</span> Offres publiées</h2>
                <p class="text-xs text-on-surface-variant mt-0.5">{{ $offres->total() }} offre(s) active(s) sur {{ $offres->total() }} publiée(s).</p>
              </div>
            </div>
            <div class="p-6">

              @if ($offres->total() == 0)
                <div class="text-center py-10">
                  <span class="material-symbols-outlined text-5xl text-outline mb-4">work_off</span>
                  <p class="text-lg font-bold font-serif text-primary mb-2">Aucune offre pour le moment</p>
                  <p class="text-sm text-on-surface-variant">Cette entreprise n'a pas encore publié d'offres d'emploi.</p>
                </div>

              @elseif ($offres->total() == 1)
                @php $o = $offres->first(); @endphp
                <a href="{{ route('job_infos', $o) }}" class="block p-5 rounded-xl hover:bg-surface-container-low/30 transition-colors group border border-outline-variant/10">
                  <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-xl bg-secondary-container/10 flex items-center justify-center flex-shrink-0">
                      <span class="material-symbols-outlined text-secondary-container text-2xl">work</span>
                    </div>
                    <div class="flex-1">
                      <h3 class="font-bold text-primary group-hover:text-secondary-container transition-colors">{{ $o->titre }}</h3>
                      <p class="text-sm text-outline mt-1">{{ $o->localisation ?? '—' }}</p>
                      <div class="flex flex-wrap items-center gap-2 mt-3">
                        <span class="px-2.5 py-0.5 bg-secondary-container/10 text-secondary-container text-2xs font-bold rounded-full">{{ $o->type?->nom ?? 'Offre' }}</span>
                        @if ($o->remote_work)
                          <span class="px-2.5 py-0.5 bg-surface-container text-outline text-2xs font-bold rounded-full">{{ $o->remote_work }}</span>
                        @endif
                        <span class="text-xs text-outline">{{ $o->created_at?->diffForHumans() }}</span>
                      </div>
                    </div>
                    <div class="text-right">
                      <div class="text-lg font-bold text-primary">{{ number_format((float) ($o->salaire_min ?? $o->salaire_max ?? 0), 0, ',', ' ') }} $</div>
                      <span class="text-xs text-outline">{{ $o->salary_type ?? '' }}</span>
                    </div>
                  </div>
                </a>

              @else
                <div class="space-y-3">
                  @foreach ($offres as $o)
                    <a href="{{ route('job_infos', $o) }}" class="block p-4 rounded-xl hover:bg-surface-container-low/30 transition-colors group border border-outline-variant/10">
                      <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-lg bg-secondary-container/10 flex items-center justify-center flex-shrink-0">
                          <span class="material-symbols-outlined text-secondary-container text-lg">work</span>
                        </div>
                        <div class="flex-1 min-w-0">
                          <h3 class="font-semibold text-primary text-sm group-hover:text-secondary-container transition-colors">{{ $o->titre }}</h3>
                          <div class="flex items-center gap-3 mt-1">
                            <span class="text-xs text-outline">{{ $o->localisation ?? '—' }}</span>
                            <span class="px-2 py-0.5 bg-secondary-container/10 text-secondary-container text-2xs font-bold rounded-full">{{ $o->type?->nom ?? 'Offre' }}</span>
                          </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                          <div class="text-sm font-bold text-primary">{{ number_format((float) ($o->salaire_min ?? $o->salaire_max ?? 0), 0, ',', ' ') }} $</div>
                          <span class="text-xs text-outline">{{ $o->created_at?->diffForHumans() }}</span>
                        </div>
                      </div>
                    </a>
                  @endforeach
                </div>
              @endif

            </div>

            @if ($offres->hasPages())
              <div class="border-t border-outline-variant/10 px-6 py-4">
                {{ $offres->withQueryString()->links('components.pagination.public-pagination') }}
              </div>
            @endif
          </div>

        </div>
      </div>
    </div>
  </section>
</main>
@endsection
