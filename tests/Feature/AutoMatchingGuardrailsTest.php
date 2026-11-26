<?php

namespace Tests\Feature;

use App\Models\CandidateSector;
use App\Models\CandidateSkill;
use App\Models\JobOfferSkill;
use App\Models\Postulation;
use App\Services\JobMatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesTestAccounts;
use Tests\TestCase;

/**
 * Garde-fous du matching automatique (Phase 0, 31 oct. 2026) :
 * plafond quotidien, mode simulation réel, ciblages candidat et offre.
 */
class AutoMatchingGuardrailsTest extends TestCase
{
    use CreatesTestAccounts;
    use RefreshDatabase;

    /**
     * Construit un couple candidat/offre réellement éligible, score 60
     * (compétence technique présente, méthodologique absente, expérience OK).
     */
    private function createEligiblePair(): array
    {
        $candidate = $this->createCandidate();
        CandidateSector::create([
            'candidate_id' => $candidate->id,
            'experience_years' => 3,
        ]);

        $references = $this->createHiringReferences();
        $offer = $this->createOfferFor(attributes: ['required_experience' => '2-3 ans']);

        JobOfferSkill::create([
            'job_offer_id' => $offer->id,
            'skill_id' => $references['technicalSkill']->id,
            'skill_type' => 'technical',
            'is_required' => true,
        ]);
        JobOfferSkill::create([
            'job_offer_id' => $offer->id,
            'skill_id' => $references['methodologicalSkill']->id,
            'skill_type' => 'methodological',
            'is_required' => true,
        ]);
        CandidateSkill::create([
            'candidate_id' => $candidate->id,
            'skill_id' => $references['technicalSkill']->id,
            'level' => 'avance',
        ]);

        return [$candidate, $offer];
    }

    private function seedAutoApplicationsToday($candidate, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $otherOffer = $this->createOfferFor();
            Postulation::create([
                'user_id' => $candidate->id,
                'offre_id' => $otherOffer->id,
                'status' => 'en_attente',
                'autopostulation' => true,
                'application_date' => now(),
                'match_score' => 70,
                'algorithm_version' => '4.0',
            ]);
        }
    }

    public function test_daily_cap_blocks_new_auto_applications(): void
    {
        [$candidate] = $this->createEligiblePair();
        $this->seedAutoApplicationsToday($candidate, 3);

        $results = app(JobMatchingService::class)->processAutoMatching();

        $this->assertSame(0, $results['applications_created']);
        $this->assertGreaterThanOrEqual(1, $results['applications_capped']);
        $this->assertSame(3, Postulation::where('user_id', $candidate->id)
            ->where('autopostulation', true)->count());
    }

    public function test_run_creates_only_up_to_the_daily_cap(): void
    {
        [$candidate, $offer] = $this->createEligiblePair();
        $this->seedAutoApplicationsToday($candidate, 2);

        $results = app(JobMatchingService::class)->processAutoMatching();

        $this->assertSame(1, $results['applications_created']);
        $created = Postulation::where('user_id', $candidate->id)
            ->where('offre_id', $offer->id)->first();
        $this->assertNotNull($created);
        $this->assertTrue((bool) $created->autopostulation);
        $this->assertSame(3, Postulation::where('user_id', $candidate->id)
            ->where('autopostulation', true)->count());
    }

    public function test_dry_run_simulates_without_creating_anything(): void
    {
        [$candidate] = $this->createEligiblePair();

        $results = app(JobMatchingService::class)->processAutoMatching(null, true);

        $this->assertSame(0, $results['applications_created']);
        $this->assertGreaterThanOrEqual(1, $results['applications_simulated']);
        $this->assertSame(0, Postulation::where('user_id', $candidate->id)->count());
    }

    public function test_candidate_targeted_run_only_matches_that_candidate(): void
    {
        [$candidateA, $offer] = $this->createEligiblePair();

        // Second candidat, éligible à la même offre.
        $candidateB = $this->createCandidate();
        CandidateSector::create([
            'candidate_id' => $candidateB->id,
            'experience_years' => 3,
        ]);
        $technicalSkillId = JobOfferSkill::where('job_offer_id', $offer->id)
            ->where('skill_type', 'technical')->value('skill_id');
        CandidateSkill::create([
            'candidate_id' => $candidateB->id,
            'skill_id' => $technicalSkillId,
            'level' => 'avance',
        ]);

        $results = app(JobMatchingService::class)->processAutoMatching($candidateA->id);

        $this->assertSame(1, $results['applications_created']);
        $this->assertSame(1, Postulation::where('user_id', $candidateA->id)->count());
        $this->assertSame(0, Postulation::where('user_id', $candidateB->id)->count());
    }

    public function test_offer_targeted_matching_creates_application(): void
    {
        [$candidate, $offer] = $this->createEligiblePair();

        $results = app(JobMatchingService::class)->processMatchingForOffer($offer->id);

        $this->assertSame(1, $results['applications_created']);
        $this->assertSame(1, Postulation::where('user_id', $candidate->id)
            ->where('offre_id', $offer->id)->count());
    }
}
