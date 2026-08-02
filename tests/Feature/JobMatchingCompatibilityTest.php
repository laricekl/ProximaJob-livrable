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

class JobMatchingCompatibilityTest extends TestCase
{
    use CreatesTestAccounts;
    use RefreshDatabase;

    public function test_missing_offer_criteria_are_not_presented_as_perfect_matches(): void
    {
        $candidate = $this->createCandidate();
        CandidateSector::create([
            'candidate_id' => $candidate->id,
            'experience_years' => 8,
        ]);
        $offer = $this->createOfferFor(attributes: ['required_experience' => null]);

        $compatibility = app(JobMatchingService::class)
            ->calculateCompatibility($offer->id, $candidate->id);

        $this->assertNull($compatibility['score']);
        $this->assertNull($compatibility['details']['technical_skills_match']);
        $this->assertNull($compatibility['details']['general_skills_match']);
        $this->assertNull($compatibility['details']['experience_match']);
    }

    public function test_only_available_criteria_are_weighted_and_more_experience_is_not_penalized(): void
    {
        $candidate = $this->createCandidate();
        CandidateSector::create([
            'candidate_id' => $candidate->id,
            'experience_years' => 8,
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

        $compatibility = app(JobMatchingService::class)
            ->calculateCompatibility($offer->id, $candidate->id);

        $this->assertSame(60.0, $compatibility['score']);
        $this->assertSame(100.0, $compatibility['details']['technical_skills_match']);
        $this->assertSame(0.0, $compatibility['details']['general_skills_match']);
        $this->assertSame(100.0, $compatibility['details']['experience_match']);
    }

    public function test_existing_application_scores_can_be_recalculated(): void
    {
        $candidate = $this->createCandidate();
        CandidateSector::create([
            'candidate_id' => $candidate->id,
            'experience_years' => 8,
        ]);
        $offer = $this->createOfferFor(attributes: ['required_experience' => null]);
        $application = Postulation::create([
            'user_id' => $candidate->id,
            'offre_id' => $offer->id,
            'match_score' => 88,
            'algorithm_version' => '3.0',
            'match_details' => [
                'technical_skills_match' => 100,
                'general_skills_match' => 100,
                'experience_match' => 40,
            ],
        ]);

        $updated = app(JobMatchingService::class)->recalculateStoredApplications();

        $application->refresh();
        $this->assertSame(1, $updated);
        $this->assertNull($application->match_score);
        $this->assertSame('4.0', $application->algorithm_version);
        $this->assertNull($application->match_details['technical_skills_match']);
        $this->assertNull($application->match_details['general_skills_match']);
        $this->assertNull($application->match_details['experience_match']);
    }
}
