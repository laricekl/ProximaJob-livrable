<?php

namespace Tests\Feature;

use App\Models\Abonnement;
use App\Models\UserAbonnement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Feature\Concerns\CreatesTestAccounts;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use CreatesTestAccounts;
    use RefreshDatabase;

    protected function createAdmin(array $attributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'prenom' => 'Admin',
            'name' => 'QA',
            'email' => 'admin+'.uniqid().'@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin-pass'),
            'status' => 'Actif',
        ], $attributes));

        $user->syncRoles(['admin']);

        return $user->fresh();
    }

    public function test_admin_can_filter_users_with_real_data(): void
    {
        $admin = $this->createAdmin();
        $candidate = $this->createCandidate([
            'name' => 'Alice',
            'prenom' => 'Martin',
            'email' => 'alice@example.com',
            'status' => 'Actif',
        ]);

        $enterpriseUser = $this->createEnterprise([
            'name' => 'Benoit',
            'prenom' => 'Durand',
            'email' => 'benoit@example.com',
            'status' => 'Actif',
        ], [
            'company_name' => 'Beta Conseil',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users', ['search' => 'Beta', 'role' => 'entreprise', 'status' => 'pending']))
            ->assertOk()
            ->assertSee('Beta Conseil')
            ->assertSee('En attente de validation')
            ->assertDontSee($candidate->email);
    }

    public function test_admin_can_filter_offers_with_real_data(): void
    {
        $admin = $this->createAdmin();
        $enterpriseUser = $this->createEnterprise([], ['company_name' => 'Gamma Labs']);
        $otherEnterprise = $this->createEnterprise([], ['company_name' => 'Delta Studio']);

        $this->createOfferFor($enterpriseUser, [
            'titre' => 'Architecte Cloud',
            'poste' => 'Architecte Cloud',
            'localisation' => 'Montreal',
            'status' => 'active',
            'date_fin' => now()->addDays(15)->toDateString(),
        ]);

        $this->createOfferFor($otherEnterprise, [
            'titre' => 'Chef de projet',
            'poste' => 'Chef de projet',
            'localisation' => 'Paris',
            'status' => 'desactive',
            'date_fin' => now()->addDays(15)->toDateString(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.offres', ['search' => 'Architecte', 'entreprise_id' => $enterpriseUser->entreprise->id, 'status' => 'active']))
            ->assertOk()
            ->assertSee('Architecte Cloud')
            ->assertSee('Gamma Labs')
            ->assertSee('Publiée')
            ->assertDontSee('Chef de projet');
    }

    public function test_admin_core_pages_render_with_real_data(): void
    {
        $admin = $this->createAdmin();
        $candidate = $this->createCandidate();
        $offer = $this->createOfferFor();
        $plan = Abonnement::create([
            'nom' => 'Premium QA',
            'description' => 'Plan de test',
            'montant' => 29,
            'duree' => 'mensuel',
            'actif' => true,
        ]);

        UserAbonnement::create([
            'user_id' => $candidate->id,
            'abonnement_id' => $plan->id,
            'date_debut' => now()->subDays(2),
            'date_fin' => now()->addMonth(),
            'status' => 'Actif',
        ]);

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard');

        $this->actingAs($admin)->get(route('admin.abonnements'))
            ->assertOk()
            ->assertSee('Premium QA');

        $this->actingAs($admin)->get(route('admin.statistiques'))
            ->assertOk()
            ->assertSee('Top entreprises recruteuses');

        $this->actingAs($admin)->get(route('admin.users.show', $candidate))
            ->assertOk()
            ->assertSee($candidate->email);

        $this->actingAs($admin)->get(route('admin.newsletters'))
            ->assertOk()
            ->assertSee('Newsletter');

        $this->actingAs($admin)->get(route('admin.parametres'))
            ->assertOk()
            ->assertSee('Paramètres');
    }

    public function test_admin_can_suspend_reactivate_and_delete_a_user_with_password_confirmation(): void
    {
        $admin = $this->createAdmin();
        $candidate = $this->createCandidate([
            'name' => 'Julie',
            'prenom' => 'Bernard',
            'email' => 'julie@example.com',
            'status' => 'Actif',
        ]);

        $this->actingAs($admin)
            ->postJson(route('admin.users.suspend', $candidate->id), ['password' => 'admin-pass'])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => "L'utilisateur Julie Bernard a été suspendu avec succès.",
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $candidate->id,
            'status' => 'Suspendu',
        ]);

        $this->actingAs($admin)
            ->postJson(route('admin.users.reactivate', $candidate->id), ['password' => 'admin-pass'])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => "L'utilisateur Julie Bernard a été réactivé avec succès.",
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $candidate->id,
            'status' => 'Actif',
        ]);

        $this->actingAs($admin)
            ->postJson(route('admin.users.delete', $candidate->id), ['password' => 'admin-pass'])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => "L'utilisateur Julie Bernard a été supprimé avec succès.",
            ]);

        $this->assertDatabaseMissing('users', [
            'id' => $candidate->id,
        ]);
    }

    public function test_admin_user_actions_reject_invalid_password_and_self_targeting(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->postJson(route('admin.users.suspend', $admin->id), ['password' => 'wrong-pass'])
            ->assertUnprocessable()
            ->assertJson([
                'success' => false,
                'message' => 'Mot de passe incorrect. Vérifiez votre mot de passe et réessayez.',
            ]);

        $this->actingAs($admin)
            ->postJson(route('admin.users.suspend', $admin->id), ['password' => 'admin-pass'])
            ->assertUnprocessable()
            ->assertJson([
                'success' => false,
                'message' => 'Vous ne pouvez pas suspendre votre propre compte.',
            ]);

        $this->actingAs($admin)
            ->postJson(route('admin.users.delete', $admin->id), ['password' => 'admin-pass'])
            ->assertUnprocessable()
            ->assertJson([
                'success' => false,
                'message' => 'Vous ne pouvez pas supprimer votre propre compte.',
            ]);
    }

    public function test_admin_can_deactivate_and_reactivate_offers_with_live_status_rules(): void
    {
        $admin = $this->createAdmin();
        $activeOffer = $this->createOfferFor(null, [
            'status' => 'active',
            'date_fin' => now()->addDays(10)->toDateString(),
        ]);
        $expiredOffer = $this->createOfferFor(null, [
            'status' => 'desactive',
            'date_fin' => now()->subDay()->toDateString(),
        ]);

        $this->actingAs($admin)
            ->patchJson(route('offres.deactivate', $activeOffer->id))
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Offre désactivée avec succès',
            ]);

        $this->assertDatabaseHas('offres', [
            'id' => $activeOffer->id,
            'status' => 'desactive',
        ]);

        $this->actingAs($admin)
            ->patchJson(route('offres.reactivate', $activeOffer->id))
            ->assertOk()
            ->assertJson([
                'success' => true,
                'status' => 'active',
                'message' => 'Offre réactivée avec succès',
            ]);

        $this->actingAs($admin)
            ->patchJson(route('offres.reactivate', $expiredOffer->id))
            ->assertOk()
            ->assertJson([
                'success' => true,
                'status' => 'expire',
                'message' => 'Offre réactivée avec succès (mais expirée car la date limite est dépassée)',
            ]);
    }
}
