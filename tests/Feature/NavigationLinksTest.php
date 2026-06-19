<?php

namespace Tests\Feature;

use App\Models\Entreprise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationLinksTest extends TestCase
{
    use RefreshDatabase;

    private function createCandidate(): User
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'status' => 'Actif',
        ]);

        $user->assignRole('candidat');

        return $user->fresh();
    }

    private function createEnterprise(): User
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'status' => 'Actif',
        ]);

        $user->assignRole('entreprise');

        Entreprise::create([
            'user_id' => $user->id,
            'company_name' => 'QA Navigation',
            'status' => 'approved',
        ]);

        return $user->fresh();
    }

    private function createAdmin(): User
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'status' => 'Actif',
        ]);

        $user->assignRole('admin');

        return $user->fresh();
    }

    public function test_public_navigation_renders_expected_links_for_guests(): void
    {
        $response = $this->get(route('welcome'));

        $response->assertOk();
        $response->assertSee('href="'.route('welcome').'"', false);
        $response->assertSee('href="'.route('offres').'"', false);
        $response->assertSee('href="'.route('ressources').'"', false);
        $response->assertSee('href="'.route('abonnement').'"', false);
        $response->assertSee('href="'.route('contact').'"', false);
        $response->assertSee('href="'.route('login').'"', false);
        $response->assertSee('href="'.route('register').'"', false);
    }

    public function test_public_navigation_shows_dashboard_entry_for_authenticated_users(): void
    {
        $candidate = $this->createCandidate();

        $this->actingAs($candidate)
            ->get(route('welcome'))
            ->assertOk()
            ->assertSee('href="'.route('dashboard').'"', false)
            ->assertDontSee('href="'.route('login').'"', false);
    }

    public function test_candidate_navigation_renders_expected_links(): void
    {
        $candidate = $this->createCandidate();

        $this->actingAs($candidate)
            ->get(route('user.home'))
            ->assertOk()
            ->assertSee('href="'.route('user.home').'"', false)
            ->assertSee('href="'.route('offres').'"', false)
            ->assertSee('href="'.route('user.historiques').'"', false)
            ->assertSee('href="'.route('infos.cv').'"', false)
            ->assertSee('href="'.route('notifications.index').'"', false)
            ->assertSee('href="'.route('user.profil-public').'"', false)
            ->assertSee('href="'.route('profile.edit').'"', false)
            ->assertSee('href="'.route('user.bonnement').'"', false);
    }

    public function test_enterprise_navigation_renders_expected_links(): void
    {
        $enterprise = $this->createEnterprise();

        $this->actingAs($enterprise)
            ->get(route('offres.publies'))
            ->assertOk()
            ->assertSee('href="'.route('offres.publies').'"', false)
            ->assertSee('href="'.route('entreprise.historique').'"', false)
            ->assertSee('href="'.route('entreprise.candidatures_ia').'"', false)
            ->assertSee('href="'.route('entreprise.promotion').'"', false)
            ->assertSee('href="'.route('entreprise.abonnements').'"', false);
    }

    public function test_admin_navigation_renders_expected_links(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('href="'.route('admin.dashboard').'"', false)
            ->assertSee('href="'.route('admin.users').'"', false)
            ->assertSee('href="'.route('admin.offres').'"', false)
            ->assertSee('href="'.route('admin.abonnements').'"', false)
            ->assertSee('href="'.route('admin.statistiques').'"', false)
            ->assertSee('href="'.route('admin.newsletters').'"', false)
            ->assertSee('href="'.route('admin.parametres').'"', false);
    }
}
