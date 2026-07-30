<?php

namespace Tests\Feature;

use App\Mail\NewsletterCampaignMail;
use App\Mail\PublicContactMessage;
use App\Models\CvGenere;
use App\Models\CvProfile;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Concerns\CreatesTestAccounts;
use Tests\TestCase;

class AuditCorrectionsTest extends TestCase
{
    use CreatesTestAccounts;
    use RefreshDatabase;

    public function test_legacy_public_candidate_file_route_is_removed(): void
    {
        $this->get('/candidatures/1/1/cv.pdf')->assertNotFound();
    }

    public function test_marketing_role_only_accesses_analytics_and_campaigns(): void
    {
        $marketing = User::factory()->create([
            'email_verified_at' => now(),
            'status' => 'Actif',
        ]);
        $marketing->syncRoles(['Marketing']);

        $this->actingAs($marketing)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($marketing)->get(route('admin.statistiques'))->assertOk();
        $this->actingAs($marketing)->get(route('admin.newsletters'))->assertOk();
        $this->actingAs($marketing)->get(route('admin.users'))->assertForbidden()->assertSee('Accès refusé');
        $this->actingAs($marketing)->get(route('admin.parametres'))->assertForbidden();
        $this->actingAs($marketing)->get(route('admin.categories.index'))->assertForbidden();
    }

    public function test_generated_cv_files_are_scoped_to_their_owner(): void
    {
        Storage::fake('public');
        $owner = $this->createCandidate();
        $attacker = $this->createCandidate();
        $profile = CvProfile::create([
            'user_id' => $owner->id,
            'nom' => 'Propriétaire',
            'prenom' => 'CV',
            'email' => $owner->email,
        ]);
        Storage::disk('public')->put('personalized-cvs/private-cv.pdf', 'pdf');
        CvGenere::create([
            'cv_profile_id' => $profile->id,
            'nom_fichier' => 'private-cv.pdf',
            'chemin_fichier' => 'personalized-cvs/private-cv.pdf',
        ]);

        $this->actingAs($owner)
            ->get(route('cv.personalization.inline', 'private-cv.pdf'))
            ->assertOk();
        $this->actingAs($attacker)
            ->get(route('cv.personalization.inline', 'private-cv.pdf'))
            ->assertNotFound();
    }

    public function test_contact_and_newsletter_actions_dispatch_real_mail(): void
    {
        Mail::fake();
        $this->post(route('contact.submit'), [
            'name' => 'Martin',
            'firstname' => 'Camille',
            'email' => 'camille@example.com',
            'message' => 'Bonjour, ceci est une demande réelle.',
        ])->assertRedirect(route('contact'));
        Mail::assertSent(PublicContactMessage::class);

        $marketing = User::factory()->create([
            'email' => 'marketing-audit@example.com',
            'email_verified_at' => now(),
            'status' => 'Actif',
        ]);
        $marketing->syncRoles(['Marketing']);

        $this->actingAs($marketing)->post(route('admin.newsletters.store'), [
            'sujet' => 'Campagne audit',
            'contenu' => 'Contenu de validation',
            'audience' => 'tous',
            'action' => 'envoyer',
        ])->assertRedirect(route('admin.newsletters'));

        Mail::assertQueued(NewsletterCampaignMail::class);
        $this->assertDatabaseHas('newsletter_campagnes', [
            'sujet' => 'Campagne audit',
            'statut' => 'envoyee',
        ]);
    }

    public function test_discovery_enterprise_is_limited_to_one_active_offer(): void
    {
        $enterprise = $this->createEnterprise([], [], false);
        $this->createOfferFor($enterprise);

        $this->actingAs($enterprise)
            ->get(route('entreprise.offres.create'))
            ->assertForbidden();
    }

    public function test_users_do_not_receive_an_implicit_candidate_role(): void
    {
        $user = User::factory()->create();

        $this->assertTrue($user->getRoleNames()->isEmpty());
    }

    public function test_used_verification_link_cannot_log_in_an_already_verified_candidate(): void
    {
        $candidate = User::factory()->create([
            'email_verified_at' => now(),
            'status' => 'Actif',
        ]);
        $candidate->syncRoles(['candidat']);
        EmailVerification::create([
            'email' => $candidate->email,
            'token' => 'verification-token-used',
            'created_at' => now(),
        ]);

        $this->get(route('verification.custom.verify', 'verification-token-used'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
        $this->assertDatabaseMissing('email_verifications', [
            'token' => 'verification-token-used',
        ]);
    }

    public function test_public_write_routes_are_rate_limited(): void
    {
        $router = app('router');

        $this->assertContains('throttle:5,1', $router->getRoutes()->getByName('contact.submit')->gatherMiddleware());
        $this->assertContains('throttle:10,1', $router->getRoutes()->getByName('check.email')->gatherMiddleware());
        $this->assertContains('throttle:3,1', $router->getRoutes()->getByName('verification.custom.resend')->gatherMiddleware());
    }
}
