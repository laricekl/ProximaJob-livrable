<style>
/* CV Generator Modal Styles */
.cv-modal-container {
    display: flex;
    height: 80vh;
    background: #f8f9fa;
    border-radius: 8px;
    overflow: hidden;
}

.cv-sidebar {
    width: 350px;
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    padding: 30px 20px;
    overflow-y: auto;
    flex-shrink: 0;
}

.btn-close-cv-main {
    position: absolute;
    top: 15px;
    right: 15px;
    background: none;
    border: none;
    color: #666;
    font-size: 1.5rem;
    cursor: pointer;
    padding: 5px;
    border-radius: 4px;
    transition: all 0.3s;
    z-index: 10;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-close-cv-main:hover {
    background: #f8f9fa;
    color: #333;
}

.progress-container {
    margin-bottom: 30px;
}

.progress-bar-cv {
    height: 8px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 10px;
}

.progress-fill {
    height: 100%;
    background: rgba(255, 255, 255, 0.8);
    width: 12.5%;
    transition: width 0.3s ease;
}

.progress-text {
    font-size: 0.9rem;
    opacity: 0.9;
}

.cv-nav {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.cv-step {
    display: flex;
    align-items: center;
    padding: 15px;
    border-radius: 8px;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.1);
    opacity: 0.7;
}

.cv-step.active {
    background: rgba(255, 255, 255, 0.2);
    opacity: 1;
}

.cv-step.completed {
    background: rgba(40, 167, 69, 0.3);
    opacity: 1;
}

.step-number {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    margin-right: 15px;
    flex-shrink: 0;
}

.cv-step.active .step-number,
.cv-step.completed .step-number {
    background: rgba(255, 255, 255, 0.3);
}

.step-info {
    flex: 1;
}

.step-title {
    font-weight: 600;
    font-size: 0.95rem;
}

.cv-form-content {
    flex: 1;
    padding: 30px;
    overflow-y: auto;
}

.cv-form-section {
    display: none;
}

.cv-form-section.active {
    display: block;
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.section-header {
    margin-bottom: 30px;
    padding-bottom: 15px;
   
}

.section-title {
    font-size: 1.4rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

.section-subtitle {
    color: #666;
    font-size: 0.9rem;
}

.form-row {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    flex: 1;
    margin-bottom: 20px;
}

.form-label {
    font-weight: 500;
    margin-bottom: 8px;
    color: #333;
    display: block;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s, box-shadow 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.field-error {
    color: #dc3545;
    font-size: 12px;
    margin-top: 5px;
    font-weight: 500;
}

.formation-item,
.competence-item,
.experience-item,
.langue-item,
.perfectionnement-item,
.benevolat-item {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 15px;
    border: 1px solid #dee2e6;
}

.model-selection {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

/* Miniatures CV */
.cv-miniature {
    background: white;
    padding: 8px;
    border-radius: 4px;
    font-size: 6px;
    line-height: 1.2;
    border: 1px solid #ddd;
    height: 160px;
    overflow: hidden;
}

.cv-header {
    text-align: center;
    margin-bottom: 4px;
    padding-bottom: 2px;
    border-bottom: 1px solid #eee;
}

.cv-name {
    font-weight: bold;
    font-size: 7px;
    color: #333;
}

.cv-contact {
    font-size: 5px;
    color: #666;
    margin-top: 1px;
}

.cv-section {
    margin-bottom: 6px;
}

.cv-section-title {
    font-weight: bold;
    font-size: 6px;
    color: #333;
    margin-bottom: 2px;
    padding-bottom: 1px;
 
}

.cv-item {
    display: flex;
    margin-bottom: 2px;
    font-size: 5px;
}

.chronologique-preview .cv-date {
    width: 25%;
    color: #666;
    margin-right: 4px;
}

.chronologique-preview .cv-content {
    flex: 1;
    color: #333;
}

.cv-skills {
    display: flex;
    flex-wrap: wrap;
    gap: 2px;
}

.cv-skill {
    background: #e9ecef;
    padding: 1px 3px;
    border-radius: 2px;
    font-size: 4px;
    color: #495057;
}

.competences-preview .cv-content {
    color: #333;
    font-size: 5px;
}

.model-option {
    border: 2px solid #e9ecef;
    border-radius: 12px;
    padding: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
    position: relative;
}

.model-option:hover {
    border-color: #007bff;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.1);
}

.model-option.selected {
    border-color: #007bff;
    background: #f8f9ff;
}

.model-preview {
    height: 200px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.model-info h4 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 8px;
    color: #333;
}

.model-info p {
    color: #666;
    font-size: 0.9rem;
    margin: 0;
}

.model-radio {
    position: absolute;
    top: 15px;
    right: 15px;
    transform: scale(1.2);
}

.cv-form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 0;
    border-top: 1px solid #e9ecef;
    margin-top: 30px;
}

.btn-right {
    display: flex;
    gap: 10px;
}

.btn {
    padding: 12px 24px;
    border-radius: 6px;
    font-weight: 500;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #545b62;
}

.btn-success {
    background: #28a745;
    color: white;
}

.btn-success:hover {
    background: #1e7e34;
}

.btn-preview {
    background: #17a2b8;
    color: white;  
}

.btn-preview:hover {
    background: #138496;
}

.btn-outline-primary {
    background: transparent;
    color: #007bff;
    border: 2px solid #007bff;
}

.btn-outline-primary:hover {
    background: #007bff;
    color: white;
}

.btn-outline-danger {
    background: transparent;
    color: #dc3545;
    border: 1px solid #dc3545;
    padding: 6px 12px;
    font-size: 12px;
    margin-top: 10px;
}

.btn-outline-danger:hover {
    background: #dc3545;
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .cv-modal-container {
        flex-direction: column;
    }

    .cv-sidebar {
        width: 100%;
        height: auto;
        max-height: 200px;
        overflow-x: auto;
    }

    .cv-nav {
        flex-direction: row;
        gap: 10px;
        padding-bottom: 10px;
    }

    .cv-step {
        min-width: 150px;
        flex-shrink: 0;
    }

    .form-row {
        flex-direction: column;
    }

    .model-selection {
        grid-template-columns: 1fr;
    }

    .cv-form-actions {
        flex-direction: column;
        gap: 15px;
    }

    .cv-form-actions .btn {
        width: 100%;
        justify-content: center;
    }
}


</style>

<!-- Modal de génération de CV -->
<div class="modal fade" id="cvGeneratorModal" tabindex="-1" aria-labelledby="cvGeneratorModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl" style="max-width: 1200px;">
        <div class="modal-content">
            <button type="button" class="btn-close-cv-main" data-bs-dismiss="modal" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="cv-modal-container">
                <!-- Sidebar de navigation -->
                <div class="cv-sidebar">
                    <div class="progress-container">
                        <div class="progress-bar-cv">
                            <div class="progress-fill" id="progressFill"></div>
                        </div>
                        <span class="progress-text" id="progressText">1/8 sections</span>
                    </div>

                    <nav class="cv-nav">
                        <div class="cv-step active" data-step="1">
                            <div class="step-number">1</div>
                            <div class="step-info">
                                <div class="step-title">Informations personnelles</div>
                            </div>
                        </div>

                        <div class="cv-step" data-step="2">
                            <div class="step-number">2</div>
                            <div class="step-info">
                                <div class="step-title">Compétences</div>
                            </div>
                        </div>

                        <div class="cv-step" data-step="3">
                            <div class="step-number">3</div>
                            <div class="step-info">
                                <div class="step-title">Expérience professionnelle</div>
                            </div>
                        </div>

                        <div class="cv-step" data-step="4">
                            <div class="step-number">4</div>
                            <div class="step-info">
                                <div class="step-title">Formation</div>
                            </div>
                        </div>

                        <div class="cv-step" data-step="5">
                            <div class="step-number">5</div>
                            <div class="step-info">
                                <div class="step-title">Perfectionnement</div>
                            </div>
                        </div>

                        <div class="cv-step" data-step="6">
                            <div class="step-number">6</div>
                            <div class="step-info">
                                <div class="step-title">Langues</div>
                            </div>
                        </div>

                        <div class="cv-step" data-step="7">
                            <div class="step-number">7</div>
                            <div class="step-info">
                                <div class="step-title">Activités bénévoles</div>
                            </div>
                        </div>

                        <div class="cv-step" data-step="8">
                            <div class="step-number">8</div>
                            <div class="step-info">
                                <div class="step-title">Modèle</div>
                            </div>
                        </div>
                    </nav>
                </div>

                <!-- Contenu principal -->
                <div class="cv-form-content">
                    <form id="cvGeneratorForm">
                        <!-- Section 1: Informations personnelles -->
                        <div class="cv-form-section active" id="section-1">
                            <div class="section-header">
                                <h3 class="section-title">Informations personnelles</h3>
                                <p class="section-subtitle">Renseignez vos informations de base</p>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Nom *</label>
                                    <input type="text" class="form-control" name="nom" required value = "{{ auth()->check() ? auth()->user()->name : '' }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Prénom *</label>
                                    <input type="text" class="form-control" name="prenom" required value = " {{auth()->check() ? auth()->user()->prenom : ''}}" readonly>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" name="email" required value = "{{auth()->check() ? auth()->user()->email : ''}}" readonly>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Téléphone *</label>
                                    <input type="tel" class="form-control" name="telephone" required value = "{{auth()->check() ? auth()->user()->telephone : ''}} "> 
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Adresse</label>
                                <input type="text" class="form-control" name="adresse" value = " {{auth()->check() ? auth()->user()->adresse : ''}}">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Ville et province</label>
                                <input type="text" class="form-control" name="ville" placeholder="Montréal (Québec)">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Code postal</label>
                                <input type="text" class="form-control" name="code_postal" placeholder="H1B 8T2">
                            </div>
                        </div>

                        <!-- Section 2: Compétences -->
                        <div class="cv-form-section" id="section-2">
                            <div class="section-header">
                                <h3 class="section-title">Compétences</h3>
                                <p class="section-subtitle">Listez vos compétences professionnelles</p>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Langues maîtrisées</label>
                                <textarea class="form-control" rows="2" name="langues_competences" placeholder="Français, anglais et connaissances de base en espagnol"></textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Logiciels maîtrisés</label>
                                <textarea class="form-control" rows="2" name="logiciels" placeholder="Word, Access, Excel, PowerPoint, Simple Comptable"></textarea>
                            </div>

                            <div id="competences-container">
                                <div class="competence-item" data-index="0">
                                    <div class="form-group">
                                        <label class="form-label">Compétence spécifique</label>
                                        <textarea class="form-control" rows="3" name="competences[0][description]" placeholder="Comptabilité générale : comptes clients, comptes fournisseurs, paie, facturation, conciliation bancaire"></textarea>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-outline-primary" onclick="addCompetence()">
                                <i class="fas fa-plus"></i> Ajouter une compétence
                            </button>
                        </div>

                        <!-- Section 3: Expérience professionnelle -->
                        <div class="cv-form-section" id="section-3">
                            <div class="section-header">
                                <h3 class="section-title">Expérience professionnelle</h3>
                                <p class="section-subtitle">Ajoutez vos emplois précédents</p>
                            </div>

                            <div id="experiences-container">
                                <div class="experience-item" data-index="0">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Période *</label>
                                            <input type="text" class="form-control" name="experiences[0][periode]" placeholder="2004-2017" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Poste *</label>
                                            <input type="text" class="form-control" name="experiences[0][poste]" placeholder="Adjointe administrative" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Entreprise et lieu</label>
                                        <input type="text" class="form-control" name="experiences[0][entreprise]" placeholder="Entreprise ABC enr., Montréal (Québec)">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Description des tâches</label>
                                        <textarea class="form-control" rows="4" name="experiences[0][description]" placeholder="• Tâche principale 1&#10;• Tâche principale 2&#10;• Tâche principale 3"></textarea>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-outline-primary" onclick="addExperience()">
                                <i class="fas fa-plus"></i> Ajouter une expérience
                            </button>
                        </div>

                        <!-- Section 4: Formation -->
                        <div class="cv-form-section" id="section-4">
                            <div class="section-header">
                                <h3 class="section-title">Formation</h3>
                                <p class="section-subtitle">Ajoutez vos études et diplômes</p>
                            </div>

                            <div id="formations-container">
                                <div class="formation-item" data-index="0">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Période</label>
                                            <input type="text" class="form-control" name="formations[0][periode]" placeholder="1995-1998">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Diplôme *</label>
                                            <input type="text" class="form-control" name="formations[0][diplome]" placeholder="Diplôme d'études collégiales en techniques administratives, option gestion" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Établissement et lieu</label>
                                        <input type="text" class="form-control" name="formations[0][etablissement]" placeholder="Cégep Saint-Laurent, Montréal (Québec)">
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-outline-primary" onclick="addFormation()">
                                <i class="fas fa-plus"></i> Ajouter une formation
                            </button>
                        </div>

                        <!-- Section 5: Perfectionnement -->
                        <div class="cv-form-section" id="section-5">
                            <div class="section-header">
                                <h3 class="section-title">Perfectionnement</h3>
                                <p class="section-subtitle">Ajoutez vos formations complémentaires</p>
                            </div>

                            <div id="perfectionnements-container">
                                <div class="perfectionnement-item" data-index="0">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Année</label>
                                            <input type="text" class="form-control" name="perfectionnements[0][annee]" placeholder="2003">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Formation</label>
                                            <input type="text" class="form-control" name="perfectionnements[0][formation]" placeholder="Actualisation en bureautique">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Établissement et lieu</label>
                                        <input type="text" class="form-control" name="perfectionnements[0][etablissement]" placeholder="Collège Informatique de la Rive-Sud, Longueuil (Québec)">
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-outline-primary" onclick="addPerfectionnement()">
                                <i class="fas fa-plus"></i> Ajouter un perfectionnement
                            </button>
                        </div>

                        <!-- Section 6: Langues -->
                        <div class="cv-form-section" id="section-6">
                            <div class="section-header">
                                <h3 class="section-title">Langues</h3>
                                <p class="section-subtitle">Indiquez les langues que vous maîtrisez</p>
                            </div>

                            <div id="langues-container">
                                <div class="langue-item" data-index="0">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Langue</label>
                                            <input type="text" class="form-control" name="langues[0][nom]" placeholder="Français">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Niveau</label>
                                            <select class="form-control" name="langues[0][niveau]">
                                                <option value="">Sélectionner</option>
                                                <option value="Langue maternelle">Langue maternelle</option>
                                                <option value="Courant">Courant</option>
                                                <option value="Intermédiaire">Intermédiaire</option>
                                                <option value="Notions de base">Notions de base</option>
                                                <option value="Connaissances de base">Connaissances de base</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-outline-primary" onclick="addLangue()">
                                <i class="fas fa-plus"></i> Ajouter une langue
                            </button>
                        </div>

                        <!-- Section 7: Activités bénévoles -->
                        <div class="cv-form-section" id="section-7">
                            <div class="section-header">
                                <h3 class="section-title">Activités bénévoles</h3>
                                <p class="section-subtitle">Ajoutez vos expériences de bénévolat</p>
                            </div>

                            <div id="benevolats-container">
                                <div class="benevolat-item" data-index="0">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">Période</label>
                                            <input type="text" class="form-control" name="benevolats[0][periode]" placeholder="2008-2009">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Rôle/Activité</label>
                                            <input type="text" class="form-control" name="benevolats[0][role]" placeholder="Bénévole lors d'activités-bénéfice au profit de Leucan">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Organisation (optionnel)</label>
                                        <input type="text" class="form-control" name="benevolats[0][organisation]" placeholder="Leucan">
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-outline-primary" onclick="addBenevolat()">
                                <i class="fas fa-plus"></i> Ajouter une activité bénévole
                            </button>
                        </div>

                        <!-- Section 8: Choix du modèle -->
                        <div class="cv-form-section" id="section-8">
                            <div class="section-header">
                                <h3 class="section-title">Choisir un modèle</h3>
                                <p class="section-subtitle">Sélectionnez le design de votre CV</p>
                            </div>

                            <div class="model-selection">
                                <div class="model-option" data-model="chronologique">
                                    <div class="model-preview">
                                        <div class="cv-miniature chronologique-preview">
                                            <div class="cv-header">
                                                <div class="cv-name">SOPHIE LAPOINTE</div>
                                                <div class="cv-contact">123, rue des Ormes<br>Montréal (Québec) H1B 2H2<br>514.555.1212<br>sophie.lapointe@email.com</div>
                                            </div>
                                            
                                            <div class="cv-section">
                                                <div class="cv-section-title">EXPÉRIENCES DE TRAVAIL</div>
                                                <div class="cv-item">
                                                    <div class="cv-date">2004-2017</div>
                                                    <div class="cv-content"><strong>Adjointe administrative</strong><br>Entreprise ABC enr., Montréal (Québec)</div>
                                                </div>
                                                <div class="cv-item">
                                                    <div class="cv-date">2003-2004</div>
                                                    <div class="cv-content"><strong>Secrétaire aux ventes</strong><br>Meubles du jardin ltée, Laval (Québec)</div>
                                                </div>
                                            </div>
                                            
                                            <div class="cv-section">
                                                <div class="cv-section-title">FORMATION</div>
                                                <div class="cv-item">
                                                    <div class="cv-date">1995-1998</div>
                                                    <div class="cv-content">Diplôme d'études collégiales<br>Cégep Saint-Laurent</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="model-info">
                                        <h4>CV Chronologique</h4>
                                        <p>Met l'accent sur l'expérience professionnelle dans l'ordre chronologique</p>
                                    </div>
                                    <input type="radio" name="model" value="chronologique" class="model-radio" checked>
                                </div>

                                <div class="model-option" data-model="competences">
                                    <div class="model-preview">
                                        <div class="cv-miniature competences-preview">
                                            <div class="cv-header">
                                                <div class="cv-name">SOPHIE LAPOINTE</div>
                                                <div class="cv-contact">123, rue des Ormes<br>Montréal (Québec) H1B 2H2<br>514.555.1212<br>sophie.lapointe@email.com</div>
                                            </div>
                                            
                                            <div class="cv-section">
                                                <div class="cv-section-title">COMPÉTENCES</div>
                                                <div class="cv-content">• Langues : français, anglais<br>• Logiciels : Word, Excel, PowerPoint<br>• Comptabilité générale : comptes clients, comptes fournisseurs</div>
                                            </div>
                                            
                                            <div class="cv-section">
                                                <div class="cv-section-title">EXPÉRIENCES DE TRAVAIL</div>
                                                <div class="cv-item">
                                                    <div class="cv-date">2004-2017</div>
                                                    <div class="cv-content"><strong>Adjointe administrative</strong><br>Entreprise ABC enr.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="model-info">
                                        <h4>CV par Compétences</h4>
                                        <p>Met l'accent sur les compétences et qualifications en premier</p>
                                    </div>
                                    <input type="radio" name="model" value="competences" class="model-radio">
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Actions -->
                    <div class="cv-form-actions">
                        <button type="button" class="btn btn-secondary" id="prevBtn" onclick="previousSection()" style="display: none;">
                            <i class="fas fa-arrow-left"></i> Précédent
                        </button>
                        <div class="btn-right">
                            <button type="button" class="btn btn-primary" id="nextBtn" onclick="nextSection()">
                                Suivant <i class="fas fa-arrow-right"></i>
                            </button>
                             <button type="button" class="btn btn-preview" id="previewBtn" onclick="showPreview()" style="display: none;">
                                    <i class="fas fa-eye"></i> Aperçu
                            </button>
                            <button type="button" class="btn btn-success" id="generateBtn" onclick="generateCV()" style="display: none;">
                                <i class="fas fa-download"></i> Générer mon CV
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentCVSection = 1;
const totalSections = 8;

// Ouvrir la modale de génération de CV
function openCVGeneratorModal() {
    const modalElement = document.getElementById('cvGeneratorModal');
    if (modalElement) {
        const modal = new bootstrap.Modal(modalElement, {
            backdrop: 'static',
            keyboard: true
        });
        modal.show();
        resetCVForm();
    }
}

// Réinitialiser le formulaire
function resetCVForm() {
    currentCVSection = 1;
    updateCVSection();
    
    const form = document.getElementById('cvGeneratorForm');
    if (form) {
        form.reset();
    }

    // Nettoyer les erreurs de validation
    document.querySelectorAll('.field-error').forEach(error => error.remove());
    document.querySelectorAll('.form-control').forEach(field => {
        field.style.borderColor = '#e9ecef';
        field.style.borderWidth = '2px';
    });

    // Réinitialiser les sections dynamiques
    resetDynamicSections();

    // Décocher les modèles
    document.querySelectorAll('.model-option').forEach(option => {
        option.classList.remove('selected');
    });
    // Cocher le modèle chronologique par défaut
    document.querySelector('.model-option[data-model="chronologique"]').classList.add('selected');
    document.querySelector('input[name="model"][value="chronologique"]').checked = true;
}

// Réinitialiser les sections dynamiques
function resetDynamicSections() {
    const containers = ['formations', 'competences', 'experiences', 'langues', 'perfectionnements', 'benevolats'];
    containers.forEach(container => {
        const containerEl = document.getElementById(`${container}-container`);
        if (containerEl) {
            const items = containerEl.querySelectorAll(`.${container.slice(0, -1)}-item`);
            for (let i = 1; i < items.length; i++) {
                items[i].remove();
            }
        }
    });
}

// Navigation entre sections
function nextSection() {
    if (validateCurrentCVSection()) {
        if (currentCVSection < totalSections) {
            currentCVSection++;
            updateCVSection();
        }
    }
}

function previousSection() {
    if (currentCVSection > 1) {
        currentCVSection--;
        updateCVSection();
    }
}

// Mettre à jour l'affichage de la section
function updateCVSection() {
    // Masquer toutes les sections
    document.querySelectorAll('.cv-form-section').forEach(section => {
        section.classList.remove('active');
    });

    // Afficher la section actuelle
    const currentSection = document.getElementById(`section-${currentCVSection}`);
    if (currentSection) {
        currentSection.classList.add('active');
    }

    // Mettre à jour la navigation
    document.querySelectorAll('.cv-step').forEach((step, index) => {
        step.classList.remove('active', 'completed');
        if (index + 1 === currentCVSection) {
            step.classList.add('active');
        } else if (index + 1 < currentCVSection) {
            step.classList.add('completed');
        }
    });

    // Mettre à jour la barre de progression
    const progressFill = document.getElementById('progressFill');
    const progressText = document.getElementById('progressText');
    if (progressFill && progressText) {
        const progressPercent = (currentCVSection / totalSections) * 100;
        progressFill.style.width = `${progressPercent}%`;
        progressText.textContent = `${currentCVSection}/${totalSections} sections`;
    }

    // Mettre à jour les boutons
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const generateBtn = document.getElementById('generateBtn');
    const previewBtn = document.getElementById('previewBtn');

    if (prevBtn) {
        prevBtn.style.display = currentCVSection > 1 ? 'inline-flex' : 'none';
    }

    if (currentCVSection === totalSections) {
        if (nextBtn) nextBtn.style.display = 'none';
        if (generateBtn) generateBtn.style.display = 'inline-flex';
        if (previewBtn) previewBtn.style.display = 'inline-flex';
    } else {
        if (nextBtn) nextBtn.style.display = 'inline-flex';
        if (generateBtn) generateBtn.style.display = 'none';
        if (previewBtn) previewBtn.style.display = 'none';
    }
}

// Valider la section actuelle
function validateCurrentCVSection() {
    const currentSection = document.getElementById(`section-${currentCVSection}`);
    if (!currentSection) return true;

    const requiredFields = currentSection.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        const fieldContainer = field.closest('.form-group');
        let errorElement = fieldContainer.querySelector('.field-error');
        
        if (!field.value.trim()) {
            field.style.borderColor = '#dc3545';
            field.style.borderWidth = '2px';
            
            if (!errorElement) {
                errorElement = document.createElement('div');
                errorElement.className = 'field-error';
                errorElement.textContent = 'Ce champ est obligatoire';
                fieldContainer.appendChild(errorElement);
            }
            isValid = false;
        } else {
            field.style.borderColor = '#e9ecef';
            field.style.borderWidth = '2px';
            
            if (errorElement) {
                errorElement.remove();
            }
        }
    });

    return isValid;
}

// Fonctions pour ajouter des éléments dynamiques
function addFormation() {
    const container = document.getElementById('formations-container');
    const index = container.children.length;
    const formationHTML = `
        <div class="formation-item" data-index="${index}">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Période</label>
                    <input type="text" class="form-control" name="formations[${index}][periode]" placeholder="1995-1998">
                </div>
                <div class="form-group">
                    <label class="form-label">Diplôme *</label>
                    <input type="text" class="form-control" name="formations[${index}][diplome]" placeholder="Diplôme d'études collégiales" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Établissement et lieu</label>
                <input type="text" class="form-control" name="formations[${index}][etablissement]" placeholder="Cégep Saint-Laurent, Montréal (Québec)">
            </div>
            <button type="button" class="btn btn-outline-danger" onclick="removeFormation(${index})">
                <i class="fas fa-trash"></i> Supprimer
            </button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', formationHTML);
}

function removeFormation(index) {
    const item = document.querySelector(`.formation-item[data-index="${index}"]`);
    if (item) item.remove();
}

function addCompetence() {
    const container = document.getElementById('competences-container');
    const index = container.children.length;
    const competenceHTML = `
        <div class="competence-item" data-index="${index}">
            <div class="form-group">
                <label class="form-label">Compétence spécifique</label>
                <textarea class="form-control" rows="3" name="competences[${index}][description]" placeholder="Description détaillée de la compétence"></textarea>
            </div>
            <button type="button" class="btn btn-outline-danger" onclick="removeCompetence(${index})">
                <i class="fas fa-trash"></i> Supprimer
            </button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', competenceHTML);
}

function removeCompetence(index) {
    const item = document.querySelector(`.competence-item[data-index="${index}"]`);
    if (item) item.remove();
}

function addExperience() {
    const container = document.getElementById('experiences-container');
    const index = container.children.length;
    const experienceHTML = `
        <div class="experience-item" data-index="${index}">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Période *</label>
                    <input type="text" class="form-control" name="experiences[${index}][periode]" placeholder="2004-2017" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Poste *</label>
                    <input type="text" class="form-control" name="experiences[${index}][poste]" placeholder="Adjointe administrative" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Entreprise et lieu</label>
                <input type="text" class="form-control" name="experiences[${index}][entreprise]" placeholder="Entreprise ABC enr., Montréal (Québec)">
            </div>
            <div class="form-group">
                <label class="form-label">Description des tâches</label>
                <textarea class="form-control" rows="4" name="experiences[${index}][description]" placeholder="• Tâche principale 1&#10;• Tâche principale 2&#10;• Tâche principale 3"></textarea>
            </div>
            <button type="button" class="btn btn-outline-danger" onclick="removeExperience(${index})">
                <i class="fas fa-trash"></i> Supprimer
            </button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', experienceHTML);
}

function removeExperience(index) {
    const item = document.querySelector(`.experience-item[data-index="${index}"]`);
    if (item) item.remove();
}

function addLangue() {
    const container = document.getElementById('langues-container');
    const index = container.children.length;
    const langueHTML = `
        <div class="langue-item" data-index="${index}">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Langue</label>
                    <input type="text" class="form-control" name="langues[${index}][nom]" placeholder="Français">
                </div>
                <div class="form-group">
                    <label class="form-label">Niveau</label>
                    <select class="form-control" name="langues[${index}][niveau]">
                        <option value="">Sélectionner</option>
                        <option value="Langue maternelle">Langue maternelle</option>
                        <option value="Courant">Courant</option>
                        <option value="Intermédiaire">Intermédiaire</option>
                        <option value="Notions de base">Notions de base</option>
                        <option value="Connaissances de base">Connaissances de base</option>
                    </select>
                </div>
            </div>
            <button type="button" class="btn btn-outline-danger" onclick="removeLangue(${index})">
                <i class="fas fa-trash"></i> Supprimer
            </button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', langueHTML);
}

function removeLangue(index) {
    const item = document.querySelector(`.langue-item[data-index="${index}"]`);
    if (item) item.remove();
}

function addPerfectionnement() {
    const container = document.getElementById('perfectionnements-container');
    const index = container.children.length;
    const perfectionnementHTML = `
        <div class="perfectionnement-item" data-index="${index}">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Année</label>
                    <input type="text" class="form-control" name="perfectionnements[${index}][annee]" placeholder="2003">
                </div>
                <div class="form-group">
                    <label class="form-label">Formation</label>
                    <input type="text" class="form-control" name="perfectionnements[${index}][formation]" placeholder="Actualisation en bureautique">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Établissement et lieu</label>
                <input type="text" class="form-control" name="perfectionnements[${index}][etablissement]" placeholder="Collège Informatique de la Rive-Sud, Longueuil (Québec)">
            </div>
            <button type="button" class="btn btn-outline-danger" onclick="removePerfectionnement(${index})">
                <i class="fas fa-trash"></i> Supprimer
            </button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', perfectionnementHTML);
}

function removePerfectionnement(index) {
    const item = document.querySelector(`.perfectionnement-item[data-index="${index}"]`);
    if (item) item.remove();
}

function addBenevolat() {
    const container = document.getElementById('benevolats-container');
    const index = container.children.length;
    const benevolatHTML = `
        <div class="benevolat-item" data-index="${index}">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Période</label>
                    <input type="text" class="form-control" name="benevolats[${index}][periode]" placeholder="2008-2009">
                </div>
                <div class="form-group">
                    <label class="form-label">Rôle/Activité</label>
                    <input type="text" class="form-control" name="benevolats[${index}][role]" placeholder="Bénévole lors d'activités-bénéfice au profit de Leucan">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Organisation (optionnel)</label>
                <input type="text" class="form-control" name="benevolats[${index}][organisation]" placeholder="Leucan">
            </div>
            <button type="button" class="btn btn-outline-danger" onclick="removeBenevolat(${index})">
                <i class="fas fa-trash"></i> Supprimer
            </button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', benevolatHTML);
}

function removeBenevolat(index) {
    const item = document.querySelector(`.benevolat-item[data-index="${index}"]`);
    if (item) item.remove();
}

// Sélection de modèle
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.model-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.model-option').forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            this.querySelector('.model-radio').checked = true;
        });
    });
});

// Collecter toutes les données du formulaire
 
 function collectCVData() {
    const form = document.getElementById('cvGeneratorForm');
    const formData = new FormData(form);

    const cvData = {
        nom: formData.get('nom') || '',
        prenom: formData.get('prenom') || '',
        email: formData.get('email') || '',
        telephone: formData.get('telephone') || '',
        adresse: formData.get('adresse') || '',
        ville: formData.get('ville') || '',
        code_postal: formData.get('code_postal') || '',
        province: extractProvince(formData.get('ville') || ''),
        langues_competences: formData.get('langues_competences') || '',
        logiciels: formData.get('logiciels') || '',
        formations: [],
        competences: [],
        experiences: [],
        langues: [],
        perfectionnements: [],
        benevolats: [],
        model: formData.get('model') || 'chronologique'
    };

    // Collecter les formations
    document.querySelectorAll('.formation-item').forEach((item, index) => {
        const periode = formData.get(`formations[${index}][periode]`) || '';
        const diplome = formData.get(`formations[${index}][diplome]`) || '';
        const etablissement = formData.get(`formations[${index}][etablissement]`) || '';
        
        if (diplome.trim()) {
            cvData.formations.push({ periode, diplome, etablissement });
        }
    });

    // Collecter les compétences
    document.querySelectorAll('.competence-item').forEach((item, index) => {
        const description = formData.get(`competences[${index}][description]`) || '';
        if (description.trim()) {
            cvData.competences.push({ description });
        }
    });

    // Collecter les expériences
    document.querySelectorAll('.experience-item').forEach((item, index) => {
        const periode = formData.get(`experiences[${index}][periode]`) || '';
        const poste = formData.get(`experiences[${index}][poste]`) || '';
        const entreprise = formData.get(`experiences[${index}][entreprise]`) || '';
        const description = formData.get(`experiences[${index}][description]`) || '';
        
        if (poste.trim()) {
            cvData.experiences.push({ periode, poste, entreprise, description });
        }
    });

    // Collecter les langues
    document.querySelectorAll('.langue-item').forEach((item, index) => {
        const nom = formData.get(`langues[${index}][nom]`) || '';
        const niveau = formData.get(`langues[${index}][niveau]`) || '';
        
        if (nom.trim()) {
            cvData.langues.push({ nom, niveau });
        }
    });

     
    document.querySelectorAll('.perfectionnement-item').forEach((item, index) => {
        const annee = formData.get(`perfectionnements[${index}][annee]`) || '';
        const formation = formData.get(`perfectionnements[${index}][formation]`) || '';
        const etablissement = formData.get(`perfectionnements[${index}][etablissement]`) || '';
        
        if (formation.trim()) {
            cvData.perfectionnements.push({ annee, formation, etablissement });
        }
    });

     
    document.querySelectorAll('.benevolat-item').forEach((item, index) => {
        const periode = formData.get(`benevolats[${index}][periode]`) || '';
        const role = formData.get(`benevolats[${index}][role]`) || '';
        const organisation = formData.get(`benevolats[${index}][organisation]`) || '';
        
        if (role.trim()) {
            cvData.benevolats.push({ periode, role, organisation });
        }
    });

    return cvData;
}

// Fonction helper pour extraire la province du champ ville
function extractProvince(villeText) {
    const matches = villeText.match(/\((.*?)\)/);
    return matches ? matches[1] : '';
}

// Afficher l'aperçu
function showPreview() {
    const selectedModel = document.querySelector('input[name="model"]:checked');
    if (!selectedModel) {
        alert('Veuillez sélectionner un modèle de CV.');
        return;
    }

    const cvData = collectCVData();
    createPreviewModal(cvData, selectedModel.value);
}

// Créer la modal d'aperçu
function createPreviewModal(cvData, modelType) {
    // Supprimer la modal existante si elle existe
    const existingModal = document.getElementById('cvPreviewModal');
    if (existingModal) {
        existingModal.remove();
    }

    // Générer le HTML de l'aperçu selon le modèle
    let previewHTML = '';
    if (modelType === 'chronologique') {
        previewHTML = generateChronologicalPreview(cvData);
    } else if (modelType === 'competences') {
        previewHTML = generateCompetencesPreview(cvData);
    }

    // Créer la modal d'aperçu
    const modalHTML = `
        <div class="modal fade" id="cvPreviewModal" tabindex="-1" aria-labelledby="cvPreviewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cvPreviewModalLabel">Aperçu de votre CV - ${modelType === 'chronologique' ? 'Modèle Chronologique' : 'Modèle par Compétences'}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="cvPreviewDocument" style="background: white; padding: 40px; margin: 20px 0; box-shadow: 0 0 10px rgba(0,0,0,0.1); min-height: 600px; max-width: 800px; margin-left: auto; margin-right: auto;">
                            ${previewHTML}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="button" class="btn btn-success" onclick="generateCVFromPreview()">
                            <i class="fas fa-download"></i> Générer le CV
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Ajouter la modal au DOM
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Afficher la modal
    const modal = new bootstrap.Modal(document.getElementById('cvPreviewModal'));
    modal.show();

    // Nettoyer la modal quand elle est fermée
    document.getElementById('cvPreviewModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

// Générer l'aperçu chronologique
function generateChronologicalPreview(data) {
    return `
        <style>
        .cv-preview {
            max-width: 700px;
            margin: 0 auto;
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
        }
        .cv-header {
            text-align: center;
            margin-bottom: 25px;
        }
        .cv-name {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }
        .cv-contact {
            font-size: 11px;
            margin: 2px 0;
        }
        .cv-section {
            margin-bottom: 20px;
        }
        .cv-section-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 10px;
            text-transform: uppercase;
             
            padding-bottom: 2px;
        }
        .cv-item {
            display: flex;
            margin-bottom: 12px;
            align-items: flex-start;
        }
        .cv-dates {
            width: 40%;
            font-weight: bold;
            flex-shrink: 0;
            margin-right: 20px;
        }
        .cv-content {
            flex: 1;
        }
        .cv-job-title {
            font-weight: bold;
        }
        .cv-company {
            font-size: 11px;
            margin-bottom: 4px;
        }
        .cv-description {
            font-size: 11px;
            line-height: 1.3;
            margin-top: 4px;
        }
        .cv-skills-section {
            font-size: 11px;
            margin-bottom: 15px;
        }
        .cv-skills-section strong {
            display: inline;
        }
        </style>

        <div class="cv-preview">
            <!-- En-tête -->
            <div class="cv-header">
                <div class="cv-name">${(data.nom || '').toUpperCase()} ${(data.prenom || '').toUpperCase()}</div>
                ${data.adresse ? `<div class="cv-contact">${data.adresse}</div>` : ''}
                ${data.ville || data.province || data.code_postal ? 
                    `<div class="cv-contact">${data.ville || ''} ${data.province ? `(${data.province})` : ''} ${data.code_postal || ''}</div>` : ''}
                ${data.telephone ? `<div class="cv-contact">${data.telephone}</div>` : ''}
                ${data.email ? `<div class="cv-contact">Courriel : ${data.email}</div>` : ''}
            </div>

            <!-- Compétences de base -->
            ${(data.langues_competences || data.logiciels) ? `
            <div class="cv-skills-section">
                ${data.langues_competences ? `<div><strong>Langues :</strong> ${data.langues_competences}</div>` : ''}
                ${data.logiciels ? `<div><strong>Logiciels :</strong> ${data.logiciels}</div>` : ''}
            </div>
            ` : ''}

            <!-- Expériences de travail -->
            ${data.experiences.length > 0 ? `
            <div class="cv-section">
                <div class="cv-section-title">EXPÉRIENCES DE TRAVAIL</div>
                ${data.experiences.map(exp => `
                    <div class="cv-item">
                        <div class="cv-dates">${exp.periode || ''}</div>
                        <div class="cv-content">
                            <div class="cv-job-title">${exp.poste || ''}</div>
                            ${exp.entreprise ? `<div class="cv-company">${exp.entreprise}</div>` : ''}
                            ${exp.description ? `<div class="cv-description">${formatDescription(exp.description)}</div>` : ''}
                        </div>
                    </div>
                `).join('')}
            </div>
            ` : ''}

            <!-- Formation -->
            ${data.formations.length > 0 ? `
            <div class="cv-section">
                <div class="cv-section-title">FORMATION</div>
                ${data.formations.map(form => `
                    <div class="cv-item">
                        <div class="cv-dates">${form.periode || ''}</div>
                        <div class="cv-content">
                            <div class="cv-job-title">${form.diplome || ''}</div>
                            ${form.etablissement ? `<div class="cv-company">${form.etablissement}</div>` : ''}
                        </div>
                    </div>
                `).join('')}
            </div>
            ` : ''}

            <!-- Perfectionnement -->
            ${data.perfectionnements.length > 0 ? `
            <div class="cv-section">
                <div class="cv-section-title">PERFECTIONNEMENT</div>
                ${data.perfectionnements.map(perf => `
                    <div class="cv-item">
                        <div class="cv-dates">${perf.annee || ''}</div>
                        <div class="cv-content">
                            <div class="cv-job-title">${perf.formation || ''}</div>
                            ${perf.etablissement ? `<div class="cv-company">${perf.etablissement}</div>` : ''}
                        </div>
                    </div>
                `).join('')}
            </div>
            ` : ''}

            <!-- Langues -->
            ${data.langues.length > 0 ? `
            <div class="cv-section">
                <div class="cv-section-title">LANGUES</div>
                <div style="font-size: 11px;">
                    ${data.langues.map(lang => `${lang.nom}${lang.niveau ? ' : ' + lang.niveau : ''}`).join(', ')}
                </div>
            </div>
            ` : ''}

            <!-- Activités bénévoles -->
            ${data.benevolats.length > 0 ? `
            <div class="cv-section">
                <div class="cv-section-title">ACTIVITÉS BÉNÉVOLES</div>
                ${data.benevolats.map(ben => `
                    <div class="cv-item">
                        <div class="cv-dates">${ben.periode || ''}</div>
                        <div class="cv-content">
                            <div class="cv-job-title">${ben.role || ''}</div>
                            ${ben.organisation ? `<div class="cv-company">${ben.organisation}</div>` : ''}
                        </div>
                    </div>
                `).join('')}
            </div>
            ` : ''}
        </div>
    `;
}

// Générer l'aperçu par compétences
function generateCompetencesPreview(data) {
    return `
        <style>
        .cv-preview {
            max-width: 700px;
            margin: 0 auto;
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
        }
        .cv-header {
            text-align: center;
            margin-bottom: 25px;
        }
        .cv-name {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }
        .cv-contact {
            font-size: 11px;
            margin: 2px 0;
        }
        .cv-section {
            margin-bottom: 20px;
        }
        .cv-section-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 10px;
            text-transform: uppercase;
             
            padding-bottom: 2px;
        }
        .cv-item {
            display: flex;
            margin-bottom: 12px;
            align-items: flex-start;
        }
        .cv-dates {
            width: 40%;
            font-weight: bold;
            flex-shrink: 0;
            margin-right: 20px;
        }
        .cv-content {
            flex: 1;
        }
        .cv-job-title {
            font-weight: bold;
        }
        .cv-company {
            font-size: 11px;
            margin-bottom: 4px;
        }
        .cv-competences-list {
            font-size: 11px;
            line-height: 1.5;
        }
        .cv-competence-item {
            margin-bottom: 8px;
        }
        .cv-skills-section {
            font-size: 11px;
            margin-bottom: 8px;
        }
        </style>

        <div class="cv-preview">
            <!-- En-tête -->
            <div class="cv-header">
                <div class="cv-name">${(data.nom || '').toUpperCase()} ${(data.prenom || '').toUpperCase()}</div>
                ${data.adresse ? `<div class="cv-contact">${data.adresse}</div>` : ''}
                ${data.ville || data.province || data.code_postal ? 
                    `<div class="cv-contact">${data.ville || ''} ${data.province ? `(${data.province})` : ''} ${data.code_postal || ''}</div>` : ''}
                ${data.telephone ? `<div class="cv-contact">${data.telephone}</div>` : ''}
                ${data.email ? `<div class="cv-contact">Courriel : ${data.email}</div>` : ''}
            </div>

            <!-- Compétences en premier -->
            ${(data.langues_competences || data.logiciels || data.competences.length > 0) ? `
            <div class="cv-section">
                <div class="cv-section-title">COMPÉTENCES</div>
                <div class="cv-competences-list">
                    ${data.langues_competences ? `<div class="cv-skills-section">• <strong>Langues :</strong> ${data.langues_competences}</div>` : ''}
                    ${data.logiciels ? `<div class="cv-skills-section">• <strong>Logiciels :</strong> ${data.logiciels}</div>` : ''}
                    ${data.competences.map(comp => `<div class="cv-competence-item">• ${formatDescription(comp.description)}</div>`).join('')}
                </div>
            </div>
            ` : ''}

            <!-- Expériences de travail -->
            ${data.experiences.length > 0 ? `
            <div class="cv-section">
                <div class="cv-section-title">EXPÉRIENCES DE TRAVAIL</div>
                ${data.experiences.map(exp => `
                    <div class="cv-item">
                        <div class="cv-dates">${exp.periode || ''}</div>
                        <div class="cv-content">
                            <div class="cv-job-title">${exp.poste || ''}</div>
                            ${exp.entreprise ? `<div class="cv-company">${exp.entreprise}</div>` : ''}
                        </div>
                    </div>
                `).join('')}
            </div>
            ` : ''}

            <!-- Formation -->
            ${data.formations.length > 0 ? `
            <div class="cv-section">
                <div class="cv-section-title">FORMATION</div>
                ${data.formations.map(form => `
                    <div class="cv-item">
                        <div class="cv-dates">${form.periode || ''}</div>
                        <div class="cv-content">
                            <div class="cv-job-title">${form.diplome || ''}</div>
                            ${form.etablissement ? `<div class="cv-company">${form.etablissement}</div>` : ''}
                        </div>
                    </div>
                `).join('')}
            </div>
            ` : ''}

            <!-- Perfectionnement -->
            ${data.perfectionnements.length > 0 ? `
            <div class="cv-section">
                <div class="cv-section-title">PERFECTIONNEMENT</div>
                ${data.perfectionnements.map(perf => `
                    <div class="cv-item">
                        <div class="cv-dates">${perf.annee || ''}</div>
                        <div class="cv-content">
                            <div class="cv-job-title">${perf.formation || ''}</div>
                            ${perf.etablissement ? `<div class="cv-company">${perf.etablissement}</div>` : ''}
                        </div>
                    </div>
                `).join('')}
            </div>
            ` : ''}

            <!-- Langues -->
            ${data.langues.length > 0 ? `
            <div class="cv-section">
                <div class="cv-section-title">LANGUES</div>
                <div style="font-size: 11px;">
                    ${data.langues.map(lang => `${lang.nom}${lang.niveau ? ' : ' + lang.niveau : ''}`).join(', ')}
                </div>
            </div>
            ` : ''}

            <!-- Activités bénévoles -->
            ${data.benevolats.length > 0 ? `
            <div class="cv-section">
                <div class="cv-section-title">ACTIVITÉS BÉNÉVOLES</div>
                ${data.benevolats.map(ben => `
                    <div class="cv-item">
                        <div class="cv-dates">${ben.periode || ''}</div>
                        <div class="cv-content">
                            <div class="cv-job-title">${ben.role || ''}</div>
                            ${ben.organisation ? `<div class="cv-company">${ben.organisation}</div>` : ''}
                        </div>
                    </div>
                `).join('')}
            </div>
            ` : ''}
        </div>
    `;
}


// Formater les descriptions (préserver les sauts de ligne)
function formatDescription(description) {
    if (!description) return '';
    return description.replace(/\n/g, '<br>');
}

// Générer le CV depuis l'aperçu
function generateCVFromPreview() {
    // Fermer la modal d'aperçu
    const previewModal = bootstrap.Modal.getInstance(document.getElementById('cvPreviewModal'));
    if (previewModal) {
        previewModal.hide();
    }
    
    // Générer le CV
    generateCV();
}

// Générer le CV final
 function generateCV() {
    const selectedModel = document.querySelector('input[name="model"]:checked');
    if (!selectedModel) {
        alert('Veuillez sélectionner un modèle de CV.');
        return;
    }

    const cvData = collectCVData();

    // Validation finale
    if (!cvData.nom || !cvData.prenom) {
        alert('Veuillez renseigner au minimum votre nom et prénom.');
        return;
    }

    // Afficher un loader
    const generateBtn = document.getElementById('generateBtn');
    const originalContent = generateBtn.innerHTML;
    generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Génération en cours...';
    generateBtn.disabled = true;
        console.log(cvData)
    // Envoyer les données au serveur
    fetch('/cv-generator/generate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify(cvData)
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        const contentType = response.headers.get('Content-Type');
        const cvGenerated = response.headers.get('X-CV-Generated');
        return response.blob();
    })
    .then(blob => {
        // Créer le nom du fichier
        const filename = `CV_${cvData.prenom}_${cvData.nom}.pdf`;
        console.log('PDF généré avec succès:', filename);
        
        // Créer un fichier File à partir du blob pour l'upload
        const file = new File([blob], filename, { type: 'application/pdf' });
        
        // Télécharger le fichier PDF
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);

        // Intégrer avec l'upload de fichier dans l'étape 2 si disponible
        if (typeof uploadedFiles !== 'undefined') {
            uploadedFiles.cv = file;
            
            // Mettre à jour l'affichage de l'upload CV
            const cvStatus = document.getElementById('cv-status');
            if (cvStatus) {
                cvStatus.innerHTML = `<div class="check-circle"><i class="fas fa-check"></i></div><span>CV généré: ${filename}</span>`;
                cvStatus.classList.remove('empty');
            }
        }

        // Fermer la modale
        const modal = bootstrap.Modal.getInstance(document.getElementById('cvGeneratorModal'));
        if (modal) {
            modal.hide();
        }

        // Afficher un message de succès
        alert('Votre CV a été généré avec succès ! Il est maintenant disponible pour votre candidature.');
        
        // Assurer que les boutons de navigation de la modal principale sont visibles
        setTimeout(() => {
            const mainModal = document.getElementById('applicationModal');
            if (mainModal && mainModal.classList.contains('show')) {
                // Récupérer les boutons de la modal principale
                const mainPrevBtn = mainModal.querySelector('#prevBtn');
                const mainNextBtn = mainModal.querySelector('#nextBtn');
                
                // Vérifier si currentStep existe dans le contexte global
                if (typeof window.currentStep !== 'undefined') {
                    if (mainPrevBtn) {
                        mainPrevBtn.style.display = window.currentStep > 1 ? 'flex' : 'none';
                    }
                    if (mainNextBtn) {
                        mainNextBtn.style.display = window.currentStep < 3 ? 'flex' : 'none';
                    }
                } else {
                    // Valeurs par défaut si currentStep n'est pas défini
                    if (mainPrevBtn) {
                        mainPrevBtn.style.display = 'flex';
                    }
                    if (mainNextBtn) {
                        mainNextBtn.style.display = 'flex';
                    }
                }
                
                // Forcer la mise à jour de l'affichage
                if (mainModal.querySelector('.form-actions')) {
                    mainModal.querySelector('.form-actions').style.display = 'flex';
                }
            }
        }, 500);
    })
    .catch(error => {
        console.error('Erreur lors de la génération du CV:', error);
        alert('Une erreur est survenue lors de la génération du CV: ' + error.message);
    })
    .finally(() => {
        // Restaurer le bouton
        generateBtn.innerHTML = originalContent;
        generateBtn.disabled = false;
    });
}
</script>