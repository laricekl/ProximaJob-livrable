<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialAuthAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_social_buttons_are_disabled_when_oauth_is_not_configured(): void
    {
        config([
            'services.google.client_id' => null,
            'services.google.client_secret' => null,
            'services.google.redirect' => null,
            'services.facebook.client_id' => null,
            'services.facebook.client_secret' => null,
            'services.facebook.redirect' => null,
        ]);

        $this->get(route('login'))
            ->assertOk()
            ->assertDontSee(route('auth.social.redirect', 'google'), false)
            ->assertDontSee(route('auth.social.redirect', 'facebook'), false)
            ->assertSee('Google bientot disponible')
            ->assertSee('Facebook bientot disponible');

        $this->get(route('register'))
            ->assertOk()
            ->assertDontSee(route('auth.social.redirect', 'google'), false)
            ->assertDontSee(route('auth.social.redirect', 'facebook'), false);
    }

    public function test_social_redirect_is_blocked_when_provider_is_not_configured(): void
    {
        config([
            'services.google.client_id' => null,
            'services.google.client_secret' => null,
            'services.google.redirect' => null,
        ]);

        $this->get(route('auth.social.redirect', 'google'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('error');
    }
}
