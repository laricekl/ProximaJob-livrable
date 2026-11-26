<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\JobMatchingService;

class RunJobMatching extends Command
{
    protected $signature = 'jobs:match {--dry-run : Simulation sans créer de postulations}';
    protected $description = 'Lance l\'algorithme de matching automatique des emplois';

    private JobMatchingService $matchingService;

    public function __construct(JobMatchingService $matchingService)
    {
        parent::__construct();
        $this->matchingService = $matchingService;
    }

    public function handle()
    {
        $this->info(' Lancement de l\'algorithme de matching automatique...');
        
        if ($this->option('dry-run')) {
            $this->warn('  MODE SIMULATION - Aucune postulation ne sera créée');
        }

        $startTime = now();
        
        try {
            // Le mode simulation est RÉELLEMENT transmis au service :
            // avant la Phase 0, l'option --dry-run affichait un avertissement
            // mais le service créait quand même les postulations.
            $results = $this->matchingService->processAutoMatching(null, (bool) $this->option('dry-run'));
            
            $this->displayResults($results, $startTime);
            
            if (empty($results['errors'])) {
                return Command::SUCCESS;
            } else {
                $this->error(' Matching terminé avec des erreurs');
                foreach ($results['errors'] as $error) {
                    $this->error('   - ' . $error);
                }
                return Command::FAILURE;
            }
            
        } catch (\Exception $e) {
            $this->error(' Erreur critique : ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function displayResults(array $results, $startTime): void
    {
        $duration = $startTime->diffInSeconds(now());
        
        $this->info(' Matching terminé en ' . $duration . ' secondes');
        $this->newLine();
        
        $this->table(
            ['Métrique', 'Valeur'],
            [
                ['Offres traitées', $results['processed_offers']],
                ['Candidats analysés', $results['candidates_matched']],
                ['Postulations créées', $results['applications_created']],
                ['Postulations simulées (dry-run)', $results['applications_simulated'] ?? 0],
                ['Postulations plafonnées (max 3/jour/candidat)', $results['applications_capped'] ?? 0],
                ['Erreurs', count($results['errors'])],
            ]
        );

        if ($results['applications_created'] > 0) {
            $this->info(' ' . $results['applications_created'] . ' nouvelles postulations automatiques créées !');
        } else {
            $this->comment('ℹ  Aucune nouvelle postulation créée cette fois-ci.');
        }
    }
}