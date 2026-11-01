<?php

namespace App\Listeners;

use App\Events\ProfileUpdated;
use App\Services\JobMatchingService;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessAutoMatching implements ShouldQueue
{
    public function handle(ProfileUpdated $event)
    {
        $matchingService = new JobMatchingService();
        // Ciblé : ne rematcher que le candidat dont le profil vient de changer.
        // (Avant la Phase 0, cet écouteur relançait un balayage global complet.)
        $matchingService->processAutoMatching($event->user->id);
    }
}