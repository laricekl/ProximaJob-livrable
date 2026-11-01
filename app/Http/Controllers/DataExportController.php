<?php

namespace App\Http\Controllers;

use App\Models\CvGenere;
use App\Models\UserAbonnement;
use Illuminate\Http\Request;

/**
 * Export des données personnelles de l'utilisateur connecté (Loi 25,
 * audit G6) : téléchargement JSON limité à SES données — compte, profil
 * CV et métadonnées des CV générés, entreprise et ses offres le cas
 * échéant, candidatures déposées, abonnements, notifications.
 *
 * Volontairement exclus : fichiers binaires (CV, lettres), données
 * d'autrui (candidatures reçues par une entreprise) et tout secret
 * (mot de passe, jetons, identifiants de connexion sociale).
 */
class DataExportController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user()->load([
            'cvProfile.formations',
            'cvProfile.competences',
            'cvProfile.experiences',
            'cvProfile.langues',
            'cvProfile.perfectionnements',
            'cvProfile.benevolats',
            'entreprise.offres',
            'postulations.offre',
            'notifications',
        ]);

        $data = [
            'exporte_le' => now()->toIso8601String(),
            'compte' => [
                'prenom' => $user->prenom,
                'nom' => $user->name,
                'email' => $user->email,
                'telephone' => $user->telephone,
                'adresse' => $user->adresse,
                'photo_profil' => $user->profile_photo_path,
                'roles' => $user->getRoleNames()->all(),
                'courriel_verifie_le' => $user->email_verified_at?->toIso8601String(),
                'compte_cree_le' => $user->created_at?->toIso8601String(),
            ],
            'profil_cv' => $user->cvProfile?->makeHidden(['user_id'])->toArray(),
            'cv_generes' => CvGenere::whereHas('cvProfile', fn ($q) => $q->where('user_id', $user->id))
                ->get(['id', 'offre_id', 'nom_fichier', 'template_style', 'date_generation', 'created_at'])
                ->toArray(),
            'entreprise' => $user->entreprise
                ? $user->entreprise->makeHidden(['user_id'])->toArray()
                : null,
            'offres_publiees' => $user->entreprise
                ? $user->entreprise->offres->map(fn ($offre) => [
                    'id' => $offre->id,
                    'titre' => $offre->titre,
                    'poste' => $offre->poste,
                    'statut' => $offre->status,
                    'creee_le' => $offre->created_at?->toIso8601String(),
                ])->all()
                : [],
            'candidatures' => $user->postulations->map(fn ($postulation) => [
                'id' => $postulation->id,
                'offre' => $postulation->offre?->titre,
                'statut' => $postulation->status,
                'automatique' => (bool) $postulation->autopostulation,
                'score_matching' => $postulation->match_score,
                'cv_joint' => ! empty($postulation->cv),
                'lettre_jointe' => ! empty($postulation->cover_letter) || ! empty($postulation->lettre_motivation),
                'deposee_le' => $postulation->created_at?->toIso8601String(),
            ])->all(),
            'abonnements' => UserAbonnement::with('abonnement')
                ->where('user_id', $user->id)
                ->get()
                ->map(fn ($abonnement) => [
                    'plan' => $abonnement->abonnement?->nom,
                    'montant' => $abonnement->abonnement?->montant,
                    'date_debut' => $abonnement->date_debut?->toDateString(),
                    'date_fin' => $abonnement->date_fin?->toDateString(),
                    'statut' => $abonnement->status,
                    'reference_paiement' => $abonnement->stripe_payment_intent_id ?? $abonnement->stripe_checkout_session_id,
                ])->all(),
            'notifications' => $user->notifications->map(fn ($notification) => [
                'titre' => $notification->title,
                'message' => $notification->message,
                'lue' => (bool) $notification->is_read,
                'recue_le' => $notification->created_at?->toIso8601String(),
            ])->all(),
        ];

        $filename = 'mes-donnees-proximajob-'.now()->format('Ymd').'.json';

        return response(
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            200,
            [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]
        );
    }
}
