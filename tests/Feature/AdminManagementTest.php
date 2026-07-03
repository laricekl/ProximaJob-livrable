<?php

namespace Tests\Feature;

use App\Models\Abonnement;
use App\Models\SiteSetting;
use App\Models\UserAbonnement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_admin_can_authenticate_from_the_admin_login_screen(): void
    {
        $admin = $this->createAdmin([
            'email' => 'admin-login@example.com',
            'password' => Hash::make('SecureAdmin123!'),
        ]);

        $response = $this->post(route('admin.login.post'), [
            'email' => $admin->email,
            'password' => 'SecureAdmin123!',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
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

    public function test_legacy_short_admin_paths_redirect_to_the_refactored_admin_pages(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->get('/admin/utilisateurs')
            ->assertRedirect(route('admin.users'));

        $this->actingAs($admin)
            ->get('/admin/offres')
            ->assertRedirect(route('admin.offres'));

        $this->actingAs($admin)
            ->get('/admin/abonnements')
            ->assertRedirect(route('admin.abonnements'));

        $this->actingAs($admin)
            ->get('/admin/statistiques')
            ->assertRedirect(route('admin.statistiques'));

        $this->actingAs($admin)
            ->get('/admin/newsletters')
            ->assertRedirect(route('admin.newsletters'));

        $this->actingAs($admin)
            ->get('/admin/parametres')
            ->assertRedirect(route('admin.parametres'));
    }

    public function test_admin_can_update_global_site_settings_and_files(): void
    {
        Storage::fake('public');

        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('parametres.update-general'), [
            'site_nom' => 'ProximaJob Demo',
            'email' => 'equipe@proximajob.test',
            'tel' => '+1 555 100 2000',
            'localisation' => 'Montreal, Canada',
            'timezone' => 'America/New_York',
            'map_embed_url' => 'https://www.google.com/maps/embed?pb=test-demo',
            'map_zoom' => 12,
            'logo' => UploadedFile::fake()->image('logo.png'),
            'favicon' => UploadedFile::fake()->image('favicon.png', 32, 32),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Paramètres sauvegardés avec succès !');

        $settings = SiteSetting::first();

        $this->assertSame('ProximaJob Demo', $settings->site_nom);
        $this->assertSame('equipe@proximajob.test', $settings->email);
        $this->assertSame('+1 555 100 2000', $settings->tel);
        $this->assertSame('Montreal, Canada', $settings->localisation);
        $this->assertSame('America/New_York', $settings->timezone);
        $this->assertSame('https://www.google.com/maps/embed?pb=test-demo', $settings->map_embed_url);
        $this->assertSame(12, $settings->map_zoom);
        Storage::disk('public')->assertExists($settings->logo);
        Storage::disk('public')->assertExists($settings->favicon);
    }

    public function test_admin_can_create_a_candidate_account(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->postJson(route('admin.users.store'), [
                'name' => 'Nadia',
                'prenom' => 'Lopez',
                'email' => 'nadia.lopez@example.com',
                'password' => 'SecurePass123!',
                'password_confirmation' => 'SecurePass123!',
                'role' => 'candidat',
                'telephone' => '+1 555 444 3333',
                'adresse' => 'Montreal',
            ])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Utilisateur créé avec succès !',
            ]);

        $user = User::where('email', 'nadia.lopez@example.com')->first();

        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('candidat'));
        $this->assertSame('Actif', $user->status);
        $this->assertSame('+1 555 444 3333', $user->telephone);
    }

    public function test_admin_can_create_an_approved_enterprise_account(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->postJson(route('admin.users.store'), [
                'name' => 'Sonia',
                'prenom' => 'Baker',
                'email' => 'sonia.baker@example.com',
                'password' => 'SecurePass123!',
                'password_confirmation' => 'SecurePass123!',
                'role' => 'entreprise',
                'company_name' => 'Northwind QA',
                'neq' => '1234567890',
                'website' => 'https://northwind.example.com',
                'description' => 'Entreprise creee depuis l admin.',
            ])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Utilisateur créé avec succès !',
            ]);

        $user = User::where('email', 'sonia.baker@example.com')->first();

        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('entreprise'));
        $this->assertDatabaseHas('entreprises', [
            'user_id' => $user->id,
            'company_name' => 'Northwind QA',
            'neq' => '1234567890',
            'website' => 'https://northwind.example.com',
            'status' => 'approved',
        ]);
    }

    public function test_admin_user_creation_returns_human_validation_errors(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->postJson(route('admin.users.store'), [
                'name' => '',
                'prenom' => '',
                'email' => 'not-an-email',
                'password' => 'short',
                'password_confirmation' => 'different',
                'role' => 'entreprise',
            ])
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Erreur de validation',
            ])
            ->assertJsonValidationErrors([
                'name',
                'prenom',
                'email',
                'password',
                'company_name',
                'neq',
            ]);
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

        $this->assertSoftDeleted('users', [
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
