<?php

namespace Tests\Unit;

use App\Services\JobMatchingService;
use ReflectionMethod;
use Tests\TestCase;

class JobMatchingServiceTest extends TestCase
{
    public function test_build_cv_prompt_accepts_manual_requirements_as_string(): void
    {
        $service = new JobMatchingService();

        $buildPrompt = new ReflectionMethod($service, 'buildCVPrompt');
        $buildPrompt->setAccessible(true);

        $prompt = $buildPrompt->invoke($service, [
            'candidate' => [
                'personal_info' => [
                    'prenom' => 'Jean',
                    'nom' => 'Dupont',
                    'email' => 'jean@example.com',
                    'telephone' => '5140000000',
                    'adresse' => 'Montreal',
                ],
                'experiences' => [
                    [
                        'poste' => 'Developpeur Full Stack',
                        'entreprise' => 'Proxima',
                        'periode' => '2022-2024',
                    ],
                ],
                'formations' => [
                    [
                        'diplome' => 'Baccalaureat en informatique',
                        'etablissement' => 'UQAM',
                        'periode' => '2018-2021',
                    ],
                ],
            ],
            'offer' => [
                'poste' => 'Developpeur Laravel',
                'competences_requises' => "Laravel, PHP\nMySQL; API REST",
                'entreprise' => [
                    'nom' => 'Tech Solutions',
                ],
            ],
        ]);

        $this->assertStringContainsString(
            "Compétences requises par l'offre: Laravel, PHP, MySQL, API REST",
            $prompt
        );
    }
}
