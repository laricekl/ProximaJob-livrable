<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Langues supportées
        $supportedLocales = ['fr', 'en'];
        
        // Récupérer la langue de la session, des paramètres ou par défaut
        $locale = request('locale') ?? Session::get('locale') ?? config('app.locale');
        
        // Vérifier si la langue est supportée
        if (in_array($locale, $supportedLocales)) {
            App::setLocale($locale);
            Session::put('locale', $locale);
        }



        return $next($request);
    }
}
