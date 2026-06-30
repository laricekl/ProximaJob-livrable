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
            <p class="text-white/80 text-sm">Votre CV personnalise est pret ! Vous pouvez le previsualiser et le telecharger.</p>
          </div>
          <div class="flex items-center gap-3 flex-shrink-0">
            <a href="{{ route('cv.personalization.form') }}" class="px-5 py-2.5 bg-white/20 text-white text-sm font-semibold rounded-xl hover:bg-white/30 transition-colors border border-white/30">Nouvelle personnalisation</a>
            <a href="{{ route('cv.personalization.download', ['filename' => $filename]) }}" class="px-5 py-2.5 bg-white text-secondary-container text-sm font-bold rounded-xl hover:bg-secondary-container/10 transition-colors flex items-center gap-2">
              <span class="material-symbols-outlined text-lg">download</span> Telecharger
            </a>
          </div>
        </div>

        <!-- PDF Preview -->
        <div class="card-glow rounded-2xl overflow-hidden">
          <div class="bg-surface-container-low px-6 py-4 border-b border-outline-variant/10 flex items-center gap-3">
            <span class="material-symbols-outlined text-outline">description</span>
            <span class="text-sm font-semibold text-primary">{{ $filename }}</span>
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
                <p class="text-xs text-outline mb-6">Le document n'est pas accessible pour le moment. Vous pouvez relancer la personnalisation ou telecharger le fichier si disponible.</p>
              </div>
              @endif
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-center mt-8">
          <a href="{{ route('cv.personalization.download', ['filename' => $filename]) }}" class="px-10 py-4 bg-secondary-container text-white font-bold rounded-xl hover:bg-secondary transition-all shadow-lg shadow-secondary-container/20 flex items-center gap-3 text-lg">
            <span class="material-symbols-outlined">download</span> Telecharger le CV
          </a>
        </div>

      </div>
    </section>

  </main>
@endsection
