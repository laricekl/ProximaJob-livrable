<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Schema;
use Illuminate\Auth\Notifications\ResetPassword;
use App\Models\SiteSetting;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Auto-créer la BDD SQLite si elle n'existe pas
        if (config('database.default') === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database');
            if ($dbPath && !file_exists($dbPath)) {
                $dir = dirname($dbPath);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
                touch($dbPath);
            }
        }
    }


    public function boot()
{
    Schema::defaultStringLength(191);
    Paginator::useBootstrapFive();
    
    try {
        view()->share('siteSettings', SiteSetting::first());
    } catch (\Exception $e) {
        view()->share('siteSettings', null);
    }

    // Personnaliser l'email de réinitialisation
    ResetPassword::createUrlUsing(function ($notifiable, $token) {
        return url(route('password.reset', [
            'token' => $token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    });

    // Utiliser un template personnalisé
    ResetPassword::toMailUsing(function ($notifiable, $token) {
        $url = url(route('password.reset', [
            'token' => $token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->view('emails.password-reset', ['url' => $url])
            ->subject('Réinitialisation de votre mot de passe - ProximaJob');
    });


     // Règles de mot de passe
    Password::defaults(function () {
        $rule = Password::min(8);

        return $this->app->isProduction()
                    ? $rule->mixedCase()->numbers()->symbols()->uncompromised()
                    : $rule;
    });
}
}

 
