<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class SocialAuthController extends Controller
{
    public function redirectToProvider($provider, Request $request)
    {
        if (! $this->isProviderConfigured($provider)) {
            return redirect()
                ->route('login')
                ->with('error', ucfirst($provider).' n\'est pas encore disponible sur cette version.');
        }

        try {
            // Stocker le mode (login ou register) et le rôle en session
            $mode = $request->query('mode', 'login'); 
            $role = $request->query('role', 'candidat');  
            
            session(['oauth_mode' => $mode]);
            session(['oauth_role' => $role]);
            
            return Socialite::driver($provider)->redirect();
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Erreur lors de la redirection vers ' . $provider);
        }
    }

    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            
            // Récupérer le mode et rôle depuis la session
            $mode = session('oauth_mode', 'login');
            $role = session('oauth_role', 'candidat');
            
            // Vérifier si l'utilisateur existe déjà
            $existingUser = User::where('email', $socialUser->getEmail())->first();
            
            if ($mode === 'register') {
                return $this->handleRegister($socialUser, $provider, $role, $existingUser);
            } else {
                return $this->handleLogin($socialUser, $provider, $existingUser);
            }
            
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Erreur OAuth: ' . $e->getMessage());
        } finally {
            // Nettoyer la session
            session()->forget(['oauth_mode', 'oauth_role']);
        }
    }

    private function handleRegister($socialUser, $provider, $role, $existingUser)
    {
        if ($existingUser) {
            // L'utilisateur existe déjà
            return redirect()->route('register')->with('error', 'Un compte avec cette adresse email existe déjà. Connectez-vous plutôt.');
        }

        // Créer un nouvel utilisateur
        $user = User::create([
            'name' => $socialUser->getName(),
            'email' => $socialUser->getEmail(),
            'role' => $role,
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'avatar' => $socialUser->getAvatar(),
            'email_verified_at' => now(),
            'password' => Hash::make(uniqid()),
        ]);

        Auth::login($user);

        return redirect()->route('user.home', absolute: false)->with('success', 'Compte créé avec succès via ' . ucfirst($provider) . ' !');
    }

    private function handleLogin($socialUser, $provider, $existingUser)
    {
        if (!$existingUser) {
            // L'utilisateur n'existe pas, proposer de s'inscrire
            return redirect()->route('register')->with('info', 'Aucun compte trouvé avec cette adresse email. Inscrivez-vous d\'abord.');
        }

        // Mettre à jour les informations OAuth si nécessaire
        $existingUser->update([
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'avatar' => $socialUser->getAvatar(),
            'email_verified_at' => now(),
        ]);

        Auth::login($existingUser);

        return redirect()->intended('/user')->with('success', 'Connexion réussie avec ' . ucfirst($provider) . ' !');
    }

    private function isProviderConfigured(string $provider): bool
    {
        $supportedProviders = ['google', 'facebook'];

        if (! in_array($provider, $supportedProviders, true)) {
            return false;
        }

        $service = config("services.{$provider}");

        return filled($service['client_id'] ?? null)
            && filled($service['client_secret'] ?? null)
            && filled($service['redirect'] ?? null);
    }
}
