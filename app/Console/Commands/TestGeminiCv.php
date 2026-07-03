<?php

namespace App\Console\Commands;

use App\Models\CvProfile;
use App\Models\Offre;
use App\Services\JobMatchingService;
use Illuminate\Console\Command;
use Prism\Prism\Prism;

class TestGeminiCv extends Command
{
    protected $signature = 'gemini:test-cv
                            {candidateId : ID du candidat (user_id)}
                            {offerId : ID de l\'offre}
                            {--step-by-step : Mode debug étape par étape}';
    protected $description = 'Teste la génération de CV avec Gemini pour un candidat et une offre';

    public function handle()
    {
        $candidateId = $this->argument('candidateId');
        $offerId = $this->argument('offerId');
        $stepByStep = $this->option('step-by-step');

        $matchingService = new JobMatchingService();

        if ($stepByStep) {
            return $this->runStepByStep($candidateId, $offerId, $matchingService);
        }

        $this->info("Génération de CV pour le candidat #{$candidateId} et l'offre #{$offerId}...");

        try {
            $result = $matchingService->generateCVForCandidate($candidateId, $offerId);

            if (isset($result['cv_html'])) {
                $this->info('✅ CV généré avec succès');
                $this->line("Longueur HTML : " . strlen($result['cv_html']) . " caractères");
            } else {
                $this->warn('⚠️ Pas de HTML dans la réponse');
                $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }
            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Erreur : " . $e->getMessage());
            return 1;
        }
    }

    private function runStepByStep($candidateId, $offerId, $matchingService): int
    {
        $this->info("🔍 Mode debug étape par étape");

        $cvProfile = CvProfile::with(['formations', 'competences', 'experiences'])
            ->where('user_id', $candidateId)->first();

        if (!$cvProfile) {
            $this->error("Profil CV non trouvé pour le user_id={$candidateId}");
            return 1;
        }

        $offer = Offre::findOrFail($offerId);

        $this->info("Candidat : {$cvProfile->prenom} {$cvProfile->nom}");
        $this->info("Email : {$cvProfile->email}");
        $this->info("Téléphone : {$cvProfile->telephone}");
        $this->info("Offre : {$offer->poste}");

        $this->info("\nFormations : " . $cvProfile->formations->count());
        $this->info("Expériences : " . $cvProfile->experiences->count());
        $this->info("Compétences : " . $cvProfile->competences->count());

        $this->info("\nTest simple Gemini...");
        try {
            $simplePrompt = "Génère un CV HTML simple pour {$cvProfile->prenom} {$cvProfile->nom} - {$offer->poste}. HTML seulement.";

            $response = Prism::text()
                ->using(\Prism\Prism\Enums\Provider::Gemini, 'gemini-2.5-flash')
                ->withPrompt($simplePrompt)
                ->generate();

            $result = trim($response->text);
            $this->info("✅ Réponse reçue : " . strlen($result) . " caractères");
        } catch (\Exception $e) {
            $this->error("❌ Erreur Gemini : " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
