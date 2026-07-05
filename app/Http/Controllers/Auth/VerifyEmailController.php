<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\EmailVerificationMail;
use App\Models\Entreprise;
use App\Models\Notification;
 


class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     * (Pour le système de vérification intégré Laravel)
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }

    /**
     * Vérification d'email via token personnalisé
     */
   /* public function verify($token)
    {
        // Utiliser la méthode du modèle
        $verification = EmailVerification::findByToken($token);

        if (!$verification) {
            return redirect()->route('register')
                ->with('error', 'Lien de vérification invalide ou expiré.');
        }

        // Activer l'utilisateur
        $user = User::where('email', $verification->email)->first();
        
        if ($user) {
            $user->update([
                'email_verified_at' => now(),
                'is_active' => true
            ]);

            // Supprimer le token utilisé
            $verification->delete();

            Auth::login($user);

            return redirect()->route('dashboard')
                ->with('success', 'Email vérifié avec succès ! Votre compte est maintenant actif.');
        }

        return redirect()->route('register')
            ->with('error', 'Utilisateur non trouvé.');
    }*/

 public function verify($token)
{ 
    // Vérifier si le token existe et est valide
    $verification = EmailVerification::where('token', $token)
        ->where('created_at', '>', now()->subHours(24)) // Token valide 24h
        ->first();

    if (!$verification) {
        return redirect()->route('verification.notice')->with([
            'error' => 'Le lien de vérification est invalide ou a expiré. Veuillez demander un nouveau lien.'
        ]);
    }

    // Trouver l'utilisateur
    $user = User::where('email', $verification->email)->first();

    if (!$user) {
        return redirect()->route('welcome')->with('error_alert', 'Utilisateur introuvable.');
    }

    // Vérifier si l'email est déjà vérifié
    if ($user->email_verified_at) {
        // Si l'utilisateur est un candidat, on le connecte directement
        if ($user->hasRole('candidat')) {
            Auth::login($user);
            return redirect()->route('welcome');
        }
        return redirect()->route('welcome')->with('info_alert', 'Votre email est déjà vérifié. Votre compte est en attente de validation administrative.');
    }

    // SI C'EST UN CANDIDAT
    if ($user->hasRole('candidat')) {
        // Vérifier l'email et activer le compte
        $user->update([
            'email_verified_at' => now(),
            'status' => 'Actif',
            'is_active' => true
        ]);

        // Connecter l'utilisateur automatiquement
        Auth::login($user);
        
        // Supprimer le token utilisé
        $verification->delete();
        
        // Rediriger vers la page d'accueil (connecté)
        return redirect()->route('welcome');
    }
    
    // SINON (C'EST UNE ENTREPRISE)

    // Vérifier l'email pour l'entreprise (reste en attente de validation admin)
    $user->update([
        'email_verified_at' => now(),
        'status' => 'pending',
        'is_active' => true
    ]);

    // Mettre à jour l'entreprise
    $entreprise = Entreprise::where('user_id', $user->id)->first();
    if ($entreprise) {
        $entreprise->update(['status' => 'pending']);
    }

    // Supprimer le token utilisé
    $verification->delete();

    // Notification pour l'administrateur (maintenant que l'email est vérifié)
    $adminUsers = User::whereHas('roles', function($q) {
        $q->where('name', 'admin');
    })->get();

    foreach ($adminUsers as $admin) {
        Notification::create([
            'user_id' => $admin->id,
            'role' => 'admin',
            'title' => 'Nouvelle entreprise en attente de validation',
            'message' => 'L\'entreprise "' . ($entreprise->company_name ?? 'N/A') . '" a confirmé son email et attend votre validation.',
            'link' => '/admin/users-details/'. $user->id,
            'is_read' => false,
        ]);
    }

    // DÉCONNECTER l'utilisateur s'il est connecté (au cas où)
    if (Auth::check()) {
        Auth::logout();
    }

    // Rediriger vers la page d'accueil avec Sweet Alert (seulement pour les entreprises)
    return redirect()->route('welcome')->with('success_alert', [
        'title' => 'Email vérifié avec succès !',
        'message' => 'Votre inscription pour l\'entreprise "' . ($entreprise->company_name ?? 'N/A') . '" est maintenant en attente de validation par nos administrateurs. Vous recevrez un email une fois votre compte activé.',
        'company_name' => $entreprise->company_name ?? 'N/A'
    ]);
}

    /**
     * Renvoyer l'email de vérification
     */
    public function resend(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)
            ->whereNull('email_verified_at')
            ->first();

        if ($user) {
            // Supprimer l'ancien token s'il existe
            EmailVerification::where('email', $user->email)->delete();

            // Créer un nouveau token
            $token = Str::random(60);
            
            EmailVerification::create([
                'email' => $user->email,
                'token' => $token,
                'created_at' => now()
            ]);

            Mail::to($user->email)->send(new EmailVerificationMail($token, $user));

            return back()->with('success', 'Un nouveau lien de vérification a été envoyé à votre adresse email.');
        }

        return back()->with('error', 'Adresse email non trouvée ou déjà vérifiée.');
    }

    /**
     * Afficher le formulaire pour renvoyer la vérification
     */
    public function showResendForm()
    {
        return view('auth.resend-verification');
    }


}