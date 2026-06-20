<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
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
                $this->ensureUsableConnection();

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

    private function ensureUsableConnection(): void
    {
        try {
            DB::connection()->getPdo();
        } catch (\Throwable $exception) {
            if (! app()->environment('local') || Config::get('database.default') !== 'mysql') {
                throw $exception;
            }

            $sqlitePath = storage_path('database.sqlite');

            if (! file_exists($sqlitePath)) {
                touch($sqlitePath);
            }

            Config::set('database.default', 'sqlite');
            Config::set('database.connections.sqlite.database', $sqlitePath);

            DB::purge('sqlite');
            DB::setDefaultConnection('sqlite');
            DB::reconnect('sqlite');
        }
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
