<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Stripe
    |--------------------------------------------------------------------------
    |
    | Paiement en ligne des abonnements (Checkout hébergé par Stripe).
    | Les clés vivent uniquement dans l'environnement (.env / coffre) :
    | jamais de clé réelle dans le code ou dans le dépôt.
    | Sans STRIPE_SECRET, le parcours de paiement répond « non configuré »
    | et l'ancien flux (activation manuelle par l'admin) reste fonctionnel.
    |
    */

    'key' => env('STRIPE_KEY'),

    'secret' => env('STRIPE_SECRET'),

    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),

    'currency' => env('STRIPE_CURRENCY', 'cad'),
];
