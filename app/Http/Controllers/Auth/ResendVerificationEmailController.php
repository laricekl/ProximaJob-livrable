<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerificationMail;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ResendVerificationEmailController extends Controller
{
    /**
     * Renvoyer l'email de vérification (sans authentification)
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $request->email)->first();

        // Vérifier si l'email n'est pas déjà vérifié
        if ($user->email_verified_at) {
            return back()->with('error', 'Cet email est déjà vérifié. Vous pouvez vous connecter.');
        }

        // Vérifier si l'utilisateur est en attente de vérification
        if ($user->status !== 'email_verification_pending') {
            return back()->with('error', 'Votre compte n\'est pas en attente de vérification d\'email.');
        }

        // Supprimer les anciens tokens pour cet email
        EmailVerification::where('email', $user->email)->delete();

        // Générer un nouveau token
        $token = Str::random(60);
        
        EmailVerification::create([
            'email' => $user->email,
            'token' => $token,
            'created_at' => now()
        ]);

        // Nettoyer les anciens tokens expirés
        EmailVerification::cleanupExpired();

        // Envoyer l'email
        Mail::to($user->email)->send(new EmailVerificationMail($token, $user));

        return back()->with('message', 'Un nouveau lien de vérification a été envoyé à votre adresse email. Veuillez vérifier votre boîte de réception et vos spams.');
    }
}