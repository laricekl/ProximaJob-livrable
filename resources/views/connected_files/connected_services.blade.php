@extends("layouts.connected_app")

@section("title", "Emploi")

@section("styles")

<style>
    /* Alert Section */
        .alert-section {
            background: #FF7A2526 !important;
            border: none !important;
        }

        .toggle-switch {
            width: 40px;
            height: 20px;
            background: gray;
            border-radius: 10px;
            position: relative;
            cursor: pointer;
        }
        
        .toggle-switch::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 2px;
            width: 16px;
            height: 16px;
            background: white;
            border-radius: 50%;
            transition: all 0.3s;
        }
        
        .toggle-switch.active {
            background: blue;
        }
        
        .toggle-switch.active::after {
            transform: translateX(20px);
        }

        /* Barre de recherche améliorée */
        .improved-search-bar {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 auto 30px;
        }

        .improved-search-input {
            flex: 1;
            border: none;
            border-right: #666 solid 1px;
            outline: none;
            padding: 12px 16px;
            font-size: 14px;
            color: #333;
        }

        .improved-search-input::placeholder {
            color: #999;
        }

        .improved-location-select {
            border: none;
            outline: none;
            padding: 12px 16px;
            font-size: 14px;
            color: #333;
            background: transparent;
            /* min-width: 200px; */
            width: 450px;
        }

        .improved-search-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .improved-search-btn:hover {
            background: #0056b3;
        }

        .location-icon {
            color: #666;
            margin-right: 8px;
        }

        /* Sidebar Styles */
        .sidebar-card {
            background: #EBF5F4;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            top: 120px;
        }

        .search-input-container {
            position: relative;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            z-index: 5;
        }

        .search-input-container input {
            padding-left: 40px;
        }

        .checkbox-list label {
            display: block;
            padding: 8px 0;
            cursor: pointer;
            font-size: 14px;
        }

        /* Job Cards */
        .job-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .job-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 25px rgba(0,0,0,0.12);
        }

        .time-badge {
            background: var(--primary-color);
            color: white;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
        }

        .bookmark-btn {
            background: none;
            border: none;
            font-size: 16px;
            cursor: pointer;
            color: #ccc;
            transition: color 0.3s;
            position: relative;
        }

        .bookmark-btn:hover {
            color: #ff6b6b;
        }

        .company-avatar {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .company-avatar img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 10px;
        }

        .job-title {
            font-size: 1.3rem;
            margin-bottom: 5px;
            color: #333;
            font-weight: 600;
        }

        .company-name {
            color: #666;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .job-tag {
            background: #e9f4ff;
            color: var(--primary-color);
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .location-text {
            color: #666;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .marge {
            margin-top: 120px;
        }

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

        /* Responsive - Style mis à jour */
        @media (max-width: 768px) {
            .container-fluid {
                padding-left: 15px !important;
                padding-right: 15px !important;
            }

            .sidebar-card {
                position: static;
                margin-bottom: 20px;
                padding: 20px;
            }

            .improved-search-bar {
                flex-direction: column;
                gap: 15px;
                padding: 15px;
            }

            .improved-search-input {
                border-right: none;
                border-bottom: 1px solid #ddd;
                padding-bottom: 15px;
            }

            .improved-location-select {
                width: 100%;
            }

            .improved-search-btn {
                width: 100%;
                justify-content: center;
            }

            /* Job Cards - Style amélioré pour mobile */
            .job-card {
                padding: 20px;
                margin-bottom: 15px;
            }

            .job-card .d-flex.justify-content-between.align-items-center.mb-3 {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
                margin-bottom: 15px !important;
            }

            .job-card .bookmark-btn {
                align-self: flex-end;
                margin-top: -10px;
            }

            .job-card .row.align-items-center {
                margin: 0;
                text-align: center;
            }

            .job-card .col-auto {
                margin-bottom: 15px;
                display: flex;
                justify-content: center;
            }

            .job-card .col {
                text-align: center;
            }

            .job-card .d-flex.flex-wrap.align-items-center.gap-2 {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }

            .job-card .d-flex.flex-wrap.align-items-center.gap-2 .job-tag {
                margin: 2px;
            }

            .job-card .btn {
                width: 100%;
                margin-top: 15px;
            }

            .company-avatar {
                margin: 0 auto;
            }

            .job-title {
                font-size: 1.1rem;
                margin-bottom: 10px;
            }

            .company-name {
                margin-bottom: 15px;
            }

            .location-text {
                justify-content: center;
                margin-bottom: 10px;
            }

            .alert-section {
                margin-bottom: 20px !important;
            }

            .marge {
                margin-top: 20px;
            }

            /* Modal Responsivité */
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
</style>

@endsection

@section("content")

    

    <!-- Modal de candidature -->
    <div class="modal fade application-modal" id="applicationModal" tabindex="-1" aria-labelledby="applicationModalLabel" aria-hidden="true" data-bs-backdrop="static">
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
                    <form id="applicationForm">
                        <!-- Étape 1: Informations personnelles -->
                        <div class="step-content active" id="step-1">
                            <div class="section-title">
                                <i class="fas fa-user section-icon"></i>
                                Informations personnelles
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Nom</label>
                                    <input type="text" class="form-input" name="nom" required>
                                    <div class="error-message" id="nom-error">Ce champ est obligatoire</div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Prénom</label>
                                    <input type="text" class="form-input" name="prenom" required>
                                    <div class="error-message" id="prenom-error">Ce champ est obligatoire</div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-input" name="email" required>
                                <div class="error-message" id="email-error">Veuillez entrer une adresse email valide</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Numéro de téléphone</label>
                                <input type="tel" class="form-input" name="telephone" required>
                                <div class="error-message" id="telephone-error">Ce champ est obligatoire</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Adresse</label>
                                <input type="text" class="form-input" name="adresse" required>
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

                            <div class="divider">
                                <span>OU</span>
                            </div>

                            <div class="cv-generator blue-background">
                                <div class="cv-generator-icon">
                                    <i class="fas fa-magic"></i>
                                </div>
                                <h3>Générer un CV automatiquement</h3>
                                <p>Créez un CV professionnel en quelques minutes avec notre assistant de création.</p>
                                <button type="button" class="generate-btn">
                                    <i class="fas fa-plus"></i>
                                    Générer mon CV
                                </button>
                            </div>
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
                Vous venez de postuler avec succès à la candidature au poste de manager. Veuillez consulter votre mail.
            </p>
            <button class="close-modal-btn" onclick="closeSuccessModal()">Fermer</button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid my-5 px-5">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="alert-section mb-5 p-3 bg-orange rounded-3">
                    <p class="fw-bold text-dark">{{ __('interface.activate_title')}}</p>
                    <p class=" text-dark">Augmenter les chances d'accéder à de nouveaux emplois</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-dark">Alertes emploi</span>
                        <div class="toggle-switch active" onclick="toggleSwitch(this)"></div>
                    </div>
                </div>
                <div class="sidebar-card mt-5">
                    <div class="mb-4">
                        <h5>Recherche par titre de poste</h5>
                        <div class="search-input-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="form-control" placeholder="Titre du poste ou entreprise">
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5>Localisation</h5>
                        <select class="form-select">
                            <option>Choisir la ville</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <h5>Catégorie</h5>
                        <div class="checkbox-list">
                            <label class="d-flex justify-content-between align-items-center">
                                <div><input type="checkbox" class="me-2"> Télécommunications</div>
                                <span class="badge bg-light text-dark">12</span>
                            </label>
                            <label class="d-flex justify-content-between align-items-center">
                                <div><input type="checkbox" class="me-2"> Hôtels & Tourisme</div>
                                <span class="badge bg-light text-dark">10</span>
                            </label>
                            <label class="d-flex justify-content-between align-items-center">
                                <div><input type="checkbox" class="me-2"> Education</div>
                                <span class="badge bg-light text-dark">8</span>
                            </label>
                            <label class="d-flex justify-content-between align-items-center">
                                <div><input type="checkbox" class="me-2"> Finance</div>
                                <span class="badge bg-light text-dark">6</span>
                            </label>
                            <a href="#" class="text-primary small">Afficher Plus</a>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5>Type d'emploi</h5>
                        <div class="checkbox-list">
                            <label class="d-flex justify-content-between align-items-center">
                                <div><input type="radio" name="type" class="me-2"> Temps plein</div>
                                <span class="badge bg-light text-dark">8</span>
                            </label>
                            <label class="d-flex justify-content-between align-items-center">
                                <div><input type="radio" name="type" class="me-2"> Temps partiel</div>
                                <span class="badge bg-light text-dark">6</span>
                            </label>
                            <label class="d-flex justify-content-between align-items-center">
                                <div><input type="radio" name="type" class="me-2"> Freelance</div>
                                <span class="badge bg-light text-dark">4</span>
                            </label>
                            <label class="d-flex justify-content-between align-items-center">
                                <div><input type="radio" name="type" class="me-2"> Stagiaire</div>
                                <span class="badge bg-light text-dark">3</span>
                            </label>
                            <label class="d-flex justify-content-between align-items-center">
                                <div><input type="radio" name="type" class="me-2"> Très fixe</div>
                                <span class="badge bg-light text-dark">2</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5>Salaire</h5>
                        <div class="text-center">
                            <div class="fw-bold mb-3">Salaire: 0 $ CAD - <span id="salaryValue">7999</span> $ CAD</div>
                            <input type="range" class="form-range" min="0" max="15000" value="7999" id="salarySlider">
                            <button class="btn btn-primary mt-3">Filtrer</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Job Listings -->
            <div class="col-md-9">
                <!-- Barre de recherche principale améliorée -->
                <div class="improved-search-bar">
                    <i class="fas fa-search" style="color: #666;"></i>
                    <input type="text" class="improved-search-input" placeholder="Intitulé du poste ou mot-clé">
                    <div style="display: flex; align-items: center;">
                        <i class="fas fa-map-marker-alt location-icon"></i>
                        <select class="improved-location-select">
                            <option>Entrez l'emplacement ou le code postal</option>
                            <option>USA</option>
                            <option>France</option>
                            <option>Benin</option>
                        </select>
                    </div>
                    <button class="improved-search-btn">
                        <i class="fas fa-search"></i>
                        Trouver un emploi
                    </button>
                </div>

                <div class="marge">
                    <div class="container my-4">
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <p class="mb-0 text-muted">Affichage des résultats 6 à 6 sur 10</p>
                                    <select class="form-select" style="width: auto;">
                                        <option>Trier par dernier</option>
                                        <option>Trier par date</option>
                                        <option>Trier par salaire</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Job 1 -->
                    <div class="job-card mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="time-badge">Il y a 10 minutes</span>
                            <button class="bookmark-btn">
                                <i class="far fa-bookmark"></i>
                            </button>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="company-avatar">
                                    <img src="https://via.placeholder.com/60x60/007bff/ffffff?text=SEC" alt="Security Company Logo">
                                </div>
                            </div>
                            <div class="col">
                                <h4 class="job-title">Directeur de la sécurité avancée</h4>
                                <p class="company-name text-muted">Détails du poste</p>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="job-tag"><i class="fas fa-building"></i> Hôtels & Tourisme</span>
                                    <span class="job-tag"><i class="fas fa-briefcase"></i> 1 emploi open</span>
                                    <span class="job-tag"><i class="fas fa-euro-sign"></i> $ CAD40000-$ CAD45000</span>
                                    <span class="location-text"><i class="fas fa-map-marker-alt"></i> Clisserville</span>
                                    <a href="{{ route("connected.connected_job_details")}}" class="btn btn-primary ms-auto">Détail du Poste</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Job 2 -->
                    <div class="job-card mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="time-badge">Il y a 24 hours</span>
                            <button class="bookmark-btn">
                                <i class="far fa-bookmark"></i>
                            </button>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="company-avatar">
                                    <img src="https://via.placeholder.com/60x60/28a745/ffffff?text=HTL" alt="Hotel & Tourism Logo">
                                </div>
                            </div>
                            <div class="col">
                                <h4 class="job-title">Animateur créatif régional</h4>
                                <p class="company-name text-muted">Détails du poste</p>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="job-tag"><i class="fas fa-building"></i> Hôtels</span>
                                    <span class="job-tag"><i class="fas fa-clock"></i> Temps partiel</span>
                                    <span class="job-tag"><i class="fas fa-euro-sign"></i> $ CAD25000-$ CAD35000</span>
                                    <span class="location-text"><i class="fas fa-map-marker-alt"></i> Clisserville</span>
                                    <button class="btn btn-primary ms-auto" onclick="openApplicationModal()">Postuler</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Job 3 -->
                    <div class="job-card mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="time-badge">Il y a jour</span>
                            <button class="bookmark-btn">
                                <i class="far fa-bookmark"></i>
                            </button>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="company-avatar">
                                    <img src="https://via.placeholder.com/60x60/ffc107/000000?text=PLN" alt="Planning Company Logo">
                                </div>
                            </div>
                            <div class="col">
                                <h4 class="job-title">Planificateur d'intégration interne</h4>
                                <p class="company-name text-muted">Détails du poste</p>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="job-tag"><i class="fas fa-hammer"></i> Construction</span>
                                    <span class="job-tag"><i class="fas fa-clock"></i> Temps partiel</span>
                                    <span class="job-tag"><i class="fas fa-euro-sign"></i> $ CAD45000-$ CAD50000</span>
                                    <span class="location-text"><i class="fas fa-map-marker-alt"></i> Canada ville</span>
                                    <button class="btn btn-primary ms-auto" onclick="openApplicationModal()">Postuler</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Job 4 -->
                    <div class="job-card mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="time-badge">Il y a 2-24 hours</span>
                            <button class="bookmark-btn">
                                <i class="far fa-bookmark"></i>
                            </button>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="company-avatar">
                                    <img src="https://via.placeholder.com/60x60/17a2b8/ffffff?text=NET" alt="Network Company Logo">
                                </div>
                            </div>
                            <div class="col">
                                <h4 class="job-title">Directeur de l'intranet du district</h4>
                                <p class="company-name text-muted">Détails du poste</p>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="job-tag"><i class="fas fa-chart-line"></i> Commercial</span>
                                    <span class="job-tag"><i class="fas fa-clock"></i> Temps partiel</span>
                                    <span class="job-tag"><i class="fas fa-euro-sign"></i> $ CAD45000-$ CAD48000</span>
                                    <span class="location-text"><i class="fas fa-map-marker-alt"></i> Canada ville</span>
                                    <button class="btn btn-primary ms-auto" onclick="openApplicationModal()">Postuler</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Job 5 -->
                    <div class="job-card mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="time-badge">Il y a jour</span>
                            <button class="bookmark-btn">
                                <i class="far fa-bookmark"></i>
                            </button>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="company-avatar">
                                    <img src="https://via.placeholder.com/60x60/dc3545/ffffff?text=BIZ" alt="Business Company Logo">
                                </div>
                            </div>
                            <div class="col">
                                <h4 class="job-title">Facilitateur de tactiques d'entreprise</h4>
                                <p class="company-name text-muted">Détails du poste</p>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="job-tag"><i class="fas fa-chart-line"></i> Commercial</span>
                                    <span class="job-tag"><i class="fas fa-clock"></i> Temps partiel</span>
                                    <span class="job-tag"><i class="fas fa-euro-sign"></i> $ CAD39000-$ CAD50000</span>
                                    <span class="location-text"><i class="fas fa-map-marker-alt"></i> Canada ville</span>
                                    <button class="btn btn-primary ms-auto" onclick="openApplicationModal()">Postuler</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Job 6 -->
                    <div class="job-card mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="time-badge">Il y a 2-24 hours</span>
                            <button class="bookmark-btn">
                                <i class="far fa-bookmark"></i>
                            </button>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="company-avatar">
                                    <img src="https://via.placeholder.com/60x60/6f42c1/ffffff?text=FIN" alt="Financial Company Logo">
                                </div>
                            </div>
                            <div class="col">
                                <h4 class="job-title">Consultant en comptes à terme</h4>
                                <p class="company-name text-muted">Détails du poste</p>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="job-tag"><i class="fas fa-university"></i> Financial services</span>
                                    <span class="job-tag"><i class="fas fa-clock"></i> Temps partiel</span>
                                    <span class="job-tag"><i class="fas fa-euro-sign"></i> $ CAD35000-$ CAD48000</span>
                                    <span class="location-text"><i class="fas fa-map-marker-alt"></i> Canada ville</span>
                                    <button class="btn btn-primary ms-auto" onclick="openApplicationModal()">Postuler</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">Suivant</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

@endsection

@section("scripts")

<script>
        // Variables globales pour le formulaire de candidature
        const API_ENDPOINT = 'https://api.example.com/submit';
        let currentStep = 1;
        let formData = {};
        let uploadedFiles = {
            cv: null,
            motivation: null
        };


        // Toggle switch functionality
        function toggleSwitch(element) {
            element.classList.toggle('active');
        }

        // Salary slider functionality
        const salarySlider = document.getElementById('salarySlider');
        const salaryValue = document.getElementById('salaryValue');
        
        if (salarySlider && salaryValue) {
            salarySlider.addEventListener('input', function() {
                salaryValue.textContent = this.value;
            });
        }

        // Ouvrir le modal de candidature
        function openApplicationModal() {
            const modal = new bootstrap.Modal(document.getElementById('applicationModal'));
            modal.show();
            resetForm();
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
            if (file) {
                uploadedFiles[type] = file;
                const statusDiv = document.getElementById(`${type}-status`);
                statusDiv.innerHTML = `<div class="check-circle"><i class="fas fa-check"></i></div><span>Fichier sélectionné</span>`;
                statusDiv.classList.remove('empty');
                
                const errorDiv = document.getElementById(`${type}-upload-error`);
                errorDiv.classList.remove('show');
            }
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
        }

        // Soumettre la candidature
        async function submitApplication() {
            try {
                const submitData = {
                    ...formData,
                    cvFile: uploadedFiles.cv ? uploadedFiles.cv.name : null,
                    motivationFile: uploadedFiles.motivation ? uploadedFiles.motivation.name : null,
                    jobTitle: 'Poste sélectionné',
                    company: 'Entreprise'
                };

                // Simulation d'une requête API
                showSuccessModal();
            } catch (error) {
                console.error('Erreur:', error);
                showSuccessModal();
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

@endsection

    