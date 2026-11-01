<?php

namespace App\Services\Payments;

use App\Models\UserAbonnement;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeGateway implements PaymentGateway
{
    private ?StripeClient $client = null;

    public function createCheckoutSession(UserAbonnement $subscription): array
    {
        $plan = $subscription->abonnement;

        $lineItem = $plan->stripe_price_id
            ? ['price' => $plan->stripe_price_id, 'quantity' => 1]
            : [
                'quantity' => 1,
                'price_data' => [
                    'currency' => config('stripe.currency', 'cad'),
                    'unit_amount' => (int) round((float) $plan->montant * 100),
                    'product_data' => [
                        'name' => 'Abonnement '.$plan->nom,
                        'description' => 'Forfait '.$plan->nom.' — '.$plan->duree.' jours',
                    ],
                ],
            ];

        $session = $this->client()->checkout->sessions->create([
            'mode' => 'payment',
            'line_items' => [$lineItem],
            'customer_email' => $subscription->user?->email,
            'client_reference_id' => (string) $subscription->id,
            'metadata' => ['user_abonnement_id' => (string) $subscription->id],
            'success_url' => route('paiement.succes').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('paiement.annule'),
        ]);

        return ['id' => $session->id, 'url' => $session->url];
    }

    public function parseWebhookEvent(string $payload, string $signature): array
    {
        $event = Webhook::constructEvent(
            $payload,
            $signature,
            (string) config('stripe.webhook_secret')
        );

        $object = $event->data->object ?? null;
        $paymentIntent = $object->payment_intent ?? null;

        return [
            'type' => $event->type,
            'session_id' => $object->id ?? null,
            'payment_intent' => is_string($paymentIntent) ? $paymentIntent : ($paymentIntent->id ?? null),
            'user_abonnement_id' => isset($object->metadata->user_abonnement_id)
                ? (int) $object->metadata->user_abonnement_id
                : null,
        ];
    }

    private function client(): StripeClient
    {
        return $this->client ??= new StripeClient((string) config('stripe.secret'));
    }
}
