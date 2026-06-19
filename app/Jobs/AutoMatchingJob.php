<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\JobMatchingService;
use Illuminate\Support\Facades\Log;

class AutoMatchingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $userId;
    
    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The maximum number of seconds the job can run.
     */
    public int $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(JobMatchingService $matchingService): void
    {
        try {
            Log::info('Début auto-matching pour utilisateur', ['user_id' => $this->userId]);
            
            // Lancer le processus de matching
            $results = $matchingService->processAutoMatching();
            
            Log::info('Auto-matching terminé pour utilisateur', [
                'user_id' => $this->userId,
                'results' => $results
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur dans AutoMatchingJob', [
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Relancer l'exception pour que Laravel gère les tentatives
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('AutoMatchingJob a échoué définitivement', [
            'user_id' => $this->userId,
            'error' => $exception->getMessage()
        ]);
    }
}