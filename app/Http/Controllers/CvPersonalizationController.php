<?php

namespace App\Http\Controllers;

use App\Services\JobMatchingService;
use App\Models\CvProfile;
use App\Models\Offre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class CvPersonalizationController extends Controller
{
    private $jobMatchingService;

    public function __construct(JobMatchingService $jobMatchingService)
    {
        $this->jobMatchingService = $jobMatchingService;
    }

    /**
     * Affiche le formulaire de personnalisation
     */
    public function showForm()
    {
        $user = Auth::user();
        $cvProfile = CvProfile::where('user_id', $user->id)->first();

        if (!$cvProfile) {
            return redirect()->route('profile.edit')
                ->with('error', 'Veuillez compléter votre profil CV avant de personnaliser.');
        }

        // Récupérer les offres récentes pour suggestions
        $recentOffers = Offre::where('status', 'active')
            ->where('date_fin', '>=', now())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get(['id', 'titre', 'entreprise_id']);

        return view('cv.personalization-form', compact('cvProfile', 'recentOffers'));
    }

    /**
     * Génère le CV personnalisé
     */
    public function generateCV(Request $request)
    {
        $request->validate([
            'offer_details' => 'required|string|min:10',
            'offer_title' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'key_requirements' => 'nullable|string',
            'template_style' => 'nullable|in:modern,classic,executive,creative'
        ]);

        try {
            $user = Auth::user();
            $cvProfile = CvProfile::where('user_id', $user->id)->first();

            if (!$cvProfile) {
                return back()->with('error', 'Profil CV non trouvé.');
            }

            // Créer un objet Offre factice avec les données du formulaire
            $virtualOffer = $this->createVirtualOffer($request);

            // Générer le CV personnalisé
            $result = $this->jobMatchingService->generatePersonalizedCVForUser($cvProfile, $virtualOffer, $user->id);

            if (isset($result['error'])) {
                return back()->with('error', $result['error']);
            }

            // Rediriger vers la prévisualisation
            return redirect()->route('cv.personalization.preview', ['filename' => $result['filename']])
                ->with('success', 'CV personnalisé généré avec succès !');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la génération : ' . $e->getMessage());
        }
    }

    /**
     * Prévisualise le CV généré
     */
    public function previewCV($filename)
    {
        $filePath = 'personalized-cvs/' . $filename;
        
        if (!Storage::disk('public')->exists($filePath)) {
            abort(404);
        }

        $fileUrl = Storage::disk('public')->url($filePath);
        
        return view('cv.preview', compact('fileUrl', 'filename'));
    }

    /**
     * Télécharge le CV
     */
    public function downloadCV($filename)
    {
        $filePath = 'personalized-cvs/' . $filename;
        
        if (!Storage::disk('public')->exists($filePath)) {
            abort(404);
        }

        return Storage::disk('public')->download($filePath, "CV_Personnalise_{$filename}.pdf");
    }

    /**
     * Crée un objet offre virtuel pour la génération
     */
    private function createVirtualOffer(Request $request): \stdClass
    {
        $offer = new \stdClass();
        $offer->id = 'virtual_' . uniqid();
        $offer->titre = $request->offer_title;
        $offer->poste = $request->offer_title;
        $offer->description = $request->offer_details;
        $offer->competences = $request->key_requirements;
        $offer->entreprise = new \stdClass();
        $offer->entreprise->name = $request->company_name ?? 'Entreprise Cible';
        
        return $offer;
    }
}