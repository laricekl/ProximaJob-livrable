<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesTestAccounts;
use Tests\TestCase;

class PublicActionsTest extends TestCase
{
    use CreatesTestAccounts;
    use RefreshDatabase;

    public function test_public_static_and_listing_pages_are_accessible(): void
    {
        $this->createOfferFor();

        foreach (['/', '/offres', '/contact', '/ressources', '/abonnement', '/terms', '/policy'] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertDontSee('Page Expired');
        }
    }

    public function test_public_offer_filters_preserve_real_results(): void
    {
        $offer = $this->createOfferFor(null, [
            'poste' => 'Architecte Donnees QA',
            'titre' => 'Architecte Donnees QA',
            'localisation' => 'Quebec',
        ]);

        $this->get('/offres?search=Architecte&localisation=Quebec&sort=latest')
            ->assertOk()
            ->assertSee('Architecte Donnees QA')
            ->assertSee('Quebec');

        $this->get(route('job_infos', $offer))
            ->assertOk()
            ->assertSee('Architecte Donnees QA');
    }

    public function test_public_offers_use_the_custom_single_row_pagination(): void
    {
        $enterprise = $this->createEnterprise(['email' => 'enterprise-pagination@example.com']);

        foreach (range(1, 7) as $index) {
            $this->createOfferFor($enterprise, [
                'titre' => "Offre pagination {$index}",
                'poste' => "Offre pagination {$index}",
                'slug' => "offre-pagination-{$index}",
            ]);
        }

        $response = $this->get('/offres?sort=latest');

        $response->assertOk()
            ->assertSee('Affichage de')
            ->assertSee('Precedent')
            ->assertSee('Suivant')
            ->assertDontSee('Montrant')
            ->assertDontSee('résultats', false);

        $content = $response->getContent();

        $this->assertSame(1, substr_count($content, 'aria-label="Pagination des offres"'));
        $this->assertGreaterThanOrEqual(3, substr_count($content, 'data-pagination-control'));
    }

    public function test_language_can_be_changed_from_public_area(): void
    {
        $this->postJson(route('set.language'), ['locale' => 'en'])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'locale' => 'en',
            ]);

        $this->assertSame('en', session('locale'));
    }

    public function test_language_rejects_unsupported_locale(): void
    {
        $this->postJson(route('set.language'), ['locale' => 'es'])
            ->assertStatus(400)
            ->assertJson(['success' => false]);
    }

    public function test_check_email_reports_existing_and_available_addresses(): void
    {
        $candidate = $this->createCandidate(['email' => 'candidate@example.com']);

        $this->postJson(route('check.email'), ['email' => $candidate->email])
            ->assertOk()
            ->assertJson(['exists' => true]);

        $this->postJson(route('check.email'), ['email' => 'new@example.com'])
            ->assertOk()
            ->assertJson(['exists' => false]);
    }

    public function test_contact_form_validates_required_fields(): void
    {
        $this->from(route('contact'))
            ->post(route('contact.submit'), [])
            ->assertRedirect(route('contact'))
            ->assertSessionHasErrors(['name', 'firstname', 'email', 'message']);
    }

    public function test_legacy_public_offer_routes_redirect_to_the_current_offers_listing(): void
    {
        $this->get(route('details.offre'))
            ->assertRedirect(route('offres'));

        $this->get(route('app_form'))
            ->assertRedirect(route('offres'));
    }
}
