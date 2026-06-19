<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureEntrepriseAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->hasRole('admin') || $user->hasRole('Marketing')) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Vous etes connecte sur un compte administration.');
        }

        if ($user->entreprise || $user->hasRole('entreprise')) {
            return $next($request);
        }

        if ($user->hasRole('candidat')) {
            return redirect()->route('user.home')
                ->with('error', 'Vous etes connecte comme candidat.');
        }

        if (!$user->entreprise) {
            abort(403);
        }

        return $next($request);
    }
}
