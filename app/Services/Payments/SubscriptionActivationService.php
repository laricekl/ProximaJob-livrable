<?php

namespace App\Services\Payments;

use App\Models\UserAbonnement;
use Illuminate\Support\Facades\DB;

/**
 * Activation d'un abonnement après confirmation du paiement.
 *
 * Idempotent : Stripe peut livrer le même webhook plusieurs fois.
 * Un abonnement déjà actif n'est jamais prolongé une deuxième fois ;
 * seule la preuve de paiement est complétée si elle manquait.
 * La fenêtre d'abonnement repart du jour du paiement, pas de la demande.
 */
class SubscriptionActivationService
{
    /**
     * @return bool true si l'abonnement vient d'être activé, false s'il l'était déjà
     */
    public function activate(UserAbonnement $subscription, ?string $paymentIntentId = null): bool
    {
        return DB::transaction(function () use ($subscription, $paymentIntentId) {
            $fresh = UserAbonnement::whereKey($subscription->id)->lockForUpdate()->firstOrFail();

            if ($fresh->status === 'Actif') {
                if ($paymentIntentId && ! $fresh->stripe_payment_intent_id) {
                    $fresh->forceFill(['stripe_payment_intent_id' => $paymentIntentId])->save();
                }

                return false;
            }

            $duration = max(1, (int) ($fresh->abonnement?->duree ?? 30));

            $fresh->forceFill([
                'status' => 'Actif',
                'date_debut' => now(),
                'date_fin' => now()->addDays($duration),
                'stripe_payment_intent_id' => $paymentIntentId ?? $fresh->stripe_payment_intent_id,
            ])->save();

            return true;
        });
    }
}
