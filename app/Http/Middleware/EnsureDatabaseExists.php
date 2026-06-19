<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class EnsureDatabaseExists
{
    private static bool $checked = false;

    public function handle(Request $request, Closure $next)
    {
        if (!self::$checked) {
            self::$checked = true;
            
            try {
                if (!Schema::hasTable('migrations')) {
                    Artisan::call('migrate', ['--force' => true]);
                }
            } catch (\Exception $e) {
                // Silencieux — l'app continue même si la BDD échoue
            }
        }

        return $next($request);
    }
}
