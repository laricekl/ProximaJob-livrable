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
            <span class="text-sm font-semibold text-primary">CV_Developpeur_Full_Stack.pdf</span>
            <span class="text-xs text-outline ml-auto">Page 1/2</span>
          </div>
          <div class="p-4 bg-surface-container-low/30">
            <div class="w-full bg-white rounded-xl border border-outline-variant/10 overflow-hidden" style="min-height: 600px;">
              @if(!empty($fileUrl))
                <iframe src="{{ $fileUrl }}" class="w-full min-h-[900px]" title="Prévisualisation du CV"></iframe>
              @else
              <div class="flex flex-col items-center justify-center h-full min-h-[600px] text-outline p-8">
                <span class="material-symbols-outlined text-6xl mb-4 text-outline/40">picture_as_pdf</span>
                <p class="text-sm font-semibold text-primary mb-2">Apercu du document PDF</p>
                <p class="text-xs text-outline mb-6">Le CV personnalise s'affichera ici</p>

                <!-- Mock CV Preview -->
                <div class="w-full max-w-2xl bg-white rounded-xl shadow-sm border border-outline-variant/10 p-10 text-left space-y-6">
                  <div class="text-center border-b border-outline-variant/10 pb-6">
                    <div class="w-16 h-16 rounded-full bg-primary-container mx-auto mb-3"></div>
                    <h2 class="text-xl font-bold font-serif text-primary">Jean Dupont</h2>
                    <p class="text-sm text-on-surface-variant">Developpeur Full Stack</p>
                    <div class="flex justify-center gap-4 mt-2 text-xs text-outline">
                      <span>jean.dupont@email.com</span>
                      <span>+33 6 12 34 56 78</span>
                      <span>Montreal, QC</span>
                    </div>
                  </div>

                  <div>
                    <h3 class="text-sm font-bold text-primary uppercase tracking-widest mb-3">Competences</h3>
                    <div class="flex flex-wrap gap-2">
                      <span class="px-2 py-1 bg-secondary-container/10 text-secondary-container text-xs font-semibold rounded">React.js</span>
                      <span class="px-2 py-1 bg-secondary-container/10 text-secondary-container text-xs font-semibold rounded">Node.js</span>
                      <span class="px-2 py-1 bg-secondary-container/10 text-secondary-container text-xs font-semibold rounded">TypeScript</span>
                      <span class="px-2 py-1 bg-secondary-container/10 text-secondary-container text-xs font-semibold rounded">PostgreSQL</span>
                      <span class="px-2 py-1 bg-secondary-container/10 text-secondary-container text-xs font-semibold rounded">Docker</span>
                      <span class="px-2 py-1 bg-secondary-container/10 text-secondary-container text-xs font-semibold rounded">AWS</span>
                    </div>
                  </div>

                  <div>
                    <h3 class="text-sm font-bold text-primary uppercase tracking-widest mb-3">Experience</h3>
                    <div class="space-y-4">
                      <div>
                        <div class="flex justify-between items-baseline">
                          <p class="text-sm font-bold text-primary">Developpeur Full Stack</p>
                          <p class="text-xs text-outline">2022 - Present</p>
                        </div>
                        <p class="text-xs text-on-surface-variant">TechCorp &bull; Montreal, QC</p>
                        <p class="text-xs text-on-surface-variant mt-1">Developpement et maintenance d'applications web full stack en collaboration avec les equipes produit et design.</p>
                      </div>
                      <div>
                        <div class="flex justify-between items-baseline">
                          <p class="text-sm font-bold text-primary">Developpeur Frontend</p>
                          <p class="text-xs text-outline">2020 - 2022</p>
                        </div>
                        <p class="text-xs text-on-surface-variant">WebAgency &bull; Lyon, France</p>
                        <p class="text-xs text-on-surface-variant mt-1">Creation d'interfaces utilisateur responsives et performantes avec React et TypeScript.</p>
                      </div>
                    </div>
                  </div>

                  <div>
                    <h3 class="text-sm font-bold text-primary uppercase tracking-widest mb-3">Formation</h3>
                    <div>
                      <div class="flex justify-between items-baseline">
                        <p class="text-sm font-bold text-primary">Master Informatique</p>
                        <p class="text-xs text-outline">2018 - 2020</p>
                      </div>
                      <p class="text-xs text-on-surface-variant">Universite de Montreal</p>
                    </div>
                  </div>
                </div>
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
