<?php

namespace App\Http\Controllers;

use App\Models\CvProfile;
use App\Models\CvFormation;
use App\Models\CvCompetence;
use App\Models\CvExperience;
use App\Models\CvLangue;
use App\Models\CvPerfectionnement;
use App\Models\CvBenevolat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CvProfileController extends Controller
{
    /**
     * Afficher le formulaire de création de CV
     */
     

    /**
 public function store(Request $request)
{
    $userId = auth()->id();
    $existingProfile = CvProfile::where('user_id', $userId)->first();
    
    if ($existingProfile) {
        return response()->json([
            'success' => false,
            'message' => 'Vous avez déjà un profil CV. Vous pouvez le modifier depuis votre espace.',
            'profile_id' => $existingProfile->id
        ], 422);
    }

    // Validation des données principales
    $validator = Validator::make($request->all(), [
        'nom' => 'required|string|max:255',
        'prenom_cv' => 'required|string|max:255',
        'email_cv' => 'required|email|max:255',
        'telephone_cv' => 'required|string|max:50',
        'adresse' => 'nullable|string',
        'ville' => 'nullable|string|max:255',
        'code_postal' => 'nullable|string|max:20',
        'langues_competences' => 'nullable|string',
        'logiciels' => 'nullable|string',
        
        // Validations pour les tableaux
        'formations' => 'nullable|array',
        'formations.*.periode' => 'nullable|string|max:100',
        'formations.*.diplome' => 'required_with:formations|exists:diplomes,id',
        'formations.*.etablissement' => 'nullable|string|max:500',
        
        'competences' => 'nullable|array',
        'competences.*.description' => 'required_with:competences|string',
        
        'experiences' => 'nullable|array',
        'experiences.*.periode' => 'required_with:experiences|string|max:100',
        'experiences.*.poste' => 'required_with:experiences|string|max:500',
        'experiences.*.entreprise' => 'nullable|string|max:500',
        'experiences.*.description' => 'nullable|string',
        
        'langues' => 'nullable|array',
        'langues.*.nom' => 'required_with:langues|string|max:100',
        'langues.*.niveau' => 'nullable|string|in:Langue maternelle,Courant,Intermédiaire,Notions de base,Connaissances de base',
        
        'perfectionnements' => 'nullable|array',
        'perfectionnements.*.annee' => 'nullable|string|max:50',
        'perfectionnements.*.formation' => 'required_with:perfectionnements|string|max:500',
        'perfectionnements.*.etablissement' => 'nullable|string|max:500',
        
        'benevolats' => 'nullable|array',
        'benevolats.*.periode' => 'nullable|string|max:100',
        'benevolats.*.role' => 'required_with:benevolats|string|max:500',
        'benevolats.*.organisation' => 'nullable|string|max:500',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Veuillez corriger les erreurs du formulaire',
            'errors' => $validator->errors()
        ], 422);
    }

    // Utiliser une transaction pour garantir l'intégrité des données
    DB::beginTransaction();

    try {
        // Extraire la province de la ville si fournie
        $province = $this->extractProvince($request->ville);

        // Créer le profil CV principal
        $cvProfile = CvProfile::create([
            'user_id' => auth()->id(),
            'nom' => $request->nom,
            'prenom' => $request->prenom_cv,
            'email' => $request->email_cv,
            'telephone' => $request->telephone_cv,
            'adresse' => $request->adresse,
            'ville' => $request->ville,
            'code_postal' => $request->code_postal,
            'province' => $province,
            'langues_competences' => $request->langues_competences,
            'logiciels' => $request->logiciels,
        ]);

        // Ajouter les formations
        if ($request->has('formations') && is_array($request->formations)) {
            foreach ($request->formations as $index => $formation) {
                if (!empty($formation['diplome'])) {
                    $diplome = \App\Models\Diplome::find($formation['diplome']);
                    
                    if ($diplome) {
                        CvFormation::create([
                            'cv_profile_id' => $cvProfile->id,
                            'periode' => $formation['periode'] ?? null,
                            'diplome' => $diplome->nom_diplome,
                            'diplome_id' => $diplome->id,
                            'etablissement' => $formation['etablissement'] ?? null,
                            'ordre' => $index
                        ]);
                    } else {
                        \Log::warning('Diplôme non trouvé', [
                            'diplome_id' => $formation['diplome'],
                            'cv_profile_id' => $cvProfile->id,
                            'index' => $index
                        ]);
                    }
                }
            }
        }

        // Ajouter les compétences spécifiques
        if ($request->has('competences') && is_array($request->competences)) {
            foreach ($request->competences as $index => $competence) {
                if (!empty($competence['description'])) {
                    CvCompetence::create([
                        'cv_profile_id' => $cvProfile->id,
                        'description' => $competence['description'],
                        'type' => 'specifique',
                        'ordre' => $index
                    ]);
                }
            }
        }

        // Ajouter les expériences professionnelles
        if ($request->has('experiences') && is_array($request->experiences)) {
            foreach ($request->experiences as $index => $experience) {
                if (!empty($experience['poste']) && !empty($experience['periode'])) {
                    CvExperience::create([
                        'cv_profile_id' => $cvProfile->id,
                        'periode' => $experience['periode'],
                        'poste' => $experience['poste'],
                        'entreprise' => $experience['entreprise'] ?? null,
                        'description' => $experience['description'] ?? null,
                        'ordre' => $index
                    ]);
                }
            }
        }

        // Ajouter les langues
        if ($request->has('langues') && is_array($request->langues)) {
            foreach ($request->langues as $index => $langue) {
                if (!empty($langue['nom'])) {
                    CvLangue::create([
                        'cv_profile_id' => $cvProfile->id,
                        'nom' => $langue['nom'],
                        'niveau' => $langue['niveau'] ?? null,
                        'ordre' => $index
                    ]);
                }
            }
        }

        // Ajouter les perfectionnements
        if ($request->has('perfectionnements') && is_array($request->perfectionnements)) {
            foreach ($request->perfectionnements as $index => $perfectionnement) {
                if (!empty($perfectionnement['formation'])) {
                    CvPerfectionnement::create([
                        'cv_profile_id' => $cvProfile->id,
                        'annee' => $perfectionnement['annee'] ?? null,
                        'formation' => $perfectionnement['formation'],
                        'etablissement' => $perfectionnement['etablissement'] ?? null,
                        'ordre' => $index
                    ]);
                }
            }
        }

        // Ajouter les activités bénévoles
        if ($request->has('benevolats') && is_array($request->benevolats)) {
            foreach ($request->benevolats as $index => $benevolat) {
                if (!empty($benevolat['role'])) {
                    CvBenevolat::create([
                        'cv_profile_id' => $cvProfile->id,
                        'periode' => $benevolat['periode'] ?? null,
                        'role' => $benevolat['role'],
                        'organisation' => $benevolat['organisation'] ?? null,
                        'ordre' => $index
                    ]);
                }
            }
        }

        DB::commit();

        // ============================================
        // 🚀 DÉCLENCHEMENT DE L'AUTO-MATCHING
        // ============================================
        try {
            // Dispatcher le job d'auto-matching avec un délai
            \App\Jobs\AutoMatchingJob::dispatch($userId)
               ->delay(now()->addSeconds(5));
            
            \Log::info('Auto-matching Job dispatché après création profil CV', [
                'user_id' => $userId,
                'cv_profile_id' => $cvProfile->id,
                'has_formations' => $cvProfile->formations()->count() > 0,
                'has_experiences' => $cvProfile->experiences()->count() > 0
            ]);
            
            $autoMatchingTriggered = true;
            
        } catch (\Exception $e) {
            // Ne pas faire échouer la création du profil si le matching échoue
            \Log::error('Erreur lors du dispatch AutoMatchingJob après création CV', [
                'user_id' => $userId,
                'cv_profile_id' => $cvProfile->id,
                'error' => $e->getMessage()
            ]);
            
            $autoMatchingTriggered = false;
        }

        return response()->json([
            'success' => true,
            'message' => 'Votre profil CV a été enregistré avec succès !' . 
                        ($autoMatchingTriggered ? ' Recherche d\'opportunités en cours...' : ''),
            'cv_profile_id' => $cvProfile->id,
            'auto_matching_triggered' => $autoMatchingTriggered
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        
        // Log détaillé de l'erreur
        \Log::error('Erreur lors de l\'enregistrement du CV:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'user_id' => auth()->id()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Une erreur est survenue lors de l\'enregistrement',
            'error' => config('app.debug') ? $e->getMessage() : 'Erreur serveur'
        ], 500);
    }
}
 

    /**
     * Afficher le formulaire d'édition
     */
    public function edit($id)
    {
        return redirect()->route('infos.cv');
    }

    /**
     * Mettre à jour un profil CV
     */
public function update(Request $request, $id)
{
    // Vérifier que l'utilisateur est autorisé à modifier ce profil
    $cvProfile = CvProfile::findOrFail($id);
    
    if ($cvProfile->user_id !== auth()->id()) {
        return response()->json([
            'success' => false,
            'message' => 'Vous n\'êtes pas autorisé à modifier ce profil.'
        ], 403);
    }

    // Validation des données
    $validator = Validator::make($request->all(), [
        'prenom_cv' => 'required|string|max:255',
        'nom' => 'required|string|max:255',
        'email_cv' => 'required|email|max:255',
        'telephone_cv' => 'nullable|string|max:50',
        'adresse' => 'nullable|string|max:500',
        'ville' => 'nullable|string|max:255',
        'code_postal' => 'nullable|string|max:20',
        'langues_competences' => 'nullable|string',
        'logiciels' => 'nullable|string',
        'formations' => 'nullable|array',
        'competences' => 'nullable|array',
        'experiences' => 'nullable|array',
        'langues' => 'nullable|array',
        'perfectionnements' => 'nullable|array',
        'benevolats' => 'nullable|array',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Veuillez corriger les erreurs du formulaire',
            'errors' => $validator->errors()
        ], 422);
    }

    DB::beginTransaction();

    try {
        // Mettre à jour les informations de base du profil CV
        $cvProfile->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom_cv,
            'email' => $request->email_cv,
            'telephone' => $request->telephone_cv,
            'adresse' => $request->adresse,
            'ville' => $request->ville,
            'code_postal' => $request->code_postal,
            'province' => $this->extractProvince($request->ville),
            'langues_competences' => $request->langues_competences,
            'logiciels' => $request->logiciels,
        ]);

        // Mettre à jour les relations (formations, compétences, expériences, etc.)
        $this->updateRelations($cvProfile, $request);

        DB::commit();

        // ============================================
        // 🚀 DÉCLENCHEMENT DE L'AUTO-MATCHING
        // ============================================
        try {
            // Dispatcher le job d'auto-matching
            \App\Jobs\AutoMatchingJob::dispatch($cvProfile->user_id)
               ->delay(now()->addSeconds(5));
            
            \Log::info('Auto-matching Job dispatché après mise à jour profil CV', [
                'user_id' => $cvProfile->user_id,
                'cv_profile_id' => $cvProfile->id,
                'has_formations' => $cvProfile->formations()->count() > 0,
                'has_experiences' => $cvProfile->experiences()->count() > 0
            ]);
            
            $autoMatchingTriggered = true;
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors du dispatch AutoMatchingJob après mise à jour CV', [
                'user_id' => $cvProfile->user_id,
                'cv_profile_id' => $cvProfile->id,
                'error' => $e->getMessage()
            ]);
            
            $autoMatchingTriggered = false;
        }

        return response()->json([
            'success' => true,
            'message' => 'Votre profil CV a été mis à jour avec succès !' . 
                        ($autoMatchingTriggered ? ' Recherche d\'opportunités en cours...' : ''),
            'cv_profile_id' => $cvProfile->id,
            'auto_matching_triggered' => $autoMatchingTriggered
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        
        \Log::error('Erreur lors de la mise à jour du CV:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'user_id' => auth()->id(),
            'cv_profile_id' => $id
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Une erreur est survenue lors de la mise à jour',
            'error' => config('app.debug') ? $e->getMessage() : 'Erreur serveur'
        ], 500);
    }
}

    /**
     * Extraire la province du champ ville
     */
    private function extractProvince($ville)
    {
        if (preg_match('/\((.*?)\)/', $ville, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Mettre à jour toutes les relations du profil CV
     */
    private function updateRelations(CvProfile $cvProfile, Request $request)
    {
        // Supprimer les anciennes données
        $cvProfile->formations()->delete();
        $cvProfile->competences()->delete();
        $cvProfile->experiences()->delete();
        $cvProfile->langues()->delete();
        $cvProfile->perfectionnements()->delete();
        $cvProfile->benevolats()->delete();

        // Recréer les relations avec les nouvelles données
        $this->createFormations($cvProfile, $request->formations ?? []);
        $this->createCompetences($cvProfile, $request->competences ?? []);
        $this->createExperiences($cvProfile, $request->experiences ?? []);
        $this->createLangues($cvProfile, $request->langues ?? []);
        $this->createPerfectionnements($cvProfile, $request->perfectionnements ?? []);
        $this->createBenevolats($cvProfile, $request->benevolats ?? []);
    }

    // Méthodes helpers pour créer les relations
    private function createFormations($cvProfile, $formations)
    {
        foreach ($formations as $index => $formation) {
            if (!empty($formation['diplome'])) {
                // Résoudre le nom du diplôme à partir de l'ID
                $diplome = \App\Models\Diplome::find($formation['diplome']);
                $diplomeNom = $diplome ? $diplome->nom_diplome : $formation['diplome'];
                $diplomeId = $diplome ? $diplome->id : null;

                CvFormation::create([
                    'cv_profile_id' => $cvProfile->id,
                    'periode' => $formation['periode'] ?? null,
                    'diplome' => $diplomeNom,
                    'diplome_id' => $diplomeId,
                    'etablissement' => $formation['etablissement'] ?? null,
                    'ordre' => $index
                ]);
            }
        }
    }

    private function createCompetences($cvProfile, $competences)
    {
        foreach ($competences as $index => $competence) {
            if (!empty($competence['description'])) {
                CvCompetence::create([
                    'cv_profile_id' => $cvProfile->id,
                    'description' => $competence['description'],
                    'type' => 'specifique',
                    'ordre' => $index
                ]);
            }
        }
    }

    private function createExperiences($cvProfile, $experiences)
    {
        foreach ($experiences as $index => $experience) {
            if (!empty($experience['poste'])) {
                CvExperience::create([
                    'cv_profile_id' => $cvProfile->id,
                    'periode' => $experience['periode'],
                    'poste' => $experience['poste'],
                    'entreprise' => $experience['entreprise'] ?? null,
                    'description' => $experience['description'] ?? null,
                    'ordre' => $index
                ]);
            }
        }
    }

    private function createLangues($cvProfile, $langues)
    {
        foreach ($langues as $index => $langue) {
            if (!empty($langue['nom'])) {
                CvLangue::create([
                    'cv_profile_id' => $cvProfile->id,
                    'nom' => $langue['nom'],
                    'niveau' => $langue['niveau'] ?? null,
                    'ordre' => $index
                ]);
            }
        }
    }

    private function createPerfectionnements($cvProfile, $perfectionnements)
    {
        foreach ($perfectionnements as $index => $perfectionnement) {
            if (!empty($perfectionnement['formation'])) {
                CvPerfectionnement::create([
                    'cv_profile_id' => $cvProfile->id,
                    'annee' => $perfectionnement['annee'] ?? null,
                    'formation' => $perfectionnement['formation'],
                    'etablissement' => $perfectionnement['etablissement'] ?? null,
                    'ordre' => $index
                ]);
            }
        }
    }

    private function createBenevolats($cvProfile, $benevolats)
    {
        foreach ($benevolats as $index => $benevolat) {
            if (!empty($benevolat['role'])) {
                CvBenevolat::create([
                    'cv_profile_id' => $cvProfile->id,
                    'periode' => $benevolat['periode'] ?? null,
                    'role' => $benevolat['role'],
                    'organisation' => $benevolat['organisation'] ?? null,
                    'ordre' => $index
                ]);
            }
        }
    }

    /**
     * Téléverse le CV source (PDF/DOCX) de l'utilisateur.
     */
    public function uploadSourceCv(Request $request)
    {
        $request->validate([
            'cv' => 'required|file|mimes:pdf,doc,docx,txt|max:4096',
        ]);

        $user = auth()->user();
        $file = $request->file('cv');
        $path = $file->storeAs(
            'cvs-source',
            'source_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension(),
            'public'
        );

        // Stocker le chemin dans le champ cv de l'utilisateur pour référence
        $user->cv = $path;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'CV source téléversé avec succès.',
            'path' => $path,
        ]);
    }

    /**
     * Importe les données du CV uploadé pour pré-remplir le formulaire CV.
     * Utilise Gemini pour analyser le CV et extraire les champs structurés.
     */
    public function importFromUploadedCv(Request $request)
    {
        $user = auth()->user();

        if (!$user->cv) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun CV source téléversé. Importez d\'abord un fichier.',
            ], 400);
        }

        $fullPath = storage_path('app/public/' . $user->cv);

        if (!file_exists($fullPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Le fichier CV source est introuvable.',
            ], 404);
        }

        // Extraire le contenu texte du CV
        $cvText = $this->extractTextFromCvFile($fullPath, $user->cv);

        if (!$cvText) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de lire le contenu de ce fichier. Essayez avec un fichier TXT ou PDF contenant du texte.',
            ], 422);
        }

        // Analyser avec Gemini (avec fallback regex si Gemini indisponible)
        $fields = $this->analyzeCvWithGemini($cvText, $user);

        return response()->json([
            'success' => true,
            'message' => 'CV analysé avec succès. Les champs ont été pré-remplis.',
            'fields' => $fields,
        ]);
    }

    /**
     * Extrait le texte brut d'un fichier CV (TXT ou PDF).
     */
    private function extractTextFromCvFile(string $path, string $filename): ?string
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if ($ext === 'txt') {
            $content = file_get_contents($path);
            return mb_convert_encoding($content ?: '', 'UTF-8');
        }

        // Pour les PDF, extraction basique du texte entre les flux
        if ($ext === 'pdf') {
            $content = file_get_contents($path);
            if (!$content) return null;

            // Extraction simple du texte lisible dans un PDF
            $text = '';
            // Décode les flux texte entre BT et ET
            if (preg_match_all('/BT\s*(.*?)\s*ET/s', $content, $blocks)) {
                foreach ($blocks[1] as $block) {
                    // Extrait le texte entre parenthèses dans les commandes Tj
                    if (preg_match_all('/\((.*?)\)\s*Tj/', $block, $matches)) {
                        $text .= implode(' ', $matches[1]) . "\n";
                    }
                }
            }

            // Fallback : extraire tout texte lisible
            if (empty(trim($text))) {
                preg_match_all('/\(([^)]{2,})\)/', $content, $matches);
                $text = implode("\n", $matches[1]);
            }

            return $text ?: null;
        }

        // DOCX — tentative d'extraction du XML
        if ($ext === 'docx') {
            $content = file_get_contents($path);
            if (!$content) return null;
            // DOCX est un ZIP contenant word/document.xml
            $tmp = sys_get_temp_dir() . '/cv_import_' . uniqid();
            file_put_contents($tmp, $content);
            $zip = new \ZipArchive();
            if ($zip->open($tmp) === true) {
                $xml = $zip->getFromName('word/document.xml');
                $zip->close();
                unlink($tmp);
                if ($xml) {
                    $text = strip_tags($xml);
                    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
                    // Nettoie les espaces multiples
                    $text = preg_replace('/\s+/', ' ', $text);
                    return trim($text);
                }
            }
            unlink($tmp);
            return null;
        }

        return null;
    }

    /**
     * Analyse le contenu d'un CV avec DeepSeek pour extraire les champs structurés.
     * Fallback sur Gemini, puis sur l'extraction regex si aucun provider n'est disponible.
     */
    private function analyzeCvWithGemini(string $cvText, $user): array
    {
        $prompt = <<<PROMPT
Tu es un assistant RH expert en analyse de CV. Analyse le CV suivant et extrait les informations dans un format JSON structuré.

CV à analyser :
```
{$cvText}
```

Retourne UNIQUEMENT un objet JSON valide avec ces clés (laisse vide si non trouvé) :
{
    "nom": "",
    "prenom_cv": "",
    "email_cv": "",
    "telephone_cv": "",
    "adresse": "",
    "ville": "",
    "code_postal": "",
    "langues_competences": "",
    "logiciels": "",
    "formations": [{"periode": "", "diplome": "", "etablissement": ""}],
    "competences": [{"description": ""}],
    "experiences": [{"periode": "", "poste": "", "entreprise": "", "description": ""}],
    "langues": [{"langue": ""}],
    "perfectionnements": [{"annee": "", "formation": "", "etablissement": ""}],
    "benevolats": [{"periode": "", "role": "", "organisation": ""}]
}

IMPORTANT: Retourne UNIQUEMENT le JSON, pas de texte autour.
PROMPT;

        // Essayer DeepSeek d'abord, puis Gemini, puis fallback regex
        $providers = [
            [\Prism\Prism\Enums\Provider::DeepSeek, 'deepseek-chat', 'DEEPSEEK_API_KEY'],
            [\Prism\Prism\Enums\Provider::Gemini, 'gemini-2.5-flash', 'GEMINI_API_KEY'],
        ];

        foreach ($providers as [$provider, $model, $envKey]) {
            if (!env($envKey)) continue;

            try {
                $response = \Prism\Prism\Prism::text()
                    ->using($provider, $model)
                    ->withPrompt($prompt)
                    ->withMaxTokens(4096)
                    ->generate();

                $jsonText = trim($response->text);
                $jsonText = preg_replace('/^```json\s*/', '', $jsonText);
                $jsonText = preg_replace('/\s*```$/', '', $jsonText);

                $fields = json_decode($jsonText, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    \Log::warning("CV analysis with {$model} returned invalid JSON", [
                        'error' => json_last_error_msg(),
                        'provider' => $model,
                    ]);
                    continue; // Essayer le provider suivant
                }

                return $this->normalizeGeminiFields($fields);

            } catch (\Exception $e) {
                \Log::warning("CV analysis failed with {$model}: " . $e->getMessage());
                continue; // Essayer le provider suivant
            }
        }

        // Aucun provider IA disponible — fallback regex
        \Log::info('CV analysis: no AI provider available, using regex fallback');
        return $this->extractFieldsFromText($cvText);
    }

    /**
     * Normalise les champs retournés par Gemini pour correspondre au formulaire.
     */
    private function normalizeGeminiFields(array $fields): array
    {
        // S'assurer que tous les champs attendus existent
        $defaults = [
            'nom', 'prenom_cv', 'email_cv', 'telephone_cv',
            'adresse', 'ville', 'code_postal', 'langues_competences', 'logiciels',
        ];

        foreach ($defaults as $key) {
            $fields[$key] = $fields[$key] ?? '';
        }

        return $fields;
    }

    /**
     * Extraction basique de champs depuis un fichier texte (fallback sans IA).
     */
    private function extractFieldsFromText(string $text): array
    {
        $fields = [
            'nom' => '',
            'prenom_cv' => '',
            'email_cv' => '',
            'telephone_cv' => '',
            'adresse' => '',
            'ville' => '',
            'code_postal' => '',
            'langues_competences' => '',
            'logiciels' => '',
        ];

        // Email
        if (preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $text, $m)) {
            $fields['email_cv'] = $m[0];
        }

        // Téléphone nord-américain
        if (preg_match('/\(?[0-9]{3}\)?[-.\s]?[0-9]{3}[-.\s]?[0-9]{4}/', $text, $m)) {
            $fields['telephone_cv'] = $m[0];
        }

        // Code postal canadien
        if (preg_match('/[A-Za-z]\d[A-Za-z]\s?\d[A-Za-z]\d/', $text, $m)) {
            $fields['code_postal'] = $m[0];
        }

        return $fields;
    }

    /**
     * Affiche le PDF du CV principal dans un iframe (inline).
     */
    public function inlinePrincipalPdf()
    {
        $cvProfile = CvProfile::with([
            'formations', 'competences', 'experiences',
            'langues', 'perfectionnements', 'benevolats',
        ])->where('user_id', auth()->id())->first();

        if (!$cvProfile) {
            abort(404, 'Aucun profil CV trouvé. Veuillez d\'abord créer votre CV.');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('cv.principal-template', compact('cvProfile'));
        $pdf->setPaper('A4');

        return $pdf->stream('cv_principal.pdf');
    }

    /**
     * Télécharge le PDF du CV principal.
     */
    public function downloadPrincipalPdf()
    {
        $cvProfile = CvProfile::with([
            'formations', 'competences', 'experiences',
            'langues', 'perfectionnements', 'benevolats',
        ])->where('user_id', auth()->id())->first();

        if (!$cvProfile) {
            abort(404, 'Aucun profil CV trouvé. Veuillez d\'abord créer votre CV.');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('cv.principal-template', compact('cvProfile'));
        $pdf->setPaper('A4');

        return $pdf->download('cv_principal.pdf');
    }
}
