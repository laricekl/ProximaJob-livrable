<?php

namespace Tests\Feature;

use App\Models\Abonnement;
use App\Models\UserAbonnement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesTestAccounts;
use Tests\TestCase;

/**
 * Décision du 1er novembre 2026 (Larice) : les candidats sont gratuits.
 * Le plan Pro candidat (49 $) est retiré de la vente : invisible pour les
 * candidats, impossible à souscrire pour eux, et absent de la vitrine
 * publique dès qu'il est désactivé. Le plan Entreprise reste vendu,
 * aux entreprises seulement.
 */
class CandidateFreePlanTest extends TestCase
{
    use CreatesTestAccounts;
    use RefreshDatabase;

    private function makePlan(string $nom, float $montant, bool $actif = true): Abonnement
    {
        return Abonnement::create([
            'nom' => $nom,
            'duree' => 30,
            'montant' => $montant,
            'description' => 'Plan de test',
            'couleur' => '#000000',
            'populaire' => false,
            'actif' => $actif,
        ]);
    }

    public function test_candidate_plan_page_offers_only_free_plans(): void
    {
        $this->makePlan('DecouverteTest', 0);
        $this->makePlan('ProTest', 49);

        $candidate = $this->createCandidate([], false);

        $this->actingAs($candidate)
            ->get(route('plan.abonnement'))
            ->assertOk()
            ->assertSee('DecouverteTest')
            ->assertDontSee('ProTest');
    }

    public function test_candidate_cannot_subscribe_to_a_paid_plan(): void
    {
        $paid = $this->makePlan('ProTest', 49);
        $candidate = $this->createCandidate([], false);

        $this->actingAs($candidate)
            ->postJson(route('abonnements.souscrire'), ['abonnement_id' => $paid->id])
            ->assertStatus(422)
            ->assertJson(['success' => false]);

        $this->assertFalse(
            UserAbonnement::where('user_id', $candidate->id)
                ->where('abonnement_id', $paid->id)
                ->exists()
        );
    }

    public function test_candidate_can_still_subscribe_to_the_free_plan(): void
    {
        $free = $this->makePlan('DecouverteTest', 0);
        $candidate = $this->createCandidate([], false);

        $this->actingAs($candidate)
            ->postJson(route('abonnements.souscrire'), ['abonnement_id' => $free->id])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('user_abonnements', [
            'user_id' => $candidate->id,
            'abonnement_id' => $free->id,
            'status' => 'Actif',
        ]);
    }

    public function test_public_showcase_hides_inactive_plans(): void
    {
        $this->makePlan('PlanVisibleTest', 0, true);
        $this->makePlan('PlanRetireTest', 49, false);

        $this->get(route('abonnement'))
            ->assertOk()
            ->assertSee('PlanVisibleTest')
            ->assertDontSee('PlanRetireTest');
    }
}
