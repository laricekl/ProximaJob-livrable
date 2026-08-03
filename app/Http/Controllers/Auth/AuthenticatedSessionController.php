<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\SecuritySetting;
use App\Models\User;
use App\Services\AdminAuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

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

    public function adminStore(LoginRequest $request, AdminAuditService $audit)
    {
        $request->merge(['is_admin' => true, 'is_Marketing' => true]);

        $request->authenticate();
        $request->session()->regenerate();
        $user = Auth::user();

        if (! $user->hasRole('admin') && ! $user->hasRole('Marketing')) {
            Auth::logout();

            return $this->rejectNonAdminLoginAttempt($request);
        }

        $settings = Schema::hasTable('security_settings')
            ? SecuritySetting::firstOrCreate([], SecuritySetting::getDefaultSettings())
            : null;

        if ($settings && ! $settings->isIpAllowed($request->ip())) {
            $audit->recordLogin('login.ip_denied', $user, 'Connexion administrateur refusée par la liste d’adresses IP.');
            Auth::logout();

            return back()->withErrors(['email' => 'Cette adresse IP n’est pas autorisée à accéder à l’administration.']);
        }

        if ($settings?->enable_2fa) {
            return $this->startAdminTwoFactorChallenge($request, $user);
        }

        return $this->handleSuccessfulLogin($request, $user);
    }

    public function showAdminTwoFactor(): View|RedirectResponse
    {
        if (! session()->has('admin_2fa')) {
            return redirect()->route('admin.login');
        }

        return view('auth.admin-two-factor');
    }

    public function verifyAdminTwoFactor(Request $request, AdminAuditService $audit): RedirectResponse
    {
        $request->validate(['code' => ['required', 'digits:6']]);
        $challenge = $request->session()->get('admin_2fa');

        if (! $challenge || now()->timestamp > ($challenge['expires_at'] ?? 0)) {
            $request->session()->forget('admin_2fa');

            return redirect()->route('admin.login')->withErrors(['email' => 'Le code de sécurité a expiré. Reconnectez-vous.']);
        }

        if (! Hash::check($request->string('code')->toString(), $challenge['code_hash'])) {
            return back()->withErrors(['code' => 'Le code de sécurité est incorrect.']);
        }

        $user = User::findOrFail($challenge['user_id']);
        abort_unless($user->hasAnyRole(['admin', 'Marketing']), 403);

        $settings = SecuritySetting::firstOrCreate([], SecuritySetting::getDefaultSettings());
        abort_unless($settings->isIpAllowed($request->ip()), 403, 'Cette adresse IP n’est pas autorisée.');

        $request->session()->forget('admin_2fa');
        Auth::login($user, (bool) ($challenge['remember'] ?? false));
        $request->session()->regenerate();
        $audit->recordLogin('login.2fa_verified', $user, 'Double authentification administrateur validée.');

        return $this->handleSuccessfulLogin($request, $user);
    }

    protected function handleSuccessfulLogin($request, $user)
    {
        $user->forceFill(['last_login_at' => now()])->save();
        if ($user->hasAnyRole(['admin', 'Marketing'])) {
            app(AdminAuditService::class)->recordLogin('login.success', $user, 'Connexion administrateur réussie.');
        }

        $redirectRoute = $this->getRedirectRoute($user);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect' => $redirectRoute,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames(),
                ],
            ]);
        }

        return redirect()->intended($redirectRoute);
    }

    private function startAdminTwoFactorChallenge(Request $request, User $user): RedirectResponse
    {
        $code = (string) random_int(100000, 999999);
        $request->session()->put('admin_2fa', [
            'user_id' => $user->id,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(10)->timestamp,
            'remember' => $request->boolean('remember'),
        ]);
        Auth::logout();

        try {
            Mail::raw(
                "Votre code de sécurité ProximaJob est {$code}. Il expire dans 10 minutes.",
                fn ($message) => $message->to($user->email)->subject('Code de sécurité ProximaJob')
            );
        } catch (\Throwable $exception) {
            $request->session()->forget('admin_2fa');
            Log::error('Échec d’envoi du code de sécurité administrateur', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);

            return redirect()->route('admin.login')->withErrors([
                'email' => 'Le code de sécurité n’a pas pu être envoyé. Vérifiez la configuration courriel.',
            ]);
        }

        return redirect()->route('admin.2fa.form');
    }

    protected function rejectAdminLoginAttempt($request)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé',
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
                'message' => 'Accès réservé aux administrateurs',
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
                    'roles' => $user->getRoleNames(),
                ],
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
