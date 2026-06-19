<?php

namespace Tests\Feature;

use App\Jobs\AutoMatchingJob;
use App\Models\JobOfferSkill;
use App\Models\Postulation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\Feature\Concerns\CreatesTestAccounts;
use Tests\TestCase;

class EnterpriseActionsTest extends TestCase
{
    use CreatesTestAccounts;
    use RefreshDatabase;

    public function test_enterprise_can_create_edit_search_and_delete_own_offer(): void
    {
        Queue::fake();

        $enterprise = $this->createEnterprise();
        $payload = $this->validOfferPayload();

        $createResponse = $this->actingAs($enterprise)
            ->postJson(route('offres.store'), $payload)
            ->assertOk()
            ->assertJson(['success' => true]);

        $offerId = $createResponse->json('offre.id');
        $this->assertDatabaseHas('offres', [
            'id' => $offerId,
            'entreprise_id' => $enterprise->entreprise->id,
            'titre' => 'Analyste QA Laravel',
            'status' => 'active',
        ]);
        $this->assertDatabaseCount('offre_diplome', 1);
        $this->assertSame(3, JobOfferSkill::where('job_offer_id', $offerId)->count());
        Queue::assertPushed(AutoMatchingJob::class);

        $this->actingAs($enterprise)
            ->getJson(route('edit.offres', $offerId))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('offre.titre', 'Analyste QA Laravel');

        $this->actingAs($enterprise)
            ->putJson(route('offres.update', $offerId), $this->validOfferPayload([
                'jobTitle' => 'Analyste QA Senior',
                'salary_min' => 82000,
                'salary_max' => 105000,
            ]))
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Offre modifiée avec succès',
            ]);

        $this->assertDatabaseHas('offres', [
            'id' => $offerId,
            'titre' => 'Analyste QA Senior',
            'salaire_min' => 82000,
            'salaire_max' => 105000,
        ]);

        $this->actingAs($enterprise)
            ->getJson(route('offres.search', ['search' => 'Senior']))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('offres.data.0.titre', 'Analyste QA Senior');

        $this->actingAs($enterprise)
            ->deleteJson(route('offres.destroy', $offerId))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('offres', ['id' => $offerId]);
    }

    public function test_enterprise_can_publish_offer_from_classic_form_flow(): void
    {
        Queue::fake();

        $enterprise = $this->createEnterprise();

        $response = $this->actingAs($enterprise)
            ->post(route('offres.store'), $this->validOfferPayload());

        $response
            ->assertRedirect(route('offres.publies'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('offres', [
            'entreprise_id' => $enterprise->entreprise->id,
            'titre' => 'Analyste QA Laravel',
            'status' => 'active',
        ]);
        Queue::assertPushed(AutoMatchingJob::class);
    }

    public function test_enterprise_offer_validation_errors_are_returned(): void
    {
        $enterprise = $this->createEnterprise();

        $this->actingAs($enterprise)
            ->postJson(route('offres.store'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'jobTitle',
                'contractType',
                'location',
                'sector',
                'remote_work',
                'job_category',
                'salary_type',
                'endDate',
                'start_date',
                'languages_data',
                'required_experience',
                'responsibilities',
                'jobDescription',
            ]);
    }

    public function test_enterprise_offer_validation_rejects_invalid_enums_dates_and_salary_range(): void
    {
        $enterprise = $this->createEnterprise();

        $this->actingAs($enterprise)
            ->postJson(route('offres.store'), $this->validOfferPayload([
                'remote_work' => 'Depuis Mars',
                'job_category' => 'piraterie',
                'salary_type' => 'par minute',
                'salary_min' => 90000,
                'salary_max' => 50000,
                'endDate' => now()->subDay()->toDateString(),
                'start_date' => 'Hier',
                'required_experience' => '100 ans',
                'custom_benefits' => 'not-json',
            ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'remote_work',
                'job_category',
                'salary_type',
                'salary_max',
                'endDate',
                'start_date',
                'required_experience',
                'custom_benefits',
            ])
            ->assertJsonPath('errors.remote_work.0', 'Choisissez un mode de travail valide : présentiel, hybride ou télétravail.')
            ->assertJsonPath('errors.salary_max.0', 'Le salaire maximum doit être supérieur ou égal au salaire minimum.')
            ->assertJsonPath('errors.endDate.0', 'La date de fin doit être aujourd\'hui ou une date future.')
            ->assertJsonPath('errors.custom_benefits.0', 'Les avantages personnalisés doivent être transmis dans un format valide.');
    }

    public function test_enterprise_cannot_edit_or_delete_another_enterprise_offer(): void
    {
        $owner = $this->createEnterprise(['email' => 'owner@example.com']);
        $otherEnterprise = $this->createEnterprise(['email' => 'other@example.com']);
        $offer = $this->createOfferFor($owner);

        $this->actingAs($otherEnterprise)
            ->getJson(route('edit.offres', $offer->id))
            ->assertForbidden()
            ->assertJson(['success' => false]);

        $this->actingAs($otherEnterprise)
            ->putJson(route('offres.update', $offer->id), $this->validOfferPayload(['jobTitle' => 'Tentative']))
            ->assertForbidden()
            ->assertJson(['success' => false]);

        $this->actingAs($otherEnterprise)
            ->deleteJson(route('offres.destroy', $offer->id))
            ->assertNotFound()
            ->assertJson(['success' => false]);

        $this->assertDatabaseHas('offres', ['id' => $offer->id]);
    }

    public function test_enterprise_can_filter_candidates_and_update_application_status(): void
    {
        $enterprise = $this->createEnterprise();
        $candidate = $this->createCandidate(['name' => 'Candidate Filtre']);
        $offer = $this->createOfferFor($enterprise);
        $postulation = Postulation::create([
            'user_id' => $candidate->id,
            'offre_id' => $offer->id,
            'autopostulation' => false,
            'status' => 'en_attente',
        ]);

        $this->actingAs($enterprise)
            ->get(route('entreprise.offres.candidatures', [
                'offre' => $offer,
                'status' => 'en_attente',
                'search' => 'Candidate',
            ]))
            ->assertOk()
            ->assertSee('Candidate Filtre');

        $this->actingAs($enterprise)
            ->putJson(route('candidature.updateStatus', $postulation), ['status' => 'accepted'])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'new_status' => 'accepted',
            ]);

        $this->assertDatabaseHas('postulations', [
            'id' => $postulation->id,
            'status' => 'accepted',
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $candidate->id,
            'role' => 'candidat',
            'title' => 'Mise à jour de votre candidature',
            'is_read' => false,
        ]);
    }

    public function test_enterprise_cannot_update_status_for_another_enterprise_application(): void
    {
        $owner = $this->createEnterprise(['email' => 'owner-status@example.com']);
        $otherEnterprise = $this->createEnterprise(['email' => 'other-status@example.com']);
        $candidate = $this->createCandidate();
        $offer = $this->createOfferFor($owner);
        $postulation = Postulation::create([
            'user_id' => $candidate->id,
            'offre_id' => $offer->id,
            'autopostulation' => false,
            'status' => 'en_attente',
        ]);

        $this->actingAs($otherEnterprise)
            ->putJson(route('candidature.updateStatus', $postulation), ['status' => 'rejected'])
            ->assertNotFound()
            ->assertJson(['success' => false]);

        $this->assertDatabaseHas('postulations', [
            'id' => $postulation->id,
            'status' => 'en_attente',
        ]);
    }

    public function test_enterprise_application_status_validation_rejects_unknown_status(): void
    {
        $enterprise = $this->createEnterprise();
        $candidate = $this->createCandidate();
        $offer = $this->createOfferFor($enterprise);
        $postulation = Postulation::create([
            'user_id' => $candidate->id,
            'offre_id' => $offer->id,
            'autopostulation' => false,
            'status' => 'en_attente',
        ]);

        $this->actingAs($enterprise)
            ->putJson(route('candidature.updateStatus', $postulation), ['status' => 'archived'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['status'])
            ->assertJsonPath('errors.status.0', 'Choisissez un statut valide : en attente, accepté ou rejeté.');

        $this->assertDatabaseHas('postulations', [
            'id' => $postulation->id,
            'status' => 'en_attente',
        ]);
    }
}
