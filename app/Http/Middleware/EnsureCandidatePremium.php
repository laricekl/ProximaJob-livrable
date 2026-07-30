<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCandidatePremium
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->hasCurrentPlan('Pro')) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Le forfait Pro actif est requis pour utiliser la personnalisation IA du CV.',
            ], 403);
        }

        return redirect()->route('plan.abonnement')
            ->with('error', 'Le forfait Pro actif est requis pour utiliser la personnalisation IA du CV.');
    }
}
