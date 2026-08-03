<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductionReadinessCommand extends Command
{
    protected $signature = 'production:check {--strict : Traiter aussi les avertissements comme des erreurs}';

    protected $description = 'Vérifie que la configuration et les services sont prêts pour la production';

    public function handle(): int
    {
        $checks = collect([
            $this->check('Environnement', app()->environment('production'), 'APP_ENV doit valoir production.'),
            $this->check('Mode debug', ! config('app.debug'), 'APP_DEBUG doit être false.'),
            $this->check('Clé applicative', $this->appKeyIsValid(), 'APP_KEY doit être une clé AES-256 générée.'),
            $this->check('URL HTTPS', str_starts_with((string) config('app.url'), 'https://'), 'APP_URL doit utiliser HTTPS.'),
            $this->check('Base persistante', config('database.default') !== 'sqlite', 'Utilisez PostgreSQL ou MySQL pour les données de production.'),
            $this->check('Connexion base', $this->databaseWorks(), 'La base de données est inaccessible.'),
            $this->check('Migrations', $this->pendingMigrations() === [], $this->pendingMigrationMessage()),
            $this->check('File asynchrone', ! in_array(config('queue.default'), ['sync', 'null'], true), 'QUEUE_CONNECTION doit être database ou redis.'),
            $this->check('Tables de queue', $this->queueTablesExist(), 'Les tables jobs et failed_jobs doivent exister.'),
            $this->check('Cache partagé', ! in_array(config('cache.default'), ['array', 'file', 'null'], true), 'CACHE_STORE doit être database ou redis.'),
            $this->check('Sessions partagées', in_array(config('session.driver'), ['database', 'redis'], true), 'SESSION_DRIVER doit être database ou redis.'),
            $this->check('Cookie HTTPS', config('session.secure') === true, 'SESSION_SECURE_COOKIE doit être true.'),
            $this->check('Sessions chiffrées', config('session.encrypt') === true, 'SESSION_ENCRYPT doit être true.'),
            $this->check('Courriel réel', $this->mailIsProductionReady(), 'Configurez un transport courriel réel et une adresse d’expédition valide.'),
            $this->check('Stockage public', is_link(public_path('storage')), 'Exécutez php artisan storage:link.'),
            $this->check('Stockage accessible', is_writable(storage_path()) && is_writable(base_path('bootstrap/cache')), 'storage et bootstrap/cache doivent être accessibles en écriture.'),
            $this->check('Configuration cachée', app()->configurationIsCached(), 'Exécutez php artisan config:cache.', warning: true),
            $this->check('Routes cachées', app()->routesAreCached(), 'Exécutez php artisan route:cache.', warning: true),
        ]);

        $this->table(['Contrôle', 'État', 'Détail'], $checks->map(fn ($check) => [
            $check['name'],
            $check['ok'] ? '<fg=green>OK</>' : ($check['warning'] ? '<fg=yellow>AVERTISSEMENT</>' : '<fg=red>ÉCHEC</>'),
            $check['ok'] ? 'Prêt' : $check['message'],
        ])->all());

        $failures = $checks->where('ok', false)->where('warning', false)->count();
        $warnings = $checks->where('ok', false)->where('warning', true)->count();
        if ($failures > 0 || ($this->option('strict') && $warnings > 0)) {
            $this->error("Production non prête : {$failures} échec(s), {$warnings} avertissement(s).");

            return self::FAILURE;
        }

        $this->info('Configuration prête pour la production.');

        return self::SUCCESS;
    }

    private function check(string $name, bool $ok, string $message, bool $warning = false): array
    {
        return compact('name', 'ok', 'message', 'warning');
    }

    private function databaseWorks(): bool
    {
        try {
            DB::select('select 1');

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    private function appKeyIsValid(): bool
    {
        $key = (string) config('app.key');
        if (str_starts_with($key, 'base64:')) {
            $decoded = base64_decode(substr($key, 7), true);

            return $decoded !== false && strlen($decoded) === 32;
        }

        return strlen($key) === 32;
    }

    private function pendingMigrations(): array
    {
        try {
            $migrator = app('migrator');
            if (! $migrator->repositoryExists()) {
                return ['table migrations absente'];
            }

            $files = array_keys($migrator->getMigrationFiles(database_path('migrations')));

            return array_values(array_diff($files, $migrator->getRepository()->getRan()));
        } catch (\Throwable) {
            return ['vérification impossible'];
        }
    }

    private function pendingMigrationMessage(): string
    {
        $pending = $this->pendingMigrations();

        return $pending === [] ? 'Prêt' : count($pending).' migration(s) doivent être appliquée(s).';
    }

    private function queueTablesExist(): bool
    {
        if (config('queue.default') !== 'database') {
            return true;
        }

        return Schema::hasTable('jobs') && Schema::hasTable('failed_jobs');
    }

    private function mailIsProductionReady(): bool
    {
        $mailer = config('mail.default');
        if (! in_array($mailer, ['smtp', 'ses', 'postmark', 'resend', 'failover', 'roundrobin'], true)) {
            return false;
        }

        $from = (string) config('mail.from.address');
        if (! filter_var($from, FILTER_VALIDATE_EMAIL) || str_contains($from, 'example.')) {
            return false;
        }

        if ($mailer === 'smtp') {
            $host = strtolower((string) config('mail.mailers.smtp.host'));

            return $host !== '' && ! in_array($host, ['localhost', '127.0.0.1', 'mailpit'], true);
        }

        return true;
    }
}
