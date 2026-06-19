<?php

namespace Tests\Feature;

use App\Models\Entreprise;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnterpriseAccessTest extends TestCase
{
    use RefreshDatabase;

    private function createEnterpriseUser(): User
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'status' => 'Actif',
        ]);

        $user->assignRole('entreprise');

        Entreprise::create([
            'user_id' => $user->id,
            'company_name' => 'QA Enterprise',
            'status' => 'approved',
        ]);

        return $user->fresh();
    }

    public function test_enterprise_users_are_redirected_to_the_enterprise_space_after_login(): void
    {
        $user = $this->createEnterpriseUser();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/entreprise');
    }

    public function test_approved_enterprise_users_can_access_the_enterprise_dashboard(): void
    {
        $user = $this->createEnterpriseUser();

        $response = $this->actingAs($user)->get('/entreprise');

        $response->assertOk();
    }

    public function test_verified_enterprise_users_can_access_the_enterprise_dashboard(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'status' => 'Actif',
        ]);

        $user->assignRole('entreprise');

        Entreprise::create([
            'user_id' => $user->id,
            'company_name' => 'Verified Enterprise',
            'status' => 'verified',
        ]);

        $response = $this->actingAs($user)->get('/entreprise');

        $response->assertOk();
    }

    public function test_candidates_are_redirected_to_their_dashboard_when_opening_the_enterprise_space(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'status' => 'Actif',
        ]);

        $user->assignRole('candidat');

        $response = $this->actingAs($user)->get('/entreprise');

        $response->assertRedirect('/user');
    }

    public function test_enterprises_are_redirected_to_their_dashboard_when_opening_the_candidate_space(): void
    {
        $user = $this->createEnterpriseUser();

        $response = $this->actingAs($user)->get('/user');

        $response->assertRedirect('/entreprise');
    }

    public function test_dashboard_route_redirects_users_to_their_role_space(): void
    {
        $candidate = User::factory()->create([
            'email_verified_at' => now(),
            'status' => 'Actif',
        ]);
        $candidate->assignRole('candidat');

        $enterprise = $this->createEnterpriseUser();

        $admin = User::factory()->create([
            'email_verified_at' => now(),
            'status' => 'Actif',
        ]);
        $admin->assignRole('admin');

        $this->actingAs($candidate)
            ->get('/dashboard')
            ->assertRedirect('/user');

        $this->actingAs($enterprise)
            ->get('/dashboard')
            ->assertRedirect('/entreprise');

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertRedirect('/admin');
    }

    public function test_enterprise_offer_form_recreates_default_sectors_when_none_exist(): void
    {
        $user = $this->createEnterpriseUser();

        $this->assertSame(0, Sector::count());

        $response = $this->actingAs($user)->get('/entreprise/offres/create');

        $response->assertOk();
        $response->assertSee('Technologie et informatique');
        $this->assertGreaterThan(0, Sector::where('is_active', true)->count());
    }
}
