<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardRedirectController extends Controller
{
    /**
     * Redirige vers le tableau de bord approprié selon le rôle de l'utilisateur.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->hasRole('admin') || $user->hasRole('Marketing')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('entreprise')) {
            return redirect()->route('offres.publies');
        }

        if ($user->hasRole('candidat')) {
            $cvProfile = $user->cvProfile;
            if (!$cvProfile || !$cvProfile->experiences()->exists()) {
                return redirect()->route('infos.cv')
                    ->with('success', 'Bienvenue ! Complétez votre profil CV pour commencer.');
            }
            return redirect()->route('user.home');
        }

        return redirect('/');
    }
}
