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
        $matchingService->processAutoMatching();
    }
}