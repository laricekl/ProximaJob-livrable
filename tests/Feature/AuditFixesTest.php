<?php

namespace Tests\Feature;

use App\Mail\EntrepriseValidated;
use App\Models\Abonnement;
use App\Models\Notification;
use App\Models\Postulation;
use App\Models\User;
use App\Models\UserAbonnement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\Feature\Concerns\CreatesTestAccounts;
use Tests\TestCase;

/**
 * Corrections de l'audit des trous (1er nov. 2026) — go de Larice
 * « corrige tout : bloquants + gênants » : B1, B2, G1, G2, G3, G4, G5, G6.
 * Chaque test prouve le comportement corrigé.
 */
class AuditFixesTest extends TestCase
{
    use CreatesTestAccounts;
    use RefreshDatabase;

    private function createAdmin(): User
    {
        $admin = User::factory()->create([
            'prenom' => 'Fixe',
            'name' => 'Admin',
            'email_verified_at' => now(),
            'status' => 'Actif',
        ]);
        $admin->syncRoles(['admin']);

        return $admin->fresh();
    }

    // ------------------------------------------------------------- B1
    public function test_entreprise_can_read_its_own_notifications(): void
    {
        $entrepriseUser = $this->createEnterprise(['email' => 'b1-entreprise@example.com']);
        Notification::create([
            'user_id' => $entrepriseUser->id,
            'role' => 'entreprise',
            'title' => 'Nouvelle candidature reçue',
            'message' => 'Un candidat a postulé à votre offre.',
            'link' => '/entreprise',
            'is_read' => false,
        ]);

        $this->actingAs($entrepriseUser)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Nouvelle candidature reçue');
    }

    public function test_notifications_page_shows_only_own_notifications(): void
    {
        $candidate = $this->createCandidate(['email' => 'b1-candidat@example.com']);
        $entrepriseUser = $this->createEnterprise(['email' => 'b1-ent2@example.com']);

        Notification::create([
            'user_id' => $candidate->id,
            'role' => 'candidat',
            'title' => 'Notification privée du candidat',
            'message' => 'Message candidat.',
            'link' => '/user',
            'is_read' => false,
        ]);
        Notification::create([
            'user_id' => $entrepriseUser->id,
            'role' => 'entreprise',
            'title' => 'Notification privée de l’entreprise',
            'message' => 'Message entreprise.',
            'link' => '/entreprise',
            'is_read' => false,
        ]);

        $this->actingAs($candidate)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Notification privée du candidat')
            ->assertDontSee('Notification privée de l’entreprise');

        $this->actingAs($entrepriseUser)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Notification privée de l’entreprise')
            ->assertDontSee('Notification privée du candidat');
    }

    // ------------------------------------------------------------- B2
    public function test_admin_approval_sends_validation_email_once(): void
    {
        Mail::fake();
        $admin = $this->createAdmin();
        $entrepriseUser = $this->createEnterprise(
            ['email' => 'b2-entreprise@example.com'],
            ['status' => 'pending'],
        );
        $entreprise = $entrepriseUser->entreprise;

        $this->actingAs($admin)
            ->patch(route('admin.entreprises.status', $entreprise), ['status' => 'approved'])
            ->assertRedirect();

        $this->assertSame('approved', $entreprise->fresh()->status);
        Mail::assertSent(EntrepriseValidated::class, 1);

        // Une seconde approbation (déjà « approved ») ne renvoie pas le courriel.
        $this->actingAs($admin)
            ->patch(route('admin.entreprises.status', $entreprise), ['status' => 'approved'])
            ->assertRedirect();

        Mail::assertSent(EntrepriseValidated::class, 1);
    }

    public function test_admin_rejection_does_not_send_validation_email(): void
    {
        Mail::fake();
        $admin = $this->createAdmin();
        $entrepriseUser = $this->createEnterprise(
            ['email' => 'b2-rejet@example.com'],
            ['status' => 'pending'],
        );

        $this->actingAs($admin)
            ->patch(route('admin.entreprises.status', $entrepriseUser->entreprise), [
                'status' => 'rejected',
                'reason' => 'Dossier incomplet.',
            ])
            ->assertRedirect();

        Mail::assertNotSent(EntrepriseValidated::class);
    }

    // ------------------------------------------------------------- G1
    public function test_enterprise_cv_preview_without_generated_cv_is_404_not_500(): void
    {
        $entrepriseUser = $this->createEnterprise(['email' => 'g1-entreprise@example.com']);
        $offer = $this->createOfferFor($entrepriseUser);
        $candidate = $this->createCandidate(['email' => 'g1-candidat@example.com'], false);
        $postulation = Postulation::create([
            'user_id' => $candidate->id,
            'offre_id' => $offer->id,
            'autopostulation' => false,
            'status' => 'en_attente',
            // Aucun CV joint ni généré : l'ancien code répondait 500.
            'cv' => null,
        ]);

        $this->actingAs($entrepriseUser)
            ->get(route('preview.cv-ia.ep', $postulation->id))
            ->assertStatus(404);
    }

    public function test_enterprise_cv_preview_of_another_enterprise_is_403(): void
    {
        $owner = $this->createEnterprise(['email' => 'g1-owner@example.com']);
        $stranger = $this->createEnterprise(['email' => 'g1-stranger@example.com']);
        $offer = $this->createOfferFor($owner);
        $candidate = $this->createCandidate(['email' => 'g1-candidat2@example.com'], false);
        $postulation = Postulation::create([
            'user_id' => $candidate->id,
            'offre_id' => $offer->id,
            'autopostulation' => false,
            'status' => 'en_attente',
            'cv' => null,
        ]);

        $this->actingAs($stranger)
            ->get(route('preview.cv-ia.ep', $postulation->id))
            ->assertStatus(403);
    }

    // ------------------------------------------------------------- G3
    public function test_status_change_redirects_enterprise_to_offer_applications(): void
    {
        Mail::fake();
        $entrepriseUser = $this->createEnterprise(['email' => 'g3-entreprise@example.com']);
        $offer = $this->createOfferFor($entrepriseUser);
        $candidate = $this->createCandidate(['email' => 'g3-candidat@example.com'], false);
        $postulation = Postulation::create([
            'user_id' => $candidate->id,
            'offre_id' => $offer->id,
            'autopostulation' => false,
            'status' => 'en_attente',
        ]);

        $this->actingAs($entrepriseUser)
            ->put(route('candidature.updateStatus', $postulation->id), ['status' => 'shortlisted'])
            ->assertRedirect(route('entreprise.offres.candidatures', $offer->id));

        $this->assertSame('shortlisted', $postulation->fresh()->status);
    }

    // ------------------------------------------------------------- G2
    public function test_principal_cv_without_profile_redirects_to_cv_creation(): void
    {
        $candidate = $this->createCandidate(['email' => 'g2-candidat@example.com'], false);

        $this->actingAs($candidate)
            ->get(route('cv.principal.inline'))
            ->assertRedirect(route('infos.cv'))
            ->assertSessionHas('error');

        $this->actingAs($candidate)
            ->get(route('cv.principal.download'))
            ->assertRedirect(route('infos.cv'))
            ->assertSessionHas('error');
    }

    public function test_enterprise_document_preview_without_file_redirects_back_with_message(): void
    {
        $entrepriseUser = $this->createEnterprise(['email' => 'g2-entreprise@example.com']);
        $offer = $this->createOfferFor($entrepriseUser);
        $candidate = $this->createCandidate(['email' => 'g2-candidat2@example.com'], false);
        $postulation = Postulation::create([
            'user_id' => $candidate->id,
            'offre_id' => $offer->id,
            'autopostulation' => false,
            'status' => 'en_attente',
            'cv' => null,
        ]);

        $this->actingAs($entrepriseUser)
            ->from(route('entreprise.offres.candidatures', $offer->id))
            ->get(route('entreprise.candidature.preview', [
                'postulationId' => $postulation->id,
                'type' => 'cv',
            ]))
            ->assertRedirect(route('entreprise.offres.candidatures', $offer->id))
            ->assertSessionHas('error');
    }

    // ------------------------------------------------------------- G4
    private function makeSubscription(User $user, string $status, ?string $paymentIntent = null): UserAbonnement
    {
        $plan = Abonnement::create([
            'nom' => 'Plan Reçu '.$status,
            'description' => 'Plan pour le test de reçu.',
            'montant' => 49,
            'duree' => 30,
            'actif' => true,
        ]);

        return UserAbonnement::create([
            'user_id' => $user->id,
            'abonnement_id' => $plan->id,
            'date_debut' => now()->subDay(),
            'date_fin' => now()->addDays(29),
            'status' => $status,
            'stripe_payment_intent_id' => $paymentIntent,
        ]);
    }

    public function test_owner_can_view_printable_receipt(): void
    {
        $candidate = $this->createCandidate(['email' => 'g4-candidat@example.com'], false);
        $subscription = $this->makeSubscription($candidate, 'Actif', 'pi_test_123');

        $this->actingAs($candidate)
            ->get(route('paiement.recu', $subscription))
            ->assertOk()
            ->assertSee('Plan Reçu Actif')
            ->assertSee('pi_test_123')
            ->assertSee('49,00');
    }

    public function test_receipt_is_forbidden_to_non_owner(): void
    {
        $owner = $this->createCandidate(['email' => 'g4-owner@example.com'], false);
        $other = $this->createCandidate(['email' => 'g4-other@example.com'], false);
        $subscription = $this->makeSubscription($owner, 'Actif', 'pi_test_456');

        $this->actingAs($other)
            ->get(route('paiement.recu', $subscription))
            ->assertForbidden();
    }

    public function test_receipt_unavailable_while_payment_pending(): void
    {
        $entrepriseUser = $this->createEnterprise(['email' => 'g4-entreprise@example.com'], [], false);
        $subscription = $this->makeSubscription($entrepriseUser, 'En attente');

        $this->actingAs($entrepriseUser)
            ->get(route('paiement.recu', $subscription))
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    // ------------------------------------------------------------- G5
    public function test_enterprise_can_cancel_own_subscription(): void
    {
        $entrepriseUser = $this->createEnterprise(['email' => 'g5-entreprise@example.com']);
        $subscription = UserAbonnement::where('user_id', $entrepriseUser->id)
            ->where('status', 'Actif')
            ->firstOrFail();

        $this->actingAs($entrepriseUser)
            ->post(route('entreprise.abonnements.cancel'))
            ->assertRedirect(route('entreprise.abonnements'));

        $this->assertSame('Annulé', $subscription->fresh()->status);
    }

    public function test_enterprise_subscriptions_page_offers_cancellation(): void
    {
        $entrepriseUser = $this->createEnterprise(['email' => 'g5-page@example.com']);

        $this->actingAs($entrepriseUser)
            ->get(route('entreprise.abonnements'))
            ->assertOk()
            ->assertSee(route('entreprise.abonnements.cancel'), false);
    }

    // ------------------------------------------------------------- G6
    public function test_candidate_can_export_own_data_as_json(): void
    {
        $candidate = $this->createCandidate(['email' => 'g6-candidat@example.com']);
        $entrepriseUser = $this->createEnterprise(['email' => 'g6-entreprise@example.com']);
        $offer = $this->createOfferFor($entrepriseUser, ['titre' => 'Offre Export G6']);
        Postulation::create([
            'user_id' => $candidate->id,
            'offre_id' => $offer->id,
            'autopostulation' => false,
            'status' => 'en_attente',
        ]);

        $response = $this->actingAs($candidate)->get(route('profile.export-donnees'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/json');
        $this->assertStringContainsString('attachment', (string) $response->headers->get('content-disposition'));

        $json = $response->getContent();
        $this->assertStringContainsString('g6-candidat@example.com', $json);
        $this->assertStringContainsString('Offre Export G6', $json);
        $this->assertStringContainsString('"abonnements"', $json);
        // Aucun secret ne doit fuiter dans l'export.
        $this->assertStringNotContainsString('password', $json);
        $this->assertStringNotContainsString('remember_token', $json);
    }

    public function test_enterprise_export_excludes_candidates_personal_data(): void
    {
        $entrepriseUser = $this->createEnterprise(['email' => 'g6-ent@example.com']);
        $offer = $this->createOfferFor($entrepriseUser, ['titre' => 'Offre Entreprise G6']);
        $candidate = $this->createCandidate(['email' => 'g6-candidat-prive@example.com'], false);
        Postulation::create([
            'user_id' => $candidate->id,
            'offre_id' => $offer->id,
            'autopostulation' => false,
            'status' => 'en_attente',
        ]);

        $response = $this->actingAs($entrepriseUser)->get(route('profile.export-donnees'));

        $response->assertOk();
        $json = $response->getContent();
        $this->assertStringContainsString('Entreprise QA', $json);
        $this->assertStringContainsString('Offre Entreprise G6', $json);
        // Les candidatures reçues contiennent les données d'autrui : exclues.
        $this->assertStringNotContainsString('g6-candidat-prive@example.com', $json);
    }

    public function test_data_export_requires_authentication(): void
    {
        $this->get(route('profile.export-donnees'))
            ->assertRedirect(route('login'));
    }
}
