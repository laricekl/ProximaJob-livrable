<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SetLocale;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\VerifyCsrfToken;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Illuminate\Routing\Middleware\ThrottleRequests;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
       
        $middleware->group('web', [
            \App\Http\Middleware\EnsureDatabaseExists::class,
            \App\Http\Middleware\ContentSecurityPolicy::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            SetLocale::class,
        ]);

         $middleware->alias([
             'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'user.status' => App\Http\Middleware\CheckUserStatus::class,
            'entreprise.access' => App\Http\Middleware\EnsureEntrepriseAccess::class,
            'candidate.access' => App\Http\Middleware\EnsureCandidateAccess::class,
             'throttle' => ThrottleRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Log des tentatives d'accès non autorisé
        $exceptions->reportable(function (\Spatie\Permission\Exceptions\UnauthorizedException $e) {
            \Log::warning('Permission denied', [
                'user_id' => auth()->id(),
                'route' => request()->path(),
                'method' => request()->method(),
            ]);
        });

        // Réponses JSON pour les erreurs d'autorisation
        $exceptions->renderable(function (\Illuminate\Auth\Access\AuthorizationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Non autorisé'], 403);
            }
        });

        // Ne pas exposer les détails d'erreur en production
        $exceptions->renderable(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if (app()->isProduction() && !$request->expectsJson()) {
                \Log::error($e->getMessage(), [
                    'exception' => get_class($e),
                    'route' => $request->path(),
                    'user_id' => auth()->id(),
                ]);
            }
        });
    })->create();




 
