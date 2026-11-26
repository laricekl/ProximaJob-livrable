<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Champs de liaison avec Stripe (Phase 1 — monétisation).
     *
     * - abonnements.stripe_price_id : prix Stripe pré-créé au tableau de bord.
     *   Optionnel : sans lui, la session Checkout est construite à la volée
     *   à partir du montant du plan (price_data).
     * - user_abonnements.stripe_checkout_session_id : session de paiement
     *   ouverte pour cette souscription en attente.
     * - user_abonnements.stripe_payment_intent_id : preuve du paiement,
     *   remplie par le webhook lors de l'activation automatique.
     */
    public function up(): void
    {
        Schema::table('abonnements', function (Blueprint $table) {
            $table->string('stripe_price_id')->nullable()->after('actif');
        });

        Schema::table('user_abonnements', function (Blueprint $table) {
            $table->string('stripe_checkout_session_id')->nullable()->index()->after('status');
            $table->string('stripe_payment_intent_id')->nullable()->after('stripe_checkout_session_id');
        });
    }

    public function down(): void
    {
        Schema::table('user_abonnements', function (Blueprint $table) {
            $table->dropColumn(['stripe_checkout_session_id', 'stripe_payment_intent_id']);
        });

        Schema::table('abonnements', function (Blueprint $table) {
            $table->dropColumn('stripe_price_id');
        });
    }
};
