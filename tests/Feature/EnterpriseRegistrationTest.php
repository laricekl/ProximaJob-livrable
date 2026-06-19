<?php

namespace Tests\Feature;

use App\Models\Entreprise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EnterpriseRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_enterprise_registration_does_not_require_rccm(): void
    {
        Mail::fake();

        $response = $this->post(route('register.entreprise'), [
            'name' => 'Martin',
            'prenom' => 'Claire',
            'email' => 'enterprise-rccm@example.com',
            'telephone' => '5145551234',
            'adresse' => '123 rue du Test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'company_name' => 'Studio Horizon',
            'neq' => 'NEQ-12345',
        ]);

        $response->assertRedirect(route('enterprise.verification.notice', absolute: false));
        $response->assertSessionDoesntHaveErrors(['rccm']);

        $user = User::where('email', 'enterprise-rccm@example.com')->first();

        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('entreprise'));
        $this->assertDatabaseHas('entreprises', [
            'user_id' => $user->id,
            'company_name' => 'Studio Horizon',
            'neq' => 'NEQ-12345',
        ]);
        $this->assertSame('email_verification_pending', Entreprise::where('user_id', $user->id)->value('status'));
    }
}
