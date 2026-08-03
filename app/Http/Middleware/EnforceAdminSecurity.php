<?php

namespace App\Http\Middleware;

use App\Models\SecuritySetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class EnforceAdminSecurity
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || ! $user->hasAnyRole(['admin', 'Marketing']) || ! Schema::hasTable('security_settings')) {
            return $next($request);
        }

        $settings = SecuritySetting::firstOrCreate([], SecuritySetting::getDefaultSettings());
        abort_unless($settings->isIpAllowed($request->ip()), 403, 'Cette adresse IP n’est pas autorisée à accéder à l’administration.');

        if ($settings->password_expiration && $settings->password_expiration_days) {
            $changedAt = $user->password_changed_at ?? $user->created_at;
            if ($changedAt && $changedAt->lte(now()->subDays($settings->password_expiration_days))) {
                return redirect()->route('profile.edit')
                    ->with('error', 'Votre mot de passe administrateur a expiré. Modifiez-le avant de continuer.');
            }
        }

        return $next($request);
    }
}
