<?php

namespace Tests\Feature;

use App\Jobs\AutoMatchingJob;
use App\Models\AutresDoc;
use App\Models\CandidateSector;
use App\Models\CvExperience;
use App\Models\CvProfile;
use App\Models\Notification;
use App\Models\Postulation;
use App\Models\UserAbonnement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

        DB::table('abonnements')->insert([
            'id' => 2,
            'nom' => 'Test candidat',
            'duree' => 'mensuel',
            'montant' => 0,
            'actif' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

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

    public function test_legacy_cv_routes_redirect_to_the_current_candidate_cv_flow(): void
    {
        $candidate = $this->createCandidate();

        $this->actingAs($candidate)
            ->get(route('cv.create'))
            ->assertRedirect(route('infos.cv'));

        $this->actingAs($candidate)
            ->get(route('cv.show', ['id' => 123]))
            ->assertRedirect(route('infos.cv'));

        $this->actingAs($candidate)
            ->get(route('cv.edit', ['id' => 123]))
            ->assertRedirect(route('infos.cv'));

        $this->actingAs($candidate)
            ->get(route('cv.form'))
            ->assertRedirect(route('cv.personalization.form'));

        $this->actingAs($candidate)
            ->post(route('cv.generate'))
            ->assertRedirect(route('cv.personalization.form'));
    }

    public function test_candidate_public_profile_uses_real_candidate_data(): void
    {
        $candidate = $this->createCandidate([
            'telephone' => '5141234567',
            'salary_expectation_min' => 78000,
        ]);
        $refs = $this->createHiringReferences();

        CvProfile::create([
            'user_id' => $candidate->id,
            'nom' => 'Martin',
            'prenom' => 'Camille',
            'email' => $candidate->email,
            'telephone' => '5141234567',
            'ville' => 'Montreal',
        ]);

        CvExperience::create([
            'cv_profile_id' => $candidate->cvProfile->id,
            'periode' => '2022 - Aujourd hui',
            'poste' => 'Developpeuse Laravel',
            'entreprise' => 'Studio Horizon',
            'description' => 'Pilotage de projets web et livraison continue.',
            'ordre' => 0,
        ]);

        CandidateSector::create([
            'candidate_id' => $candidate->id,
            'sector_id' => $refs['sector']->id,
            'diplome_id' => $refs['diplome']->id,
            'experience_years' => 5,
        ]);

        $candidate->skills()->attach($refs['technicalSkill']->id, [
            'level' => 'avance',
            'years_experience' => 5,
            'is_validated' => true,
        ]);

        $this->actingAs($candidate)
            ->get(route('user.profil-public'))
            ->assertOk()
            ->assertSee('Camille Martin')
            ->assertSee('Developpeuse Laravel')
            ->assertSee('Montreal')
            ->assertSee('78000')
            ->assertSee('Laravel');
    }

    public function test_candidate_application_detail_page_uses_real_application_data(): void
    {
        $candidate = $this->createCandidate();
        $enterprise = $this->createEnterprise([], ['company_name' => 'Studio Horizon']);
        $offer = $this->createOfferFor($enterprise, [
            'titre' => 'Analyste Produit',
            'poste' => 'Analyste Produit',
            'localisation' => 'Quebec',
        ]);

        Postulation::create([
            'user_id' => $candidate->id,
            'offre_id' => $offer->id,
            'status' => 'en_attente',
            'autopostulation' => false,
        ]);

        $this->actingAs($candidate)
            ->get(route('user.detail-candidature'))
            ->assertOk()
            ->assertSee('Analyste Produit')
            ->assertSee('Studio Horizon')
            ->assertSee('Quebec')
            ->assertSee('Postulation candidate manuelle');
    }

    public function test_candidate_can_generate_preview_and_download_a_personalized_cv_without_gemini(): void
    {
        Storage::fake('public');

        $candidate = $this->createCandidate([
            'email' => 'cv-fallback@example.com',
        ]);

        CvProfile::create([
            'user_id' => $candidate->id,
            'nom' => 'Martin',
            'prenom' => 'Camille',
            'email' => $candidate->email,
            'telephone' => '5141234567',
            'ville' => 'Montreal',
            'logiciels' => 'Laravel, PHP, SQL',
        ]);

        CvExperience::create([
            'cv_profile_id' => $candidate->cvProfile->id,
            'periode' => '2022 - Aujourd hui',
            'poste' => 'Developpeuse full stack',
            'entreprise' => 'Studio Horizon',
            'description' => 'Conception et livraison de produits web.',
            'ordre' => 0,
        ]);

        $response = $this->actingAs($candidate)->post(route('cv.personalization.generate'), [
            'offer_title' => 'Developpeuse Laravel',
            'offer_details' => 'Nous recherchons une developpeuse Laravel capable de livrer des interfaces fiables et des integrations propres.',
            'company_name' => 'Entreprise QA',
            'key_requirements' => 'Laravel, tests automatises, architecture propre',
            'template_style' => 'modern',
            'accent_color' => 'green',
            'font_style' => 'classic',
            'density' => 'compact',
            'section_order' => 'experience_first',
            'page_limit' => 1,
            'summary_tone' => 'direct',
            'sections_present' => 1,
            'sections' => ['software', 'languages'],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'CV personnalisé généré avec succès !');

        $files = Storage::disk('public')->files('personalized-cvs');

        $this->assertCount(1, $files);
        $this->assertStringEndsWith('.pdf', $files[0]);

        $this->assertDatabaseHas('cv_generes', [
            'cv_profile_id' => $candidate->cvProfile->id,
            'nom_fichier' => 'CV adapte - Developpeuse Laravel - Entreprise QA',
            'chemin_fichier' => $files[0],
        ]);

        $filename = basename($files[0]);

        $this->actingAs($candidate)
            ->get(route('cv.personalization.preview', ['filename' => $filename]))
            ->assertOk()
            ->assertSee($filename)
            ->assertDontSee('Jean Dupont')
            ->assertDontSee('TechCorp');

        $this->actingAs($candidate)
            ->get(route('cv.personalization.inline', ['filename' => $filename]))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($candidate)
            ->get(route('cv.personalization.download', ['filename' => $filename]))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_candidate_cv_personalization_form_can_prefill_from_offer(): void
    {
        $candidate = $this->createCandidate();
        $enterprise = $this->createEnterprise([], [
            'company_name' => 'Atelier Produit Nord',
        ]);
        $offer = $this->createOfferFor($enterprise, [
            'titre' => 'Analyste QA Produit',
            'poste' => 'Analyste QA Produit',
            'description' => 'Valider les parcours utilisateurs et documenter les anomalies prioritaires.',
            'criteres' => 'Tests fonctionnels, communication, Jira',
        ]);

        CvProfile::create([
            'user_id' => $candidate->id,
            'nom' => 'Martin',
            'prenom' => 'Camille',
            'email' => $candidate->email,
            'telephone' => '5141234567',
        ]);

        $this->actingAs($candidate)
            ->get(route('cv.personalization.form', ['offre_id' => $offer->id]))
            ->assertOk()
            ->assertSee('value="Analyste QA Produit"', false)
            ->assertSee('value="Atelier Produit Nord"', false)
            ->assertSee('Valider les parcours utilisateurs et documenter les anomalies prioritaires.')
            ->assertSee('Tests fonctionnels, communication, Jira');
    }

    public function test_candidate_can_preview_and_download_principal_cv_pdf(): void
    {
        $candidate = $this->createCandidate([
            'email' => 'principal-pdf@example.com',
        ]);

        $profile = CvProfile::create([
            'user_id' => $candidate->id,
            'nom' => 'Martin',
            'prenom' => 'Camille',
            'email' => $candidate->email,
            'telephone' => '5141234567',
            'ville' => 'Montreal',
            'logiciels' => 'Laravel, PHP, SQL',
            'langues_competences' => 'Gestion de projets web, communication, analyse.',
        ]);

        CvExperience::create([
            'cv_profile_id' => $profile->id,
            'periode' => '2022 - Aujourd hui',
            'poste' => 'Developpeuse full stack',
            'entreprise' => 'Studio Horizon',
            'description' => 'Conception et livraison de produits web.',
            'ordre' => 0,
        ]);

        $this->actingAs($candidate)
            ->get(route('cv.principal.inline'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($candidate)
            ->get(route('cv.principal.download'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_candidate_cv_personalization_rejects_invalid_display_options(): void
    {
        $candidate = $this->createCandidate();

        CvProfile::create([
            'user_id' => $candidate->id,
            'nom' => 'Martin',
            'prenom' => 'Camille',
            'email' => $candidate->email,
            'telephone' => '5141234567',
        ]);

        $this->actingAs($candidate)->from(route('cv.personalization.form'))->post(route('cv.personalization.generate'), [
            'offer_title' => 'Developpeuse Laravel',
            'offer_details' => 'Nous recherchons une developpeuse Laravel capable de livrer des interfaces fiables.',
            'template_style' => 'creative',
            'accent_color' => 'neon',
            'page_limit' => 9,
            'sections_present' => 1,
            'sections' => ['salary'],
        ])
            ->assertRedirect(route('cv.personalization.form'))
            ->assertSessionHasErrors(['template_style', 'accent_color', 'page_limit', 'sections.0']);
    }

    public function test_candidate_can_upload_source_cv_from_cv_builder(): void
    {
        Storage::fake('public');

        $candidate = $this->createCandidate();

        $response = $this->actingAs($candidate)->postJson(route('cv.upload-source'), [
            'cv' => UploadedFile::fake()->create('source-cv.pdf', 64, 'application/pdf'),
        ]);

        $response
            ->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonPath('filename', fn ($filename) => str_starts_with($filename, 'source_'.$candidate->id.'_'))
            ->assertJsonPath('path', fn ($path) => str_starts_with($path, 'cvs-source/source_'.$candidate->id.'_'));

        $candidate->refresh();

        $this->assertNotNull($candidate->cv);
        $this->assertStringStartsWith('cvs-source/source_'.$candidate->id.'_', $candidate->cv);
        Storage::disk('public')->assertExists($candidate->cv);
    }

    public function test_candidate_without_cv_profile_is_redirected_to_builder_before_cv_personalization(): void
    {
        $candidate = $this->createCandidate();

        $this->actingAs($candidate)
            ->get(route('cv.personalization.form'))
            ->assertRedirect(route('infos.cv'))
            ->assertSessionHas('error', 'Veuillez compléter votre profil CV avant de personnaliser.');
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
