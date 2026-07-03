<?php

namespace App\Console\Commands;

use App\Models\Offre;
use App\Models\Diplome;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class DiagnoseOffer extends Command
{
    protected $signature = 'offer:diagnose {id : ID de l\'offre à diagnostiquer}';
    protected $description = 'Diagnostique une offre et ses relations';

    public function handle()
    {
        $offerId = $this->argument('id');

        $this->info("🔍 Diagnostic de l'offre #{$offerId}");

        $offer = Offre::with(['diplome', 'sector'])->find($offerId);

        if (!$offer) {
            $this->error("Offre #{$offerId} introuvable.");
            return 1;
        }

        // Infos de base
        $this->table(
            ['Champ', 'Valeur'],
            [
                ['ID', $offer->id],
                ['Titre', $offer->titre],
                ['Poste', $offer->poste],
                ['Sector ID', $offer->sector_id],
                ['Sector Name', $offer->sector->name ?? 'NULL'],
                ['Diplome ID', $offer->diplome_id],
                ['Diplome Name', $offer->diplome->nom_diplome ?? 'NULL'],
                ['Années expérience', $offer->annee_experience],
                ['Status', $offer->status],
            ]
        );

        // Test relation diplôme
        $this->info("\n📚 Test de la relation diplôme :");
        $directDiplome = Diplome::find($offer->diplome_id);
        $relationDiplome = $offer->diplome;

        $this->line("Diplôme direct (find) : " . ($directDiplome ? "✅ {$directDiplome->nom_diplome}" : "❌ Introuvable"));
        $this->line("Diplôme via relation : " . ($relationDiplome ? "✅ {$relationDiplome->nom_diplome}" : "❌ Relation cassée"));

        // Structure de la table
        $this->info("\n📋 Colonnes de la table 'offres' :");
        $columns = Schema::getColumnListing('offres');
        $this->line(implode(', ', $columns));
        $this->line("Colonne diplome_id présente : " . (in_array('diplome_id', $columns) ? '✅ Oui' : '❌ Non'));

        return 0;
    }
}
