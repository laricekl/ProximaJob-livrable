<?php

namespace App\Jobs;

use App\Models\Offre;
use App\Services\Translation\OffreTranslationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

/**
 * Traduit une offre en arrière-plan après sa publication ou sa
 * modification. Un échec ne bloque jamais l'offre : l'affichage
 * anglais retombe simplement sur le français.
 */
class TranslateOffreJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public function __construct(public int $offreId)
    {
    }

    public function handle(OffreTranslationService $service): void
    {
        try {
            $offre = Offre::find($this->offreId);

            if ($offre) {
                $service->translate($offre);
            }
        } catch (\Throwable $e) {
            Log::warning('TranslateOffreJob échoué pour l\'offre ' . $this->offreId . ' : ' . $e->getMessage());
        }
    }
}
