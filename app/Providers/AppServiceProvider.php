<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Auto-créer la BDD SQLite si elle n'existe pas
        if ($this->app->environment(['local', 'testing']) && config('database.default') === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database');
            if ($dbPath && ! file_exists($dbPath)) {
                $dir = dirname($dbPath);
                if (! is_dir($dir)) {
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
            if (Schema::hasTable('site_settings')) {
                $configuredTimezone = SiteSetting::value('timezone');
                if ($configuredTimezone && in_array($configuredTimezone, timezone_identifiers_list(), true)) {
                    config(['app.timezone' => $configuredTimezone]);
                    date_default_timezone_set($configuredTimezone);
                }
            }

            View::composer('*', function ($view) {
                $view->with('siteSettings', SiteSetting::firstOrCreate([], SiteSetting::defaults()));
            });
        } catch (\Exception $e) {
            View::composer('*', function ($view) {
                $view->with('siteSettings', null);
            });
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

            return (new MailMessage)
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
