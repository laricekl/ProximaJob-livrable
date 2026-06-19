@extends('layouts.app')

@section('title', 'Réinitialiser le mot de passe')

@section('styles')
<style>
    /* Utiliser les mêmes styles que email.blade.php */
    .forgot-password-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 20px;
    }

    .forgot-password-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 24px;
        padding: 40px;
        width: 100%;
        max-width: 450px;
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.2);
        text-align: center;
    }

    .forgot-password-card h2 {
        font-size: 28px;
        font-weight: 700;
        background: linear-gradient(135deg, #667eea, #764ba2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 32px;
    }

    .form-group {
        margin-bottom: 24px;
        text-align: left;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #374151;
        font-weight: 500;
    }

    .form-group input {
        width: 100%;
        padding: 16px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .form-group input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .submit-btn {
        width: 100%;
        padding: 16px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .submit-btn:hover {
        transform: translateY(-2px);
    }

    .alert-danger {
        background-color: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 24px;
    }
</style>
@endsection

@section('content')
<div class="forgot-password-container">
    <div class="forgot-password-card">
        <h2>Réinitialiser le mot de passe</h2>

        {{-- Messages d'erreur --}}
        @if ($errors->any())
            <div class="alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="email">Adresse e-mail</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ $email ?? old('email') }}"
                       required 
                       readonly>
            </div>

            <div class="form-group">
                <label for="password">Nouveau mot de passe</label>
                <input type="password" 
                       id="password" 
                       name="password"
                       placeholder="••••••••"
                       required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input type="password" 
                       id="password_confirmation" 
                       name="password_confirmation"
                       placeholder="••••••••"
                       required>
            </div>

            <button type="submit" class="submit-btn">
                Réinitialiser le mot de passe
            </button>
        </form>
    </div>
</div>
@endsection