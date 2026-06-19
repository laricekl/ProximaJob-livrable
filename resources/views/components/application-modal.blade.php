
<style>
/* Styles pour le modal de candidature */
.application-modal .modal-dialog {
    max-width: 800px;
}

.application-modal .modal-content {
    border-radius: 12px;
    border: none;
    overflow: hidden;
}

.application-modal .form-header {
    background: white;
    padding: 30px 40px 20px;
    border-bottom: 1px solid #e9ecef;
    position: relative;
}

.application-modal .close-btn {
    position: absolute;
    top: 20px;
    right: 30px;
    background: none;
    border: none;
    font-size: 24px;
    color: #666;
    cursor: pointer;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.application-modal .form-title {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 30px;
    color: #333;
}

.application-modal .progress-steps {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 40px;
    margin-bottom: 20px;
}

.application-modal .step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
}

.application-modal .step-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 18px;
    margin-bottom: 8px;
    position: relative;
    z-index: 2;
}

.application-modal .step-circle.active {
    background: #007bff;
    color: white;
}

.application-modal .step-circle.inactive {
    background: #e9ecef;
    color: #666;
}

.application-modal .step-label {
    font-size: 14px;
    color: #666;
    text-align: center;
}

.application-modal .step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 25px;
    left: 65px;
    width: 60px;
    height: 2px;
    background: #e9ecef;
    z-index: 1;
}

.application-modal .form-content {
    padding: 40px;
}

.application-modal .step-content {
    display: none;
}

.application-modal .step-content.active {
    display: block;
}

.application-modal .section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 20px;
    font-weight: 600;
    color: #333;
    margin-bottom: 30px;
}

.application-modal .section-icon {
    color: #007bff;
}

.application-modal .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.application-modal .form-group {
    margin-bottom: 20px;
}

.application-modal .form-group.full-width {
    grid-column: 1 / -1;
}

.application-modal .form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #333;
}

.application-modal .form-input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 16px;
    transition: border-color 0.3s;
}

.application-modal .form-input:focus {
    outline: none;
    border-color: #007bff;
}

.application-modal .subsection-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin-bottom: 20px;
    margin-top: 25px;
}

.application-modal .blue-background {
    background: linear-gradient(135deg, #e3f2fd, #f0f8ff) !important;
    border: 1px solid #bbdefb;
}

.application-modal .upload-section {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 30px;
    margin-bottom: 30px;
}

.application-modal .upload-item {
    margin-bottom: 25px;
}

.application-modal .upload-item:last-child {
    margin-bottom: 0;
}

.application-modal .upload-label {
    display: block;
    margin-bottom: 10px;
    font-weight: 500;
    color: #333;
}

.application-modal .upload-area {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
}

.application-modal .upload-btn {
    background: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    font-size: 14px;
    flex-shrink: 0;
}

.application-modal .upload-status {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    flex-grow: 1;
    justify-content: flex-end;
}

.application-modal .upload-status.empty {
    color: #666;
}

.application-modal .upload-status:not(.empty) {
    color: #28a745;
}

.application-modal .upload-status .check-circle {
    width: 24px;
    height: 24px;
    background: #28a745;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
    margin-right: 8px;
}

.application-modal .summary-section {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
}

.application-modal .summary-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 15px;
    color: #333;
}

.application-modal .summary-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 10px;
}

.application-modal .summary-item {
    font-size: 16px;
    margin-bottom: 12px;
}

.application-modal .summary-item.inline {
    display: flex;
    align-items: center;
    gap: 8px;
}

.application-modal .summary-item.full-width {
    grid-column: 1 / -1;
}

.application-modal .summary-label {
    font-weight: 600;
    color: #333;
    min-width: fit-content;
}

.application-modal .summary-value {
    color: #666;
    font-weight: 400;
}

.application-modal .documents-list {
    list-style: none;
    padding: 0;
}

.application-modal .documents-list li {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    font-size: 14px;
    color: #007bff;
}

.application-modal .checkbox-group {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin: 25px 0;
}

.application-modal .checkbox {
    margin-top: 3px;
}

.application-modal .checkbox-label {
    font-size: 14px;
    color: #333;
    line-height: 1.5;
}

.application-modal .error-message {
    color: #dc3545;
    font-size: 12px;
    margin-top: 5px;
    display: none;
}

.application-modal .error-message.show {
    display: block;
}

.application-modal .form-input.error {
    border-color: #dc3545;
}

.application-modal .upload-error {
    color: #dc3545;
    font-size: 12px;
    margin-top: 8px;
    display: none;
}

.application-modal .upload-error.show {
    display: block;
}

.application-modal .checkbox-error {
    color: #dc3545;
    font-size: 12px;
    margin-top: 8px;
    display: none;
}

.application-modal .checkbox-error.show {
    display: block;
}

.application-modal .form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 30px;
    border-top: 1px solid #e9ecef;
}

.application-modal .btn-secondary {
    background: transparent;
    color: #666;
    border: none;
    padding: 12px 0;
    font-size: 16px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
}

.application-modal .btn-primary {
    background: #007bff;
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
}

.application-modal .btn-primary:hover {
    background: #0056b3;
}

.divider {
    text-align: center;
    margin: 40px 0;
    position: relative;
    color: #666;
    font-weight: 500;
}

.divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: #e9ecef;
    z-index: 1;
}

.divider span {
    background: #f8f9fa;
    padding: 0 20px;
    position: relative;
    z-index: 2;
}

/* CV Generator */
.cv-generator {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 30px;
    text-align: center;
}

.cv-generator-icon {
    color: #007bff;
    font-size: 24px;
    margin-bottom: 15px;
}

.cv-generator h3 {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 10px;
    color: #333;
}

.cv-generator p {
    color: #666;
    margin-bottom: 20px;
    font-size: 14px;
}

.generate-btn {
    background: transparent;
    color: #007bff;
    border: 2px solid #007bff;
    padding: 12px 24px;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

/* Success Modal */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-overlay.active {
    display: flex;
}

.success-modal {
    background: white;
    border-radius: 12px;
    padding: 60px 40px;
    text-align: center;
    max-width: 400px;
    width: 90%;
}

.success-icon {
    width: 80px;
    height: 80px;
    background: #007bff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 25px;
    color: white;
    font-size: 36px;
}

.success-title {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 15px;
    color: #333;
}

.success-message {
    color: #666;
    margin-bottom: 30px;
    line-height: 1.6;
}

.close-modal-btn {
    background: #e9ecef;
    color: #333;
    border: none;
    padding: 12px 30px;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
}

/* Modal Responsivité */
@media (max-width: 768px) {
    .application-modal .modal-dialog {
        margin: 10px;
    }

    .application-modal .form-header {
        padding: 20px 15px;
    }

    .application-modal .form-content {
        padding: 20px 15px;
    }

    .application-modal .form-row {
        grid-template-columns: 1fr;
        gap: 15px;
    }

    .application-modal .progress-steps {
        gap: 20px;
    }

    .application-modal .step:not(:last-child)::after {
        width: 30px;
        left: 45px;
    }

    .application-modal .upload-area {
        flex-direction: column;
        gap: 10px;
        align-items: stretch;
    }

    .application-modal .upload-status {
        justify-content: center;
    }

    .application-modal .summary-row {
        grid-template-columns: 1fr;
    }

    .application-modal .form-actions {
        flex-direction: column;
        gap: 15px;
    }

    .application-modal .btn-primary,
    .application-modal .btn-secondary {
        width: 100%;
        justify-content: center;
    }

    .success-modal {
        margin: 20px;
        padding: 40px 30px;
    }
}

input[readonly],
textarea[readonly],
select[readonly] {
    background-color: #f5f5f5;   
    color: #666;                
    border-color: #ddd;        
    cursor: not-allowed;       
}

 
</style>

<!-- Modal de candidature -->
<div class="modal fade application-modal" id="applicationModal" tabindex="-1" aria-labelledby="applicationModalLabel" aria-hidden="true" data-bs-backdrop="static" data-default-offre-id="{{ isset($offre) ? $offre->id : '' }}">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <!-- Header -->
            <div class="form-header">
                <button class="close-btn" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                <h1 class="form-title">{{ __('interface.apply_now')}}</h1>

                <!-- Progress Steps -->
                <div class="progress-steps">
                    <div class="step">
                        <div class="step-circle active" id="step-1-circle">1</div>
                        <div class="step-label">Informations</div>
                    </div>
                    <div class="step">
                        <div class="step-circle inactive" id="step-2-circle">2</div>
                        <div class="step-label">Documents</div>
                    </div>
                    <div class="step">
                        <div class="step-circle inactive" id="step-3-circle">3</div>
                        <div class="step-label">Validation</div>
                    </div>
                </div>
            </div>

            <!-- Form Content -->
            <div class="form-content">
                <form id="applicationForm" enctype="multipart/form-data">
                    <!-- Étape 1: Informations personnelles -->
                    <div class="step-content active" id="step-1">
                        <div class="section-title">
                            <i class="fas fa-user section-icon"></i>
                            Informations personnelles
                        </div>
                            <input type="hidden" name="offre_id" id="offre_id" value="{{ isset($offre) ? $offre->id : '' }}">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Nom</label>
                                <input type="text" class="form-input" name="nom" required value ="{{ auth()->user()->name ?? '' }}" readonly>
                                <div class="error-message" id="nom-error">Ce champ est obligatoire</div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Prénom</label>
                                <input type="text" class="form-input" name="prenom" required value ="{{ auth()->user()->prenom ?? '' }}" readonly>
                                <div class="error-message" id="prenom-error">Ce champ est obligatoire</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-input" name="email" required value ="{{ auth()->user()->email ?? '' }}" readonly>
                            <div class="error-message" id="email-error">Veuillez entrer une adresse email valide</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Numéro de téléphone</label>
                            <input type="tel" class="form-input" name="telephone" required value ="{{ auth()->user()->telephone ?? '' }}" readonly>
                            <div class="error-message" id="telephone-error">Ce champ est obligatoire</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Adresse</label>
                            <input type="text" class="form-input" name="adresse" required value ="{{ auth()->user()->adresse ?? '' }}" readonly>
                            <div class="error-message" id="adresse-error">Ce champ est obligatoire</div>
                        </div>
                    </div>

                    <!-- Étape 2: Documents -->
                    <div class="step-content" id="step-2">
                        <div class="section-title">
                            <i class="fas fa-paperclip section-icon"></i>
                            Documents requis
                        </div>

                        <div class="subsection-title">
                            Uploader mes documents
                        </div>

                        <div class="upload-section blue-background">
                            <div class="upload-item">
                                <label class="upload-label">CV (Format PDF ou DOC)</label>
                                <div class="upload-area">
                                    <button type="button" class="upload-btn" onclick="triggerFileUpload('cv')">Parcourir</button>
                                    <div class="upload-status empty" id="cv-status">
                                        <span>Aucun fichier sélectionné</span>
                                    </div>
                                </div>
                                <input type="file" id="cv-upload" name="cv" accept=".pdf,.doc,.docx" style="display: none;" onchange="handleFileUpload('cv', this)">
                                <div class="upload-error" id="cv-upload-error">Veuillez uploader votre CV</div>
                            </div>

                            <div class="upload-item">
                                <label class="upload-label">Lettre de motivation (Format PDF ou DOC)</label>
                                <div class="upload-area">
                                    <button type="button" class="upload-btn" onclick="triggerFileUpload('motivation')">Parcourir</button>
                                    <div class="upload-status empty" id="motivation-status">
                                        <span>Aucun fichier sélectionné</span>
                                    </div>
                                </div>
                                <input type="file" id="motivation-upload" name="motivation" accept=".pdf,.doc,.docx" style="display: none;" onchange="handleFileUpload('motivation', this)">
                                <div class="upload-error" id="motivation-upload-error">Veuillez uploader votre lettre de motivation</div>
                            </div>
                        </div>

                <div class="subsection-title">
                    Autres documents (optionnels)
                </div>

                <div class="upload-section blue-background" id="additional-documents-section">
                    <div id="additional-documents-container">
                        <!-- Les documents seront ajoutés dynamiquement ici -->
                    </div>
                    
                    <div class="text-center mt-3">
                        <button type="button" class="generate-btn" id="add-document-btn">
                            <i class="fas fa-plus"></i>
                            Ajouter un document
                        </button>
                    </div>
                </div>

                    <!-- Template caché pour les nouveaux documents -->
                    <template id="document-template">
                        <div class="upload-item additional-document" data-index="{index}">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="upload-label">Document supplémentaire</label>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-doc-btn">
                                    <i class="fas fa-times"></i> Supprimer
                                </button>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Intitulé du document</label>
                                <input type="text" class="form-input" name="additional_docs[{index}][intitule]" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Description (optionnelle)</label>
                                <input type="text" class="form-input" name="additional_docs[{index}][description]">
                            </div>
                            
                            <div class="upload-area">
                                <button type="button" class="upload-btn browse-doc-btn">Parcourir</button>
                                <div class="upload-status empty">
                                    <span>Aucun fichier sélectionné</span>
                                </div>
                            </div>
                            <input type="file" class="d-none additional-file-input" name="additional_docs[{index}][file]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <div class="upload-error">Veuillez sélectionner un fichier</div>
                        </div>
                    </template>


                        <div class="divider">
                            <span>OU</span>
                        </div>

                        <div class="cv-generator blue-background">
                            <div class="cv-generator-icon">
                                <i class="fas fa-magic"></i>
                            </div>
                            <h3>Générer un CV automatiquement</h3>
                            <p>Créez un CV professionnel en quelques minutes avec notre assistant de création.</p>
                            <button type="button" class="generate-btn" onclick="openCVGeneratorModal()">
                                <i class="fas fa-plus"></i>
                                Générer mon CV
                            </button>
                        </div>
                        <div id="cvGeneratorModalContainer"></div>
                    </div>

                    <!-- Étape 3: Validation -->
                    <div class="step-content" id="step-3">
                        <div class="section-title">
                            <i class="fas fa-check-circle section-icon"></i>
                            Validation
                        </div>

                        <div class="summary-section">
                            <div class="summary-title">Informations personnelles</div>
                            <div class="summary-row">
                                <div class="summary-item inline">
                                    <span class="summary-label">Nom:</span> <span class="summary-value" id="summary-nom">-</span>
                                </div>
                                <div class="summary-item inline">
                                    <span class="summary-label">Prénom:</span> <span class="summary-value" id="summary-prenom">-</span>
                                </div>
                            </div>
                            <div class="summary-row">
                                <div class="summary-item inline">
                                    <span class="summary-label">Email:</span> <span class="summary-value" id="summary-email">-</span>
                                </div>
                                <div class="summary-item inline">
                                    <span class="summary-label">Téléphone:</span> <span class="summary-value" id="summary-telephone">-</span>
                                </div>
                            </div>
                            <div class="summary-item inline full-width">
                                <span class="summary-label">Adresse:</span> <span class="summary-value" id="summary-adresse">-</span>
                            </div>
                        </div>

                        <div class="summary-section">
                            <div class="summary-title">Documents</div>
                            <ul class="documents-list" id="documents-summary">
                            </ul>
                        </div>

                        <div class="checkbox-group">
                            <input type="checkbox" class="checkbox" id="terms" name="terms" required>
                            <label for="terms" class="checkbox-label">
                                J'ai lu et j'approuve les conditions d'utilisations
                            </label>
                        </div>
                        <div class="checkbox-error" id="terms-error">Vous devez accepter les conditions d'utilisation</div>
                    </div>
                </form>

                <!-- Actions -->
                <div class="form-actions">
                    <button type="button" class="btn-secondary" id="prevBtn" onclick="previousStep()" style="display: none;">
                        <i class="fas fa-arrow-left"></i>
                        Retour
                    </button>
                    <button type="button" class="btn-primary" id="nextBtn" onclick="nextStep()">
                        Suivant
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal-overlay" id="successModal">
    <div class="success-modal">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        <h2 class="success-title">Candidature réussie !</h2>
        <p class="success-message">
           Votre candidature a été envoyée avec succès!. Veuillez consulter votre mail.
        </p>
        <button class="close-modal-btn" onclick="closeSuccessModal()">Fermer</button>
    </div>
</div>

<script>



    // Variables globales pour le formulaire de candidature
    const API_ENDPOINT = 'https://api.example.com/submit';
    let currentStep = 1;
    let formData = {};
    let uploadedFiles = {
        cv: null,
        motivation: null
    };
    
    // Rendre currentStep accessible globalement
    window.currentStep = currentStep;

    // Ouvrir le modal de candidature
    function openApplicationModal(offreId = null) {
        const modalElement = document.getElementById('applicationModal');
        const modal = new bootstrap.Modal(modalElement);
        const resolvedOffreId = offreId || modalElement.dataset.defaultOffreId || '';

        resetForm();
        document.getElementById('offre_id').value = resolvedOffreId;
        modal.show();
    }


      function handleApplyClick(offreId) {
            @if(auth()->check())
                // Utilisateur connecté : ouvrir directement le modal de candidature
                openApplicationModal(offreId);
            @else
                // Utilisateur non connecté : stocker l'ID et ouvrir le modal de connexion
                pendingApplicationId = offreId;
                
                // Ouvrir le modal de connexion (ajustez le sélecteur selon votre modal)
                $('#connectModal').modal('show'); // Si vous utilisez Bootstrap modal
                // ou
                // openConnectModal(); // Si vous avez une fonction personnalisée
            @endif
        }


    // Réinitialiser le formulaire
    function resetForm() {
        currentStep = 1;
        formData = {};
        uploadedFiles = { cv: null, motivation: null };
        updateStep();

        document.getElementById('applicationForm').reset();

        document.querySelectorAll('.error-message, .upload-error, .checkbox-error').forEach(element => {
            element.classList.remove('show');
        });

        document.getElementById('cv-status').innerHTML = '<span>Aucun fichier sélectionné</span>';
        document.getElementById('cv-status').classList.add('empty');
        document.getElementById('motivation-status').innerHTML = '<span>Aucun fichier sélectionné</span>';
        document.getElementById('motivation-status').classList.add('empty');
    }

    // Navigation des étapes
    function nextStep() {
        if (validateCurrentStep()) {
            if (currentStep < 3) {
                currentStep++;
                updateStep();
            } else {
                submitApplication();
            }
        }
    }

    function previousStep() {
        if (currentStep > 1) {
            currentStep--;
            updateStep();
        }
    }

    function updateStep() {
        // Mettre à jour la variable globale
        window.currentStep = currentStep;
        
        document.querySelectorAll('.step-content').forEach(step => {
            step.classList.remove('active');
        });

        document.getElementById(`step-${currentStep}`).classList.add('active');

        for (let i = 1; i <= 3; i++) {
            const circle = document.getElementById(`step-${i}-circle`);
            if (i <= currentStep) {
                circle.classList.remove('inactive');
                circle.classList.add('active');
            } else {
                circle.classList.remove('active');
                circle.classList.add('inactive');
            }
        }

        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        prevBtn.style.display = currentStep > 1 ? 'flex' : 'none';

        if (currentStep === 3) {
            nextBtn.innerHTML = '<i class="fas fa-check"></i> Finaliser la candidature';
            updateSummary();
        } else {
            nextBtn.innerHTML = 'Suivant <i class="fas fa-arrow-right"></i>';
        }
    }

    function validateCurrentStep() {
        if (currentStep === 1) {
            const form = document.getElementById('applicationForm');
            let isValid = true;

            const nomField = form.nom;
            const nomError = document.getElementById('nom-error');
            if (!nomField.value.trim()) {
                nomField.classList.add('error');
                nomError.classList.add('show');
                isValid = false;
            } else {
                nomField.classList.remove('error');
                nomError.classList.remove('show');
            }

            const prenomField = form.prenom;
            const prenomError = document.getElementById('prenom-error');
            if (!prenomField.value.trim()) {
                prenomField.classList.add('error');
                prenomError.classList.add('show');
                isValid = false;
            } else {
                prenomField.classList.remove('error');
                prenomError.classList.remove('show');
            }

            const emailField = form.email;
            const emailError = document.getElementById('email-error');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailField.value.trim() || !emailRegex.test(emailField.value)) {
                emailField.classList.add('error');
                emailError.classList.add('show');
                isValid = false;
            } else {
                emailField.classList.remove('error');
                emailError.classList.remove('show');
            }

            const telephoneField = form.telephone;
            const telephoneError = document.getElementById('telephone-error');
            if (!telephoneField.value.trim()) {
                telephoneField.classList.add('error');
                telephoneError.classList.add('show');
                isValid = false;
            } else {
                telephoneField.classList.remove('error');
                telephoneError.classList.remove('show');
            }

            const adresseField = form.adresse;
            const adresseError = document.getElementById('adresse-error');
            if (!adresseField.value.trim()) {
                adresseField.classList.add('error');
                adresseError.classList.add('show');
                isValid = false;
            } else {
                adresseField.classList.remove('error');
                adresseError.classList.remove('show');
            }

            if (isValid) {
                formData.nom = form.nom.value;
                formData.prenom = form.prenom.value;
                formData.email = form.email.value;
                formData.telephone = form.telephone.value;
                formData.adresse = form.adresse.value;
            }

            return isValid;
        } else if (currentStep === 2) {
            let isValid = true;

            const cvError = document.getElementById('cv-upload-error');
            if (!uploadedFiles.cv) {
                cvError.classList.add('show');
                isValid = false;
            } else {
                cvError.classList.remove('show');
            }

            const motivationError = document.getElementById('motivation-upload-error');
            if (!uploadedFiles.motivation) {
                motivationError.classList.add('show');
                isValid = false;
            } else {
                motivationError.classList.remove('show');
            }

            return isValid;
        } else if (currentStep === 3) {
            const termsCheckbox = document.getElementById('terms');
            const termsError = document.getElementById('terms-error');

            if (!termsCheckbox.checked) {
                termsError.classList.add('show');
                return false;
            } else {
                termsError.classList.remove('show');
                return true;
            }
        }
        return true;
    }

    // Gestion des uploads
    function triggerFileUpload(type) {
        document.getElementById(`${type}-upload`).click();
    }

    function handleFileUpload(type, input) {
        const file = input.files[0];
        if (!file) return;

        // Vérification de l'extension
        const validExtensions = ['pdf', 'doc', 'docx'];
        const fileExtension = file.name.split('.').pop().toLowerCase();
        
        if (!validExtensions.includes(fileExtension)) {
            // Afficher l'erreur
            const errorDiv = document.getElementById(`${type}-upload-error`);
            errorDiv.textContent = `Format invalide. Seuls ${validExtensions.join(', ')} sont acceptés`;
            errorDiv.classList.add('show');
            
            // Réinitialiser l'input
            input.value = '';
            return;
        }

        // Si le fichier est valide
        uploadedFiles[type] = file;
        const statusDiv = document.getElementById(`${type}-status`);
        statusDiv.innerHTML = `
            <div class="check-circle">
                <i class="fas fa-check"></i>
            </div>
            <span>${file.name} (${(file.size/1024).toFixed(2)} Ko)</span>
        `;
        statusDiv.classList.remove('empty');
        
        // Cacher les erreurs
        document.getElementById(`${type}-upload-error`).classList.remove('show');
    }

    // Mettre à jour le récapitulatif
    function updateSummary() {
        document.getElementById('summary-nom').textContent = formData.nom || '-';
        document.getElementById('summary-prenom').textContent = formData.prenom || '-';
        document.getElementById('summary-email').textContent = formData.email || '-';
        document.getElementById('summary-telephone').textContent = formData.telephone || '-';
        document.getElementById('summary-adresse').textContent = formData.adresse || '-';

        const docsList = document.getElementById('documents-summary');
        docsList.innerHTML = '';

        if (uploadedFiles.cv) {
            docsList.innerHTML += `<li><i class="fas fa-file-pdf"></i> CV: ${uploadedFiles.cv.name}</li>`;
        }

        if (uploadedFiles.motivation) {
            docsList.innerHTML += `<li><i class="fas fa-file-pdf"></i> Lettre de motivation: ${uploadedFiles.motivation.name}</li>`;
        }
         // Ajouter les documents supplémentaires au récapitulatif
        document.querySelectorAll('.additional-document').forEach(doc => {
            const fileInput = doc.querySelector('.additional-file-input');
            const intitule = doc.querySelector('input[name^="additional_docs"][name$="[intitule]"]').value;
            
            if (fileInput.files[0]) {
                docsList.innerHTML += `<li><i class="fas fa-file-alt"></i> ${intitule}: ${fileInput.files[0].name}</li>`;
            }
        });
    }

    // Soumettre la candidature
  // Soumettre la candidature avec debugging amélioré
async function submitApplication() {
    try {
        // Vérifier qu'on a bien une offre_id
        const offreId = document.getElementById('offre_id').value;
        if (!offreId) {
            throw new Error("Aucune offre sélectionnée");
        }

        // Préparer FormData
        const formDataToSend = new FormData();
        formDataToSend.append('offre_id', offreId);

        // Ajouter les fichiers principaux
        if (!uploadedFiles.cv) {
            throw new Error("Veuillez uploader votre CV");
        }
        if (!uploadedFiles.motivation) {
            throw new Error("Veuillez uploader votre lettre de motivation");
        }

        formDataToSend.append('cv', uploadedFiles.cv);
        formDataToSend.append('motivation', uploadedFiles.motivation);

        // Ajouter les documents supplémentaires
        const additionalDocs = document.querySelectorAll('.additional-document');
        additionalDocs.forEach((docElement, index) => {
            const fileInput = docElement.querySelector('.additional-file-input');
            const intitule = docElement.querySelector('input[name^="additional_docs"][name$="[intitule]"]').value;
            const description = docElement.querySelector('input[name^="additional_docs"][name$="[description]"]').value;
            
            if (fileInput.files[0]) {
                formDataToSend.append(`additional_docs[${index}][file]`, fileInput.files[0]);
                formDataToSend.append(`additional_docs[${index}][intitule]`, intitule);
                if (description) {
                    formDataToSend.append(`additional_docs[${index}][description]`, description);
                }
            }
        });

        // Afficher un loader
        document.getElementById('nextBtn').disabled = true;
        document.getElementById('nextBtn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';

        // Log pour debugging
        //console.log('Données à envoyer:', {
        //    offre_id: offreId,
         //   cv: uploadedFiles.cv?.name,
          //  motivation: uploadedFiles.motivation?.name,
          //  additional_docs: Array.from(additionalDocs).map(doc => ({
             //   intitule: doc.querySelector('input[name^="additional_docs"][name$="[intitule]"]').value,
               // file: doc.querySelector('.additional-file-input').files[0]?.name
           // }))
      //  });

        // Envoyer les données
        const response = await fetch('{{ route("candidatures.store") }}', {
            method: 'POST',
            body: formDataToSend,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        const responseContentType = response.headers.get('content-type') || '';
        const responseBody = responseContentType.includes('application/json')
            ? await response.json()
            : await response.text();

        // Log la réponse complète
       // console.log('Réponse du serveur:', data);
       // console.log('Status de la réponse:', response.status);

        if (!response.ok) {
            console.error('Erreur détaillée:', responseBody);

            let message = `Erreur HTTP ${response.status}`;

            if (typeof responseBody === 'object' && responseBody !== null) {
                if (responseBody.errors) {
                    const validationMessages = Object.values(responseBody.errors)
                        .flat()
                        .filter(Boolean);

                    if (validationMessages.length > 0) {
                        message = validationMessages.join('\n');
                    }
                } else if (responseBody.message) {
                    message = responseBody.message;
                }
            } else if (typeof responseBody === 'string' && responseBody.trim()) {
                message = responseBody.slice(0, 200);
            }

            throw new Error(message);
        }

        // Afficher le message de succès
        showSuccessModal();
        
    } catch (error) {
        console.error('Erreur complète:', error);
        
        // Afficher l'erreur à l'utilisateur avec plus de détails
        alert(`Erreur: ${error.message}`);
        
        // Réactiver le bouton
        document.getElementById('nextBtn').disabled = false;
        document.getElementById('nextBtn').innerHTML = '<i class="fas fa-check"></i> Finaliser la candidature';
    }
}
    // Afficher la modal de succès
    function showSuccessModal() {
        const applicationModal = bootstrap.Modal.getInstance(document.getElementById('applicationModal'));
        if (applicationModal) {
            applicationModal.hide();
        }

        setTimeout(() => {
            document.getElementById('successModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }, 300);
    }

    // Fermer la modal de succès
    function closeSuccessModal() {
        document.getElementById('successModal').classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    // Vérifier si on doit ouvrir le modal au chargement
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('successModal').classList.remove('active');
        document.body.style.overflow = 'auto';

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('openModal') === 'true') {
            setTimeout(() => {
                openApplicationModal();
            }, 500);
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    });


</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let docCounter = 0;
    
    // Ajouter un nouveau document
    document.getElementById('add-document-btn').addEventListener('click', function() {
        const container = document.getElementById('additional-documents-container');
        const template = document.getElementById('document-template');
        const html = template.innerHTML.replace(/{index}/g, docCounter);
        container.insertAdjacentHTML('beforeend', html);
        docCounter++;
    });
    
    // Gérer les événements dynamiquement
    document.addEventListener('click', function(e) {
        // Bouton Parcourir
        if (e.target.classList.contains('browse-doc-btn')) {
            e.preventDefault();
            const fileInput = e.target.closest('.additional-document').querySelector('.additional-file-input');
            fileInput.click();
        }
        
        // Bouton Supprimer
        if (e.target.classList.contains('remove-doc-btn')) {
            e.preventDefault();
            e.target.closest('.additional-document').remove();
        }
    });
    
    // Gérer le changement de fichier
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('additional-file-input')) {
            const file = e.target.files[0];
            const uploadItem = e.target.closest('.additional-document');
            const statusDiv = uploadItem.querySelector('.upload-status');
            
            if (file) {
                statusDiv.innerHTML = `
                    <div class="check-circle">
                        <i class="fas fa-check"></i>
                    </div>
                    <span>${file.name}</span>
                `;
                statusDiv.classList.remove('empty');
            }
        }
    });
});
</script>
