@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center py-5 px-4">
                    <!-- Icône principale -->
                    <div class="mb-4">
                        <i class="fas fa-envelope-open-text fa-5x text-primary"></i>
                    </div>
                    
                    <!-- Titre principal -->
                    <h2 class="mb-3 fw-bold">Vérifiez votre adresse email</h2>
                    
                    <p class="lead text-muted mb-4">
                        Nous venons d'envoyer un email de vérification à :
                    </p>
                    
                    <!-- Email affiché -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <p class="h5 text-primary mb-0">
                            <i class="fas fa-envelope me-2"></i>
                            <strong>{{ session('email') ?? 'votre adresse email' }}</strong>
                        </p>
                    </div>
                    
                    <!-- Instructions -->
                    <div class="alert alert-info d-flex align-items-start text-start" role="alert">
                        <i class="fas fa-info-circle fa-lg me-3 mt-1"></i>
                        <div>
                            <strong>Prochaines étapes :</strong>
                            <ol class="mb-0 mt-2 ps-3">
                                <li>Consultez votre boîte de réception</li>
                                <li>Cliquez sur le lien de vérification dans l'email</li>
                                <li>Votre inscription sera ensuite soumise à nos administrateurs pour validation</li>
                            </ol>
                        </div>
                    </div>
                    
                    <!-- Messages de succès/erreur -->
                    @if (session('message'))
                        <div class="alert alert-success d-flex align-items-center" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <div>{{ session('message') }}</div>
                        </div>
                    @endif
                    
                    @if (session('error'))
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <div>{{ session('error') }}</div>
                        </div>
                    @endif
                    
                    <hr class="my-4">
                    
                    <!-- Section renvoyer l'email -->
                    <div class="mb-4">
                        <p class="text-muted mb-3">
                            <i class="fas fa-question-circle"></i> Vous n'avez pas reçu l'email ?
                        </p>
                        
                        <form method="POST" action="{{ route('verification.resend') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="email" value="{{ session('email') }}">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-redo me-2"></i>
                                Renvoyer l'email de vérification
                            </button>
                        </form>
                    </div>
                    
                    <!-- Bouton retour -->
                    <div class="mt-4">
                        <a href="{{ route('login') }}" class="btn btn-link text-decoration-none">
                            <i class="fas fa-arrow-left me-2"></i>
                            Retour à la page de connexion
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Card conseils -->
            <div class="card mt-4 border-0 shadow-sm">
                <div class="card-body">
                     
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Vérifiez votre dossier <strong>spam</strong> ou <strong>courrier indésirable</strong>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Assurez-vous d'avoir saisi la bonne adresse email
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Le lien de vérification expire après <strong>24 heures</strong>
                        </li>
                        
                    </ul>
                </div>
            </div>
            
            <!-- Info supplémentaire -->
            <div class="text-center mt-4">
                <p class="small text-muted">
                    <i class="fas fa-shield-alt me-1"></i>
                    Vos données sont sécurisées et ne seront jamais partagées avec des tiers
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        border-radius: 15px;
    }
    
    .alert {
        border-radius: 10px;
    }
    
    .btn {
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .bg-light {
        background-color: #f8f9fa !important;
    }
</style>
@endpush

