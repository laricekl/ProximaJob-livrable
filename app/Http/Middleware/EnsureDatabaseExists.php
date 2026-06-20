<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
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

                if ($this->shouldSeedDemoData()) {
                    Artisan::call('db:seed', ['--force' => true]);
                }
            } catch (\Exception $e) {
                // Silencieux — l'app continue même si la BDD échoue
            }
        }

        return $next($request);
    }

    private function shouldSeedDemoData(): bool
    {
        $requiredTables = ['roles', 'entreprises', 'offres', 'candidate_sectors'];

        foreach ($requiredTables as $table) {
            if (!Schema::hasTable($table)) {
                return false;
            }
        }

        return DB::table('roles')->count() === 0
            || DB::table('entreprises')->count() === 0
            || DB::table('offres')->count() === 0
            || DB::table('candidate_sectors')->count() === 0;
    }
}
