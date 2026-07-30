<?php

namespace Tests\Feature\Concerns;

use App\Models\Categorie;
use App\Models\Diplome;
use App\Models\Entreprise;
use App\Models\Offre;
use App\Models\Sector;
use App\Models\Skill;
use App\Models\TypeOffre;
use App\Models\User;
use App\Models\Abonnement;
use App\Models\UserAbonnement;

trait CreatesTestAccounts
{
    protected function createCandidate(array $attributes = [], bool $withProPlan = true): User
    {
        $user = User::factory()->create(array_merge([
            'prenom' => 'Camille',
            'name' => 'Martin',
            'email_verified_at' => now(),
            'status' => 'Actif',
        ], $attributes));

        $user->syncRoles(['candidat']);
        if ($withProPlan) {
            $this->attachCurrentPlan($user, 'Pro', 49, 30);
        }

        return $user->fresh();
    }

    protected function createEnterprise(array $attributes = [], array $enterpriseAttributes = [], bool $withEnterprisePlan = true): User
    {
        $user = User::factory()->create(array_merge([
            'prenom' => 'Alex',
            'name' => 'Entreprise QA',
            'email_verified_at' => now(),
            'status' => 'Actif',
        ], $attributes));

        $user->syncRoles(['entreprise']);

        Entreprise::create(array_merge([
            'user_id' => $user->id,
            'company_name' => 'Entreprise QA',
            'status' => 'approved',
        ], $enterpriseAttributes));
        if ($withEnterprisePlan) {
            $this->attachCurrentPlan($user, 'Entreprise', 399, 365);
        }

        return $user->fresh();
    }

    protected function attachCurrentPlan(User $user, string $name, float $amount, int $duration): UserAbonnement
    {
        $plan = Abonnement::firstOrCreate(
            ['nom' => $name],
            [
                'description' => 'Forfait actif pour les tests fonctionnels.',
                'montant' => $amount,
                'duree' => $duration,
                'actif' => true,
            ]
        );

        return UserAbonnement::create([
            'user_id' => $user->id,
            'abonnement_id' => $plan->id,
            'date_debut' => now()->subDay(),
            'date_fin' => now()->addDays($duration),
            'status' => 'Actif',
        ]);
    }

    protected function createOfferFor(?User $enterpriseUser = null, array $attributes = []): Offre
    {
        $enterpriseUser ??= $this->createEnterprise();
        $type = TypeOffre::firstOrCreate(['nom' => 'Temps plein']);
        $category = Categorie::firstOrCreate(['nom' => 'Technologie']);
        $sector = Sector::firstOrCreate(
            ['name' => 'Technologies de l information'],
            ['is_active' => true]
        );

        return Offre::create(array_merge([
            'entreprise_id' => $enterpriseUser->entreprise->id,
            'titre' => 'Developpeur Laravel QA',
            'poste' => 'Developpeur Laravel QA',
            'description' => 'Offre creee pour les tests de bout en bout.',
            'localisation' => 'Montreal',
            'categorie_id' => $category->id,
            'sector_id' => $sector->id,
            'type_id' => $type->id,
            'status' => 'active',
            'salaire_min' => 70000,
            'salaire_max' => 95000,
            'remote_work' => 'hybride',
            'job_category' => 'permanent',
            'salary_type' => 'annual',
            'date_fin' => now()->addMonth()->toDateString(),
            'start_date' => 'Des que possible',
            'langues' => 'Francais',
            'required_experience' => '3 ans',
            'responsibilities' => 'Livrer des fonctionnalites fiables.',
        ], $attributes));
    }

    protected function createHiringReferences(): array
    {
        return [
            'type' => TypeOffre::firstOrCreate(['nom' => 'Temps plein']),
            'sector' => Sector::firstOrCreate(['name' => 'Technologies de l information'], ['is_active' => true]),
            'diplome' => Diplome::firstOrCreate(
                ['nom_diplome' => 'Baccalaureat informatique'],
                ['niveau_education' => 'UNIVERSITAIRE_1ER_CYCLE', 'statut' => 'ACTIF']
            ),
            'technicalSkill' => Skill::firstOrCreate(
                ['name' => 'Laravel'],
                ['slug' => 'laravel', 'category' => 'Hard Skills', 'is_active' => true, 'importance_level' => 5]
            ),
            'methodologicalSkill' => Skill::firstOrCreate(
                ['name' => 'Communication'],
                ['slug' => 'communication', 'category' => 'Soft Skills', 'is_active' => true, 'importance_level' => 4]
            ),
            'digitalSkill' => Skill::firstOrCreate(
                ['name' => 'Outils collaboratifs'],
                ['slug' => 'outils-collaboratifs', 'category' => 'Competences numeriques essentielles', 'is_active' => true, 'importance_level' => 3]
            ),
        ];
    }

    protected function validOfferPayload(array $overrides = []): array
    {
        $refs = $this->createHiringReferences();

        return array_merge([
            'jobTitle' => 'Analyste QA Laravel',
            'contractType' => $refs['type']->id,
            'location' => 'Montreal',
            'sector' => $refs['sector']->id,
            'employment_type' => 'Permanent',
            'remote_work' => 'Hybride',
            'job_category' => 'informatique',
            'salary_type' => 'annuel',
            'salary_min' => 72000,
            'salary_max' => 98000,
            'endDate' => now()->addMonth()->toDateString(),
            'start_date' => 'Immédiate',
            'diplomes' => [
                ['id' => $refs['diplome']->id, 'obligatoire' => true],
            ],
            'skillls' => [$refs['technicalSkill']->id],
            'methodological_skills' => [$refs['methodologicalSkill']->id],
            'num_skills' => [$refs['digitalSkill']->id],
            'languages_data' => 'Francais, Anglais',
            'required_experience' => '2-3 ans',
            'education_level' => 'Universitaire',
            'otherCriteria' => 'Autonomie',
            'benefits' => ['Assurance collective'],
            'custom_benefits' => json_encode(['Budget formation']),
            'responsibilities' => 'Analyser, tester et documenter.',
            'jobDescription' => 'Role QA complet dans une equipe produit.',
        ], $overrides);
    }
}
