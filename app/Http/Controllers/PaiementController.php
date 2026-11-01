<?php

namespace App\Http\Controllers;

use App\Models\UserAbonnement;
use App\Services\Payments\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Parcours de paiement des abonnements (Stripe Checkout hébergé).
 *
 * La souscription est d'abord créée « En attente » par les parcours
 * existants (candidat / entreprise) ; ce contrôleur ouvre la session de
 * paiement. L'activation, elle, arrive par le webhook Stripe — jamais
 * par la simple page de retour, qu'un navigateur peut simuler.
 */
class PaiementController extends Controller
{
    public function checkout(Request $request, UserAbonnement $userAbonnement, PaymentGateway $gateway)
    {
        abort_unless($userAbonnement->user_id === Auth::id(), 403);

        $plan = $userAbonnement->abonnement;

        if ($userAbonnement->status !== 'En attente' || ! $plan || (float) $plan->montant <= 0) {
            return $this->refuse($request, 'Cet abonnement ne requiert pas de paiement en ligne.', 422);
        }

        if (! config('stripe.secret')) {
            return $this->refuse($request, "Le paiement en ligne n'est pas encore configuré. Votre demande reste en attente de validation.", 503);
        }

        try {
            $session = $gateway->createCheckoutSession($userAbonnement);
        } catch (\Throwable $e) {
            Log::error('Création de session Stripe impossible', [
                'user_abonnement_id' => $userAbonnement->id,
                'erreur' => $e->getMessage(),
            ]);

            return $this->refuse($request, 'Le service de paiement est temporairement indisponible. Réessayez dans quelques minutes.', 502);
        }

        $userAbonnement->forceFill(['stripe_checkout_session_id' => $session['id']])->save();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'url' => $session['url']]);
        }

        return redirect()->away($session['url']);
    }

    public function succes()
    {
        return redirect($this->abonnementUrl())
            ->with('success', 'Paiement reçu ! Votre abonnement est activé automatiquement dès la confirmation de Stripe, habituellement en quelques secondes.');
    }

    public function annule()
    {
        return redirect($this->abonnementUrl())
            ->with('error', 'Paiement annulé : aucun montant n\'a été prélevé. Votre abonnement reste en attente, vous pouvez réessayer quand vous voulez.');
    }

    private function refuse(Request $request, string $message, int $status)
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $message], $status);
        }

        return back()->with('error', $message);
    }

    private function abonnementUrl(): string
    {
        $user = Auth::user();

        return ($user && $user->entreprise)
            ? route('entreprise.abonnements')
            : route('abonnement');
    }
}
