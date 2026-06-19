<?php

namespace Tests\Feature;

use App\Jobs\AutoMatchingJob;
use App\Models\AutresDoc;
use App\Models\Notification;
use App\Models\Postulation;
use App\Models\UserAbonnement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Tests\Feature\Concerns\CreatesTestAccounts;
use Tests\TestCase;

class CandidateActionsTest extends TestCase
{
    use CreatesTestAccounts;
    use RefreshDatabase;

    public function test_candidate_can_submit_manual_application_with_required_files(): void
    {
        $candidate = $this->createCandidate();
        $enterprise = $this->createEnterprise();
        $offer = $this->createOfferFor($enterprise);

        $response = $this->actingAs($candidate)->postJson(route('candidatures.store'), [
            'offre_id' => $offer->id,
            'cv' => UploadedFile::fake()->create('cv.pdf', 64, 'application/pdf'),
            'motivation' => UploadedFile::fake()->create('motivation.pdf', 64, 'application/pdf'),
            'additional_docs' => [
                [
                    'intitule' => 'Portfolio',
                    'description' => 'Exemples de realisations',
                    'file' => UploadedFile::fake()->create('portfolio.pdf', 64, 'application/pdf'),
                ],
            ],
        ]);

        $response
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('postulations', [
            'user_id' => $candidate->id,
            'offre_id' => $offer->id,
            'autopostulation' => false,
            'status' => 'en_attente',
        ]);

        $postulation = Postulation::where('user_id', $candidate->id)->where('offre_id', $offer->id)->firstOrFail();
        $this->assertNotNull($postulation->cv);
        $this->assertNotNull($postulation->lettre_motivation);

        $this->assertDatabaseHas('autres_docs', [
            'id_postulation' => $postulation->id,
            'intitule' => 'Portfolio',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $enterprise->id,
            'role' => 'entreprise',
            'title' => 'Nouvelle candidature reçue',
            'is_read' => false,
        ]);
    }

    public function test_candidate_application_requires_cv_and_motivation_files(): void
    {
        $candidate = $this->createCandidate();
        $offer = $this->createOfferFor();

        $this->actingAs($candidate)
            ->postJson(route('candidatures.store'), ['offre_id' => $offer->id])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['cv', 'motivation'])
            ->assertJsonPath('errors.cv.0', 'Veuillez joindre votre CV.')
            ->assertJsonPath('errors.motivation.0', 'Veuillez joindre votre lettre de motivation.');
    }

    public function test_candidate_cannot_apply_to_inactive_offer(): void
    {
        $candidate = $this->createCandidate();
        $offer = $this->createOfferFor(null, ['status' => 'closed']);

        $this->actingAs($candidate)
            ->postJson(route('candidatures.store'), [
                'offre_id' => $offer->id,
                'cv' => UploadedFile::fake()->create('cv.pdf', 64, 'application/pdf'),
                'motivation' => UploadedFile::fake()->create('motivation.pdf', 64, 'application/pdf'),
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['offre_id'])
            ->assertJsonPath('errors.offre_id.0', 'Cette offre n\'est plus disponible. Merci de choisir une autre offre.');

        $this->assertDatabaseMissing('postulations', [
            'user_id' => $candidate->id,
            'offre_id' => $offer->id,
        ]);
    }

    public function test_candidate_can_update_profile_sector_skills_and_subscription(): void
    {
        Queue::fake();

        $candidate = $this->createCandidate();
        $refs = $this->createHiringReferences();

        $this->actingAs($candidate)
            ->postJson(route('user.profile.update'), [
                'name' => 'Martin',
                'prenom' => 'Camille',
                'email' => $candidate->email,
                'telephone' => '5140000000',
                'adresse' => '123 rue Test',
                'sector_id' => $refs['sector']->id,
                'diplome_id' => $refs['diplome']->id,
                'experience_years' => 4,
                'salary_expectation_min' => 85000,
                'skills' => [$refs['technicalSkill']->id],
                'skill_levels' => [$refs['technicalSkill']->id => 'avancé'],
                'skill_years' => [$refs['technicalSkill']->id => 4],
            ])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'auto_matching_triggered' => true,
            ]);

        $candidate->refresh();

        $this->assertSame('5140000000', $candidate->telephone);
        $this->assertSame(85000, (int) $candidate->salary_expectation_min);
        $this->assertDatabaseHas('candidate_sectors', [
            'candidate_id' => $candidate->id,
            'sector_id' => $refs['sector']->id,
            'diplome_id' => $refs['diplome']->id,
            'experience_years' => 4,
        ]);
        $this->assertDatabaseHas('candidate_skills', [
            'candidate_id' => $candidate->id,
            'skill_id' => $refs['technicalSkill']->id,
            'level' => 'avance',
        ]);
        Queue::assertPushed(AutoMatchingJob::class);

        $this->actingAs($candidate)
            ->postJson(route('abonnements.souscrire'))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('user_abonnements', [
            'user_id' => $candidate->id,
            'abonnement_id' => 2,
            'status' => 'Actif',
        ]);
        $this->assertTrue(UserAbonnement::where('user_id', $candidate->id)->exists());
    }

    public function test_candidate_can_manage_notifications(): void
    {
        $candidate = $this->createCandidate();
        $notification = Notification::create([
            'user_id' => $candidate->id,
            'role' => 'candidat',
            'title' => 'Test notification',
            'message' => 'Message notification',
            'link' => '/user',
            'is_read' => false,
        ]);

        $this->actingAs($candidate)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Test notification');

        $this->actingAs($candidate)
            ->get('/notifications/unread-count')
            ->assertOk()
            ->assertJson(['success' => true, 'count' => 1]);

        $this->actingAs($candidate)
            ->post("/notifications/{$notification->id}/mark-as-read")
            ->assertOk()
            ->assertJson(['success' => true, 'unread_count' => 0]);

        $this->assertTrue($notification->fresh()->is_read);
    }

    public function test_candidate_cannot_manage_enterprise_offer_mutations(): void
    {
        $candidate = $this->createCandidate();
        $offer = $this->createOfferFor();

        $this->actingAs($candidate)
            ->deleteJson(route('offres.destroy', $offer->id))
            ->assertRedirect('/user');

        $this->assertDatabaseHas('offres', ['id' => $offer->id]);
    }

    public function test_candidate_profile_rejects_invalid_sector_skill_and_salary_values(): void
    {
        $candidate = $this->createCandidate();

        $this->actingAs($candidate)
            ->postJson(route('user.profile.update'), [
                'name' => 'Martin',
                'prenom' => 'Camille',
                'email' => $candidate->email,
                'sector_id' => 999,
                'experience_years' => 99,
                'salary_expectation_min' => -1,
                'skills' => [999],
                'skill_levels' => [999 => 'maitre'],
                'skill_years' => [999 => 99],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'sector_id',
                'experience_years',
                'salary_expectation_min',
                'skills.0',
                'skill_levels.999',
                'skill_years.999',
            ]);
    }
}
