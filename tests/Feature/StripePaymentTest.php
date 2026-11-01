<?php

namespace Tests\Feature;

use App\Models\Abonnement;
use App\Models\User;
use App\Models\UserAbonnement;
use App\Services\Payments\PaymentGateway;
use App\Services\Payments\StripeGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesTestAccounts;
use Tests\TestCase;

/**
 * Fausse passerelle : aucun appel réseau, aucun vrai compte Stripe.
 * Elle rejoue le contrat de StripeGateway avec des valeurs fixes.
 */
class FakePaymentGateway implements PaymentGateway
{
    public int $checkoutCalls = 0;

    public array $nextEvent = [
        'type' => 'checkout.session.completed',
        'session_id' => 'cs_test_123',
        'payment_intent' => 'pi_test_123',
        'user_abonnement_id' => null,
    ];

    public function createCheckoutSession(UserAbonnement $subscription): array
    {
        $this->checkoutCalls++;

        return ['id' => 'cs_test_123', 'url' => 'https://checkout.stripe.test/pay/cs_test_123'];
    }

    public function parseWebhookEvent(string $payload, string $signature): array
    {
        return $this->nextEvent;
    }
}

class StripePaymentTest extends TestCase
{
    use CreatesTestAccounts;
    use RefreshDatabase;

    private FakePaymentGateway $gateway;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'stripe.secret' => 'sk_test_fake',
            'stripe.webhook_secret' => 'whsec_fake',
        ]);

        $this->gateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $this->gateway);
    }

    private function pendingSubscription(User $user, float $amount = 49, int $days = 30): UserAbonnement
    {
        $plan = Abonnement::create([
            'nom' => 'Plan test '.$amount,
            'duree' => $days,
            'montant' => $amount,
            'description' => 'Plan de test.',
            'actif' => true,
        ]);

        return UserAbonnement::create([
            'user_id' => $user->id,
            'abonnement_id' => $plan->id,
            'date_debut' => now(),
            'date_fin' => now()->addDays($days),
            'status' => 'En attente',
        ]);
    }

    public function test_checkout_returns_stripe_url_and_stores_session_id(): void
    {
        $user = $this->createCandidate([], false);
        $subscription = $this->pendingSubscription($user);

        $response = $this->actingAs($user)->postJson(route('paiement.checkout', $subscription));

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('url', 'https://checkout.stripe.test/pay/cs_test_123');

        $this->assertSame(1, $this->gateway->checkoutCalls);
        $this->assertDatabaseHas('user_abonnements', [
            'id' => $subscription->id,
            'stripe_checkout_session_id' => 'cs_test_123',
        ]);
    }

    public function test_checkout_redirects_browser_to_stripe(): void
    {
        $user = $this->createCandidate([], false);
        $subscription = $this->pendingSubscription($user);

        $this->actingAs($user)
            ->post(route('paiement.checkout', $subscription))
            ->assertRedirect('https://checkout.stripe.test/pay/cs_test_123');
    }

    public function test_checkout_is_forbidden_for_another_users_subscription(): void
    {
        $owner = $this->createCandidate([], false);
        $intruder = $this->createCandidate(['email' => 'intrus@example.test'], false);
        $subscription = $this->pendingSubscription($owner);

        $this->actingAs($intruder)
            ->postJson(route('paiement.checkout', $subscription))
            ->assertForbidden();

        $this->assertSame(0, $this->gateway->checkoutCalls);
    }

    public function test_checkout_refuses_an_already_active_subscription(): void
    {
        $user = $this->createCandidate([], false);
        $subscription = $this->pendingSubscription($user);
        $subscription->forceFill(['status' => 'Actif'])->save();

        $this->actingAs($user)
            ->postJson(route('paiement.checkout', $subscription))
            ->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->assertSame(0, $this->gateway->checkoutCalls);
    }

    public function test_checkout_reports_clearly_when_stripe_is_not_configured(): void
    {
        config(['stripe.secret' => null]);

        $user = $this->createCandidate([], false);
        $subscription = $this->pendingSubscription($user);

        $this->actingAs($user)
            ->postJson(route('paiement.checkout', $subscription))
            ->assertStatus(503)
            ->assertJsonPath('success', false);

        $this->assertSame(0, $this->gateway->checkoutCalls);
    }

    public function test_webhook_activates_the_pending_subscription(): void
    {
        $user = $this->createCandidate([], false);
        $subscription = $this->pendingSubscription($user);
        $subscription->forceFill(['stripe_checkout_session_id' => 'cs_test_123'])->save();

        $this->gateway->nextEvent['user_abonnement_id'] = $subscription->id;

        $this->postWebhook()->assertOk()->assertJsonPath('received', true);

        $fresh = $subscription->fresh();
        $this->assertSame('Actif', $fresh->status);
        $this->assertSame('pi_test_123', $fresh->stripe_payment_intent_id);
        $this->assertTrue($fresh->date_debut->isToday());
        $this->assertTrue($fresh->date_fin->isSameDay(now()->addDays(30)));
    }

    public function test_webhook_is_idempotent_when_stripe_retries(): void
    {
        $user = $this->createCandidate([], false);
        $subscription = $this->pendingSubscription($user);
        $this->gateway->nextEvent['user_abonnement_id'] = $subscription->id;

        $this->postWebhook()->assertOk();

        // Une prolongation manuelle déplace la fin : un webhook rejoué ne doit pas l'écraser.
        $subscription->fresh()->forceFill(['date_fin' => now()->addDays(10)])->save();

        $this->postWebhook()->assertOk();

        $fresh = $subscription->fresh();
        $this->assertSame('Actif', $fresh->status);
        $this->assertTrue($fresh->date_fin->isSameDay(now()->addDays(10)));
    }

    public function test_webhook_ignores_unrelated_event_types(): void
    {
        $user = $this->createCandidate([], false);
        $subscription = $this->pendingSubscription($user);

        $this->gateway->nextEvent = [
            'type' => 'payment_intent.created',
            'session_id' => null,
            'payment_intent' => 'pi_autre',
            'user_abonnement_id' => $subscription->id,
        ];

        $this->postWebhook()->assertOk()->assertJsonPath('received', true);

        $this->assertSame('En attente', $subscription->fresh()->status);
    }

    public function test_webhook_rejects_an_invalid_signature_with_the_real_gateway(): void
    {
        $this->app->instance(PaymentGateway::class, new StripeGateway);
        config(['stripe.webhook_secret' => 'whsec_vrai_secret_de_test']);

        $this->call(
            'POST',
            route('stripe.webhook'),
            [],
            [],
            [],
            ['HTTP_STRIPE_SIGNATURE' => 't=1700000000,v1=signature_bidons', 'CONTENT_TYPE' => 'application/json'],
            json_encode(['type' => 'checkout.session.completed'])
        )->assertBadRequest();
    }

    public function test_webhook_refuses_when_no_secret_is_configured(): void
    {
        config(['stripe.webhook_secret' => null]);

        $this->postWebhook()->assertStatus(503);
    }

    private function postWebhook()
    {
        return $this->call(
            'POST',
            route('stripe.webhook'),
            [],
            [],
            [],
            ['HTTP_STRIPE_SIGNATURE' => 't=1,v1=fake', 'CONTENT_TYPE' => 'application/json'],
            json_encode(['type' => $this->gateway->nextEvent['type']])
        );
    }
}
