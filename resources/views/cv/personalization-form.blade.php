@extends('layouts.candidat')
@section('title', 'Adapter mon CV a une offre')
@section('content')
  <main class="flex-grow pt-32">

    <section class="py-12 px-4 md:px-10">
      <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <p class="text-xs font-black uppercase tracking-[0.18em] text-secondary-container">Personnalisation</p>
            <h1 class="mt-2 text-3xl font-bold font-serif text-primary">Adapter mon CV a une offre</h1>
            <p class="mt-2 text-sm text-on-surface-variant">Cette étape utilise votre CV principal comme base. Pour modifier vos expériences, formations ou compétences, revenez au CV principal.</p>
          </div>
          <a href="{{ route('infos.cv') }}" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-outline-variant/10 bg-white px-4 py-3 text-sm font-semibold text-primary transition-colors hover:bg-surface-container-low">
            <span class="material-symbols-outlined text-lg">edit_note</span> CV principal
          </a>
        </div>

        <!-- Formulaire principal -->
        <div class="card-glow rounded-2xl overflow-hidden">
          <div class="bg-secondary-container px-8 py-6 text-white">
            <h2 class="text-2xl font-bold font-serif">Informations de l'offre</h2>
            <p class="text-sm text-white/80 mt-1">Ajoutez le contexte du poste pour produire une version ciblee de votre CV</p>
          </div>

          <div class="p-8">
            <form class="space-y-6" action="{{ route('cv.personalization.generate') }}" method="POST">
              @csrf
              @if ($selectedOffer)
                <input type="hidden" name="offre_id" value="{{ $selectedOffer->id }}">
              @endif
              <div>
                <label class="block text-sm font-semibold text-primary mb-2" for="offer_title">Poste vise <span class="text-secondary-container">*</span></label>
                <input type="text" id="offer_title" name="offer_title" list="recent-offers" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary placeholder:text-outline focus:border-secondary-container/50 focus:ring-0 transition-all" placeholder="Ex: Developpeur Full Stack Senior" value="{{ old('offer_title', $selectedOffer->titre ?? '') }}" required />
                @if(!empty($recentOffers) && $recentOffers->count())
                  <datalist id="recent-offers">
                    @foreach($recentOffers as $offer)
                      <option value="{{ $offer->titre }}"></option>
                    @endforeach
                  </datalist>
                @endif
              </div>

              <div>
                <label class="block text-sm font-semibold text-primary mb-2" for="company_name">Nom de l'entreprise</label>
                <input type="text" id="company_name" name="company_name" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary placeholder:text-outline focus:border-secondary-container/50 focus:ring-0 transition-all" placeholder="Ex: Google, Startup XYZ..." value="{{ old('company_name', $selectedOfferCompany ?? '') }}" />
              </div>

              <div>
                <label class="block text-sm font-semibold text-primary mb-2" for="offer_details">Description du poste <span class="text-secondary-container">*</span></label>
                <textarea id="offer_details" name="offer_details" rows="5" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary placeholder:text-outline focus:border-secondary-container/50 focus:ring-0 transition-all resize-none" placeholder="Decrivez les missions principales, le contexte..." required>{{ old('offer_details', $selectedOfferDetails ?? '') }}</textarea>
                <p class="text-xs text-outline mt-1.5">Plus vous etes precis, mieux nous pouvons adapter votre CV</p>
                @if (isset($offerDataQuality) && $offerDataQuality === 'insuffisante')
                  <div class="mt-3 rounded-xl border border-warning-light bg-warning-light px-4 py-3 text-sm text-warning-dark flex items-start gap-2">
                    <span class="material-symbols-outlined text-lg flex-shrink-0">warning</span>
                    <span>L'offre sélectionnée contient peu d'informations. Pour un CV vraiment personnalisé, ajoutez plus de détails dans la description.</span>
                  </div>
                @elseif (isset($offerDataQuality) && $offerDataQuality === 'faible')
                  <div class="mt-3 rounded-xl border border-warning-light/50 bg-warning-light/50 px-4 py-3 text-sm text-warning-dark flex items-start gap-2">
                    <span class="material-symbols-outlined text-lg flex-shrink-0">info</span>
                    <span>La description est courte. Ajoutez plus de détails pour un meilleur résultat.</span>
                  </div>
                @endif
              </div>

              <div>
                <label class="block text-sm font-semibold text-primary mb-2" for="key_requirements">Competences / Exigences cles</label>
                <textarea id="key_requirements" name="key_requirements" rows="3" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary placeholder:text-outline focus:border-secondary-container/50 focus:ring-0 transition-all resize-none" placeholder="Ex: React, Node.js, Gestion d'equipe, Agile...">{{ old('key_requirements', $selectedOfferRequirements ?? '') }}</textarea>
                <p class="text-xs text-outline mt-1.5">Listez les competences techniques et soft skills recherchees</p>
              </div>

              <div>
                <label class="block text-sm font-semibold text-primary mb-3">Style de CV</label>
                <div class="grid grid-cols-3 gap-3">
                  <input type="radio" name="template_style" id="modern" value="modern" class="template-radio hidden" checked />
                  <label for="modern" class="border-2 border-outline-variant/20 rounded-xl p-4 text-center cursor-pointer transition-all hover:border-secondary-container/30">
                    <span class="material-symbols-outlined text-2xl text-primary mb-2">auto_awesome</span>
                    <span class="block text-sm font-semibold text-primary">Moderne</span>
                  </label>

                  <input type="radio" name="template_style" id="classic" value="classic" class="template-radio hidden" />
                  <label for="classic" class="border-2 border-outline-variant/20 rounded-xl p-4 text-center cursor-pointer transition-all hover:border-secondary-container/30">
                    <span class="material-symbols-outlined text-2xl text-primary mb-2">description</span>
                    <span class="block text-sm font-semibold text-primary">Classique</span>
                  </label>

                  <input type="radio" name="template_style" id="executive" value="executive" class="template-radio hidden" />
                  <label for="executive" class="border-2 border-outline-variant/20 rounded-xl p-4 text-center cursor-pointer transition-all hover:border-secondary-container/30">
                    <span class="material-symbols-outlined text-2xl text-primary mb-2">work</span>
                    <span class="block text-sm font-semibold text-primary">Executive</span>
                  </label>
                </div>
              </div>

              <div class="grid gap-6 md:grid-cols-2">
                <div>
                  <label class="block text-sm font-semibold text-primary mb-3">Couleur</label>
                  <div class="grid grid-cols-5 gap-2">
                    @foreach ([
                      'blue' => ['Bleu', '#2f5f8f'],
                      'green' => ['Vert', '#2f7d5c'],
                      'bordeaux' => ['Bordeaux', '#8a334b'],
                      'anthracite' => ['Anthracite', '#343a40'],
                      'petrol' => ['Petrole', '#28666e'],
                    ] as $value => [$label, $color])
                      <label class="cursor-pointer">
                        <input type="radio" name="accent_color" value="{{ $value }}" class="peer hidden" @checked($value === 'blue')>
                        <span class="flex h-11 items-center justify-center rounded-xl border-2 border-outline-variant/20 bg-white text-xs font-bold text-primary transition-all peer-checked:border-secondary-container peer-checked:bg-secondary-container/10" title="{{ $label }}">
                          <span class="h-5 w-5 rounded-full" style="background-color: {{ $color }}"></span>
                        </span>
                      </label>
                    @endforeach
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-semibold text-primary mb-3">Longueur</label>
                  <div class="grid grid-cols-3 gap-2">
                    <label class="cursor-pointer"><input type="radio" name="page_limit" value="1" class="peer hidden"><span class="block rounded-xl border-2 border-outline-variant/20 px-3 py-3 text-center text-xs font-bold text-primary peer-checked:border-secondary-container peer-checked:bg-secondary-container/10">1 page</span></label>
                    <label class="cursor-pointer"><input type="radio" name="page_limit" value="2" class="peer hidden" checked><span class="block rounded-xl border-2 border-outline-variant/20 px-3 py-3 text-center text-xs font-bold text-primary peer-checked:border-secondary-container peer-checked:bg-secondary-container/10">2 pages</span></label>
                    <label class="cursor-pointer"><input type="radio" name="page_limit" value="3" class="peer hidden"><span class="block rounded-xl border-2 border-outline-variant/20 px-3 py-3 text-center text-xs font-bold text-primary peer-checked:border-secondary-container peer-checked:bg-secondary-container/10">3 pages</span></label>
                  </div>
                  <p class="mt-1.5 text-xs text-outline">2 pages est recommande. 3 pages convient aux profils tres experimentes.</p>
                </div>
              </div>

              <div class="grid gap-6 md:grid-cols-2">
                <div>
                  <label class="block text-sm font-semibold text-primary mb-2" for="font_style">Police</label>
                  <select id="font_style" name="font_style" class="w-full rounded-xl border border-outline-variant/30 bg-white px-4 py-3 text-sm text-primary focus:border-secondary-container/50 focus:ring-0">
                    <option value="sober" selected>Sobre</option>
                    <option value="modern">Moderne</option>
                    <option value="classic">Classique</option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-semibold text-primary mb-2" for="density">Densite</label>
                  <select id="density" name="density" class="w-full rounded-xl border border-outline-variant/30 bg-white px-4 py-3 text-sm text-primary focus:border-secondary-container/50 focus:ring-0">
                    <option value="airy">Aere</option>
                    <option value="balanced" selected>Equilibre</option>
                    <option value="compact">Compact</option>
                  </select>
                </div>
              </div>

              <div class="grid gap-6 md:grid-cols-2">
                <div>
                  <label class="block text-sm font-semibold text-primary mb-2" for="section_order">Ordre des sections</label>
                  <select id="section_order" name="section_order" class="w-full rounded-xl border border-outline-variant/30 bg-white px-4 py-3 text-sm text-primary focus:border-secondary-container/50 focus:ring-0">
                    <option value="skills_first" selected>Competences puis experiences</option>
                    <option value="experience_first">Experiences puis competences</option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-semibold text-primary mb-2" for="summary_tone">Ton du resume</label>
                  <select id="summary_tone" name="summary_tone" class="w-full rounded-xl border border-outline-variant/30 bg-white px-4 py-3 text-sm text-primary focus:border-secondary-container/50 focus:ring-0">
                    <option value="professional" selected>Professionnel</option>
                    <option value="direct">Direct</option>
                    <option value="human">Plus humain</option>
                    <option value="technical">Plus technique</option>
                  </select>
                </div>
              </div>

              <div>
                <label class="block text-sm font-semibold text-primary mb-3">Sections visibles</label>
                <input type="hidden" name="sections_present" value="1">
                <div class="grid gap-2 sm:grid-cols-2">
                  @foreach ([
                    'software' => 'Logiciels',
                    'languages' => 'Langues',
                    'perfectionnements' => 'Perfectionnement',
                    'benevolats' => 'Activites benevoles',
                  ] as $value => $label)
                    <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-outline-variant/20 bg-white px-4 py-3 text-sm font-semibold text-primary">
                      <input type="checkbox" name="sections[]" value="{{ $value }}" class="rounded border-outline-variant/40 text-secondary-container focus:ring-secondary-container" checked>
                      {{ $label }}
                    </label>
                  @endforeach
                </div>
                <p class="mt-1.5 text-xs text-outline">Selon la longueur choisie, certaines informations sont priorisees dans l'application, jamais indiquees dans le PDF.</p>
              </div>

              <div class="flex justify-end pt-4 border-t border-outline-variant/10">
                <button type="submit" class="px-8 py-3.5 bg-secondary-container text-white font-bold rounded-xl hover:bg-secondary transition-all shadow-lg shadow-secondary-container/20 flex items-center gap-2">
                  <span class="material-symbols-outlined">auto_awesome</span> Generer mon CV personnalise
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Conseils -->
        <div class="mt-6 bg-secondary-container/10 backdrop-blur-md border border-secondary-container/20 rounded-2xl p-6">
          <h3 class="font-bold text-primary flex items-center gap-2 mb-3"><span class="material-symbols-outlined text-secondary-container">tips_and_updates</span> Conseils pour une meilleure personnalisation</h3>
          <ul class="space-y-2 text-sm text-on-surface-variant">
            <li class="flex items-start gap-2"><span class="material-symbols-outlined text-secondary-container text-sm mt-0.5">check_circle</span> Copiez-collez directement la description de l'offre</li>
            <li class="flex items-start gap-2"><span class="material-symbols-outlined text-secondary-container text-sm mt-0.5">check_circle</span> Soulignez les mots-cles importants de l'annonce</li>
            <li class="flex items-start gap-2"><span class="material-symbols-outlined text-secondary-container text-sm mt-0.5">check_circle</span> Mentionnez les technologies et outils specifiques</li>
            <li class="flex items-start gap-2"><span class="material-symbols-outlined text-secondary-container text-sm mt-0.5">check_circle</span> Indiquez le niveau d'experience requis</li>
          </ul>
        </div>

      </div>
    </section>

  </main>
@endsection
