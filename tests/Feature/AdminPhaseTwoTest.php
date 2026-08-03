<?php

namespace Tests\Feature;

use App\Models\Abonnement;
use App\Models\Postulation;
use App\Models\SecuritySetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\Feature\Concerns\CreatesTestAccounts;
use Tests\TestCase;

class AdminPhaseTwoTest extends TestCase
{
    use CreatesTestAccounts, RefreshDatabase;

    private function admin(array $attributes = []): User
    {
        $admin = User::factory()->create(array_merge([
            'email_verified_at' => now(),
            'status' => 'Actif',
            'password' => Hash::make('SecureAdmin123!'),
        ], $attributes));
        $admin->syncRoles(['admin']);

        return $admin;
    }

    public function test_admin_ip_allowlist_and_password_expiration_are_enforced(): void
    {
        $admin = $this->admin(['password_changed_at' => now()->subDays(60)]);
        $settings = SecuritySetting::firstOrCreate([], SecuritySetting::getDefaultSettings());

        $settings->update(['admin_ips' => ['10.0.0.1']]);
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertForbidden();

        $settings->update([
            'admin_ips' => [],
            'password_expiration' => true,
            'password_expiration_days' => 30,
        ]);
        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHas('error');
    }

    public function test_admin_two_factor_email_challenge_is_required_and_can_be_verified(): void
    {
        Mail::fake();
        $admin = $this->admin(['email' => 'two-factor@example.com']);
        SecuritySetting::firstOrCreate([], SecuritySetting::getDefaultSettings())->update(['enable_2fa' => true]);

        $this->post(route('admin.login.post'), [
            'email' => $admin->email,
            'password' => 'SecureAdmin123!',
        ])->assertRedirect(route('admin.2fa.form'))->assertSessionHas('admin_2fa');
        $this->assertGuest();

        $this->withSession(['admin_2fa' => [
            'user_id' => $admin->id,
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(5)->timestamp,
            'remember' => false,
        ]])->post(route('admin.2fa.verify'), ['code' => '123456'])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
        $this->assertDatabaseHas('admin_audit_logs', ['action' => 'login.2fa_verified', 'admin_id' => $admin->id]);
    }

    public function test_admin_mutations_are_audited_automatically(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.categories.store'), ['nom' => 'Audit automatique'])
            ->assertRedirect();

        $this->assertDatabaseHas('admin_audit_logs', [
            'action' => 'admin.request.post',
            'admin_id' => $admin->id,
        ]);
    }

    public function test_complex_password_policy_is_enforced_for_new_admin_accounts(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Sécurité',
            'prenom' => 'Faible',
            'email' => 'weak-admin@example.com',
            'password' => 'motdepassefaible',
            'password_confirmation' => 'motdepassefaible',
            'role' => 'admin',
        ])->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['email' => 'weak-admin@example.com']);
    }

    public function test_admin_can_open_an_application_file_and_save_internal_notes(): void
    {
        $admin = $this->admin();
        $candidate = $this->createCandidate();
        $offer = $this->createOfferFor();
        $application = Postulation::create([
            'user_id' => $candidate->id,
            'offre_id' => $offer->id,
            'status' => 'en_attente',
            'autopostulation' => false,
            'match_score' => 82,
        ]);

        $this->actingAs($admin)->get(route('admin.candidatures.show', $application))
            ->assertOk()->assertSee($candidate->email)->assertSee('82 %')->assertSee('Notes internes');

        $this->actingAs($admin)->patch(route('admin.candidatures.notes', $application), [
            'admin_notes' => 'Profil à rappeler mardi.',
        ])->assertRedirect();

        $this->assertDatabaseHas('postulations', ['id' => $application->id, 'admin_notes' => 'Profil à rappeler mardi.']);
        $this->assertDatabaseHas('admin_audit_logs', ['action' => 'candidature.notes', 'subject_id' => $application->id]);
    }

    public function test_admin_can_manage_a_plan_without_json(): void
    {
        $admin = $this->admin();
        $payload = [
            'nom' => 'Plan Phase 2',
            'description' => 'Plan administrable sans JSON.',
            'montant' => 59,
            'duree' => 30,
            'couleur' => '#155e75',
            'actif' => 1,
            'populaire' => 0,
            'fonctionnalites' => ['CV multiples', 'Suivi prioritaire'],
        ];

        $this->actingAs($admin)->post(route('admin.abonnements.store'), $payload)
            ->assertRedirect(route('admin.abonnements'))->assertSessionHasNoErrors();
        $plan = Abonnement::where('nom', 'Plan Phase 2')->firstOrFail();
        $this->assertSame(['CV multiples', 'Suivi prioritaire'], $plan->fonctionnalites()->pluck('nom')->all());

        $this->actingAs($admin)->put(route('admin.abonnements.update', $plan), array_merge($payload, [
            'nom' => 'Plan Phase 2 amélioré',
            'fonctionnalites' => ['CV multiples', 'Support humain'],
        ]))->assertRedirect(route('admin.abonnements'));
        $this->assertDatabaseHas('abonnements', ['id' => $plan->id, 'nom' => 'Plan Phase 2 amélioré']);
        $this->assertDatabaseHas('abonnement_fonctionnalites', ['abonnement_id' => $plan->id, 'nom' => 'Support humain']);

        $this->actingAs($admin)->delete(route('admin.abonnements.destroy', $plan))
            ->assertRedirect(route('admin.abonnements'));
        $this->assertSoftDeleted('abonnements', ['id' => $plan->id]);
    }
}
