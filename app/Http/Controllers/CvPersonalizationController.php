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
    public function showForm(Request $request)
    {
        $user = Auth::user();
        $cvProfile = CvProfile::where('user_id', $user->id)->first();

        if (!$cvProfile) {
            return redirect()->route('infos.cv')
                ->with('error', 'Veuillez compléter votre profil CV avant de personnaliser.');
        }

        // Récupérer les offres récentes pour suggestions
        $recentOffers = Offre::where('status', 'active')
            ->where('date_fin', '>=', now())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get(['id', 'titre', 'entreprise_id']);

        $selectedOffer = null;
        $selectedOfferDetails = '';
        $selectedOfferRequirements = '';
        $selectedOfferCompany = '';
        $offerSkills = [];
        $offerDataQuality = null;

        if ($request->filled('offre_id')) {
            $selectedOffer = Offre::with(['entreprise'])
                ->whereKey($request->integer('offre_id'))
                ->first();

            if ($selectedOffer) {
                $selectedOfferDetails = $selectedOffer->description
                    ?: $selectedOffer->missions
                    ?: $selectedOffer->responsibilities
                    ?: '';

                // Enrichir avec les compétences sélectionnées dans l'offre
                $offerSkills = $selectedOffer->skills()
                    ->with('skill')
                    ->get()
                    ->pluck('skill.name')
                    ->filter()
                    ->toArray();

                $selectedOfferRequirements = $selectedOffer->competences
                    ?: $selectedOffer->criteres
                    ?: '';

                if (!empty($offerSkills)) {
                    $skillsText = implode(', ', $offerSkills);
                    $selectedOfferRequirements = $selectedOfferRequirements
                        ? $selectedOfferRequirements . "\nCompétences requises : " . $skillsText
                        : 'Compétences requises : ' . $skillsText;
                }

                $selectedOfferCompany = $selectedOffer->entreprise->company_name ?? '';

                // Qualité des données de l'offre
                $offerDataQuality = (mb_strlen($selectedOfferDetails) > 50) ? 'bonne' : 'faible';
                if ($offerDataQuality === 'faible' && empty($offerSkills)) {
                    $offerDataQuality = 'insuffisante';
                }
            }
        }

        return view('cv.personalization-form', compact(
            'cvProfile',
            'recentOffers',
            'selectedOffer',
            'selectedOfferDetails',
            'selectedOfferRequirements',
            'selectedOfferCompany',
            'offerSkills',
            'offerDataQuality'
        ));
    }

    /**
     * Génère le CV personnalisé
     */
    public function generateCV(Request $request)
    {
        $request->validate([
            'offer_details' => 'required|string|min:20',
            'offer_title' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'key_requirements' => 'required_without:offre_id|nullable|string|min:3',
            'template_style' => 'nullable|in:modern,classic,executive',
            'accent_color' => 'nullable|in:blue,green,bordeaux,anthracite,petrol',
            'font_style' => 'nullable|in:sober,modern,classic',
            'density' => 'nullable|in:airy,balanced,compact',
            'section_order' => 'nullable|in:skills_first,experience_first',
            'page_limit' => 'nullable|integer|in:1,2,3',
            'summary_tone' => 'nullable|in:direct,professional,human,technical',
            'sections_present' => 'nullable|boolean',
            'sections' => 'nullable|array',
            'sections.*' => 'in:software,languages,perfectionnements,benevolats',
            'offre_id' => ['nullable', 'integer', 'exists:offres,id'],
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
            return redirect()->route('cv.personalization.preview', array_filter([
                'filename' => $result['filename'],
                'offre_id' => $request->integer('offre_id') ?: null,
            ]))->with('success', 'CV personnalisé généré avec succès !');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la génération : ' . $e->getMessage());
        }
    }

    /**
     * Retourne le PDF brut pour affichage dans un iframe.
     */
    public function inlineCV($filename)
    {
        $filePath = 'personalized-cvs/' . $filename;

        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'CV introuvable.');
        }

        $fullPath = Storage::disk('public')->path($filePath);

        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }

    /**
     * Prévisualise le CV généré
     */
    public function previewCV(Request $request, $filename)
    {
        $filePath = 'personalized-cvs/' . $filename;
        
        if (!Storage::disk('public')->exists($filePath)) {
            abort(404);
        }

        $fileUrl = route('cv.personalization.inline', ['filename' => $filename]);
        $returnToApplicationUrl = null;

        if ($request->filled('offre_id')) {
            $offer = Offre::whereKey($request->integer('offre_id'))->first();
            $returnToApplicationUrl = $offer
                ? route('job_details', $offer) . '?openModal=true'
                : null;
        }
        
        return view('cv.preview', compact('fileUrl', 'filename', 'returnToApplicationUrl'));
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

        $user = Auth::user();
        $candidateName = $user
            ? str($user->prenom.' '.$user->name)->squish()->ascii()->replaceMatches('/[^A-Za-z0-9]+/', '_')->trim('_')
            : 'candidat';
        $downloadName = 'CV_'.$candidateName.'.pdf';

        return Storage::disk('public')->download($filePath, $downloadName);
    }

    /**
     * Crée un objet offre virtuel pour la génération
     */
    private function createVirtualOffer(Request $request): \stdClass
    {
        $offer = new \stdClass();
        $offer->id = $request->integer('offre_id') ?: 'virtual_' . uniqid();
        $offer->titre = $request->offer_title;
        $offer->poste = $request->offer_title;
        $offer->description = $request->offer_details;

        // Enrichir avec les skills de l'offre si disponible
        $competences = $request->key_requirements ?: '';
        if ($request->filled('offre_id')) {
            $dbOffer = Offre::with('skills.skill')->find($request->integer('offre_id'));
            if ($dbOffer && $dbOffer->skills->isNotEmpty()) {
                $skillsText = $dbOffer->skills->pluck('skill.name')->filter()->implode(', ');
                $competences = $competences ? $competences . ', ' . $skillsText : $skillsText;
            }
        }
        $offer->competences = $competences;

        $offer->entreprise = new \stdClass();
        $offer->entreprise->name = $request->company_name ?? 'Entreprise Cible';
        $offer->customization_options = [
            'template_style' => $request->input('template_style', 'modern'),
            'accent_color' => $request->input('accent_color', 'blue'),
            'font_style' => $request->input('font_style', 'sober'),
            'density' => $request->input('density', 'balanced'),
            'section_order' => $request->input('section_order', 'skills_first'),
            'page_limit' => (int) $request->input('page_limit', 2),
            'summary_tone' => $request->input('summary_tone', 'professional'),
            'sections' => $request->boolean('sections_present')
                ? $request->input('sections', [])
                : $request->input('sections', ['software', 'languages', 'perfectionnements', 'benevolats']),
        ];
        
        return $offer;
    }
}
