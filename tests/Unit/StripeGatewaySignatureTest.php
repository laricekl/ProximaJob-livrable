<?php

namespace Tests\Unit;

use App\Services\Payments\StripeGateway;
use Stripe\Exception\SignatureVerificationException;
use Tests\TestCase;

/**
 * La vérification de signature du webhook est le verrou de sécurité du
 * paiement : elle est testée ici avec la vraie implémentation Stripe,
 * un payload signé localement (aucun réseau).
 */
class StripeGatewaySignatureTest extends TestCase
{
    private string $secret = 'whsec_cle_de_test_signature';

    protected function setUp(): void
    {
        parent::setUp();

        config(['stripe.webhook_secret' => $this->secret]);
    }

    private function payload(): string
    {
        return json_encode([
            'id' => 'evt_test_1',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_9',
                    'payment_intent' => 'pi_test_9',
                    'metadata' => ['user_abonnement_id' => '42'],
                ],
            ],
        ]);
    }

    private function sign(string $payload): string
    {
        $timestamp = time();

        return 't='.$timestamp.',v1='.hash_hmac('sha256', $timestamp.'.'.$payload, $this->secret);
    }

    public function test_a_valid_signature_is_accepted_and_normalized(): void
    {
        $payload = $this->payload();

        $event = (new StripeGateway)->parseWebhookEvent($payload, $this->sign($payload));

        $this->assertSame('checkout.session.completed', $event['type']);
        $this->assertSame('cs_test_9', $event['session_id']);
        $this->assertSame('pi_test_9', $event['payment_intent']);
        $this->assertSame(42, $event['user_abonnement_id']);
    }

    public function test_a_tampered_payload_is_rejected(): void
    {
        $payload = $this->payload();
        $signature = $this->sign($payload);
        $tampered = str_replace('cs_test_9', 'cs_test_modifiee', $payload);

        $this->expectException(SignatureVerificationException::class);

        (new StripeGateway)->parseWebhookEvent($tampered, $signature);
    }
}
