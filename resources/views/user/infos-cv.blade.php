@extends('layouts.candidat')
@section('title', 'Mon CV')
@section('content')
  <main class="flex-grow pt-32 pb-16">

    <section class="py-8 px-4 md:px-10">
      <div class="max-w-6xl mx-auto">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-end mb-6">
          <a href="{{ route('cv.personalization.form') }}" class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-white border border-outline-variant/10 text-sm font-semibold text-secondary-container hover:bg-surface-container-low transition-colors">
            <span class="material-symbols-outlined text-lg">auto_awesome</span> Personnaliser mon CV pour un poste
          </a>
          <a href="{{ route('cv.personalization.form') }}" class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-white border border-outline-variant/10 text-sm font-semibold text-secondary-container hover:bg-surface-container-low transition-colors">
            <span class="material-symbols-outlined text-lg">visibility</span> Previsualiser mon CV
          </a>
        </div>

        <div class="flex flex-col md:flex-row gap-0 card-glow rounded-2xl overflow-hidden">

          <!-- Sidebar -->
          <aside class="md:w-80 flex-shrink-0 bg-gradient-to-b from-primary-container to-slate-800 text-white p-8">
            <div class="mb-8">
              <div class="w-full bg-white/20 rounded-full h-2 mb-3 overflow-hidden">
                <div class="bg-white/80 h-full rounded-full transition-all duration-300" id="progressFill" style="width:12.5%"></div>
              </div>
              <p class="text-sm text-white/80" id="progressText">1/8 sections</p>
            </div>

            <nav class="space-y-2">
              <div class="cv-step active flex items-center gap-3 p-3 rounded-xl cursor-pointer" data-step="1">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold flex-shrink-0">1</div>
                <span class="text-sm font-semibold">Informations personnelles</span>
              </div>
              <div class="cv-step flex items-center gap-3 p-3 rounded-xl cursor-pointer" data-step="2">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold flex-shrink-0">2</div>
                <span class="text-sm font-semibold">Competences</span>
              </div>
              <div class="cv-step flex items-center gap-3 p-3 rounded-xl cursor-pointer" data-step="3">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold flex-shrink-0">3</div>
                <span class="text-sm font-semibold">Experience professionnelle</span>
              </div>
              <div class="cv-step flex items-center gap-3 p-3 rounded-xl cursor-pointer" data-step="4">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold flex-shrink-0">4</div>
                <span class="text-sm font-semibold">Formation</span>
              </div>
              <div class="cv-step flex items-center gap-3 p-3 rounded-xl cursor-pointer" data-step="5">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold flex-shrink-0">5</div>
                <span class="text-sm font-semibold">Perfectionnement</span>
              </div>
              <div class="cv-step flex items-center gap-3 p-3 rounded-xl cursor-pointer" data-step="6">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold flex-shrink-0">6</div>
                <span class="text-sm font-semibold">Langues</span>
              </div>
              <div class="cv-step flex items-center gap-3 p-3 rounded-xl cursor-pointer" data-step="7">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold flex-shrink-0">7</div>
                <span class="text-sm font-semibold">Activites benevoles</span>
              </div>
              <div class="cv-step flex items-center gap-3 p-3 rounded-xl cursor-pointer" data-step="8">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold flex-shrink-0">8</div>
                <span class="text-sm font-semibold">Enregistrement</span>
              </div>
            </nav>
          </aside>

          <!-- Contenu du formulaire -->
          <div class="flex-1 p-8">
            <form id="cvDataForm">
              @csrf

              <!-- Section 1: Infos personnelles -->
              <div class="cv-form-section active" id="section-1">
                <div class="mb-8 pb-4 border-b border-outline-variant/10">
                  <h2 class="text-2xl font-bold font-serif text-primary">Informations personnelles</h2>
                  <p class="text-sm text-on-surface-variant mt-1">Renseignez vos informations de base</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                  <div><label class="block text-sm font-semibold text-primary mb-1.5">Nom *</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="nom" required /></div>
                  <div><label class="block text-sm font-semibold text-primary mb-1.5">Prenom *</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="prenom_cv" required /></div>
                  <div><label class="block text-sm font-semibold text-primary mb-1.5">Email *</label><input type="email" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="email_cv" required /></div>
                  <div><label class="block text-sm font-semibold text-primary mb-1.5">Telephone *</label><input type="tel" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="telephone_cv" required /></div>
                  <div class="md:col-span-2"><label class="block text-sm font-semibold text-primary mb-1.5">Adresse</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="adresse" /></div>
                  <div><label class="block text-sm font-semibold text-primary mb-1.5">Ville et province</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="ville" placeholder="Montreal (Quebec)" /></div>
                  <div><label class="block text-sm font-semibold text-primary mb-1.5">Code postal</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="code_postal" placeholder="H1B 8T2" /></div>
                </div>
              </div>

              <!-- Section 2: Competences -->
              <div class="cv-form-section" id="section-2">
                <div class="mb-8 pb-4 border-b border-outline-variant/10">
                  <h2 class="text-2xl font-bold font-serif text-primary">Competences</h2>
                  <p class="text-sm text-on-surface-variant mt-1">Listez vos competences professionnelles</p>
                </div>
                <div class="space-y-5">
                  <div class="bg-white rounded-2xl border border-outline-variant/10 p-5 md:p-6 space-y-5">
                    <div><label class="block text-sm font-semibold text-primary mb-1.5">Langues maitrisees</label><textarea class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all resize-none" rows="2" name="langues_competences" placeholder="Francais, anglais et connaissances de base en espagnol"></textarea></div>
                    <div><label class="block text-sm font-semibold text-primary mb-1.5">Logiciels maitrises</label><textarea class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all resize-none" rows="2" name="logiciels" placeholder="Word, Access, Excel, PowerPoint, Simple Comptable"></textarea></div>
                  </div>

                  <div class="bg-white rounded-2xl border border-outline-variant/10 p-5 md:p-6">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-5">
                      <div>
                        <h3 class="text-lg font-bold text-primary">Competences specifiques</h3>
                        <p class="text-sm text-on-surface-variant">Ajoutez une carte par competence ou savoir-faire important.</p>
                      </div>
                      <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-secondary-container/10 text-secondary-container text-sm font-semibold hover:bg-secondary-container/15 transition-colors" onclick="addCompetence()"><span class="material-symbols-outlined text-lg">add_circle</span> Ajouter une competence</button>
                    </div>
                    <div id="competences-container" class="space-y-4">
                      <div class="repeatable-item bg-surface-container-low rounded-xl p-5 border border-outline-variant/10" data-index="0">
                        <label class="block text-sm font-semibold text-primary mb-1.5">Competence specifique</label>
                        <textarea class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all resize-none" rows="3" name="competences[0][description]" placeholder="Comptabilite generale : comptes clients, comptes fournisseurs, paie, facturation, conciliation bancaire"></textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Section 3: Experience -->
              <div class="cv-form-section" id="section-3">
                <div class="mb-8 pb-4 border-b border-outline-variant/10">
                  <h2 class="text-2xl font-bold font-serif text-primary">Experience professionnelle</h2>
                  <p class="text-sm text-on-surface-variant mt-1">Ajoutez vos emplois precedents</p>
                </div>
                <div class="bg-white rounded-2xl border border-outline-variant/10 p-5 md:p-6">
                  <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-5">
                    <div>
                      <h3 class="text-lg font-bold text-primary">Blocs d'experience</h3>
                      <p class="text-sm text-on-surface-variant">Une carte correspond a un poste occupe.</p>
                    </div>
                    <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-secondary-container/10 text-secondary-container text-sm font-semibold hover:bg-secondary-container/15 transition-colors" onclick="addExperience()"><span class="material-symbols-outlined text-lg">add_circle</span> Ajouter une experience</button>
                  </div>
                  <div id="experiences-container" class="space-y-5">
                    <div class="repeatable-item bg-surface-container-low rounded-xl p-5 border border-outline-variant/10" data-index="0">
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div><label class="block text-sm font-semibold text-primary mb-1.5">Periode *</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="experiences[0][periode]" placeholder="2004-2017" required /></div>
                        <div><label class="block text-sm font-semibold text-primary mb-1.5">Poste *</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="experiences[0][poste]" placeholder="Adjointe administrative" required /></div>
                      </div>
                      <div class="mb-4"><label class="block text-sm font-semibold text-primary mb-1.5">Entreprise et lieu</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="experiences[0][entreprise]" placeholder="Entreprise ABC enr., Montreal (Quebec)" /></div>
                      <div><label class="block text-sm font-semibold text-primary mb-1.5">Description des taches</label><textarea class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all resize-none" rows="4" name="experiences[0][description]" placeholder="• Tache principale 1&#10;• Tache principale 2&#10;• Tache principale 3"></textarea></div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Section 4: Formation -->
              <div class="cv-form-section" id="section-4">
                <div class="mb-8 pb-4 border-b border-outline-variant/10">
                  <h2 class="text-2xl font-bold font-serif text-primary">Formation</h2>
                  <p class="text-sm text-on-surface-variant mt-1">Ajoutez vos etudes et diplomes</p>
                </div>
                <div class="bg-white rounded-2xl border border-outline-variant/10 p-5 md:p-6">
                  <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-5">
                    <div>
                      <h3 class="text-lg font-bold text-primary">Blocs de formation</h3>
                      <p class="text-sm text-on-surface-variant">Ajoutez un bloc pour chaque diplome ou parcours.</p>
                    </div>
                    <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-secondary-container/10 text-secondary-container text-sm font-semibold hover:bg-secondary-container/15 transition-colors" onclick="addFormation()"><span class="material-symbols-outlined text-lg">add_circle</span> Ajouter une formation</button>
                  </div>
                  <div id="formations-container" class="space-y-5">
                    <div class="repeatable-item bg-surface-container-low rounded-xl p-5 border border-outline-variant/10" data-index="0">
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div><label class="block text-sm font-semibold text-primary mb-1.5">Periode</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="formations[0][periode]" placeholder="1995-1998" /></div>
                        <div><label class="block text-sm font-semibold text-primary mb-1.5">Diplome *</label><select class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="formations[0][diplome]" required><option value="">Selectionner un diplome</option><option value="bac">Baccalaureat</option><option value="bts">BTS / DUT</option><option value="licence">Licence</option><option value="master">Master</option><option value="doctorat">Doctorat</option></select></div>
                      </div>
                      <div><label class="block text-sm font-semibold text-primary mb-1.5">Etablissement et lieu</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="formations[0][etablissement]" placeholder="Cegep Saint-Laurent, Montreal (Quebec)" /></div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Section 5: Perfectionnement -->
              <div class="cv-form-section" id="section-5">
                <div class="mb-8 pb-4 border-b border-outline-variant/10">
                  <h2 class="text-2xl font-bold font-serif text-primary">Perfectionnement</h2>
                  <p class="text-sm text-on-surface-variant mt-1">Ajoutez vos formations complementaires</p>
                </div>
                <div class="bg-white rounded-2xl border border-outline-variant/10 p-5 md:p-6">
                  <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-5">
                    <div>
                      <h3 class="text-lg font-bold text-primary">Perfectionnements</h3>
                      <p class="text-sm text-on-surface-variant">Ajoutez vos formations courtes et mises a jour de competences.</p>
                    </div>
                    <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-secondary-container/10 text-secondary-container text-sm font-semibold hover:bg-secondary-container/15 transition-colors" onclick="addPerfectionnement()"><span class="material-symbols-outlined text-lg">add_circle</span> Ajouter un perfectionnement</button>
                  </div>
                  <div id="perfectionnements-container" class="space-y-5">
                    <div class="repeatable-item bg-surface-container-low rounded-xl p-5 border border-outline-variant/10" data-index="0">
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div><label class="block text-sm font-semibold text-primary mb-1.5">Annee</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="perfectionnements[0][annee]" placeholder="2003" /></div>
                        <div><label class="block text-sm font-semibold text-primary mb-1.5">Formation</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="perfectionnements[0][formation]" placeholder="Actualisation en bureautique" /></div>
                      </div>
                      <div><label class="block text-sm font-semibold text-primary mb-1.5">Etablissement et lieu</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="perfectionnements[0][etablissement]" placeholder="College Informatique de la Rive-Sud, Longueuil (Quebec)" /></div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Section 6: Langues -->
              <div class="cv-form-section" id="section-6">
                <div class="mb-8 pb-4 border-b border-outline-variant/10">
                  <h2 class="text-2xl font-bold font-serif text-primary">Langues</h2>
                  <p class="text-sm text-on-surface-variant mt-1">Indiquez les langues que vous maitrisez</p>
                </div>
                <div class="bg-white rounded-2xl border border-outline-variant/10 p-5 md:p-6">
                  <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-5">
                    <div>
                      <h3 class="text-lg font-bold text-primary">Blocs de langues</h3>
                      <p class="text-sm text-on-surface-variant">Ajoutez une carte par langue avec son niveau.</p>
                    </div>
                    <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-secondary-container/10 text-secondary-container text-sm font-semibold hover:bg-secondary-container/15 transition-colors" onclick="addLangue()"><span class="material-symbols-outlined text-lg">add_circle</span> Ajouter une langue</button>
                  </div>
                  <div id="langues-container" class="space-y-5">
                    <div class="repeatable-item bg-surface-container-low rounded-xl p-5 border border-outline-variant/10" data-index="0">
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label class="block text-sm font-semibold text-primary mb-1.5">Langue</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="langues[0][nom]" placeholder="Francais" /></div>
                        <div><label class="block text-sm font-semibold text-primary mb-1.5">Niveau</label><select class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="langues[0][niveau]"><option value="">Selectionner</option><option value="Langue maternelle">Langue maternelle</option><option value="Courant">Courant</option><option value="Intermediaire">Intermediaire</option><option value="Notions de base">Notions de base</option><option value="Connaissances de base">Connaissances de base</option></select></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Section 7: Benevolat -->
              <div class="cv-form-section" id="section-7">
                <div class="mb-8 pb-4 border-b border-outline-variant/10">
                  <h2 class="text-2xl font-bold font-serif text-primary">Activites benevoles</h2>
                  <p class="text-sm text-on-surface-variant mt-1">Ajoutez vos experiences de benevolat</p>
                </div>
                <div class="bg-white rounded-2xl border border-outline-variant/10 p-5 md:p-6">
                  <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-5">
                    <div>
                      <h3 class="text-lg font-bold text-primary">Blocs de benevolat</h3>
                      <p class="text-sm text-on-surface-variant">Ajoutez chaque implication dans une carte separee.</p>
                    </div>
                    <button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-secondary-container/10 text-secondary-container text-sm font-semibold hover:bg-secondary-container/15 transition-colors" onclick="addBenevolat()"><span class="material-symbols-outlined text-lg">add_circle</span> Ajouter une activite benevole</button>
                  </div>
                  <div id="benevolats-container" class="space-y-5">
                    <div class="repeatable-item bg-surface-container-low rounded-xl p-5 border border-outline-variant/10" data-index="0">
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div><label class="block text-sm font-semibold text-primary mb-1.5">Periode</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="benevolats[0][periode]" placeholder="2008-2009" /></div>
                        <div><label class="block text-sm font-semibold text-primary mb-1.5">Role / Activite</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="benevolats[0][role]" placeholder="Benevole lors d'activites-benefice au profit de Leucan" /></div>
                      </div>
                      <div><label class="block text-sm font-semibold text-primary mb-1.5">Organisation (optionnel)</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="benevolats[0][organisation]" placeholder="Leucan" /></div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Section 8: Recapitulatif & Enregistrement -->
              <div class="cv-form-section" id="section-8">
                <div class="mb-8 pb-4 border-b border-outline-variant/10">
                  <h2 class="text-2xl font-bold font-serif text-primary">Enregistrement</h2>
                  <p class="text-sm text-on-surface-variant mt-1">Confirmez l'enregistrement de vos informations</p>
                </div>

                <div id="successAlert" class="hidden bg-secondary-container/10 border border-secondary-container/20 rounded-2xl p-6 mb-6">
                  <div class="flex items-center gap-3 mb-2">
                    <span class="material-symbols-outlined text-secondary-container text-2xl">check_circle</span>
                    <h3 class="font-bold text-secondary-container">Vos informations ont ete enregistrees avec succes !</h3>
                  </div>
                  <p class="text-sm text-secondary-container ml-9">Ces informations seront utilisees pour generer votre CV ulterieurement.</p>
                </div>

                <div class="bg-white rounded-xl border border-outline-variant/10 p-6">
                  <h3 class="font-bold text-primary mb-4">Recapitulatif de vos informations</h3>
                  <div id="summary-content" class="text-sm text-on-surface-variant space-y-3">
                    <p class="text-outline italic">Naviguez jusqu'a la derniere section pour voir le recapitulatif.</p>
                  </div>
                </div>
              </div>

            </form>

            <!-- Actions -->
            <div class="flex justify-between items-center pt-6 mt-8 border-t border-outline-variant/10">
              <button type="button" id="prevBtn" class="px-6 py-3 bg-surface-container text-primary text-sm font-semibold rounded-xl hover:bg-surface-container-low transition-colors flex items-center gap-2" style="display:none" onclick="previousSection()">
                <span class="material-symbols-outlined text-sm">arrow_back</span> Precedent
              </button>
              <div></div>
              <div class="flex items-center gap-3">
                <button type="button" id="nextBtn" class="px-6 py-3 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-all flex items-center gap-2" onclick="nextSection()">
                  Suivant <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </button>
                <button type="button" id="saveBtn" class="px-6 py-3 bg-secondary-container text-white text-sm font-bold rounded-xl hover:bg-secondary transition-all flex items-center gap-2" style="display:none" onclick="saveCVData(event)">
                  <span class="material-symbols-outlined text-sm">save</span> Enregistrer
                </button>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

  </main>
@endsection
@section('scripts')
  <script>
    let currentSection = 1;
    const totalSections = 8;

    function updateWizardUI() {
      document.querySelectorAll('.cv-form-section').forEach((section, index) => {
        section.classList.toggle('active', index + 1 === currentSection);
        section.style.display = index + 1 === currentSection ? 'block' : 'none';
      });

      document.querySelectorAll('.cv-step').forEach((step, index) => {
        step.classList.toggle('active', index + 1 === currentSection);
        step.classList.toggle('bg-white/15', index + 1 === currentSection);
      });

      const progress = (currentSection / totalSections) * 100;
      document.getElementById('progressFill').style.width = `${progress}%`;
      document.getElementById('progressText').textContent = `${currentSection}/${totalSections} sections`;
      document.getElementById('prevBtn').style.display = currentSection === 1 ? 'none' : 'inline-flex';
      document.getElementById('nextBtn').style.display = currentSection === totalSections ? 'none' : 'inline-flex';
      document.getElementById('saveBtn').style.display = currentSection === totalSections ? 'inline-flex' : 'none';

      if (currentSection === totalSections) {
        updateSummary();
      }
    }

    function nextSection() {
      if (currentSection < totalSections) {
        currentSection += 1;
        updateWizardUI();
      }
    }

    function previousSection() {
      if (currentSection > 1) {
        currentSection -= 1;
        updateWizardUI();
      }
    }

    function addRepeatableItem(containerId, html) {
      const container = document.getElementById(containerId);
      const index = container.querySelectorAll('.repeatable-item').length;
      container.insertAdjacentHTML('beforeend', html(index));
    }

    function addCompetence() {
      addRepeatableItem('competences-container', (index) => `
        <div class="repeatable-item bg-surface-container-low rounded-xl p-5 border border-outline-variant/10" data-index="${index}">
          <label class="block text-sm font-semibold text-primary mb-1.5">Competence specifique</label>
          <textarea class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all resize-none" rows="3" name="competences[${index}][description]"></textarea>
        </div>
      `);
    }

    function addExperience() {
      addRepeatableItem('experiences-container', (index) => `
        <div class="repeatable-item bg-surface-container-low rounded-xl p-5 border border-outline-variant/10" data-index="${index}">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div><label class="block text-sm font-semibold text-primary mb-1.5">Periode *</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="experiences[${index}][periode]" required /></div>
            <div><label class="block text-sm font-semibold text-primary mb-1.5">Poste *</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="experiences[${index}][poste]" required /></div>
          </div>
          <div class="mb-4"><label class="block text-sm font-semibold text-primary mb-1.5">Entreprise et lieu</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="experiences[${index}][entreprise]" /></div>
          <div><label class="block text-sm font-semibold text-primary mb-1.5">Description des taches</label><textarea class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all resize-none" rows="4" name="experiences[${index}][description]"></textarea></div>
        </div>
      `);
    }

    function addFormation() {
      addRepeatableItem('formations-container', (index) => `
        <div class="repeatable-item bg-surface-container-low rounded-xl p-5 border border-outline-variant/10" data-index="${index}">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div><label class="block text-sm font-semibold text-primary mb-1.5">Periode</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="formations[${index}][periode]" /></div>
            <div><label class="block text-sm font-semibold text-primary mb-1.5">Diplome *</label><select class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="formations[${index}][diplome]" required><option value="">Selectionner un diplome</option><option value="bac">Baccalaureat</option><option value="bts">BTS / DUT</option><option value="licence">Licence</option><option value="master">Master</option><option value="doctorat">Doctorat</option></select></div>
          </div>
          <div><label class="block text-sm font-semibold text-primary mb-1.5">Etablissement et lieu</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="formations[${index}][etablissement]" /></div>
        </div>
      `);
    }

    function addPerfectionnement() {
      addRepeatableItem('perfectionnements-container', (index) => `
        <div class="repeatable-item bg-surface-container-low rounded-xl p-5 border border-outline-variant/10" data-index="${index}">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div><label class="block text-sm font-semibold text-primary mb-1.5">Annee</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="perfectionnements[${index}][annee]" /></div>
            <div><label class="block text-sm font-semibold text-primary mb-1.5">Formation</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="perfectionnements[${index}][formation]" /></div>
          </div>
          <div><label class="block text-sm font-semibold text-primary mb-1.5">Etablissement et lieu</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="perfectionnements[${index}][etablissement]" /></div>
        </div>
      `);
    }

    function addLangue() {
      addRepeatableItem('langues-container', (index) => `
        <div class="repeatable-item bg-surface-container-low rounded-xl p-5 border border-outline-variant/10" data-index="${index}">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="block text-sm font-semibold text-primary mb-1.5">Langue</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="langues[${index}][nom]" /></div>
            <div><label class="block text-sm font-semibold text-primary mb-1.5">Niveau</label><select class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="langues[${index}][niveau]"><option value="">Selectionner</option><option value="Langue maternelle">Langue maternelle</option><option value="Courant">Courant</option><option value="Intermediaire">Intermediaire</option><option value="Notions de base">Notions de base</option><option value="Connaissances de base">Connaissances de base</option></select></div>
          </div>
        </div>
      `);
    }

    function addBenevolat() {
      addRepeatableItem('benevolats-container', (index) => `
        <div class="repeatable-item bg-surface-container-low rounded-xl p-5 border border-outline-variant/10" data-index="${index}">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div><label class="block text-sm font-semibold text-primary mb-1.5">Periode</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="benevolats[${index}][periode]" /></div>
            <div><label class="block text-sm font-semibold text-primary mb-1.5">Role / Activite</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="benevolats[${index}][role]" /></div>
          </div>
          <div><label class="block text-sm font-semibold text-primary mb-1.5">Organisation</label><input type="text" class="w-full px-4 py-3 bg-white border border-outline-variant/30 rounded-xl text-sm text-primary focus:border-secondary-container/50 focus:ring-0 transition-all" name="benevolats[${index}][organisation]" /></div>
        </div>
      `);
    }

    function updateSummary() {
      const formData = new FormData(document.getElementById('cvDataForm'));
      const summary = [
        ['Nom', formData.get('nom')],
        ['Prenom', formData.get('prenom_cv')],
        ['Email', formData.get('email_cv')],
        ['Telephone', formData.get('telephone_cv')],
        ['Ville', formData.get('ville')],
      ].filter(([, value]) => value);

      document.getElementById('summary-content').innerHTML = summary.length
        ? summary.map(([label, value]) => `<p><span class="font-semibold text-primary">${label}:</span> ${value}</p>`).join('')
        : '<p class="text-outline italic">Remplissez au moins vos informations de base pour afficher le recapitulatif.</p>';
    }

    async function saveCVData(event) {
      event.preventDefault();
      const form = document.getElementById('cvDataForm');
      const formData = new FormData(form);

      try {
        const response = await fetch('{{ route('cv.store') }}', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
          },
          body: formData
        });

        const data = await response.json();
        const successAlert = document.getElementById('successAlert');

        if (response.ok && data.success) {
          successAlert.classList.remove('hidden');
          successAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
          alert(data.message || "Une erreur est survenue lors de l'enregistrement.");
        }
      } catch (error) {
        alert("Impossible d'enregistrer le profil CV pour le moment.");
      }
    }

    document.addEventListener('DOMContentLoaded', () => {
      updateWizardUI();
      document.querySelectorAll('.cv-step').forEach((step) => {
        step.addEventListener('click', () => {
          currentSection = Number(step.dataset.step);
          updateWizardUI();
        });
      });
    });
  </script>
@endsection
