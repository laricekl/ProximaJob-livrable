<!DOCTYPE html>
<html lang="fr">

<head>
    @php
        $brandName = $siteSettings?->site_nom ?? 'ProximaJob';
        $brandFavicon = $siteSettings?->favicon_url ?? asset('favicon.ico');
        $brandLogo = $siteSettings?->logo_url ?? asset('img/img-bg-rmv.png');
    @endphp
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('head_content')
    <title>{{ $brandName }} - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ $brandFavicon }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
        }

        /* Navbar */
        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            background: white !important;
        }

        .navbar-brand img {
            height: 80px;
        }

        .navbar-nav .nav-link {
            color: blue !important;
            font-weight: 500;
            margin: 0 10px;
        }

        .navbar-nav .nav-link:hover {
            color: rgb(255, 106, 0) !important;
        }

        .nav-link.active {
            color: rgb(255, 106, 0) !important;
            font-weight: 600;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            position: relative;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-size: cover;
            background-position: center;
            border: 2px solid #007bff;
        }

        /* Profile Dropdown Menu */
        .profile-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            min-width: 250px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1000;
            margin-top: 10px;
        }

        .profile-dropdown.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-header {
            padding: 20px;
            border-bottom: 1px solid #f0f0f0;
            text-align: center;
        }

        .dropdown-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: url('https://images.unsplash.com/photo-1494790108755-2616b2e01e8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=120&q=80');
            background-size: cover;
            background-position: center;
            margin: 0 auto 10px;
            border: 3px solid #007bff;
        }

        .dropdown-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .dropdown-email {
            font-size: 14px;
            color: #666;
        }

        .dropdown-menu {
            padding: 10px 0;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: #333;
            text-decoration: none;
            transition: background 0.3s;
            font-size: 14px;
        }

        .dropdown-item:hover {
            background: #f8f9fa;
            color: #333;
        }

        .dropdown-item i {
            width: 16px;
            color: #666;
        }

        .dropdown-divider {
            height: 1px;
            background: #f0f0f0;
            margin: 10px 0;
        }

        .notification-icon {
            position: relative;
            padding: 8px;
            color: #666;
            font-size: 18px;
        }

        .notification-badge {
            position: absolute;
            top: 2px;
            right: 2px;
            width: 8px;
            height: 8px;
            background: #dc3545;
            border-radius: 50%;
        }



        /* Footer */
        .footer {
            background: #1a1a1a;
            color: white;
            margin-top: 60px;
            padding: 50px 0 20px;
        }

        .footer h5 {
            color: white;
            margin-bottom: 15px;
        }

        .footer p,
        .footer a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer a:hover {
            color: var(--primary-color);
        }

        .footer-bottom {
            border-top: 1px solid #333;
            padding-top: 20px;
            margin-top: 30px;
        }

        .control .btn {
            width: 100%;
        }

        /* Hamburger Button Styling */
        .navbar-toggler {
            border: 2px solid #007bff;
            border-radius: 8px;
            padding: 6px 10px;
            transition: all 0.3s ease;
            outline: none !important;
            box-shadow: none !important;
        }

        .navbar-toggler:hover {
            background-color: #007bff;
            border-color: #007bff;
        }

        .navbar-toggler:focus {
            outline: none !important;
            box-shadow: none !important;
            border-color: #007bff;
        }

        .navbar-toggler:active {
            background-color: transparent !important;
            border-color: #007bff !important;
            box-shadow: none !important;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%23007bff' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
            width: 22px;
            height: 22px;
            transition: all 0.3s ease;
        }

        .navbar-toggler:hover .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='white' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* Cross icon when expanded */
        .navbar-toggler[aria-expanded="true"] .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%23007bff' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M6 6l18 18M6 24L24 6'/%3e%3c/svg%3e");
        }

        .navbar-toggler[aria-expanded="true"]:hover .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='white' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M6 6l18 18M6 24L24 6'/%3e%3c/svg%3e");
        }



        @media (max-width: 768px) {
            .hero {
                background-attachment: scroll;
                padding: 50px 0;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .sidebar-card {
                position: relative;
                top: 0;
            }

            .navbar-brand img {
                height: 60px;
            }

            /* Modal Mobile */
            .modal-dialog {
                margin: 10px;
            }

            .modal-body {
                padding: 20px 15px;
            }

            .row.mb-3 {
                margin-bottom: 1rem !important;
            }

            .row.mb-3 .col-md-6 {
                margin-bottom: 15px;
            }

            /* Footer Mobile */
            .footer-bottom {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .footer .col-lg-3 {
                margin-bottom: 30px;
            }

            .control .btn {
                margin-top: 10px;
            }

            /* Mobile Navigation Actions for Connected Users */
            .navbar-nav {
                margin-bottom: 15px;
            }

            .navbar-nav .nav-link {
                margin: 5px 0 !important;
                padding: 8px 15px !important;
                border-radius: 5px;
                transition: all 0.3s ease;
            }

            .navbar-nav .nav-link.active {
                background-color: #007bff !important;
                color: white !important;
                width: 100%;
            }

            .mobile-user-actions {
                display: flex;
                flex-direction: column;
                gap: 15px;
                padding: 15px 0;
                border-top: 1px solid #dee2e6;
                margin-top: 15px;
                align-items: center;
            }

            .mobile-user-row {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 20px;
                width: 100%;
            }

            .mobile-notification {
                background: #f8f9fa;
                border: 1px solid #dee2e6;
                border-radius: 50%;
                width: 45px;
                height: 45px;
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
                transition: all 0.3s ease;
            }

            .mobile-notification:hover {
                background: #e9ecef;
            }

            .mobile-user-profile {
                background: #f8f9fa;
                border: 1px solid #dee2e6;
                border-radius: 50%;
                width: 45px;
                height: 45px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
                cursor: pointer;
            }

            .mobile-user-profile:hover {
                background: #e9ecef;
            }

            .mobile-user-profile .user-avatar {
                width: 35px;
                height: 35px;
            }

            .mobile-language-selector {
                display: flex;
                justify-content: center;
                margin-top: 5px;
                padding-top: 15px;
                border-top: 1px solid #dee2e6;
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .mobile-notification {
                background: #f8f9fa;
                border: 1px solid #dee2e6;
                border-radius: 50%;
                width: 45px;
                height: 45px;
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
                transition: all 0.3s ease;
            }

            .mobile-notification:hover {
                background: #e9ecef;
            }

            .mobile-notification .btn {
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .mobile-notification i {
                font-size: 1.2rem;
                color: #666;
            }
        }




        .goog-te-banner-frame,
        .goog-te-gadget,
        .skiptranslate,
        #goog-gt-tt,
        .goog-te-balloon-frame,
        .goog-tooltip,
        .goog-tooltip-content,
        .goog-text-highlight {
            display: none !important;
        }


        body>.skiptranslate,
        .VIpgJd-ZVi9od-aZ2wEe-wOHMyf {
            display: none !important;
        }

        body {
            top: 0 !important;
            position: static !important;
        }
    </style>
    @yield('styles')
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="{{ route('welcome') }}">
                <img src="{{ $brandLogo }}" alt="{{ $brandName }}" style="height: 5em; width: auto;">
            </a>

            {{-- Cloche mobile --}}
            <div class="d-lg-none mobile-notification position-relative me-2">
                <button class="btn btn-link p-0 border-0" data-bs-toggle="modal" data-bs-target="#notificationsModal">
                    <i class="fas fa-bell"></i>
                    @if ($unreadNotificationsCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $unreadNotificationsCount }}
                        </span>
                    @endif
                </button>
            </div>


            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('connected') ? 'active' : '' }}"
                            href="{{ route('user.home') }}">{{ __('interface.job_offers') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('connected/candidatures') ? 'active' : '' }}"
                            href="{{ route('user.historiques') }}">{{ __('interface.history') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                            href="{{ route('user.abonnement') }}">{{ __('interface.subscription') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('connected/candidatures-ia') ? 'active' : '' }}"
                            href="{{ route('user.historiques_ia') }}">{{ __('interface.ai_application') }}</a>
                    </li>
                </ul>

                <div class="nav-actions d-flex align-items-center">

                    <div class="notification-icon position-relative d-none d-lg-block">
                        <button class="btn btn-link p-0 border-0" data-bs-toggle="modal"
                            data-bs-target="#notificationsModal">
                            <i class="fas fa-bell"></i>
                            @if ($unreadNotificationsCount > 0)
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ $unreadNotificationsCount }}
                                </span>
                            @endif
                        </button>
                    </div>

                    <div class="user-profile ms-3" onclick="openProfileModal()">
                        <div class="user-avatar"
                            style="background-image: url('{{ Auth::user()->profile_photo_path ? asset('assets/images/user_pdp/' . Auth::user()->profile_photo_path) : asset('assets/images/user_pdp/default.jpg') }}')">
                        </div>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="ms-3">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>

                    {{-- Mobile : avatar + logout dans le collapse --}}
                    <div class="d-lg-none ms-3 d-flex align-items-center gap-2">
                        <div class="mobile-user-profile" onclick="openProfileModal()">
                            <div class="user-avatar"
                                style="background-image: url('{{ Auth::user()->profile_photo_path ? asset('assets/images/user_pdp/' . Auth::user()->profile_photo_path) : asset('assets/images/user_pdp/default.jpg') }}')">
                            </div>
                        </div>
                    </div>
                </div>

                <div style="margin-left: 2em">
                    @include('components.language-selector')

                </div>
            </div>
        </div>
    </nav>

    <!-- Modal de profil utilisateur -->
    <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Barre de progression -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold">{{ __('interface.profile_completed') }}</span>
                            <span class="text-primary fw-bold"
                                id="completionPercentage">{{ Auth::user()->profileCompletionPercentage() }}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div id="completionBar" class="progress-bar bg-primary" role="progressbar"
                                style="width: {{ Auth::user()->profileCompletionPercentage() }}%"
                                aria-valuenow="{{ Auth::user()->profileCompletionPercentage() }}" aria-valuemin="0"
                                aria-valuemax="100"></div>
                        </div>
                    </div>





                    <!-- Informations personnelles -->
                    <div class="mb-4">


                        <!-- Dans le modal de profil -->
                        <form id="profileForm" enctype="multipart/form-data">
                            @csrf
                            <!-- Photo de profil -->
                            <div class="text-center mb-4">
                                <div class="position-relative d-inline-block">
                                    <img id="profileImagePreview"
                                        src="{{ Auth::user()->profile_photo_path ? asset('assets/images/user_pdp/' . Auth::user()->profile_photo_path) : asset('assets/images/user_pdp/default.jpg') }}"
                                        class="rounded-circle" width="80" height="80"
                                        style="border: 3px solid #007bff;">
                                    <input type="file" id="profilePhoto" name="profile_photo" accept="image/*"
                                        style="display: none;">
                                </div>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-primary btn-sm"
                                        onclick="document.getElementById('profilePhoto').click()">
                                        <i class="fas fa-camera me-1"></i>
                                        Changer Photo
                                    </button>
                                </div>
                            </div>

                            <h6 class="fw-bold mb-3">
                                <i class="fas fa-user text-primary me-2"></i>
                                {{ __('interface.personal_info') }}
                            </h6>

                            <!-- Informations personnelles -->
                            <div class="mb-4">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nom</label>
                                        <input type="text" name="name" class="form-control"
                                            value="{{ old('name', Auth()->user()->name) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Prénom</label>
                                        <input type="text" name="prenom" class="form-control"
                                            value="{{ old('prenom', Auth()->user()->prenom) }}">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Email</label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('email', Auth()->user()->email) }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Téléphone</label>
                                    <input type="tel" name="telephone" class="form-control"
                                        value="{{ old('telephone', Auth()->user()->telephone) }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Adresse</label>
                                    <textarea name="adresse" class="form-control" rows="2">{{ old('adresse', Auth()->user()->adresse) }}</textarea>
                                </div>
                            </div>

                            <!-- CV -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">
                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                    Curriculum Vitae
                                </h6>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Télécharger votre CV</label>
                                    <input type="file" name="cv" class="form-control"
                                        accept=".pdf,.doc,.docx,.txt">
                                    <small class="text-muted">Formats acceptés: PDF, DOC, DOCX, TXT (Max: 5MB)</small>

                                    @if (Auth()->user()->cv)
                                        <div class="cv-preview mt-3">
                                            <div
                                                class="d-flex align-items-center p-3 bg-light rounded-3 border-start border-4 border-success">
                                                <div class="cv-indicator me-3">
                                                    <div class="bg-success rounded-circle p-2">
                                                        <i class="fas fa-check text-white"></i>
                                                    </div>
                                                </div>
                                                <div class="cv-details flex-grow-1">
                                                    <span class="fw-semibold text-success">CV disponible</span>
                                                    <div class="text-muted small">Dernière mise à jour:
                                                        {{ Auth()->user()->updated_at->format('d/m/Y') }}
                                                    </div>
                                                </div>
                                                <div class="cv-actions">
                                                    <a href="{{ asset(Auth()->user()->cv) }}" target="_blank"
                                                        class="btn btn-success btn-sm rounded-pill px-3">
                                                        <i class="fas fa-external-link-alt me-1"></i>
                                                        Ouvrir
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-4">
                                <!-- Secteur d'activité -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Secteur d'activité</label>
                                    <select name="sector_id" class="form-select select2-single">
                                        <option value="">Sélectionnez un secteur</option>
                                        @foreach ($sectors as $sector)
                                            @php
                                                $userSectorId = Auth()->user()->candidateSector->sector_id ?? null;
                                            @endphp
                                            <option value="{{ $sector->id }}"
                                                {{ old('sector_id', $userSectorId) == $sector->id ? 'selected' : '' }}>
                                                {{ $sector->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Dernier diplôme
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Dernier diplôme</label>
                                    <select name="diplome_id" class="form-select select2-single">
                                        <option value="">Sélectionnez un diplôme</option>
                                        @foreach ($diplomes as $diplome)
@php
    $userDiplomeId = Auth()->user()->candidateSector->diplome_id ?? null;
@endphp
                                            <option value="{{ $diplome->id }}" {{ old('diplome_id', $userDiplomeId) == $diplome->id ? 'selected' : '' }}>
                                                {{ $diplome->nom_diplome }} - {{ $diplome->nom_anglais }} - {{ $diplome->sigle }}
                                            </option>
@endforeach
                                    </select>
                                </div>-->

                                <!-- Années d'expérience -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Années d'expérience</label>
                                    <input type="number" name="experience_years" class="form-control"
                                        value="{{ old('experience_years', Auth()->user()->candidateSector->experience_years ?? '') }}"
                                        placeholder="Ex: 5" min="0" max="50">
                                </div>

                                <!-- Compétences -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Compétences</label>
                                    <select name="skills[]" class="form-select select2-multiple" multiple="multiple">
                                        @foreach ($skills as $skill)
                                            @php
                                                // Méthode robuste qui fonctionne toujours
                                                try {
                                                    $userSkillIds = Auth::user()->skills->pluck('id')->toArray();
                                                } catch (\Exception $e) {
                                                    // Fallback manuel si la relation échoue
                                                    $userSkillIds = \App\Models\CandidateSkill::where(
                                                        'candidate_id',
                                                        Auth::id(),
                                                    )
                                                        ->pluck('skill_id')
                                                        ->toArray();
                                                }
                                                $selected = in_array($skill->id, old('skills', $userSkillIds));
                                            @endphp
                                            <option value="{{ $skill->id }}" {{ $selected ? 'selected' : '' }}>
                                                {{ $skill->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Tapez pour rechercher ou cliquez pour
                                        sélectionner</small>
                                </div>

                                <!-- Niveau de compétence (pour chaque compétence) -->
                                <div class="mb-3" id="skillLevelsContainer" style="display: none;">
                                    <label class="form-label fw-semibold">Niveaux des compétences</label>
                                    <div id="skillLevels"></div>
                                </div>

                                <!-- Prétention salariale -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Prétention salariale ($ CAD)</label>
                                    <input type="number" name="salary_expectation" class="form-control"
                                        value="{{ old('salary_expectation', Auth()->user()->salary_expectation_min) }}"
                                        placeholder="Ex: 40000" min="0" step="1">
                                </div>
                            </div>


                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">
                                    <i class="fas fa-shield-alt text-success me-2"></i>
                                    Sécurité
                                </h6>
                                <button class="btn btn-primary" type="button" onclick="openPasswordModal()">
                                    <i class="fas fa-key me-1"></i>
                                    Change Password
                                </button>
                                <a class="btn btn-primary" href="{{ Route('infos.cv') }}">
                                    <i class="fas fa-book me-1"></i>
                                    Infos CV
                                </a>
                                <a class="btn btn-primary" href="{{ Route('cv.personalization.form') }}">
                                    <i class="fas fa-book me-1"></i>
                                    Personnaliser mon CV
                                </a>

                            </div>

                            <!-- Boutons de validation -->
                            <div class="modal-footer border-0 pt-0">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </form>






                    </div>


                </div>


            </div>
        </div>
    </div>

    <!-- Modal de changement de mot de passe -->
    <div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Changer le mot de passe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('profile.change-password') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="current_password" class="form-label fw-semibold">Mot de passe actuel</label>
                            <input type="password"
                                class="form-control @error('current_password') is-invalid @enderror"
                                id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label fw-semibold">Nouveau mot de passe</label>
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                id="new_password" name="new_password" required>
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label fw-semibold">Confirmer le nouveau
                                mot de passe</label>
                            <input type="password" class="form-control" id="new_password_confirmation"
                                name="new_password_confirmation" required>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                Changer le mot de passe
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>

                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>



    <!-- Modal de succès -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="mb-3">
                        <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Mot de passe modifié avec succès!</h5>
                    <p class="text-muted">Votre mot de passe a été mis à jour.</p>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="notificationsModal" tabindex="-1" aria-labelledby="notificationsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Notifications</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6>Vos notifications récentes</h6>
                        <button class="btn btn-sm btn-outline-primary" onclick="markAllAsRead()">
                            Tout marquer comme lu
                        </button>
                    </div>

                    <div id="notificationList" class="list-group">
                        @foreach ($notifications as $notification)
                            <a href="{{ $notification->link }}"
                                class="list-group-item list-group-item-action {{ !$notification->is_read ? 'bg-light' : '' }}"
                                onclick="handleNotificationClick('{{ $notification->id }}', '{{ $notification->link }}')">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ $notification->title }}</strong>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">{{ $notification->message }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <!--  <a href=" " class="btn btn-primary">
                        Voir toutes les notifications
                    </a>  -->
                </div>
            </div>
        </div>
    </div>

    @yield('content')

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="footer-brand mb-3">
                        <img src="{{ $brandLogo }}" alt="{{ $brandName }}"
                            style="height: 10em; width: auto;">

                    </div>
                    <p>La plateforme de recrutement intelligente qui connecte les talents exceptionnels aux opportunités
                        professionnelles.</p>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Pour les candidats</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('offres') }}">Parcourir les emplois</a></li>
                        <li><a href="{{ route('abonnement') }}">Abonnements</a></li>
                        <li><a href="{{ route('contact') }}">Télécharger</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Catégories d'emploi</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('offres') }}">Télécommunications</a></li>
                        <li><a href="{{ route('offres') }}">Hôtels & Tourisme</a></li>
                        <li><a href="{{ route('offres') }}">Design</a></li>
                        <li><a href="{{ route('offres') }}">Education</a></li>
                        <li><a href="{{ route('offres') }}">Finance</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Newsletter</h5>
                    <p>Recevez chaque mois dans votre boîte mail nos nouveautés.</p>
                    <div class="control">
                        <input type="email" class="form-control mb-3" placeholder="Email">
                        <button class="btn btn-primary">S'abonner</button>
                    </div>
                </div>
            </div>
            <div class="footer-bottom d-flex justify-content-between align-items-center flex-wrap">
                <p class="mb-0">Mentions légales © 2024 Tous droits réservés.</p>
                <div>
                    <a href="{{ route('policy') }}" class="me-3">Privacy Policy</a>
                    <a href="{{ route('terms') }}">Terms & Conditions</a>
                </div>
            </div>
        </div>
    </footer>
    @if (session('forbidden'))
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script>
            Swal.fire({
                icon: 'error',
                title: 'Accès refusé',
                text: '{{ session('forbidden') }}',
            });
        </script>
    @endif
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Open profile modal
        function openProfileModal() {
            const modal = new bootstrap.Modal(document.getElementById('profileModal'));
            modal.show();
        }

        // Open password change modal
        function openPasswordModal() {
            const modal = new bootstrap.Modal(document.getElementById('passwordModal'));
            modal.show();
        }

        // Change password function
        function changePassword() {
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (!currentPassword || !newPassword || !confirmPassword) {
                alert('Veuillez remplir tous les champs');
                return;
            }

            if (newPassword !== confirmPassword) {
                alert('Les nouveaux mots de passe ne correspondent pas');
                return;
            }

            // Simuler la réussite du changement de mot de passe
            // Ici vous pouvez ajouter votre logique de changement de mot de passe

            // Fermer la modale de changement de mot de passe
            const passwordModal = bootstrap.Modal.getInstance(document.getElementById('passwordModal'));
            passwordModal.hide();

            // Afficher la modale de succès
            setTimeout(() => {
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();

                // Réinitialiser le formulaire
                document.getElementById('passwordChangeForm').reset();
            }, 300);
        }
    </script>

    <script>
        // Prévisualisation de l'image
        document.getElementById('profilePhoto').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('profileImagePreview').src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Soumission du formulaire
        document.getElementById('profileForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            try {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';

                // Ne pas envoyer le champ profile_photo s'il n'y a pas de fichier sélectionné
                if (!document.getElementById('profilePhoto').files.length) {
                    formData.delete('profile_photo');
                }

                const response = await fetch('{{ route('user.profile.update') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Erreur lors de la mise à jour');
                }

                // Afficher un message de succès
                Swal.fire({
                    icon: 'success',
                    title: 'Succès',
                    text: 'Profil mis à jour avec succès!',
                    confirmButtonColor: '#007bff',
                }).then(() => {

                    if (data.profile_photo_url) {
                        document.getElementById('profileImagePreview').src = data.profile_photo_url;

                        const avatars = document.querySelectorAll('.user-avatar');
                        avatars.forEach(avatar => {
                            avatar.style.backgroundImage = `url(${data.profile_photo_url})`;
                        });
                    }


                });

            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: error.message,
                    confirmButtonColor: '#007bff',
                });
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        });
    </script>

    <script>
        //changement de mot de pass 
        function changePassword() {
            const form = document.getElementById('passwordChangeForm');
            const formData = new FormData(form);
            const submitBtn = document.querySelector('#passwordModal .btn-primary');
            const originalBtnText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';

            fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw err;
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    const passwordModal = bootstrap.Modal.getInstance(document.getElementById('passwordModal'));
                    passwordModal.hide();

                    Swal.fire({
                        icon: 'success',
                        title: 'Succès',
                        text: 'Mot de passe mis à jour avec succès',
                        confirmButtonColor: '#007bff',
                    });
                })
                .catch(error => {
                    const errorMsg = error.message || 'Une erreur est survenue lors du changement de mot de passe';
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: errorMsg,
                        confirmButtonColor: '#007bff',
                    });
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                });
        }
    </script>

    <script>
        function markNotificationAsRead(notificationId) {
            fetch(`/notifications/${notificationId}/mark-as-read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Mettre à jour le badge
                        updateNotificationBadge(data.unread_count);

                        // Retirer le style de notification non lue
                        const notificationElement = document.querySelector(
                            `[data-notification-id="${notificationId}"]`);
                        if (notificationElement) {
                            notificationElement.classList.remove('bg-blue-50', 'bg-light', 'unread-notification');
                        }

                        showToast('Notification marquée comme lue', 'success');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast(error.message || 'Erreur lors de la mise à jour', 'error');
                });
        }

        // Fonction unique pour marquer toutes comme lues
        function markAllAsRead() {
            const button = document.querySelector('[onclick="markAllAsRead()"]');
            const originalText = button ? button.innerHTML : '';

            if (button) {
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement...';
            }

            fetch('/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Mettre à jour l'interface
                        document.querySelectorAll('.bg-blue-50, .bg-light, .unread-notification').forEach(el => {
                            el.classList.remove('bg-blue-50', 'bg-light', 'unread-notification');
                        });

                        // Mettre à jour tous les badges
                        updateNotificationBadge(0);


                        showToast(data.message || 'Toutes les notifications marquées comme lues', 'success');
                        window.location.reload();


                    } else {
                        throw new Error(data.message || 'Erreur inconnue');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast(error.message || 'Erreur lors de la mise à jour', 'error');
                })
                .finally(() => {
                    if (button) {
                        button.disabled = false;
                        button.innerHTML = originalText;
                    }

                });

        }

        // Fonction pour mettre à jour le badge de notifications
        function updateNotificationBadge(count) {
            document.querySelectorAll('.notification-badge, .bg-danger').forEach(badge => {
                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'block';
                } else {
                    badge.style.display = 'none';
                }
            });
        }

        // Fonction utilitaire pour les notifications toast
        function showToast(message, type = 'success') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    icon: type,
                    title: message
                });
            } else {

                console.log(`${type.toUpperCase()}: ${message}`);
                alert(message);

            }
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Notifications JS loaded');


            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error(
                    'CSRF token not found. Make sure you have <meta name="csrf-token" content="{{ csrf_token() }}"> in your HTML head.'
                );
            }
        });
    </script>

    <script>
        // Fonction pour gérer les clics sur les notifications
        function handleNotificationClick(notificationId, link) {
            // Marquer la notification comme lue
            markNotificationAsRead(notificationId);

            // Gérer les liens spéciaux
            if (link === 'javascript:void(0)' || link === 'javascript:openProfileModal()') {
                // Ouvrir le modal de profil
                openProfileModal();

                // Fermer le modal des notifications
                const notificationsModal = bootstrap.Modal.getInstance(document.getElementById('notificationsModal'));
                if (notificationsModal) {
                    notificationsModal.hide();
                }
            } else if (link.startsWith('http') || link.startsWith('/')) {
                // Lien normal, navigation standard
                window.location.href = link;
            }
        }

        // Fonction pour ouvrir le modal de profil
        function openProfileModal() {
            const modal = new bootstrap.Modal(document.getElementById('profileModal'));
            modal.show();
        }

        // Fonction pour marquer une notification comme lue (version mise à jour)
        function markNotificationAsRead(notificationId) {
            fetch(`/notifications/${notificationId}/mark-as-read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Mettre à jour le badge
                        updateNotificationBadge(data.unread_count);

                        // Retirer le style de notification non lue
                        const notificationElement = document.querySelector(`[onclick*="${notificationId}"]`);
                        if (notificationElement) {
                            notificationElement.classList.remove('bg-light');
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Fonction pour mettre à jour le badge de notification
        function updateNotificationBadge(count) {
            const badge = document.querySelector('.notification-badge');
            if (badge) {
                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            // Pour les selects multiples (compétences)
            $('.select2-multiple').select2({
                theme: 'bootstrap-5',
                placeholder: "Sélectionnez des compétences",
                allowClear: true,
                width: '100%'
            });

            // Pour les selects simples (secteur, diplôme)
            $('.select2-single').select2({
                theme: 'bootstrap-5',
                placeholder: "Sélectionnez une option",
                allowClear: true,
                width: '100%'
            });
        });
    </script>


    @yield('scripts')
</body>

</html>
