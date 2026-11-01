<?php

use App\Http\Middleware\AuditAdminAction;
use App\Http\Middleware\CheckUserStatus;
use App\Http\Middleware\ContentSecurityPolicy;
use App\Http\Middleware\EnforceAdminSecurity;
use App\Http\Middleware\EnsureCandidateAccess;
use App\Http\Middleware\EnsureCandidatePremium;
use App\Http\Middleware\EnsureDatabaseExists;
use App\Http\Middleware\EnsureEntrepriseAccess;
use App\Http\Middleware\SetLocale;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands()
    ->withMiddleware(function (Middleware $middleware): void {
        // Le webhook Stripe arrive sans jeton CSRF : sa signature fait foi.
        $middleware->validateCsrfTokens(except: ['stripe/webhook']);
        // Extend Laravel's default web group instead of replacing it. The
        // defaults provide encrypted cookies, CSRF protection and bindings.
        $middleware->web(append: [
            EnsureDatabaseExists::class,
            ContentSecurityPolicy::class,
            SetLocale::class,
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'user.status' => CheckUserStatus::class,
            'entreprise.access' => EnsureEntrepriseAccess::class,
            'candidate.access' => EnsureCandidateAccess::class,
            'candidate.premium' => EnsureCandidatePremium::class,
            'admin.security' => EnforceAdminSecurity::class,
            'admin.audit' => AuditAdminAction::class,
            'throttle' => ThrottleRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Log des tentatives d'accès non autorisé
        $exceptions->reportable(function (UnauthorizedException $e) {
            Log::warning('Permission denied', [
                'user_id' => auth()->id(),
                'route' => request()->path(),
                'method' => request()->method(),
            ]);
        });

        // Réponses JSON pour les erreurs d'autorisation
        $exceptions->renderable(function (AuthorizationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Non autorisé'], 403);
            }
        });

        // Ne pas exposer les détails d'erreur en production
        $exceptions->renderable(function (Throwable $e, Request $request) {
            if (app()->isProduction() && ! $request->expectsJson()) {
                Log::error($e->getMessage(), [
                    'exception' => get_class($e),
                    'route' => $request->path(),
                    'user_id' => auth()->id(),
                ]);
            }
        });
    })->create();
