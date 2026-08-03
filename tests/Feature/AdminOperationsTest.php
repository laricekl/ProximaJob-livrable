<?php

namespace Tests\Feature;

use App\Models\AdminAuditLog;
use App\Models\NewsletterCampagne;
use App\Models\Postulation;
use App\Models\Ressource;
use App\Models\SecuritySetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesTestAccounts;
use Tests\TestCase;

class AdminOperationsTest extends TestCase
{
    use CreatesTestAccounts, RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create(['email_verified_at' => now(), 'status' => 'Actif']);
        $admin->syncRoles(['admin']);

        return $admin;
    }

    public function test_admin_operations_pages_are_available_only_to_admins(): void
    {
        $admin = $this->admin();

        foreach (['admin.entreprises.index', 'admin.candidatures.index', 'admin.ressources.index', 'admin.pilotage'] as $route) {
            $this->actingAs($admin)->get(route($route))->assertOk();
        }

        $candidate = $this->createCandidate();
        $this->actingAs($candidate)->get(route('admin.pilotage'))->assertForbidden();
    }

    public function test_admin_can_moderate_an_enterprise_and_public_visibility(): void
    {
        $admin = $this->admin();
        $enterpriseUser = $this->createEnterprise([], ['status' => 'pending'], false);
        $enterprise = $enterpriseUser->entreprise;

        $this->actingAs($admin)->patch(route('admin.entreprises.status', $enterprise), ['status' => 'approved'])
            ->assertRedirect()->assertSessionHasNoErrors();
        $this->assertDatabaseHas('entreprises', ['id' => $enterprise->id, 'status' => 'approved']);

        $this->actingAs($admin)->patch(route('admin.entreprises.featured', $enterprise))->assertRedirect();
        $this->assertDatabaseHas('entreprises', ['id' => $enterprise->id, 'is_featured' => true]);
        $this->assertDatabaseHas('admin_audit_logs', ['action' => 'entreprise.featured', 'subject_id' => $enterprise->id]);

        $this->actingAs($admin)->patch(route('admin.entreprises.status', $enterprise), [
            'status' => 'rejected', 'reason' => 'NEQ impossible à confirmer.',
        ])->assertRedirect();
        $this->assertDatabaseHas('entreprises', ['id' => $enterprise->id, 'status' => 'rejected', 'rejection_reason' => 'NEQ impossible à confirmer.']);
    }

    public function test_admin_can_manage_global_applications(): void
    {
        $admin = $this->admin();
        $candidate = $this->createCandidate();
        $offer = $this->createOfferFor();
        $application = Postulation::create([
            'user_id' => $candidate->id,
            'offre_id' => $offer->id,
            'status' => 'en_attente',
            'autopostulation' => false,
        ]);

        $this->actingAs($admin)->get(route('admin.candidatures.index', ['search' => $candidate->email]))
            ->assertOk()->assertSee($candidate->email)->assertSee($offer->titre);

        $this->actingAs($admin)->patch(route('admin.candidatures.status', $application), ['status' => 'shortlisted'])
            ->assertRedirect();
        $this->assertDatabaseHas('postulations', ['id' => $application->id, 'status' => 'shortlisted']);
    }

    public function test_legacy_application_and_enterprise_statuses_are_presented_as_canonical_statuses(): void
    {
        $admin = $this->admin();
        $enterpriseUser = $this->createEnterprise([], ['status' => 'email_verification_pending'], false);
        $candidate = $this->createCandidate();
        $offer = $this->createOfferFor();
        Postulation::create([
            'user_id' => $candidate->id,
            'offre_id' => $offer->id,
            'status' => 'acceptee',
            'autopostulation' => false,
        ]);

        $this->actingAs($admin)->get(route('admin.entreprises.index', ['status' => 'pending']))
            ->assertOk()
            ->assertSee($enterpriseUser->entreprise->company_name)
            ->assertSee('À valider');

        $this->actingAs($admin)->get(route('admin.candidatures.index', ['status' => 'accepted']))
            ->assertOk()
            ->assertSee('Acceptées')
            ->assertSee('value="accepted" selected', false);
    }

    public function test_admin_can_edit_a_user_from_the_detail_page(): void
    {
        $admin = $this->admin();
        $candidate = $this->createCandidate(['email' => 'before@example.com']);

        $this->actingAs($admin)->put(route('admin.users.update', $candidate), [
            'name' => 'Tremblay',
            'prenom' => 'Jeanne',
            'email' => 'jeanne.tremblay@example.com',
            'telephone' => '514-555-0100',
            'adresse' => 'Montréal',
            'role' => 'candidat',
        ])->assertRedirect(route('admin.users.show', $candidate));

        $this->assertDatabaseHas('users', [
            'id' => $candidate->id,
            'email' => 'jeanne.tremblay@example.com',
            'telephone' => '514-555-0100',
        ]);
    }

    public function test_admin_can_create_update_and_delete_resources(): void
    {
        $admin = $this->admin();
        $payload = ['titre' => 'Guide CV', 'type' => 'guide', 'fichier_url' => 'https://example.com/guide.pdf', 'description' => 'Guide pratique', 'access' => 'gratuit'];

        $this->actingAs($admin)->post(route('admin.ressources.store'), $payload)->assertRedirect();
        $resource = Ressource::where('titre', 'Guide CV')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.ressources.update', $resource), array_merge($payload, ['titre' => 'Guide CV avancé']))->assertRedirect();
        $this->assertDatabaseHas('ressources', ['id' => $resource->id, 'titre' => 'Guide CV avancé']);

        $this->actingAs($admin)->delete(route('admin.ressources.destroy', $resource))->assertRedirect();
        $this->assertDatabaseMissing('ressources', ['id' => $resource->id]);
    }

    public function test_admin_can_manage_security_and_newsletter_drafts(): void
    {
        $admin = $this->admin();
        $campaign = NewsletterCampagne::create(['sujet' => 'Brouillon', 'contenu' => 'Texte', 'audience' => 'tous', 'statut' => 'brouillon']);

        $this->actingAs($admin)->put(route('admin.newsletters.update', $campaign), [
            'sujet' => 'Brouillon corrigé', 'contenu' => 'Nouveau texte', 'audience' => 'candidats',
        ])->assertRedirect();
        $this->assertDatabaseHas('newsletter_campagnes', ['id' => $campaign->id, 'sujet' => 'Brouillon corrigé']);

        $this->actingAs($admin)->patch(route('admin.security.update'), [
            'complex_passwords' => 1,
            'brute_force_protection' => 1,
            'admin_activities_log' => 1,
            'log_retention_days' => 90,
            'admin_ips' => "127.0.0.1\n10.0.0.1",
        ])->assertRedirect();

        $settings = SecuritySetting::firstOrFail();
        $this->assertTrue($settings->complex_passwords);
        $this->assertSame(['127.0.0.1', '10.0.0.1'], $settings->admin_ips);
        $this->assertGreaterThanOrEqual(2, AdminAuditLog::count());

        $this->actingAs($admin)->delete(route('admin.newsletters.destroy', $campaign))->assertRedirect();
        $this->assertDatabaseMissing('newsletter_campagnes', ['id' => $campaign->id]);
    }
}
