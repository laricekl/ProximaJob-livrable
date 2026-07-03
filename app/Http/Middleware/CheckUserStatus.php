<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $useractif = Auth::user()->status;

            // Vérifier si le compte candidat est actif
            if ($user->hasRole('candidat') && $useractif != 'Actif') {
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Votre compte est inactif. Veuillez contacter notre assistance.');
            }
            
            // Vérifier si l'entreprise est vérifiée (pour les rôles entreprise)
            if ($user->hasRole('entreprise')) {
                $entreprise = $user->entreprise;
                $allowedEntrepriseStatuses = ['approved', 'verified'];

                if (!$entreprise || !in_array($entreprise->status, $allowedEntrepriseStatuses, true)) {
                    Auth::logout();
                    return redirect()->route('login')
                        ->with('error', 'Votre compte entreprise est en attente de validation administrative.');
                }
            }
        }

        return $next($request);
    }
}
