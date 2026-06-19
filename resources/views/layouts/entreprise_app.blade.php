 <!DOCTYPE html>
 <html lang="fr">

 <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <meta name="csrf-token" content="{{ csrf_token() }}">
     <title>@yield('title')</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
     <link rel="icon" type="image/ico" href="{{ asset('favicon.ico') }}">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
     <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
     <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
         rel="stylesheet" />
     <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">


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
             background: url('https://images.unsplash.com/photo-1494790108755-2616b2e01e8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80');
             background-size: cover;
             background-position: center;
             border: 2px solid #007bff;
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

         /* Mobile notification bell - Always visible */
         .mobile-notification-bell {
             position: relative;
             background: #f8f9fa;
             border: 1px solid #dee2e6;
             border-radius: 50%;
             width: 40px;
             height: 40px;
             display: flex;
             align-items: center;
             justify-content: center;
             transition: all 0.3s ease;
         }

         .mobile-notification-bell:hover {
             background: #e9ecef;
             transform: scale(1.05);
         }

         .mobile-notification-bell .fas {
             font-size: 16px;
             color: #007bff;
         }

         .mobile-notification-bell .position-absolute {
             top: -2px;
             right: -2px;
         }

         /* Responsive styling for mobile */
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

             /* Mobile Navigation Actions for Enterprise Users */
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

             /* Mobile notification bell positioned next to hamburger */
             .mobile-nav-container {
                 display: flex;
                 align-items: center;
                 gap: 10px;
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
     <!-- Header -->
     <nav class="navbar navbar-expand-lg navbar-light bg-light">
         <div class="container">
             <a class="navbar-brand" href="{{ route('welcome') }}">
                 <img src="{{ asset('img/img-bg-rmv.png') }}" alt="ProximaJob" style="height: 5em; width: auto;">
             </a>

             <!-- Mobile navigation container with notification bell -->
             <div class="d-lg-none mobile-nav-container">
                 <!-- Mobile Notification Bell - Always visible -->
                 <div class="mobile-notification-bell position-relative">
                     <button
                         class="btn btn-link p-0 border-0 d-flex align-items-center justify-content-center w-100 h-100"
                         data-bs-toggle="modal" data-bs-target="#notificationsModal">
                         <i class="fas fa-bell"></i>
                         @if ($unreadNotificationsCount > 0)
                             <span
                                 class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                 {{ $unreadNotificationsCount }}
                                 <span class="visually-hidden">unread notifications</span>
                             </span>
                         @endif
                     </button>
                 </div>

                 <!-- Hamburger button -->
                 <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                     <span class="navbar-toggler-icon"></span>
                 </button>
             </div>

             <div class="collapse navbar-collapse" id="navbarNav">
                 <ul class="navbar-nav mx-auto">
                     <li class="nav-item">
                         <a class="nav-link {{ request()->is('entreprise') ? 'active' : '' }}"
                             href="{{ route('offres.publies') }}">{{ __('interface.job_offers') }}</a>
                     </li>
                     <li class="nav-item">
                         <a class="nav-link {{ request()->is('entreprise/historique') ? 'active' : '' }}"
                             href="{{ route('entreprise.historique') }}">{{ __('interface.history') }}</a>
                     </li>
                     <!--  <li class="nav-item">
                        <a class="nav-link  " href=" "> </a>
                    </li> -->
                     <!-- <li class="nav-item">
                        <a class="nav-link {{ request()->is('entreprise/candidatures-ia') ? 'active' : '' }}" href="{{ route('entreprise.candidatures_ia') }}">{{ __('interface.ai_application') }}</a>
                    </li>-->
                     <!-- <li class="nav-item">
                        <a class="nav-link {{ request()->is('entreprise/promotion') ? 'active' : '' }}" href="{{ route('entreprise.promotion') }}"> Promotion </a>
                    </li> -->
                 </ul>

                 <!-- Desktop Navigation Actions -->
                 <div class="nav-actions d-none d-lg-flex">
                     @include('components.language-selector')
                     <div class="notification-icon position-relative">
                         <button class="btn btn-link p-0 border-0" data-bs-toggle="modal"
                             data-bs-target="#notificationsModal">
                             <i class="fas fa-bell"></i>
                             @if ($unreadNotificationsCount > 0)
                                 <span
                                     class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                     {{ $unreadNotificationsCount }}
                                     <span class="visually-hidden">unread notifications</span>
                                 </span>
                             @endif
                         </button>
                     </div>

                     <div class="user-profile" onclick="openProfileModal()">
                         <div class="user-avatar"
                             style="background-image: url('{{ Auth::user()->profile_photo_path ? asset('assets/images/user_pdp/' . Auth::user()->profile_photo_path) : asset('assets/images/user_pdp/default.jpg') }}')">
                         </div>
                     </div>
                     <form action="{{ route('logout') }}" method="POST" class="ms-2">
                         @csrf
                         <button type="submit" class="btn btn-outline-danger btn-sm">
                             <i class="fas fa-sign-out-alt"></i> Logout
                         </button>
                     </form>
                 </div>


                 <!-- Mobile Navigation Actions -->
                 <div class="d-lg-none mobile-user-actions">
                     <div class="mobile-user-row">
                         <div class="mobile-user-profile" onclick="openProfileModal()">
                             <div class="user-avatar"
                                 style="background-image: url('{{ Auth::user()->profile_photo_path ? asset('assets/images/user_pdp/' . Auth::user()->profile_photo_path) : asset('assets/images/user_pdp/profildefault.jpeg') }}')">
                             </div>
                         </div>
                     </div>
                     <div class="mobile-language-selector">
                         @include('components.language-selector')
                     </div>
                     <form action="{{ route('logout') }}" method="POST" class="ms-2">
                         @csrf
                         <button type="submit" class="btn btn-outline-danger btn-sm">
                             <i class="fas fa-sign-out-alt"></i> Logout
                         </button>
                     </form>
                 </div>
             </div>
         </div>
     </nav>

     <!-- Modal de profil utilisateur -->
     <!-- Modal de profil utilisateur -->
     <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel"
         aria-hidden="true">
         <div class="modal-dialog modal-lg">
             <div class="modal-content">
                 <div class="modal-header border-0 pb-0">
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                 </div>
                 <div class="modal-body">
                     <!-- Barre de progression
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold">{{ __('interface.profile_completed') }}</span>
                            <span class="text-primary fw-bold" id="completionPercentage">{{ Auth::user()->profileCompletionPercentage() }}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div id="completionBar" class="progress-bar bg-primary" role="progressbar"
                                style="width: {{ Auth::user()->profileCompletionPercentage() }}%"
                                aria-valuenow="{{ Auth::user()->profileCompletionPercentage() }}"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>-->





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

                                 <div class="mb-4">
                                     <h6 class="fw-bold mb-3">
                                         <i class="fas fa-shield-alt text-success me-2"></i>
                                         Sécurity
                                     </h6>
                                     <button class="btn btn-primary" type="button" onclick="openPasswordModal()">
                                         <i class="fas fa-key me-1"></i>
                                         Change Password
                                     </button>
                                 </div>
                             </div>

                             <!-- Boutons de validation -->
                             <div class="modal-footer border-0 pt-0">
                                 <button type="button" class="btn btn-secondary"
                                     data-bs-dismiss="modal">Annuler</button>
                                 <button type="submit" class="btn btn-primary">Enregistrer</button>
                             </div>
                         </form>




                     </div>

                     <!-- Sécurité -->

                 </div>

                 <!-- Boutons de validation -->

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
                     <form id="passwordChangeForm" action="{{ route('password.update') }}" method="POST">
                         @csrf
                         @method('PUT')
                         <div class="mb-3">
                             <label class="form-label fw-semibold">Mot de passe actuel</label>
                             <input type="password" name="current_password" class="form-control" required>
                         </div>
                         <div class="mb-3">
                             <label class="form-label fw-semibold">Nouveau mot de passe</label>
                             <input type="password" name="password" class="form-control" required minlength="8">
                         </div>
                         <div class="mb-3">
                             <label class="form-label fw-semibold">Confirmer le nouveau mot de passe</label>
                             <input type="password" name="password_confirmation" class="form-control" required>
                         </div>
                     </form>
                 </div>
                 <div class="modal-footer border-0 pt-0">
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                     <button type="button" class="btn btn-primary" onclick="changePassword()">Enregistrer</button>
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
                                 onclick="markNotificationAsRead('{{ $notification->id }}')">
                                 <div class="d-flex justify-content-between">
                                     <strong>{{ $notification->title }}</strong>
                                     <small
                                         class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
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
                         <img src="{{ asset('img/img-bg-rmv.png') }}" alt="ProximaJob"
                             style="height: 10em; width: auto;">

                     </div>
                     <p>{{ __('interface.intelligent_recruitment') }}</p>
                 </div>
                 <div class="col-lg-3 col-md-6 mb-4">
                     <h5>{{ __('interface.for_candidates') }}</h5>
                     <ul class="list-unstyled">
                         <li><a href="{{ route('offres') }}">{{ __('interface.browse_jobs') }}</a></li>
                         <li><a href="{{ route('abonnement') }}">{{ __('interface.subscription') }}</a></li>
                         <li><a href="{{ route('contact') }}">{{ __('interface.download_app') }}</a></li>
                     </ul>
                 </div>
                 <div class="col-lg-3 col-md-6 mb-4">
                     <h5>{{ __('interface.') }}</h5>
                     <ul class="list-unstyled">
                         <li><a href="{{ route('offres') }}">{{ __('interface.job_categories') }}</a></li>
                         <li><a href="{{ route('offres') }}">{{ __('interface.telecommunications') }}</a></li>
                         <li><a href="{{ route('offres') }}">{{ __('interface.hotels_tourism') }}</a></li>
                         <li><a href="{{ route('offres') }}">{{ __('interface.education') }}</a></li>
                         <li><a href="{{ route('offres') }}">{{ __('interface.finance') }}</a></li>
                     </ul>
                 </div>
                 <div class="col-lg-3 col-md-6 mb-4">
                     <h5>{{ __('interface.newsletter') }}</h5>
                     <p>{{ __('interface.receive_updates') }}</p>
                     <div class="control">
                         <input type="email" class="form-control mb-3" placeholder="Email">
                         <button class="btn btn-primary">S'abonner</button>
                     </div>
                 </div>
             </div>
             <div class="footer-bottom d-flex justify-content-between align-items-center flex-wrap">
                 <p class="mb-0">Mentions légales © 2024 Tous droits réservés.</p>
                 <div>
                     <a href="{{ route('policy') }}" class="me-3">{{ __('interface.privacy_policy') }}</a>
                     <a href="{{ route('terms') }}">{{ __('interface.terms_conditions') }}</a>
                 </div>
             </div>
         </div>
     </footer>

     @if (session('forbidden'))
         <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
         <script>
             Swal.fire({
                 icon: 'error',
                 title: 'Accès refusé',
                 text: '{{ session('forbidden') }}',
             });
         </script>
     @endif

     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
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

                 const response = await fetch('{{ route('entreprise.profile.update') }}', {
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
                     // Mettre à jour l'image de profil sans recharger la page
                     if (data.profile_photo_url) {
                         document.getElementById('profileImagePreview').src = data.profile_photo_url;
                         // Mettre aussi à jour l'avatar dans la navbar
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
             // Utilisation de SweetAlert2 si disponible
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
                 // Solution de fallback
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
     @yield('scripts')
 </body>

 </html>
