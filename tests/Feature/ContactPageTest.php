<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_can_be_submitted(): void
    {
        $response = $this->post(route('contact.submit'), [
            'name' => 'Doe',
            'firstname' => 'Jane',
            'email' => 'jane@example.com',
            'message' => 'Bonjour, je souhaite en savoir plus sur ProximaJob.',
        ]);

        $response
            ->assertRedirect(route('contact'))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');
    }

    public function test_enterprise_candidate_route_has_the_expected_prefix(): void
    {
        $this->assertSame(
            '/entreprise/candidat/42',
            route('entreprise.connected_candidate_details', 42, false)
        );
    }
}
