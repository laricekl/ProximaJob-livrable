
@php
    $candidateCvProfile = auth()->check() ? auth()->user()->cvProfile : null;
    $generatedCvByOffer = $candidateCvProfile
        ? $candidateCvProfile->cvGeneres()
            ->whereNotNull('offre_id')
            ->orderByDesc('date_generation')
            ->get()
            ->unique('offre_id')
            ->mapWithKeys(fn ($cv) => [
                (string) $cv->offre_id => [
                    'id' => $cv->id,
                    'name' => $cv->display_name,
                    'file' => 'Document PDF',
                    'previewUrl' => route('cv.personalization.preview', ['filename' => basename($cv->chemin_fichier), 'offre_id' => $cv->offre_id]),
                ],
            ])
            ->all()
        : [];
@endphp

<style>
/* Styles pour le modal de candidature */
.application-modal {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 1050;
    align-items: flex-start;
    justify-content: center;
    overflow-y: auto;
    padding: 24px 16px;
    background: rgba(var(--pj-primary-rgb), 0.58);
}

.application-modal.show {
    display: flex !important;
}

.application-modal .modal-dialog {
    max-width: 720px;
    width: 100%;
    margin: auto;
}

.application-modal .modal-content {
    border-radius: 20px;
    border: 1px solid rgba(var(--pj-border-rgb, 15,23,42), 0.18);
    overflow: hidden;
    background: rgba(255, 255, 255, 0.96);
    box-shadow: 0 30px 80px rgba(var(--pj-primary-rgb), 0.18);
    backdrop-filter: blur(18px);
}

.application-modal .form-header {
    background: linear-gradient(180deg, rgba(255,255,255,0.98) 0%, rgba(248,250,252,0.92) 100%);
    padding: 22px 28px 14px;
    border-bottom: 1px solid rgba(var(--pj-border-rgb, 15,23,42), 0.18);
    position: relative;
}

.application-modal .close-btn {
    position: absolute;
    top: 16px;
    right: 20px;
    background: none;
    border: none;
    font-size: 24px;
    color: var(--pj-text-muted, #76777d);
    cursor: pointer;
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9999px;
    transition: all 0.2s ease;
}

.application-modal .close-btn:hover {
    background: rgba(var(--pj-border-rgb, 15,23,42), 0.12);
    color: var(--pj-primary, #1f2433);
}

.application-modal .form-title {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 18px;
    color: var(--pj-primary, #1f2433);
    letter-spacing: -0.02em;
}

.application-modal .progress-steps {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 28px;
    margin-bottom: 8px;
}

.application-modal .step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
}

.application-modal .step-circle {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 15px;
    margin-bottom: 6px;
    position: relative;
    z-index: 2;
}

.application-modal .step-circle.active {
    background: var(--pj-accent, #eb843c);
    color: white;
    box-shadow: 0 12px 28px rgba(var(--pj-accent-rgb), 0.28);
}

.application-modal .step-circle.inactive {
    background: var(--pj-surface-container, #eceef0);
    color: var(--pj-text-muted, #76777d);
}

.application-modal .step-label {
    font-size: 12px;
    color: var(--pj-text-muted, #76777d);
    text-align: center;
    font-weight: 600;
}

.application-modal .step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 19px;
    left: 50px;
    width: 50px;
    height: 2px;
    background: rgba(var(--pj-border-rgb, 15,23,42), 0.28);
    z-index: 1;
}

.application-modal .form-content {
    padding: 26px 28px;
    background: linear-gradient(180deg, rgba(255,255,255,0.96) 0%, rgba(248,250,252,0.92) 100%);
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
    font-size: 17px;
    font-weight: 600;
    color: var(--pj-primary, #1f2433);
    margin-bottom: 18px;
}

.application-modal .section-icon {
    color: var(--pj-accent, #eb843c);
}

.application-modal .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 14px;
}

.application-modal .form-group {
    margin-bottom: 14px;
}

.application-modal .form-group.full-width {
    grid-column: 1 / -1;
}

.application-modal .form-label {
    display: block;
    margin-bottom: 6px;
    font-weight: 500;
    color: var(--pj-primary, #1f2433);
}

.application-modal .form-input {
    width: 100%;
    padding: 10px 13px;
    border: 1px solid rgba(var(--pj-border-rgb, 15,23,42), 0.35);
    border-radius: 12px;
    font-size: 14px;
    transition: border-color 0.3s, box-shadow 0.3s, background 0.3s;
    background: rgba(255, 255, 255, 0.88);
    color: var(--pj-primary, #1f2433);
}

.application-modal .form-input:focus {
    outline: none;
    border-color: rgba(var(--pj-accent-rgb), 0.55);
    box-shadow: 0 0 0 4px rgba(var(--pj-accent-rgb), 0.12);
}

.application-modal .subsection-title {
    font-size: 14px;
    font-weight: 600;
    color: var(--pj-primary, #1f2433);
    margin-bottom: 10px;
    margin-top: 16px;
}

.application-modal .blue-background {
    background: linear-gradient(135deg, rgba(var(--pj-accent-rgb), 0.08), rgba(255, 247, 237, 0.94)) !important;
    border: 1px solid rgba(var(--pj-accent-rgb), 0.14);
}

.application-modal .upload-section {
    background: rgba(248, 250, 252, 0.84);
    border-radius: 16px;
    padding: 14px;
    margin-bottom: 14px;
}

.application-modal .upload-item {
    margin-bottom: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid rgba(var(--pj-border-rgb, 15,23,42), 0.16);
}

.application-modal .upload-item:last-child {
    margin-bottom: 0;
}

.application-modal .upload-label {
    display: block;
    margin-bottom: 6px;
    font-weight: 500;
    color: var(--pj-primary, #1f2433);
}

.application-modal .upload-area {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
}

.application-modal .upload-btn {
    background: var(--pj-accent, #eb843c);
    color: white;
    border: none;
    padding: 9px 15px;
    border-radius: 9999px;
    font-weight: 700;
    cursor: pointer;
    font-size: 12px;
    flex-shrink: 0;
    transition: all 0.2s ease;
}

.application-modal .upload-btn:hover {
    background: var(--pj-accent-strong, #d9732c);
    transform: translateY(-1px);
}

.application-modal .upload-btn:disabled {
    background: #0f766e;
    cursor: default;
    transform: none;
}

.application-modal .upload-status {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    flex-grow: 1;
    justify-content: flex-end;
}

.application-modal .upload-status.empty {
    color: var(--pj-text-muted, #76777d);
}

.application-modal .upload-status:not(.empty) {
    color: #0f766e;
}

.application-modal .generated-cv-choice {
    display: none;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 10px;
    padding: 12px;
    border: 1px solid rgba(47, 95, 143, 0.18);
    border-radius: 14px;
    background: rgba(47, 95, 143, 0.08);
}

.application-modal .generated-cv-choice.active {
    display: flex;
}

.application-modal .generated-cv-choice.selected {
    border-color: rgba(47, 125, 92, 0.45);
    background: rgba(47, 125, 92, 0.1);
}

.application-modal .generated-cv-actions {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: 8px;
}

.application-modal .upload-status .check-circle {
    width: 24px;
    height: 24px;
    background: #0f766e;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
    margin-right: 8px;
}

.application-modal .summary-section {
    background: rgba(248, 250, 252, 0.86);
    border-radius: 16px;
    padding: 16px;
    margin-bottom: 14px;
    border: 1px solid rgba(var(--pj-border-rgb, 15,23,42), 0.14);
}

.application-modal .summary-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 15px;
    color: var(--pj-primary, #1f2433);
}

.application-modal .summary-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 10px;
}

.application-modal .summary-item {
    font-size: 14px;
    margin-bottom: 8px;
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
    color: var(--pj-primary, #1f2433);
    min-width: fit-content;
}

.application-modal .summary-value {
    color: var(--pj-on-surface-variant, #45464d);
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
    color: var(--pj-accent, #eb843c);
}

.application-modal .checkbox-group {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin: 16px 0;
}

.application-modal .checkbox {
    margin-top: 3px;
}

.application-modal .checkbox-label {
    font-size: 14px;
    color: var(--pj-on-surface, #191c1e);
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
    padding-top: 18px;
    border-top: 1px solid rgba(var(--pj-border-rgb, 15,23,42), 0.18);
}

.application-modal .btn-secondary {
    background: transparent;
    color: var(--pj-text-muted, #76777d);
    border: 1px solid rgba(var(--pj-border-rgb, 15,23,42), 0.28);
    padding: 10px 16px;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    border-radius: 9999px;
    font-weight: 700;
    transition: all 0.2s ease;
}

.application-modal .btn-secondary:hover {
    background: rgba(var(--pj-border-rgb, 15,23,42), 0.12);
    color: var(--pj-primary, #1f2433);
}

.application-modal .btn-primary {
    background: var(--pj-accent, #eb843c);
    color: white;
    border: none;
    padding: 10px 22px;
    border-radius: 9999px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.application-modal .btn-primary:hover {
    background: var(--pj-accent-strong, #d9732c);
    transform: translateY(-1px);
}

.divider {
    text-align: center;
    margin: 40px 0;
    position: relative;
    color: var(--pj-text-muted, #76777d);
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
    background: rgba(255, 255, 255, 0.94);
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
    color: var(--pj-accent, #eb843c);
    font-size: 24px;
    margin-bottom: 15px;
}

.cv-generator h3 {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 10px;
    color: var(--pj-primary, #1f2433);
}

.cv-generator p {
    color: var(--pj-text-muted, #76777d);
    margin-bottom: 20px;
    font-size: 14px;
}

.generate-btn {
    background: transparent;
    color: var(--pj-accent, #eb843c);
    border: 1px solid rgba(var(--pj-accent-rgb), 0.3);
    padding: 12px 24px;
    border-radius: 9999px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.generate-btn:hover {
    background: rgba(var(--pj-accent-rgb), 0.08);
    border-color: rgba(var(--pj-accent-rgb), 0.45);
}

.application-modal .document-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-top: 10px;
}

.application-modal .compact-hint {
    color: var(--pj-text-muted, #76777d);
    font-size: 12px;
    line-height: 1.45;
    margin: 0;
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
    background: #0f766e;
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
    color: var(--pj-primary, #1f2433);
}

.success-message {
    color: var(--pj-text-muted, #76777d);
    margin-bottom: 30px;
    line-height: 1.6;
}

.close-modal-btn {
    background: var(--pj-surface-container, #eceef0);
    color: var(--pj-primary, #1f2433);
    border: none;
    padding: 12px 30px;
    border-radius: 9999px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
}

/* Modal Responsivité */
@media (max-width: 768px) {
    .application-modal {
        padding: 10px;
    }

    .application-modal .modal-dialog {
        margin: 0;
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
    background-color: rgba(255, 255, 255, 0.88);
    color: var(--pj-text-muted, #76777d);
    border-color: rgba(var(--pj-border-rgb, 15,23,42), 0.22);
    cursor: not-allowed;       
}

 
</style>

<!-- Modal de candidature -->
<div class="modal fade application-modal" id="applicationModal" tabindex="-1" aria-labelledby="applicationModalLabel" aria-hidden="true" data-bs-backdrop="static" data-default-offre-id="{{ isset($offre) ? $offre->id : '' }}">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <!-- Header -->
            <div class="form-header">
                <button type="button" class="close-btn" data-bs-dismiss="modal" onclick="closeApplicationModal()" aria-label="Close">
                    <span class="material-symbols-outlined">close</span>
                </button>
                <h1 class="form-title" id="applicationModalTitle">{{ ($existingPostulation ?? null) && !in_array($existingPostulation->status, ['accepted', 'rejected'], true) ? 'Mettre à jour ma candidature' : __('interface.apply_now') }}</h1>

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
                <form id="applicationForm" action="{{ route('candidatures.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Étape 1: Informations personnelles -->
                    <div class="step-content active" id="step-1">
                        <div class="section-title">
                            <span class="material-symbols-outlined section-icon">person</span>
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

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Courriel</label>
                                <input type="email" class="form-input" name="email" required value ="{{ auth()->user()->email ?? '' }}" readonly>
                                <div class="error-message" id="email-error">Veuillez entrer une adresse courriel valide</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Numéro de téléphone</label>
                                <input type="tel" class="form-input" name="telephone" required value ="{{ auth()->user()->telephone ?? '' }}" readonly>
                                <div class="error-message" id="telephone-error">Ce champ est obligatoire</div>
                            </div>
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
                            <span class="material-symbols-outlined section-icon">attach_file</span>
                            Documents requis
                        </div>

                        <input type="hidden" id="generated-cv-id" name="generated_cv_id" value="">
                        <div class="generated-cv-choice" id="generated-cv-choice">
                            <div>
                                <div class="upload-label">CV prêt pour cette offre</div>
                                <div class="upload-status" id="generated-cv-status">
                                    <span>Aucun CV sélectionné</span>
                                </div>
                            </div>
                            <div class="generated-cv-actions">
                                <button type="button" class="upload-btn" id="use-generated-cv-btn">Utiliser ce CV</button>
                                <a href="#" target="_blank" rel="noopener" class="upload-btn" id="preview-generated-cv-link">Voir</a>
                            </div>
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
                                <div class="document-toolbar">
                                    <p class="compact-hint">Utilisez un CV existant, importez un fichier, ou générez une version depuis votre CV principal.</p>
                                    <button type="button" class="generate-btn" onclick="openCVGeneratorModal()">
                                        <span class="material-symbols-outlined text-base">auto_awesome</span>
                                        Générer
                                    </button>
                                </div>
                            </div>

                            <div class="upload-item">
                                <label class="upload-label">Lettre de présentation <span class="text-xs text-outline">(optionnelle)</span></label>
                                <div class="upload-area">
                                    <button type="button" class="upload-btn" onclick="triggerFileUpload('motivation')">Parcourir</button>
                                    <div class="upload-status empty" id="motivation-status">
                                        <span>Aucun fichier sélectionné</span>
                                    </div>
                                </div>
                                <input type="file" id="motivation-upload" name="motivation" accept=".pdf,.doc,.docx" style="display: none;" onchange="handleFileUpload('motivation', this)">
                                <div class="upload-error" id="motivation-upload-error">Format invalide pour la lettre de présentation</div>
                            </div>
                        </div>

                        <div class="subsection-title">
                            Documents optionnels
                        </div>

                        <div class="upload-section blue-background" id="additional-documents-section">
                            <div id="additional-documents-container">
                                <!-- Les documents seront ajoutés dynamiquement ici -->
                            </div>

                            <div class="text-center mt-3">
                                <button type="button" class="generate-btn" id="add-document-btn">
                                    <span class="material-symbols-outlined text-base">add</span>
                                    Ajouter un document
                                </button>
                            </div>
                        </div>

                    <!-- Template caché pour les nouveaux documents -->
                    <template id="document-template">
                        <div class="upload-item additional-document" data-index="{index}">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="upload-label">Document supplémentaire</label>
                                <button type="button" class="remove-doc-btn">
                                    <span class="material-symbols-outlined text-base">close</span> Supprimer
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

                        <div id="cvGeneratorModalContainer"></div>
                    </div>

                    <!-- Étape 3: Validation -->
                    <div class="step-content" id="step-3">
                        <div class="section-title">
                            <span class="material-symbols-outlined section-icon">verified</span>
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
                                    <span class="summary-label">Courriel:</span> <span class="summary-value" id="summary-email">-</span>
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
                        <span class="material-symbols-outlined text-lg">arrow_back</span>
                        Retour
                    </button>
                    <button type="button" class="btn-primary" id="nextBtn" onclick="nextStep()">
                        Suivant
                        <span class="material-symbols-outlined text-lg">arrow_forward</span>
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
            <span class="material-symbols-outlined">check</span>
        </div>
        <h2 class="success-title" id="successModalTitle">Candidature réussie !</h2>
        <p class="success-message" id="successModalMessage">
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
    const generatedCvByOffer = @json($generatedCvByOffer);
    const existingApplicationStatus = @json(($existingPostulation ?? null)?->status);
    const isApplicationUpdate = Boolean(existingApplicationStatus && !['accepted', 'rejected'].includes(existingApplicationStatus));
    let selectedGeneratedCv = null;
    let successReloadTimer = null;
    
    // Rendre currentStep accessible globalement
    window.currentStep = currentStep;

    function resolveBootstrapModal(modalElement) {
        if (window.bootstrap?.Modal) {
            return window.bootstrap.Modal.getInstance(modalElement) || new window.bootstrap.Modal(modalElement);
        }

        return null;
    }

    function openFallbackModal(modalElement) {
        modalElement.style.display = 'flex';
        modalElement.classList.add('show');
        modalElement.removeAttribute('aria-hidden');
        document.body.classList.add('modal-open');
        document.body.style.overflow = 'hidden';
    }

    function closeFallbackModal(modalElement) {
        modalElement.classList.remove('show');
        modalElement.style.display = 'none';
        modalElement.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
        document.body.style.overflow = 'auto';
    }

    function closeApplicationModal() {
        const modalElement = document.getElementById('applicationModal');
        const modal = resolveBootstrapModal(modalElement);

        if (modal) {
            modal.hide();
        } else {
            closeFallbackModal(modalElement);
        }
    }

    window.openCVGeneratorModal = function() {
        const modalElement = document.getElementById('applicationModal');
        const offerId = document.getElementById('offre_id')?.value || modalElement?.dataset.defaultOffreId || '';
        const url = new URL('{{ route('cv.personalization.form') }}', window.location.origin);

        if (offerId) {
            url.searchParams.set('offre_id', offerId);
        }

        window.location.href = url.toString();
    };

    // Ouvrir le modal de candidature
    function openApplicationModal(offreId = null) {
        const modalElement = document.getElementById('applicationModal');
        const modal = resolveBootstrapModal(modalElement);
        const resolvedOffreId = offreId || modalElement.dataset.defaultOffreId || '';

        resetForm();
        document.getElementById('offre_id').value = resolvedOffreId;
        updateGeneratedCvChoice(resolvedOffreId);

        if (modal) {
            modal.show();
        } else {
            openFallbackModal(modalElement);
        }
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
        selectedGeneratedCv = null;
        updateStep();

        document.getElementById('applicationForm').reset();

        document.querySelectorAll('.error-message, .upload-error, .checkbox-error').forEach(element => {
            element.classList.remove('show');
        });

        document.getElementById('cv-status').innerHTML = '<span>Aucun fichier sélectionné</span>';
        document.getElementById('cv-status').classList.add('empty');
        document.getElementById('motivation-status').innerHTML = '<span>Aucun fichier sélectionné</span>';
        document.getElementById('motivation-status').classList.add('empty');
        document.getElementById('generated-cv-id').value = '';
        document.getElementById('generated-cv-choice').classList.remove('active', 'selected');
    }

    function updateGeneratedCvChoice(offreId) {
        const choice = document.getElementById('generated-cv-choice');
        const status = document.getElementById('generated-cv-status');
        const previewLink = document.getElementById('preview-generated-cv-link');
        const useButton = document.getElementById('use-generated-cv-btn');
        const generatedCv = generatedCvByOffer[String(offreId)] || null;

        selectedGeneratedCv = null;
        document.getElementById('generated-cv-id').value = '';
        choice.classList.remove('active', 'selected');
        useButton.disabled = false;
        useButton.innerHTML = 'Utiliser ce CV';

        if (!generatedCv) {
            status.innerHTML = '<span>Aucun CV sélectionné</span>';
            previewLink.setAttribute('href', '#');
            return;
        }

        choice.classList.add('active');
        choice.dataset.cvId = generatedCv.id;
        choice.dataset.cvName = generatedCv.name;
        status.innerHTML = `<span>${generatedCv.name}</span><span class="text-xs text-outline">Document PDF</span>`;
        previewLink.setAttribute('href', generatedCv.previewUrl);
        selectGeneratedCvForApplication();
    }

    function selectGeneratedCvForApplication() {
        const choice = document.getElementById('generated-cv-choice');
        if (!choice?.dataset.cvId) return;

        selectedGeneratedCv = {
            id: choice.dataset.cvId,
            name: choice.dataset.cvName || 'CV',
        };
        document.getElementById('generated-cv-id').value = selectedGeneratedCv.id;
        choice.classList.add('selected');
        const useButton = document.getElementById('use-generated-cv-btn');
        useButton.disabled = true;
        useButton.innerHTML = '<span class="material-symbols-outlined text-base">check</span> Sélectionné';

        uploadedFiles.cv = null;
        document.getElementById('cv-upload').value = '';
        const statusDiv = document.getElementById('cv-status');
        statusDiv.innerHTML = `
            <div class="check-circle">
                <span class="material-symbols-outlined text-[12px]">check</span>
            </div>
            <span>${selectedGeneratedCv.name}</span>
        `;
        statusDiv.classList.remove('empty');
        document.getElementById('cv-upload-error').classList.remove('show');
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
            nextBtn.innerHTML = `<span class="material-symbols-outlined text-lg">check</span> ${isApplicationUpdate ? 'Mettre à jour la candidature' : 'Finaliser la candidature'}`;
            updateSummary();
        } else {
            nextBtn.innerHTML = 'Suivant <span class="material-symbols-outlined text-lg">arrow_forward</span>';
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
            if (!uploadedFiles.cv && !selectedGeneratedCv) {
                cvError.classList.add('show');
                isValid = false;
            } else {
                cvError.classList.remove('show');
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
        if (type === 'cv') {
            selectedGeneratedCv = null;
            document.getElementById('generated-cv-id').value = '';
            document.getElementById('generated-cv-choice').classList.remove('selected');
            const useButton = document.getElementById('use-generated-cv-btn');
            useButton.disabled = false;
            useButton.innerHTML = 'Utiliser ce CV';
        }
        const statusDiv = document.getElementById(`${type}-status`);
        statusDiv.innerHTML = `
            <div class="check-circle">
                <span class="material-symbols-outlined text-[12px]">check</span>
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
            docsList.innerHTML += `<li><span class="material-symbols-outlined text-base">description</span> CV: ${uploadedFiles.cv.name}</li>`;
        } else if (selectedGeneratedCv) {
            docsList.innerHTML += `<li><span class="material-symbols-outlined text-base">description</span> CV: ${selectedGeneratedCv.name}</li>`;
        }

        if (uploadedFiles.motivation) {
            docsList.innerHTML += `<li><span class="material-symbols-outlined text-base">mail</span> Lettre de présentation: ${uploadedFiles.motivation.name}</li>`;
        }
         // Ajouter les documents supplémentaires au récapitulatif
        document.querySelectorAll('.additional-document').forEach(doc => {
            const fileInput = doc.querySelector('.additional-file-input');
            const intitule = doc.querySelector('input[name^="additional_docs"][name$="[intitule]"]').value;
            
            if (fileInput.files[0]) {
                docsList.innerHTML += `<li><span class="material-symbols-outlined text-base">attach_file</span> ${intitule}: ${fileInput.files[0].name}</li>`;
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
        if (!uploadedFiles.cv && !selectedGeneratedCv) {
            throw new Error("Veuillez uploader votre CV");
        }
        if (selectedGeneratedCv) {
            formDataToSend.append('generated_cv_id', selectedGeneratedCv.id);
        } else {
            formDataToSend.append('cv', uploadedFiles.cv);
        }
        if (uploadedFiles.motivation) {
            formDataToSend.append('motivation', uploadedFiles.motivation);
        }

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
        document.getElementById('nextBtn').innerHTML = '<span class="material-symbols-outlined animate-spin text-lg">progress_activity</span> Envoi en cours...';

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
        document.getElementById('nextBtn').innerHTML = '<span class="material-symbols-outlined text-lg">check</span> Finaliser la candidature';
    }
}
    // Afficher la modal de succès
    function showSuccessModal() {
        document.getElementById('successModalTitle').textContent = isApplicationUpdate
            ? 'Candidature mise à jour'
            : 'Candidature réussie !';
        document.getElementById('successModalMessage').textContent = isApplicationUpdate
            ? 'Votre dossier de candidature a été mis à jour avec succès.'
            : 'Votre candidature a été envoyée avec succès.';

        const applicationModalElement = document.getElementById('applicationModal');
        const applicationModal = resolveBootstrapModal(applicationModalElement);
        if (applicationModal) {
            applicationModal.hide();
        } else {
            closeFallbackModal(applicationModalElement);
        }

        setTimeout(() => {
            document.getElementById('successModal').classList.add('active');
            document.body.style.overflow = 'hidden';
            successReloadTimer = window.setTimeout(() => {
                window.location.reload();
            }, 1400);
        }, 300);
    }

    // Fermer la modal de succès
    function closeSuccessModal() {
        if (successReloadTimer) {
            window.clearTimeout(successReloadTimer);
            successReloadTimer = null;
        }
        document.getElementById('successModal').classList.remove('active');
        document.body.style.overflow = 'auto';
        window.location.reload();
    }

    // Vérifier si on doit ouvrir le modal au chargement
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('successModal').classList.remove('active');
        document.body.style.overflow = 'auto';
        document.getElementById('use-generated-cv-btn')?.addEventListener('click', selectGeneratedCvForApplication);

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
                        <span class="material-symbols-outlined text-[12px]">check</span>
                    </div>
                    <span>${file.name}</span>
                `;
                statusDiv.classList.remove('empty');
            }
        }
    });
});
</script>
