<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCandidateAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->hasRole('entreprise')) {
            return redirect()->route('offres.publies')
                ->with('error', 'Vous etes connecte comme entreprise.');
        }

        if ($user->hasRole('admin') || $user->hasRole('Marketing')) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Vous etes connecte sur un compte administration.');
        }

        if (!$user->hasRole('candidat')) {
            \Log::warning('Accès candidat refusé — rôle non candidat', [
                'user_id' => $user->id,
                'roles' => $user->roles->pluck('name')->toArray(),
                'route' => request()->path(),
            ]);
            abort(403, 'Accès réservé aux candidats. Vous n\'avez pas les permissions nécessaires.');
        }

        return $next($request);
    }
}
