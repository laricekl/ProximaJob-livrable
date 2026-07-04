@extends('layouts.candidat')
@section('title', 'Prévisualiser CV')
@section('content')
  <main class="flex-grow pt-32">

    <section class="py-10 px-4 md:px-10">
      <div class="max-w-5xl mx-auto">

        <!-- Success Header -->
        <div class="bg-secondary-container rounded-2xl p-6 md:p-8 mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
          <div>
            <h1 class="text-2xl font-bold font-serif text-white mb-1">Previsualisation de votre CV</h1>
            <p class="text-white/80 text-sm">Votre CV est pret. Vous pouvez le verifier dans l'apercu PDF.</p>
          </div>
          <div class="flex items-center gap-3 flex-shrink-0">
            @if(!empty($returnToApplicationUrl))
              <a href="{{ $returnToApplicationUrl }}" class="px-5 py-2.5 bg-white/20 text-white text-sm font-semibold rounded-xl hover:bg-white/30 transition-colors border border-white/30">Retour postuler</a>
            @endif
            <a href="{{ route('cv.personalization.form', request('offre_id') ? ['offre_id' => request('offre_id')] : []) }}" class="px-5 py-2.5 bg-white/20 text-white text-sm font-semibold rounded-xl hover:bg-white/30 transition-colors border border-white/30">Nouvelle personnalisation</a>
          </div>
        </div>

        <!-- PDF Preview -->
        <div class="card-glow rounded-2xl overflow-hidden">
          <div class="bg-surface-container-low px-6 py-4 border-b border-outline-variant/10 flex items-center gap-3">
            <span class="material-symbols-outlined text-outline">description</span>
            <span class="text-sm font-semibold text-primary">CV</span>
            <span class="text-xs text-outline ml-auto">Document PDF</span>
          </div>
          <div class="p-4 bg-surface-container-low/30">
            <div class="w-full bg-white rounded-xl border border-outline-variant/10 overflow-hidden" style="min-height: 600px;">
              @if(!empty($fileUrl))
                <iframe src="{{ $fileUrl }}" class="w-full min-h-[900px]" title="Prévisualisation du CV"></iframe>
              @else
              <div class="flex flex-col items-center justify-center h-full min-h-[600px] text-outline p-8">
                <span class="material-symbols-outlined text-6xl mb-4 text-outline/40">picture_as_pdf</span>
                <p class="text-sm font-semibold text-primary mb-2">Apercu du document PDF</p>
                <p class="text-xs text-outline mb-6">Le document n'est pas accessible pour le moment. Vous pouvez relancer la personnalisation.</p>
              </div>
              @endif
            </div>
          </div>
        </div>

      </div>
    </section>

  </main>
@endsection
