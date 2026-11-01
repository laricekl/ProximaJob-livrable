<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// NOTE : la commande « jobs:match » est définie par la classe
// App\Console\Commands\RunJobMatching (app/Console/Commands/RunJobMatching.php).
// Ne pas la redéfinir ici : un doublon historique (closure qui dispatchait
// AutoMatchingJob avec un ID codé en dur) a été retiré le 31 oct. 2026 (Phase 0).

Artisan::command('matching:recalculate {--offer= : Limiter le recalcul à une offre}', function () {
    $offerId = $this->option('offer');
    $offerId = $offerId !== null ? (int) $offerId : null;

    $updated = app(\App\Services\JobMatchingService::class)
        ->recalculateStoredApplications($offerId);

    $this->info("{$updated} candidature(s) recalculée(s) avec l’algorithme 4.0.");
})->purpose('Recalcule les scores de compatibilité déjà enregistrés');

/*
|--------------------------------------------------------------------------
| Tâches Programmées (Schedule)
|--------------------------------------------------------------------------
*/

//  MATCHING AUTOMATIQUE TOUTES LES HEURES
Schedule::command('jobs:match')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Matching automatique des emplois - toutes les heures')
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Matching automatique programmé exécuté avec succès');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Échec du matching automatique programmé');
    });

//  NETTOYAGE NOCTURNE (optionnel - pour nettoyer les logs, expired jobs, etc.)
Schedule::command('queue:prune-batches --hours=48')
    ->dailyAt('02:00')
    ->description('Nettoyage des batches de jobs expirés');

// RAPPORT HEBDOMADAIRE (optionnel)
Schedule::call(function () {
    // Ici vous pourriez générer un rapport de matching
    \Illuminate\Support\Facades\Log::info('Rapport hebdomadaire de matching', [
        'semaine' => now()->weekOfYear,
        'date' => now()->toDateString()
    ]);
})->weekly()->mondays()->at('09:00')->description('Rapport hebdomadaire de matching');
