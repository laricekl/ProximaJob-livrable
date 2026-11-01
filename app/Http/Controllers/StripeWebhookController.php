<?php

namespace App\Http\Controllers;

use App\Models\UserAbonnement;
use App\Services\Payments\PaymentGateway;
use App\Services\Payments\SubscriptionActivationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;

/**
 * Réception des webhooks Stripe.
 *
 * Seul canal d'activation automatique : la signature est vérifiée par la
 * passerelle avant tout traitement. Répondre 200 même quand la
 * souscription est introuvable évite que Stripe ne réessaie en boucle
 * pour un événement qui ne nous concerne pas ; le cas est journalisé.
 */
class StripeWebhookController extends Controller
{
    public function __invoke(Request $request, PaymentGateway $gateway, SubscriptionActivationService $activator)
    {
        if (! config('stripe.webhook_secret')) {
            Log::warning('Webhook Stripe reçu alors qu\'aucun secret de webhook n\'est configuré.');

            return response()->json(['error' => 'Webhook non configuré.'], 503);
        }

        try {
            $event = $gateway->parseWebhookEvent(
                $request->getContent(),
                (string) $request->header('Stripe-Signature', '')
            );
        } catch (SignatureVerificationException) {
            return response()->json(['error' => 'Signature invalide.'], 400);
        } catch (\UnexpectedValueException) {
            return response()->json(['error' => 'Payload illisible.'], 400);
        }

        if ($event['type'] === 'checkout.session.completed') {
            $subscription = $this->findSubscription($event);

            if ($subscription) {
                $activator->activate($subscription, $event['payment_intent']);
            } else {
                Log::warning('Webhook Stripe : souscription introuvable pour la session complétée.', $event);
            }
        }

        return response()->json(['received' => true]);
    }

    private function findSubscription(array $event): ?UserAbonnement
    {
        if (! empty($event['user_abonnement_id'])) {
            return UserAbonnement::find($event['user_abonnement_id']);
        }

        if (! empty($event['session_id'])) {
            return UserAbonnement::where('stripe_checkout_session_id', $event['session_id'])->first();
        }

        return null;
    }
}
