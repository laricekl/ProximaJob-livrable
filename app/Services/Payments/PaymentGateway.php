<?php

namespace App\Services\Payments;

use App\Models\UserAbonnement;
use Stripe\Exception\SignatureVerificationException;

/**
 * Frontière entre l'application et le fournisseur de paiement.
 *
 * L'implémentation réelle parle à Stripe ; les tests lient une fausse
 * passerelle, donc aucun test ne touche le réseau ni un vrai compte.
 */
interface PaymentGateway
{
    /**
     * Ouvre une session de paiement hébergée pour une souscription en attente.
     *
     * @return array{id: string, url: string}
     */
    public function createCheckoutSession(UserAbonnement $subscription): array;

    /**
     * Vérifie la signature d'un webhook et normalise son contenu.
     *
     * @return array{type: string, session_id: ?string, payment_intent: ?string, user_abonnement_id: ?int}
     *
     * @throws SignatureVerificationException si la signature est invalide
     * @throws \UnexpectedValueException si le payload est illisible
     */
    public function parseWebhookEvent(string $payload, string $signature): array;
}
