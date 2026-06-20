<?php

namespace Tests\Feature;

use App\Models\Abonnement;
use App\Models\CandidateSector;
use App\Models\Diplome;
use App\Models\Notification;
use App\Models\Postulation;
use App\Models\Sector;
use App\Models\Skill;
use App\Models\User;
use App\Models\UserAbonnement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Feature\Concerns\CreatesTestAccounts;
use Tests\TestCase;

class RouteSmokeTest extends TestCase
{
    use CreatesTestAccounts;
    use RefreshDatabase;

    private function createAdmin(array $attributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'prenom' => 'Admin',
            'name' => 'Smoke',
            'email' => 'admin-smoke@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin-pass'),
            'status' => 'Actif',
        ], $attributes));

        $user->syncRoles(['admin']);

        return $user->fresh();
    }

    public function test_public_candidate_enterprise_and_admin_routes_do_not_return_server_errors(): void
    {
        $candidate = $this->createCandidate(['email' => 'candidate-smoke@example.com']);
        $enterprise = $this->createEnterprise(['email' => 'enterprise-smoke@example.com'], ['company_name' => 'Smoke Enterprise']);
        $admin = $this->createAdmin();
        $offer = $this->createOfferFor($enterprise, [
            'titre' => 'Offre Smoke',
            'poste' => 'Offre Smoke',
            'slug' => 'offre-smoke',
        ]);

        $sector = Sector::firstOrCreate(['slug' => 'smoke-sector'], ['name' => 'Smoke Sector', 'is_active' => true]);
        $diplome = Diplome::firstOrCreate(
            ['nom_diplome' => 'Diplome Smoke'],
            ['niveau_education' => 'UNIVERSITAIRE_1ER_CYCLE', 'statut' => 'ACTIF']
        );
        $skill = Skill::firstOrCreate(
            ['name' => 'Smoke Skill'],
            ['slug' => 'smoke-skill', 'category' => 'Hard Skills', 'importance_level' => 3, 'is_active' => true]
        );
        $plan = Abonnement::create([
            'nom' => 'Smoke Plan',
            'description' => 'Plan de navigation smoke',
            'montant' => 19.99,
            'duree' => 'mensuel',
            'actif' => true,
        ]);

        CandidateSector::create([
            'candidate_id' => $candidate->id,
            'sector_id' => $sector->id,
            'diplome_id' => $diplome->id,
            'experience_years' => 2,
        ]);
        $candidate->skills()->attach($skill->id, [
            'level' => 'intermediaire',
            'years_experience' => 2,
            'is_validated' => true,
        ]);

        $postulation = Postulation::create([
            'user_id' => $candidate->id,
            'offre_id' => $offer->id,
            'autopostulation' => true,
            'status' => 'en_attente',
        ]);

        Notification::create([
            'user_id' => $candidate->id,
            'role' => 'candidat',
            'title' => 'Smoke notification',
            'message' => 'Message smoke',
            'link' => '/user',
            'is_read' => false,
        ]);

        UserAbonnement::create([
            'user_id' => $enterprise->id,
            'abonnement_id' => $plan->id,
            'date_debut' => now()->subDays(7),
            'date_fin' => now()->addMonth(),
            'status' => 'Actif',
        ]);

        $guestRoutes = [
            route('welcome'),
            route('offres'),
            route('contact'),
            route('ressources'),
            route('abonnement'),
            route('login'),
            route('register'),
            route('entreprise.register'),
            route('password.request'),
        ];

        foreach ($guestRoutes as $url) {
            $response = $this->get($url);
            $this->assertLessThan(500, $response->getStatusCode(), "Guest route failed: {$url}");
        }

        $candidateRoutes = [
            route('user.home'),
            route('user.historiques'),
            route('user.historiques_ia'),
            route('infos.cv'),
            route('cv.personalization.form'),
            route('user.profil-public'),
            route('user.detail-candidature'),
            route('notifications.index'),
            route('profile.edit'),
        ];

        foreach ($candidateRoutes as $url) {
            $response = $this->actingAs($candidate)->get($url);
            $this->assertLessThan(500, $response->getStatusCode(), "Candidate route failed: {$url}");
        }

        $enterpriseRoutes = [
            route('offres.publies'),
            route('entreprise.offres.create'),
            route('entreprise.historique'),
            route('entreprise.candidatures_ia'),
            route('entreprise.abonnements'),
            route('entreprise.promotion'),
            route('entreprise.offres.candidatures', $offer),
            route('entreprise.connected_candidate_details', $candidate),
        ];

        foreach ($enterpriseRoutes as $url) {
            $response = $this->actingAs($enterprise)->get($url);
            $this->assertLessThan(500, $response->getStatusCode(), "Enterprise route failed: {$url}");
        }

        $adminRoutes = [
            route('admin.dashboard'),
            route('admin.users'),
            route('admin.offres'),
            route('admin.abonnements'),
            route('admin.statistiques'),
            route('admin.newsletters'),
            route('admin.parametres'),
            route('admin.users.show', $candidate),
        ];

        foreach ($adminRoutes as $url) {
            $response = $this->actingAs($admin)->get($url);
            $this->assertLessThan(500, $response->getStatusCode(), "Admin route failed: {$url}");
        }
    }
}
