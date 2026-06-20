<?php

namespace Tests\Feature;

use App\Models\Entreprise;
use App\Models\Notification;
use App\Models\Offre;
use App\Models\Postulation;
use App\Models\Abonnement;
use App\Models\UserAbonnement;
use App\Models\TypeOffre;
use App\Models\Categorie;
use App\Models\Diplome;
use App\Models\Sector;
use App\Models\Skill;
use App\Models\CandidateSector;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticatedSectionsTest extends TestCase
{
    use RefreshDatabase;

    private function createCandidateUser(): User
    {
        $user = User::factory()->create([
            'prenom' => 'Jean',
            'name' => 'Dupont',
            'email_verified_at' => now(),
            'status' => 'Actif',
        ]);

        $user->assignRole('candidat');

        return $user->fresh();
    }

    private function createEnterpriseUser(): User
    {
        $user = User::factory()->create([
            'prenom' => 'Emma',
            'name' => 'TechCorp',
            'email_verified_at' => now(),
            'status' => 'Actif',
        ]);

        $user->assignRole('entreprise');

        Entreprise::create([
            'user_id' => $user->id,
            'company_name' => 'TechCorp',
            'status' => 'approved',
        ]);

        return $user->fresh();
    }

    private function createOfferForEnterprise(Entreprise $entreprise): Offre
    {
        $type = TypeOffre::create(['nom' => 'CDI']);
        $category = Categorie::create(['nom' => 'Tech']);

        return Offre::create([
            'entreprise_id' => $entreprise->id,
            'titre' => 'Développeur Full Stack',
            'poste' => 'Développeur Full Stack',
            'description' => 'Offre de test pour QA.',
            'localisation' => 'Montréal',
            'categorie_id' => $category->id,
            'type_id' => $type->id,
            'status' => 'active',
        ]);
    }

    public function test_candidate_pages_load_with_real_data(): void
    {
        $candidate = $this->createCandidateUser();
        $enterpriseUser = $this->createEnterpriseUser();
        $offer = $this->createOfferForEnterprise($enterpriseUser->entreprise);

        Postulation::create([
            'user_id' => $candidate->id,
            'offre_id' => $offer->id,
            'autopostulation' => false,
            'status' => 'en_attente',
        ]);

        Notification::create([
            'user_id' => $candidate->id,
            'role' => 'candidat',
            'title' => 'Notification test',
            'message' => 'Une mise a jour de test.',
            'link' => '/user/historique-candidatures',
            'is_read' => false,
        ]);

        $this->actingAs($candidate)
            ->get('/user/historique-candidatures')
            ->assertOk()
            ->assertSee('Développeur Full Stack');

        $this->actingAs($candidate)
            ->get('/user/profil-public')
            ->assertOk()
            ->assertSee('Jean');

        $this->actingAs($candidate)
            ->get('/user/detail-candidature')
            ->assertOk()
            ->assertSee('Développeur Full Stack');

        $this->actingAs($candidate)
            ->get('/notifications')
            ->assertOk()
            ->assertSee('Notification test');
    }

    public function test_enterprise_pages_load_with_real_data(): void
    {
        $enterpriseUser = $this->createEnterpriseUser();
        $candidate = $this->createCandidateUser();
        $offer = $this->createOfferForEnterprise($enterpriseUser->entreprise);
        $sector = Sector::create(['name' => 'Technologie', 'slug' => 'technologie', 'is_active' => true]);
        $diplome = Diplome::create(['nom_diplome' => 'Baccalaureat informatique', 'niveau_education' => 'UNIVERSITAIRE_1ER_CYCLE', 'statut' => 'ACTIF']);
        $skill = Skill::create(['name' => 'Laravel', 'slug' => 'laravel', 'category' => 'Hard Skills', 'importance_level' => 5, 'is_active' => true]);
        $plan = Abonnement::create([
            'nom' => 'Business',
            'description' => 'Plan entreprise',
            'montant' => 49.99,
            'duree' => 'mensuel',
            'actif' => true,
        ]);

        Postulation::create([
            'user_id' => $candidate->id,
            'offre_id' => $offer->id,
            'autopostulation' => true,
            'status' => 'en_attente',
        ]);

        CandidateSector::create([
            'candidate_id' => $candidate->id,
            'sector_id' => $sector->id,
            'diplome_id' => $diplome->id,
            'experience_years' => 3,
        ]);
        $candidate->skills()->attach($skill->id, [
            'level' => 'avance',
            'years_experience' => 3,
            'is_validated' => true,
        ]);

        UserAbonnement::create([
            'user_id' => $enterpriseUser->id,
            'abonnement_id' => $plan->id,
            'date_debut' => now()->subWeek(),
            'date_fin' => now()->addMonth(),
            'status' => 'Actif',
        ]);

        $this->actingAs($enterpriseUser)
            ->get('/entreprise')
            ->assertOk();

        $this->actingAs($enterpriseUser)
            ->get('/entreprise/offres/create')
            ->assertOk();

        $this->actingAs($enterpriseUser)
            ->get('/entreprise/historique')
            ->assertOk()
            ->assertSee('Développeur Full Stack');

        $this->actingAs($enterpriseUser)
            ->get('/entreprise/candidatures-ia')
            ->assertOk()
            ->assertSee('Développeur Full Stack');

        $this->actingAs($enterpriseUser)
            ->get(route('entreprise.abonnements'))
            ->assertOk()
            ->assertSee('Business');

        $this->actingAs($enterpriseUser)
            ->get(route('entreprise.promotion'))
            ->assertOk()
            ->assertSee('Développeur Full Stack');

        $this->actingAs($enterpriseUser)
            ->get(route('entreprise.offres.candidatures', $offer))
            ->assertOk()
            ->assertSee('Développeur Full Stack');

        $this->actingAs($enterpriseUser)
            ->get(route('entreprise.connected_candidate_details', $candidate))
            ->assertOk()
            ->assertSee('Jean')
            ->assertSee('Baccalaureat informatique')
            ->assertSee('Laravel');
    }
}
