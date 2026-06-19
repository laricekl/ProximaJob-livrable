<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ProximaJob - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/ico" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --orange-color: #ff6a00;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
        }

        /* Header */
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

        .nav-link.active::after {
            width: 100%;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Footer */
        .footer {
            background: #1a1a1a;
            color: white;
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

        /* Mobile Navigation Actions */
        @media (max-width: 991.98px) {
            .navbar-nav {
                margin-bottom: 15px;
            }

            .mobile-nav-actions {
                display: flex;
                flex-direction: column;
                gap: 10px;
                padding: 15px 0;
                border-top: 1px solid #dee2e6;
                margin-top: 15px;
            }

            .mobile-nav-actions .nav-link {
                display: block;
                text-align: center;
                padding: 12px 20px;
                background-color: #f8f9fa;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                color: #007bff !important;
                text-decoration: none;
                font-weight: 500;
                margin: 0;
                transition: all 0.3s ease;
                width: 100%;
                box-sizing: border-box;
            }

            .mobile-nav-actions .nav-link:hover {
                background-color: #e9ecef;
                color: #ff6a00 !important;
            }

            .mobile-nav-actions .nav-link.active {
                background-color: #007bff !important;
                color: white !important;
                border-color: #007bff;
            }

            /* Style pour les liens de navigation mobile dans le collapse */
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

            .mobile-nav-actions .btn {
                width: 100%;
                padding: 12px 20px;
                border-radius: 8px;
                font-weight: 500;
            }

            .mobile-language-selector {
                display: flex;
                justify-content: center;
                margin-top: 15px;
                padding-top: 15px;
                border-top: 1px solid #dee2e6;
            }

            .navbar-brand img {
                height: 60px;
            }
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .pricing-card.premium {
                transform: none;
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

        .lang-opt {
            padding: 9px 14px;
            cursor: pointer;
            font-size: 14px;
            color: #333;
            transition: background 0.2s;
        }

        .lang-opt:hover {
            background: #f8f9fa;
        }

        .lang-wrap {
            margin-left: auto;
            margin-right: 10px;
        }

        @media (min-width: 992px) {
            .lang-wrap {
                margin-left: 0;
                margin-right: 12px;
            }
        }
    </style>
    @yield('styles')
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="{{ route('welcome') }}">
                <img src="{{ asset('img/img-bg-rmv.png') }}" alt="ProximaJob" style="height: 5em; width: auto;">
            </a>




            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}"
                            href="{{ route('welcome') }}">Acceuil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('services') ? 'active' : '' }} "
                            href="{{ route('offres') }}">Offres</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('ressources') ? 'active' : '' }}"
                            href="{{ route('ressources') }}">Ressources</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('abonnements') ? 'active' : '' }}"
                            href="{{ route('abonnement') }}">Tarifs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('contacts') ? 'active' : '' }}"
                            href="{{ route('contact') }}">Contact</a>
                    </li>
                </ul>

                <div class="nav-actions d-flex align-items-center">
                    @guest
                        <a href="{{ Route('login') }}" class="nav-link link-primary me-4">Se connecter</a>
                        @if (Route::has('register'))
                            <a href="{{ Route('register') }}" class="btn btn-primary">S'inscrire</a>
                        @endif
                    @endguest

                    @auth
                        <div class="notification-icon ms-3">
                            <i class="fas fa-bell"></i>
                            <div class="notification-badge"></div>
                        </div>
                        <div class="user-profile ms-3" onclick="openProfileModal()" title="{{ auth()->user()->name }}">
                            <div class="user-avatar"></div>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="ms-3">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-sign-out-alt"></i>Déconnexion
                            </button>
                        </form>
                    @endauth

                </div>

                <div style="margin-left: 2em">
                    @include('components.language-selector')
                </div>
            </div>
        </div>
    </nav>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



    @yield('scripts')
</body>

</html>
