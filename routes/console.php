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

// Commande pour lancer le matching automatique manuellement
Artisan::command('jobs:match', function () {
    $this->info('Lancement du matching automatique...');
    
    try {
        // Dispatcher le job de matching
        \App\Jobs\AutoMatchingJob::dispatch(1); //  
        
        $this->info(' Job de matching dispatché avec succès !');
        $this->line('Le traitement se fait en arrière-plan via la queue.');
        
    } catch (\Exception $e) {
        $this->error(' Erreur lors du lancement : ' . $e->getMessage());
    }
    
})->purpose('Lance le matching automatique des emplois');

// Commande pour traiter immédiatement le matching (sans queue)
Artisan::command('jobs:match-now', function () {
    $this->info('Traitement immédiat du matching automatique...');
    
    try {
        $matchingService = app(\App\Services\JobMatchingService::class);
        $results = $matchingService->processAutoMatching();
        
        $this->info('Matching terminé !');
        $this->table(['Métrique', 'Valeur'], [
            ['Offres traitées', $results['processed_offers']],
            ['Candidats matchés', $results['candidates_matched']],
            ['Candidatures créées', $results['applications_created']],
            ['Notifications envoyées', $results['notifications_sent']],
            ['Erreurs', count($results['errors'])]
        ]);
        
        if (!empty($results['errors'])) {
            $this->warn('Erreurs rencontrées :');
            foreach ($results['errors'] as $error) {
                $this->line('  • ' . $error);
            }
        }
        
    } catch (\Exception $e) {
        $this->error(' Erreur lors du traitement : ' . $e->getMessage());
    }
    
})->purpose('Traite immédiatement le matching automatique (sans queue)');

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

//  MATCHING INTENSIF LE MATIN (optionnel - plus de chances de matches)
Schedule::command('jobs:match')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Matching automatique matinal intensif');

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