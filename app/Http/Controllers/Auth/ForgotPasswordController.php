<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    /**
     * Afficher le formulaire de demande de reset
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Envoyer le lien de reset par email
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'L\'adresse email est requise',
            'email.email' => 'Veuillez saisir une adresse email valide',
            'email.exists' => 'Aucun compte n\'est associé à cette adresse email'
        ]);

        // Vérifier que l'utilisateur a le rôle de chercheur d'emploi
        $user = \App\Models\User::where('email', $request->email)->first();
        
        if ($user->hasRole('admin')) {
            return back()->withErrors([
                'email' => 'Unerreur c\'est produite.'
            ]);
        }

        // Envoyer le lien de reset
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with([
                'status' => 'Un lien de réinitialisation a été envoyé à votre adresse email.'
            ]);
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }
}