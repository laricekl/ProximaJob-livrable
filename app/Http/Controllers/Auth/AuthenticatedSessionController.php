<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class AuthenticatedSessionController extends Controller 
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }   
    
    public function admincreate(): View
    {
        return view('auth.admin-login');
    }

    /**
     * Handle an incoming authentication request.
     */

      public function store(LoginRequest $request)
    {
        
        if ($request->input('is_admin')) {
            abort(403, 'Accès non autorisé à cette méthode de connexion');
        }

        $request->authenticate();
        $request->session()->regenerate();
        $user = Auth::user();

        // Empêcher les admins de se connecter via cette route
        if ($user->hasRole('admin') || $user->hasRole('Marketing')) {
            Auth::logout();
            return $this->rejectAdminLoginAttempt($request);
        }

        return $this->handleSuccessfulLogin($request, $user);
    }


    public function adminStore(LoginRequest $request)
    {
        
        //$request->merge(['is_admin' => true]);
        $request->merge(['is_admin' => true, 'is_Marketing' => true]);

        $request->authenticate();
        $request->session()->regenerate();
        $user = Auth::user();

        
        if (!$user->hasRole('admin') && !$user->hasRole('Marketing')) {
            Auth::logout();
            return $this->rejectNonAdminLoginAttempt($request);
        }

        return $this->handleSuccessfulLogin($request, $user);
    }


    protected function handleSuccessfulLogin($request, $user)
    {
        $redirectRoute = $this->getRedirectRoute($user);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect' => $redirectRoute,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames()
                ]
            ]);
        }

        return redirect()->intended($redirectRoute);
    }

       protected function rejectAdminLoginAttempt($request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé'
            ], 403);
        }

        return redirect()->route('login')
            ->with('error', 'Veuillez utiliser le formulaire de connexion administrateur');
    }

     protected function rejectNonAdminLoginAttempt($request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Accès réservé aux administrateurs'
            ], 403);
        }

        return redirect()->route('admin.login')
            ->with('error', 'Accès réservé aux administrateurs');
    }



 protected function getRedirectRoute($user)
    {
        if ($user->hasRole('admin') || $user->hasRole('Marketing')) {
            return route('admin.dashboard', absolute: false);
        } elseif ($user->hasRole('entreprise')) {
            return route('offres.publies', absolute: false);
        } else {
            return route('user.home', absolute: false);
        }
    }

        public function appstore(LoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();
        
        $user = Auth::user();
        
         
       /* $redirectRoute = $user->hasRole('admin') ? 'admin.dashboard' : 
                        ($user->hasRole('entreprise') ? 'entreprise.dashboard' : 'user.home');*/
 $redirectRoute = ($user->hasRole('admin') || $user->hasRole('Marketing'))
    ? 'admin.dashboard'
    : ($user->hasRole('entreprise')
        ? 'offres.publies'
        : 'user.home');

        
        // Si c'est une requête AJAX, retourner une réponse JSON
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Connexion réussie',
                'redirect' => route($redirectRoute),
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames()
                ]
            ]);
        }
        
        // Redirection normale pour les requêtes non-AJAX
        return redirect()->route($redirectRoute);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
