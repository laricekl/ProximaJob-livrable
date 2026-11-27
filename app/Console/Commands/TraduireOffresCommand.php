<?php

namespace App\Console\Commands;

use App\Models\Offre;
use App\Services\Translation\OffreTranslationService;
use Illuminate\Console\Command;

/**
 * Rattrapage : traduit toutes les offres dont la traduction en
 * cache est absente ou périmée (ex. offres créées avant la mise
 * en place, ou après l'ajout d'une clé de traduction).
 */
class TraduireOffresCommand extends Command
{
    protected $signature = 'offres:traduire';

    protected $description = 'Traduit en anglais (en cache) les offres qui ne le sont pas encore';

    public function handle(OffreTranslationService $service): int
    {
        $translated = 0;
        $skipped = 0;

        Offre::query()->orderBy('id')->each(function (Offre $offre) use ($service, &$translated, &$skipped) {
            if ($service->isCached($offre)) {
                $skipped++;
                return;
            }

            $service->translate($offre) ? $translated++ : $skipped++;
        });

        $this->info("Offres traduites : {$translated} — déjà en cache ou sans pilote : {$skipped}");

        return self::SUCCESS;
    }
}
