<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class CVGeneratorController extends Controller
{
  
    
 public function generateCV(Request $request)
{
    // Validation des données (ajout des champs manquants)
    $validated = $request->validate([
        'model' => 'required|in:chronologique,competences',
        'nom' => 'required|string|max:255',
        'prenom' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'telephone' => 'required|string|max:20',
        'adresse' => 'nullable|string|max:500',
        'ville' => 'nullable|string|max:255',
        'code_postal' => 'nullable|string|max:20',
        'province' => 'nullable|string|max:255',
        'langues_competences' => 'nullable|string|max:1000',
        'logiciels' => 'nullable|string|max:1000',
        'site_web' => 'nullable|url|max:255',
        'objectif' => 'nullable|string|max:1000',
        'centres_interet' => 'nullable|string|max:500',
        'formations' => 'nullable|array',
        'formations.*' => 'array',
        'competences' => 'nullable|array',
        'competences.*' => 'array',
        'experiences' => 'nullable|array',
        'experiences.*' => 'array',
        'langues' => 'nullable|array',
        'langues.*' => 'array',
        // AJOUT DES CHAMPS MANQUANTS
        'perfectionnements' => 'nullable|array',
        'perfectionnements.*' => 'array',
        'benevolats' => 'nullable|array',
        'benevolats.*' => 'array',
    ]);

    // Préparer les données pour la vue (ajout des champs manquants)
    $cvData = [
        'nom' => $validated['nom'],
        'prenom' => $validated['prenom'],
        'email' => $validated['email'],
        'telephone' => $validated['telephone'],
        'adresse' => $validated['adresse'] ?? '',
        'ville' => $validated['ville'] ?? '',
        'code_postal' => $validated['code_postal'] ?? '',
        'province' => $validated['province'] ?? '',
        'langues_competences' => $validated['langues_competences'] ?? '',
        'logiciels' => $validated['logiciels'] ?? '',
        'site_web' => $validated['site_web'] ?? '',
        'objectif' => $validated['objectif'] ?? '',
        'centres_interet' => $validated['centres_interet'] ?? '',
        'formations' => $this->processFormations($validated['formations'] ?? []),
        'competences' => $validated['competences'] ?? [],
        'experiences' => $this->processExperiences($validated['experiences'] ?? []),
        'langues' => $validated['langues'] ?? [],
        // AJOUT DES DONNÉES MANQUANTES
        'perfectionnements' => $validated['perfectionnements'] ?? [],
        'benevolats' => $validated['benevolats'] ?? [],
    ];

    // Choisir le template selon le modèle sélectionné
    $templateName = $validated['model'] === 'chronologique' 
        ? 'templates.cv-chronologique' 
        : 'templates.cv-competences';

    try {
        // Debug log pour vérifier les données reçues
        Log::info('Données CV reçues:', [
            'perfectionnements' => $cvData['perfectionnements'],
            'benevolats' => $cvData['benevolats'],
            'formations' => $cvData['formations'],
            'experiences' => $cvData['experiences']
        ]);

        // Vérifier que le template existe
        if (!view()->exists($templateName)) {
            throw new \Exception("Le template $templateName n'existe pas");
        }

        // Générer le PDF
        $pdf = Pdf::loadView($templateName, $cvData);

        // Configuration du PDF
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => false,
            'defaultFont' => 'DejaVu Sans',
            'margin_top' => 10,
            'margin_right' => 10,
            'margin_bottom' => 10,
            'margin_left' => 10,
            'isRemoteEnabled' => false,
        ]);

        // Nom du fichier
        $filename = "CV_{$cvData['prenom']}_{$cvData['nom']}.pdf";

        // Log pour debug
        Log::info('Génération CV réussie', ['filename' => $filename]);

        // Retourner le PDF avec les bonnes en-têtes
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('X-CV-Generated', 'true')
            ->header('X-CV-Filename', $filename);

    } catch (\Exception $e) {
        Log::error('Erreur génération CV', [
            'error' => $e->getMessage(),
            'template' => $templateName,
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'error' => 'Erreur lors de la génération du CV: ' . $e->getMessage(),
            'success' => false
        ], 500);
    }
}

    private function processFormations($formations)
    {
        return array_map(function($formation) {
            // Convertir les dates si nécessaire
            if (isset($formation['date_debut'])) {
                $formation['annee_debut'] = date('Y', strtotime($formation['date_debut']));
            }
            if (isset($formation['date_fin'])) {
                $formation['annee_fin'] = date('Y', strtotime($formation['date_fin']));
            }
            return $formation;
        }, $formations);
    }

    private function processExperiences($experiences)
    {
        return array_map(function($experience) {
            // Convertir les dates si nécessaire
            if (isset($experience['date_debut'])) {
                $experience['annee_debut'] = date('Y-m', strtotime($experience['date_debut']));
            }
            if (isset($experience['date_fin'])) {
                $experience['annee_fin'] = date('Y-m', strtotime($experience['date_fin']));
            }
            return $experience;
        }, $experiences);
    }

    public function showForm()
    {
        return view('cv.modals.cv-generator-modal');
    }
}
