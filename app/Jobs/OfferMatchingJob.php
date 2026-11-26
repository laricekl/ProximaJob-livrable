<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\JobMatchingService;
use Illuminate\Support\Facades\Log;

/**
 * Matching ciblé sur UNE offre (créé à la Phase 0).
 * Remplace l'ancien déclenchement qui passait l'ID de l'utilisateur
 * entreprise à AutoMatchingJob — lequel l'ignorait et balayait tout.
 */
class OfferMatchingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $offerId;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The maximum number of seconds the job can run.
     */
    public int $timeout = 120;

    public function __construct(int $offerId)
    {
        $this->offerId = $offerId;
    }

    public function handle(JobMatchingService $matchingService): void
    {
        try {
            Log::info('Début auto-matching pour offre', ['offer_id' => $this->offerId]);

            $results = $matchingService->processMatchingForOffer($this->offerId);

            Log::info('Auto-matching terminé pour offre', [
                'offer_id' => $this->offerId,
                'results' => $results,
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur dans OfferMatchingJob', [
                'offer_id' => $this->offerId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('OfferMatchingJob a échoué définitivement', [
            'offer_id' => $this->offerId,
            'error' => $exception->getMessage(),
        ]);
    }
}
