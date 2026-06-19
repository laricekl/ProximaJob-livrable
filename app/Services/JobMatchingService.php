<?php

namespace App\Services;

use App\Models\Offre;
use App\Models\User;
use App\Models\Notification;  
use App\Models\CandidateSector;
use App\Models\CandidateSkill;
use App\Models\JobOfferSkill;
use App\Models\Postulation;
use App\Models\CvProfile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Prism\Prism\Prism;
use Prism\Prism\Enums\Provider;

class JobMatchingService
{
    private const MIN_MATCH_SCORE = 0;
    private const MAX_APPLICATIONS_PER_DAY = 3;
    

    private function parseExperienceInterval(string $expString): ?array
    {
        $expString = trim($expString);
        
        // Table de conversion exacte basée sur votre formulaire
        $conversion = [
            '0–6 mois' => ['min' => 0, 'max' => 0.5],
            '0-6 mois' => ['min' => 0, 'max' => 0.5],
            '1' => ['min' => 1, 'max' => 1],
            '2–3' => ['min' => 2, 'max' => 3],
            '2-3' => ['min' => 2, 'max' => 3],
            '5' => ['min' => 5, 'max' => null], // null = pas de maximum
            '0' => null, // "Non exigée"
        ];
        
        // Vérifier dans la table
        if (isset($conversion[$expString])) {
            return $conversion[$expString];
        }
        
        // Extraire les nombres si format différent
        if (preg_match('/(\d+)\s*(?:–|-)\s*(\d+)/', $expString, $matches)) {
            return ['min' => (float)$matches[1], 'max' => (float)$matches[2]];
        }
        
        if (preg_match('/^(\d+)/', $expString, $matches)) {
            // Juste un nombre
            return ['min' => (float)$matches[1], 'max' => (float)$matches[1]];
        }
        
        return null;  
    }


    private function calculateTechnicalSkillsPercentage(int $offerId, int $candidateId): float
    {
        // Compétences techniques requises par l'offre
        $requiredTechnicalSkillIds = JobOfferSkill::where('job_offer_id', $offerId)
            ->where('skill_type', 'technical')
            ->pluck('skill_id')
            ->toArray();
        
        if (empty($requiredTechnicalSkillIds)) {
            return 100; // Pas de compétences techniques requises
        }
        
        // Compétences techniques du candidat
        $candidateTechnicalSkillIds = CandidateSkill::where('candidate_id', $candidateId)
            ->pluck('skill_id')
            ->toArray();
        
        // Calculer le pourcentage de correspondance
        $matchingSkills = array_intersect($requiredTechnicalSkillIds, $candidateTechnicalSkillIds);
        $matchPercentage = (count($matchingSkills) / count($requiredTechnicalSkillIds)) * 100;
        
        return round($matchPercentage, 2);
    }

    private function calculateGeneralSkillsScore(int $offerId, int $candidateId): float
    {
        // Compétences générales requises par l'offre
        $requiredGeneralSkillIds = JobOfferSkill::where('job_offer_id', $offerId)
            ->where('skill_type', 'general')
            ->pluck('skill_id')
            ->toArray();
        
        if (empty($requiredGeneralSkillIds)) {
            return 100; // Pas de compétences générales requises
        }
        
        // Compétences générales du candidat
        $candidateGeneralSkillIds = CandidateSkill::where('candidate_id', $candidateId)
            ->pluck('skill_id')
            ->toArray();
        
        // Calculer le pourcentage de correspondance
        $matchingSkills = array_intersect($requiredGeneralSkillIds, $candidateGeneralSkillIds);
        $matchPercentage = (count($matchingSkills) / count($requiredGeneralSkillIds)) * 100;
        
        return round($matchPercentage, 2);
    }

    private function calculateMatchScore(int $offerId, int $candidateId): float
    {
        $score = 0;

        // 1. Compatibilité compétences techniques (40%)
        $technicalSkillsPercentage = $this->calculateTechnicalSkillsPercentage($offerId, $candidateId);
        $score += $technicalSkillsPercentage * 0.40;

        // 2. Compatibilité compétences générales (40%)
        $generalSkillsScore = $this->calculateGeneralSkillsScore($offerId, $candidateId);
        $score += $generalSkillsScore * 0.40;

        // 3. Expérience (20%)
        $experienceScore = $this->calculateExperienceScore($offerId, $candidateId);
        $score += $experienceScore * 0.20;

        return round($score, 2);
    }
    
    /**
     * Ajoute la condition d'expérience selon l'intervalle
     */
    private function addExperienceCondition($query, ?array $experienceInterval): void
    {
        if ($experienceInterval === null) {
            // "Non exigée" → pas de condition d'expérience
            return;
        }
        
        $min = $experienceInterval['min'];
        $max = $experienceInterval['max'];
        
        if ($max === null) {
            // "5 ans et plus" → expérience >= 5
            $query->where('experience_years', '>=', $min);
        } elseif ($min === 0) {
            // "0-6 mois" → expérience entre 0 et 0.5
            $query->whereBetween('experience_years', [0, $max]);
        } elseif ($min === $max) {
            // "1 an" → expérience exactement 1 (ou >= 1 selon préférence)
            $query->where('experience_years', '>=', $min);
        } else {
            // "2-3 ans" → expérience entre 2 et 3
            $query->whereBetween('experience_years', [$min, $max]);
        }
    }
    
    /**
     * Calcule le score d'expérience basé sur l'intervalle
     */
 

    private function calculateExperienceScore(int $offerId, int $candidateId): float
{
    $offer = Offre::findOrFail($offerId);
    $candidate = User::findOrFail($candidateId);
    
    // Convertir l'expérience requise en intervalle
    $experienceInterval = $this->parseExperienceInterval($offer->required_experience ?? '0');
    $candidateExp = $candidate->candidateSector->experience_years ?? 0;
    
    // Si pas d'exigence d'expérience → score parfait
    if ($experienceInterval === null) {
        return 100;
    }
    
    $min = $experienceInterval['min'];
    $max = $experienceInterval['max'];
    
    // Cas 1: Intervalle fixe (ex: "2-3 ans")
    if ($max !== null) {
        if ($candidateExp >= $min && $candidateExp <= $max) {
            // Parfaitement dans l'intervalle
            return 100;
        }
        
        if ($candidateExp < $min) {
            // Moins que le minimum
            // Éviter la division par zéro
            if ($min == 0) {
                // Si le minimum est 0, alors le candidat avec 0 expérience est dans l'intervalle
                // On retourne un score proportionnel à l'expérience par rapport au maximum
                if ($max > 0) {
                    $ratio = $candidateExp / $max;
                    if ($ratio >= 0.9) return 80;
                    if ($ratio >= 0.7) return 60;
                    if ($ratio >= 0.5) return 40;
                    return 20;
                }
                return 20; // Fallback
            }
            
            $ratio = $candidateExp / $min;
            if ($ratio >= 0.9) return 80;    // Presque (90%+)
            if ($ratio >= 0.7) return 60;    // Proche (70%+)
            if ($ratio >= 0.5) return 40;    // Moitié (50%+)
            return 20;                       // Loin
        }
        
        // Plus que le maximum (surqualifié)
        // Éviter la division par zéro
        if ($max == 0) {
            // Si le maximum est 0, alors le candidat est surqualifié
            return 40;
        }
        
        $overqualifiedRatio = ($candidateExp - $max) / $max;
        if ($overqualifiedRatio <= 0.5) return 80;  // Un peu surqualifié (≤50% en plus)
        if ($overqualifiedRatio <= 1.0) return 60;  // Modérément surqualifié (≤100% en plus)
        return 40; // Très surqualifié
    }
    
    // Cas 2: Minimum seulement (ex: "5 ans et plus")
    if ($candidateExp >= $min) {
        // A au moins le minimum requis
        if ($min == 0) {
            // Si le minimum est 0, toutes les expériences sont acceptables
            return 100;
        }
        
        if ($candidateExp == $min) return 100;          // Exactement
        if ($candidateExp <= $min * 1.5) return 90;     // Un peu plus (≤50% en plus)
        if ($candidateExp <= $min * 2) return 80;       // Plus expérimenté (≤100% en plus)
        return 70; // Beaucoup plus expérimenté
    }
    
    // Moins que le minimum requis
    // Éviter la division par zéro
    if ($min == 0) {
        // Si le minimum est 0 mais le candidat a moins que 0 (impossible), retourner 0
        return 0;
    }
    
    $ratio = $candidateExp / $min;
    if ($ratio >= 0.9) return 80; // Presque (90%+)
    if ($ratio >= 0.7) return 60; // Proche (70%+)
    if ($ratio >= 0.5) return 40; // Moitié (50%+)
    return 20;                    // Loin
}
    
    /**
     * Lance le processus de matching automatique
     */
    public function processAutoMatching(): array
    {
        $results = [
            'processed_offers' => 0,
            'candidates_matched' => 0,
            'applications_created' => 0,
            'notifications_sent' => 0,
            'personalized_cvs_generated' => 0,
            'cover_letters_generated'  => 0,
            'errors' => []
        ];

        try {
            $activeOffers = $this->getActiveOffers();
            $results['processed_offers'] = $activeOffers->count();

            $activeOffers->each(function ($offerId) use (&$results) {
                $this->processOfferMatching($offerId, $results);
            });

            Log::info('Auto-matching terminé', $results);
            
        } catch (\Exception $e) {
            $results['errors'][] = $e->getMessage();
            Log::error('Erreur dans auto-matching', ['error' => $e->getMessage()]);
        }

        return $results;
    }

    /**
     * Traite le matching pour une offre spécifique
     */
    private function processOfferMatching(int $offerId, array &$results): void
    {
        $eligibleCandidates = $this->findEligibleCandidates($offerId);

        $results['candidates_details'] = [];  

        $eligibleCandidates->each(function ($candidate) use ($offerId, &$results) {
            // Formater les données du candidat
            $candidateData = $this->formatCandidateData($candidate);
            $results['candidates_details'][] = $candidateData;

            $matchScore = $this->calculateMatchScore($offerId, $candidate->id);

            if ($matchScore >= self::MIN_MATCH_SCORE) {
                if (!$this->hasAlreadyApplied($candidate->id, $offerId)) {
                    $this->createAutoApplication($candidate->id, $offerId, $matchScore, $results);
                }
            }
        });

        $results['candidates_matched'] += $eligibleCandidates->count();
    }

    /**
     * Récupère les IDs des offres actives et non expirées
     */
    private function getActiveOffers(): Collection
    {
        return Offre::where('status', 'active')
            ->where('date_fin', '>=', Carbon::today())
            ->pluck('id');
    }

    /**
     * Trouve les IDs des candidats éligibles pour une offre
     */
    private function findEligibleCandidates(int $offerId): Collection
    {
        $offer = Offre::with(['diplomes' => function($query) {
            $query->wherePivot('obligatoire', true);
        }])->findOrFail($offerId);
        
        $experienceInterval = $this->parseExperienceInterval($offer->required_experience ?? '0');
        
        // Récupérer les compétences techniques requises pour l'offre
        $requiredTechnicalSkillIds = JobOfferSkill::where('job_offer_id', $offerId)
            ->where('skill_type', 'technical')
            ->pluck('skill_id')
            ->toArray();
        
        $query = User::with([
                'cvProfile.formations',
                'cvProfile.competences',
                'cvProfile.experiences', 
                'cvProfile.langues',
                'cvProfile.perfectionnements',
                'cvProfile.benevolats',
                'cvProfile.cvGeneres',
                'candidateSector',
                'candidateSkills'
            ]);
        
        // Ajouter uniquement la condition d'expérience
        $query->whereHas('candidateSector', function ($query) use ($experienceInterval) {
            // Ajouter uniquement la condition d'expérience
            $this->addExperienceCondition($query, $experienceInterval);
        });

        // Filtrer par diplômes obligatoires s'il y en a
        if ($offer->diplomes->isNotEmpty()) {
            $requiredDiplomeIds = $offer->diplomes->pluck('id')->toArray();
            
            foreach ($requiredDiplomeIds as $diplomeId) {
                $query->whereHas('cvProfile.formations', function ($q) use ($diplomeId) {
                    $q->where('diplome_id', $diplomeId);
                });
            }
        }

        // Filtrer par compétences techniques si l'offre en a
        if (!empty($requiredTechnicalSkillIds)) {
            $minRequiredSkills = max(1, ceil(count($requiredTechnicalSkillIds) * 0.6)); // Minimum 60% des compétences
            
            $query->whereHas('candidateSkills', function ($q) use ($requiredTechnicalSkillIds, $minRequiredSkills) {
                $q->whereIn('skill_id', $requiredTechnicalSkillIds)
                  ->groupBy('candidate_id')
                  ->havingRaw('COUNT(DISTINCT skill_id) >= ?', [$minRequiredSkills]);
            });
        }

        return $query->get();
    }

    /**
     * Formate les données complètes d'un candidat pour l'utilisation
     */
    private function formatCandidateData(User $candidate): array
    {
        $cvProfile = $candidate->cvProfile;

        if (!$cvProfile) {
            return [
                'user_id' => $candidate->id,
                'error' => 'Aucun profil CV trouvé'
            ];
        }

        return [
            'user_id' => $candidate->id,
            'personal_info' => [
                'nom_complet' => $cvProfile->nom_complet,
                'email' => $cvProfile->email,
                'telephone' => $cvProfile->telephone,
                'adresse' => $cvProfile->adresse,
                'ville' => $cvProfile->ville,
                'code_postal' => $cvProfile->code_postal,
                'province' => $cvProfile->province,
            ],
            'formations' => $cvProfile->formations->map(function ($formation) {
                return [
                    'periode' => $formation->periode,
                    'diplome' => $formation->diplome,
                    'etablissement' => $formation->etablissement,
                    'ordre' => $formation->ordre
                ];
            }),
            'experiences' => $cvProfile->experiences->map(function ($experience) {
                return [
                    'periode' => $experience->periode,
                    'poste' => $experience->poste,
                    'entreprise' => $experience->entreprise,
                    'description' => $experience->description,
                    'ordre' => $experience->ordre
                ];
            }),
            'competences' => [
                'specifiques' => $cvProfile->competences->where('type', 'specifique')->values(),
                'generales' => $cvProfile->competences->where('type', 'generale')->values(),
                'logiciels' => $cvProfile->logiciels ? explode(',', $cvProfile->logiciels) : [],
                'langues_competences' => $cvProfile->langues_competences
            ],
            'langues' => $cvProfile->langues->map(function ($langue) {
                return [
                    'nom' => $langue->nom,
                    'niveau' => $langue->niveau,
                    'ordre' => $langue->ordre
                ];
            }),
            'perfectionnements' => $cvProfile->perfectionnements->map(function ($perfectionnement) {
                return [
                    'annee' => $perfectionnement->annee,
                    'formation' => $perfectionnement->formation,
                    'etablissement' => $perfectionnement->etablissement,
                    'ordre' => $perfectionnement->ordre
                ];
            }),
            'benevolats' => $cvProfile->benevolats->map(function ($benevolat) {
                return [
                    'periode' => $benevolat->periode,
                    'role' => $benevolat->role,
                    'organisation' => $benevolat->organisation,
                    'ordre' => $benevolat->ordre
                ];
            }),
            'cv_generes' => $cvProfile->cvGeneres->map(function ($cvGenere) {
                return [
                    'nom_fichier' => $cvGenere->nom_fichier,
                    'chemin_fichier' => $cvGenere->chemin_fichier,
                    'url_fichier' => $cvGenere->url_fichier
                ];
            }),
            'experience_info' => $candidate->candidateSector ? [
                'experience_years' => $candidate->candidateSector->experience_years
            ] : null
        ];
    }

    private function hasAlreadyApplied(int $candidateId, int $offerId): bool
    {
        return Postulation::where('user_id', $candidateId)
            ->where('offre_id', $offerId)
            ->exists();
    }

    /**
     * Crée une postulation automatique avec CV personnalisé
     */
    private function createAutoApplication(int $candidateId, int $offerId, float $matchScore, array &$results): void
    {
        $candidate = User::findOrFail($candidateId);
        $offer = Offre::with('entreprise.user')->findOrFail($offerId);

        // Générer le CV personnalisé
        $personalizedCVPath = $this->generatePersonalizedCV($candidateId, $offerId);
        
        if ($personalizedCVPath) {
            $results['personalized_cvs_generated']++;
        }


         // Générer la lettre de motivation
    $coverLetterPath = $this->generateCoverLetter($candidateId, $offerId);
    
    if ($coverLetterPath) {
        $results['cover_letters_generated']++;
    }


        $matchDetails = [
            'technical_skills_match' => $this->calculateTechnicalSkillsPercentage($offerId, $candidateId),
            'general_skills_match' => $this->calculateGeneralSkillsScore($offerId, $candidateId),
            'experience_match' => $this->calculateExperienceScore($offerId, $candidateId),
            'algorithm_version' => '3.0',
            'personalized_cv_generated' => !empty($personalizedCVPath),
             'cover_letter_generated' => !empty($coverLetterPath)
        ];
            
        // Créer la postulation
        $postulation = Postulation::create([
                'user_id' => $candidateId,
                'offre_id' => $offerId,
                'match_score' => $matchScore,
                'application_date' => now(),
                'status' => 'en_attente', 
                'autopostulation' => true,
                'cv' => $personalizedCVPath ?? $candidate->cv,
                'cover_letter' => $coverLetterPath,  
                'algorithm_version' => '3.0',
                'match_details' => json_encode($matchDetails)
            ]);

        // Notification pour le CANDIDAT
        Notification::create([
            'user_id' => $candidateId,  
            'role' => 'candidat',
            'title' => 'Candidature Automatique',
            'message' => 'Votre profil a été proposé pour l\'offre "' . $offer->titre . '"',
            'link' => "/user/historique-candidature_ia",
            'is_read' => false,
        ]);

        // Notification pour l'ENTREPRISE
        if ($offer->entreprise && $offer->entreprise->user) {
            Notification::create([
                'user_id' => $offer->entreprise->user->id,
                'role' => 'entreprise',  
                'title' => 'Nouvelle candidature ',
                'message' => 'Un candidat a postulé à votre offre "' . $offer->titre . '"',
                'link' => "/entreprise/offres/{$offerId}/candidatures",  
                'is_read' => false,
            ]);
        }

        $results['applications_created']++;
        $results['notifications_sent'] += 2;

        Log::info('Postulation automatique créée avec CV personnalisé', [
            'candidate_id' => $candidateId,
            'offer_id' => $offerId,
            'score' => $matchScore,
            'personalized_cv' => !empty($personalizedCVPath)
        ]);
    }

    /**
     * Génère un CV personnalisé avec Gemini
     */
    private function generatePersonalizedCV(int $candidateId, int $offerId): ?string
    {
        try {
            $cvProfile = CvProfile::with([
                'formations',
                'competences', 
                'experiences'
            ])->where('user_id', $candidateId)->first();

            if (!$cvProfile) {
                Log::warning('Aucun profil CV trouvé', ['candidate_id' => $candidateId]);
                return null;
            }

            $offer = Offre::findOrFail($offerId);
            
            $promptData = $this->preparePromptDataForGemini($cvProfile->id, $offer->id);
            $cvHtml = $this->generateCVWithGemini($promptData);
            
            if (!$cvHtml) {
                Log::warning('Échec Gemini, utilisation template classique', [
                    'candidate_id' => $candidateId,
                    'offer_id' => $offerId
                ]);
                return $this->generateClassicCV($cvProfile, $offer);
            }
            
            // Convertir en PDF
            $pdf = PDF::loadHTML($cvHtml);
            $filename = 'cv_gemini_' . $cvProfile->id . '_' . $offer->id . '_' . time() . '.pdf';
            $filePath = 'personalized-cvs/' . $filename;
            
            Storage::disk('public')->put($filePath, $pdf->output());
            
            Log::info('CV Gemini généré avec succès', [
                'candidate_id' => $candidateId,
                'file_path' => $filePath
            ]);
            
            return $filePath;
            
        } catch (\Exception $e) {
            Log::error('Erreur génération CV', [
                'candidate_id' => $candidateId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Version ultra-rapide  
     */
    private function generateUltraSimpleCV(CvProfile $cvProfile, Offre $offer): ?string
    {
        try {
            $prompt = "CV HTML pour {$cvProfile->prenom} {$cvProfile->nom} - {$offer->poste}. Style professionnel. HTML seulement.";
            
            $response = Prism::text()
                ->using(Provider::Gemini, 'gemini-2.5-flash')
                ->withPrompt($prompt)
                ->generate();

            return trim($response->text);
            
        } catch (\Exception $e) {
            Log::warning('Échec version ultra-simple', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Fallback - Génère un CV avec le template classique
     */
    private function generateClassicCV(CvProfile $cvProfile, Offre $offer): ?string
    {
        try {
            $personalizedData = $this->personalizeCVData($cvProfile, $offer);
            $pdf = PDF::loadView('cv.personalized-template', $personalizedData);
            
            $filename = 'cv_classic_' . $cvProfile->prenom . '_' . $cvProfile->nom . '_' . $offer->id . '_' . time() . '.pdf';
            $filePath = 'personalized-cvs/' . $filename;
            
            Storage::disk('public')->put($filePath, $pdf->output());
            
            return $filePath;
            
        } catch (\Exception $e) {
            Log::error('Erreur génération CV classique', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Prépare les données pour le prompt Gemini
     */
    private function preparePromptDataForGemini(int $cvProfileId, int $offerId): array
    {
        // Charger le CV Profile avec toutes les relations
        $cvProfile = CvProfile::with([
            'formations' => function($query) {
                $query->orderBy('ordre')->orderBy('created_at');
            },
            'experiences' => function($query) {
                $query->orderBy('ordre')->orderBy('created_at', 'desc');
            },
            'competences' => function($query) {
                $query->orderBy('type')->orderBy('created_at');
            },
            'langues' => function($query) {
                $query->orderBy('ordre')->orderBy('created_at');
            },
            'perfectionnements' => function($query) {
                $query->orderBy('ordre')->orderBy('created_at', 'desc');
            },
            'benevolats' => function($query) {
                $query->orderBy('ordre')->orderBy('created_at', 'desc');
            }
        ])->findOrFail($cvProfileId);

        // CORRECTION : Charger l'offre avec la relation many-to-many 'diplomes'
        $offer = Offre::with([
            'diplomes',
            'skills.skill',
            'entreprise'
        ])->findOrFail($offerId);

        // CORRECTION : Récupérer tous les noms de diplômes
        $diplomesNoms = $offer->diplomes->pluck('nom_diplome')->toArray();

        return [
            'candidate' => [
                'personal_info' => [
                    'nom' => $cvProfile->nom,
                    'prenom' => $cvProfile->prenom,
                    'email' => $cvProfile->email,
                    'telephone' => $cvProfile->telephone,
                    'adresse' => $cvProfile->adresse,
                    'ville' => $cvProfile->ville,
                    'code_postal' => $cvProfile->code_postal,
                    'province' => $cvProfile->province,
                ],
                'formations' => $cvProfile->formations->map(function ($formation) {
                    return [
                        'periode' => $formation->periode,
                        'diplome' => $formation->diplome,
                        'etablissement' => $formation->etablissement,
                        'description' => $formation->description,
                        'ordre' => $formation->ordre
                    ];
                })->toArray(),
                'experiences' => $cvProfile->experiences->map(function ($experience) {
                    return [
                        'periode' => $experience->periode,
                        'poste' => $experience->poste,
                        'entreprise' => $experience->entreprise,
                        'description' => $experience->description,
                        'ordre' => $experience->ordre
                    ];
                })->toArray(),
                'competences' => [
                    'specifiques' => $cvProfile->competences->where('type', 'specifique')
                        ->pluck('description')
                        ->toArray(),
                    'generales' => $cvProfile->competences->where('type', 'generale')
                        ->pluck('description')
                        ->toArray(),
                    'logiciels' => $cvProfile->logiciels ? 
                        array_map('trim', explode(',', $cvProfile->logiciels)) : [],
                    'langues_competences' => $cvProfile->langues_competences
                ],
                'langues' => $cvProfile->langues->map(function ($langue) {
                    return [
                        'nom' => $langue->nom,
                        'niveau' => $langue->niveau,
                        'ordre' => $langue->ordre
                    ];
                })->toArray(),
                'perfectionnements' => $cvProfile->perfectionnements->map(function ($perfectionnement) {
                    return [
                        'annee' => $perfectionnement->annee,
                        'formation' => $perfectionnement->formation,
                        'etablissement' => $perfectionnement->etablissement,
                        'description' => $perfectionnement->description,
                        'ordre' => $perfectionnement->ordre
                    ];
                })->toArray(),
                'benevolats' => $cvProfile->benevolats->map(function ($benevolat) {
                    return [
                        'periode' => $benevolat->periode,
                        'role' => $benevolat->role,
                        'organisation' => $benevolat->organisation,
                        'description' => $benevolat->description,
                        'ordre' => $benevolat->ordre
                    ];
                })->toArray(),
                'autres_informations' => [
                    'objectif_professionnel' => $cvProfile->objectif_professionnel,
                    'autres_competences' => $cvProfile->autres_competences,
                    'interets' => $cvProfile->interets,
                ]
            ],
            'offer' => [
                'titre' => $offer->titre,
                'poste' => $offer->poste,
                'description' => $offer->description,
                'competences_requises' => $offer->competences,
                'langues_requises' => $offer->langues,
                'diplomes_requis' => $diplomesNoms, // CORRECTION : Tableau de diplômes
                'annee_experience' => $offer->annee_experience,
                'missions' => $offer->missions,
                'criteres' => $offer->criteres,
                'entreprise' => [
                    'nom' => $offer->entreprise->name ?? null,
                    'description' => $offer->entreprise->description ?? null,
                ],
                'skills_detailed' => $offer->skills->map(function ($jobOfferSkill) {
                    return [
                        'skill_name' => $jobOfferSkill->skill->name ?? null,
                        'weight' => $jobOfferSkill->weight,
                        'level' => $jobOfferSkill->level,
                    ];
                })->toArray(),
                'avantages' => $offer->avantages,
                'type_contrat' => $offer->type_contrat,
                'salaire' => $offer->salaire,
                'lieu_travail' => $offer->lieu_travail,
            ]
        ];
    }

    /**
     * Prépare les données pour Gemini avec des données manuelles
     */
    private function preparePromptDataForManualCustomization(int $cvProfileId, array $offerData): array
    {
        // Charger le CV Profile avec toutes les relations
        $cvProfile = CvProfile::with([
            'formations' => function($query) {
                $query->orderBy('ordre')->orderBy('created_at');
            },
            'experiences' => function($query) {
                $query->orderBy('ordre')->orderBy('created_at', 'desc');
            },
            'competences' => function($query) {
                $query->orderBy('type')->orderBy('created_at');
            },
            'langues' => function($query) {
                $query->orderBy('ordre')->orderBy('created_at');
            },
            'perfectionnements' => function($query) {
                $query->orderBy('ordre')->orderBy('created_at', 'desc');
            },
            'benevolats' => function($query) {
                $query->orderBy('ordre')->orderBy('created_at', 'desc');
            }
        ])->findOrFail($cvProfileId);

        return [
            'candidate' => [
                'personal_info' => [
                    'nom' => $cvProfile->nom,
                    'prenom' => $cvProfile->prenom,
                    'email' => $cvProfile->email,
                    'telephone' => $cvProfile->telephone,
                    'adresse' => $cvProfile->adresse,
                    'ville' => $cvProfile->ville,
                    'code_postal' => $cvProfile->code_postal,
                    'province' => $cvProfile->province,
                ],
                'formations' => $cvProfile->formations->map(function ($formation) {
                    return [
                        'periode' => $formation->periode,
                        'diplome' => $formation->diplome,
                        'etablissement' => $formation->etablissement,
                        'description' => $formation->description,
                    ];
                })->toArray(),
                'experiences' => $cvProfile->experiences->map(function ($experience) {
                    return [
                        'periode' => $experience->periode,
                        'poste' => $experience->poste,
                        'entreprise' => $experience->entreprise,
                        'description' => $experience->description,
                    ];
                })->toArray(),
                'competences' => [
                    'specifiques' => $cvProfile->competences->where('type', 'specifique')
                        ->pluck('description')
                        ->toArray(),
                    'generales' => $cvProfile->competences->where('type', 'generale')
                        ->pluck('description')
                        ->toArray(),
                    'logiciels' => $cvProfile->logiciels ? 
                        array_map('trim', explode(',', $cvProfile->logiciels)) : [],
                    'langues_competences' => $cvProfile->langues_competences
                ],
                'langues' => $cvProfile->langues->map(function ($langue) {
                    return [
                        'nom' => $langue->nom,
                        'niveau' => $langue->niveau,
                    ];
                })->toArray(),
                'perfectionnements' => $cvProfile->perfectionnements->map(function ($perfectionnement) {
                    return [
                        'annee' => $perfectionnement->annee,
                        'formation' => $perfectionnement->formation,
                        'etablissement' => $perfectionnement->etablissement,
                    ];
                })->toArray(),
                'benevolats' => $cvProfile->benevolats->map(function ($benevolat) {
                    return [
                        'periode' => $benevolat->periode,
                        'role' => $benevolat->role,
                        'organisation' => $benevolat->organisation,
                    ];
                })->toArray(),
            ],
            'offer' => [
                'titre' => $offerData['offer_title'],
                'poste' => $offerData['offer_title'],
                'description' => $offerData['offer_details'],
                'competences_requises' => $offerData['key_requirements'] ?? '',
                'entreprise' => [
                    'nom' => $offerData['company_name'] ?? 'Entreprise Cible'
                ],
                'template_style' => $offerData['template_style'] ?? 'modern',
                // NOUVEAUX CHAMPS POUR LA GESTION DES DIPLÔMES
                'diplome_requis' => $offerData['required_diploma'] ?? null,
                'masquer_diplomes_superieurs' => $offerData['hide_higher_degrees'] ?? false
            ],
            'customization_instructions' => [
                'hide_higher_degrees' => $offerData['hide_higher_degrees'] ?? false,
                'required_diploma_level' => $offerData['required_diploma'] ?? null,
                'focus_on_relevant_experience' => $offerData['focus_relevant'] ?? true,
                'simplify_language' => $offerData['simplify_language'] ?? false
            ]
        ];
    }
    
    /**
     * Génère le CV HTML avec Gemini
     */
 private function generateCVWithGemini(array $promptData): ?string
{
    try {
        $prompt = $this->buildCVPrompt($promptData);
        Log::info('Envoi prompt Gemini', [
            'longueur' => strlen($prompt),
            'candidate' => $promptData['candidate']['personal_info']['prenom'] . ' ' . $promptData['candidate']['personal_info']['nom']
        ]);

        $response = Prism::text()
            ->using(Provider::Gemini, 'gemini-2.5-flash')
            ->withPrompt($prompt)
            ->generate();

        $result = trim($response->text);
        // Nettoyer la réponse
        $result = $this->cleanGeminiResponse($result);

        // Log un extrait du résultat pour débogage
        Log::info('Réponse Gemini (extrait)', ['extrait' => substr($result, 0, 500)]);

        if (empty($result) || strlen($result) < 500) {
            Log::error('Réponse Gemini trop courte après nettoyage', ['longueur' => strlen($result)]);
            return null;
        }

        return $result;
    } catch (\Exception $e) {
        Log::error('Erreur Gemini', ['error' => $e->getMessage()]);
        return null;
    }
}

    /**
     * Nettoie la réponse de Gemini
     */
 private function cleanGeminiResponse(string $html): string
{
    // Log le contenu brut avant nettoyage
    Log::info('Contenu brut Gemini avant nettoyage', [
        'preview' => substr($html, 0, 500),
        'longueur' => strlen($html)
    ]);
    
    // Retirer les backticks de code
    $html = preg_replace('/```html\s*/', '', $html);
    $html = preg_replace('/```\s*/', '', $html);
    
    // Retirer les éventuels préfixes de texte
    $html = preg_replace('/^.*?<html/is', '<html', $html);
    $html = preg_replace('/^.*?<\!DOCTYPE/is', '<!DOCTYPE', $html);
    
    // Log après nettoyage
    Log::info('Contenu Gemini après nettoyage', [
        'preview' => substr($html, 0, 500),
        'longueur' => strlen($html)
    ]);
    
    // Si toujours trop court, générer un HTML minimal
    if (strlen($html) < 500) {
        Log::warning('Réponse Gemini insuffisante, génération fallback');
        return $this->generateFallbackHTML();
    }
    
    return trim($html);
}

private function generateFallbackHTML(): string
{
    return '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>CV Personnalisé</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; }
            .header { text-align: center; margin-bottom: 30px; }
            .section { margin-bottom: 20px; }
            h1 { color: #333; }
            h2 { color: #666; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>CV Personnalisé</h1>
            <p>Généré automatiquement par notre système</p>
        </div>
        <div class="section">
            <p>Ce CV a été personnalisé pour correspondre aux exigences du poste.</p>
            <p>Les détails complets sont disponibles dans le profil du candidat.</p>
        </div>
    </body>
    </html>';
}

    /**
     * Construit le prompt pour Gemini avec gestion des diplômes
     */
 private function buildCVPrompt(array $promptData): string
{
    $candidate = $promptData['candidate'];
    $offer = $promptData['offer'];
    
    $prompt = "Génère un CV HTML professionnel pour {$candidate['personal_info']['prenom']} {$candidate['personal_info']['nom']} qui postule au poste de {$offer['poste']} chez {$offer['entreprise']['nom']}.

INSTRUCTIONS CRITIQUES :
1. Retourne UNIQUEMENT du code HTML COMPLET (avec <!DOCTYPE html>, <html>, <head>, <body>)
2. Style CSS intégré dans la balise <style>
3. Format A4, police professionnelle (Arial, sans-serif)
4. Maximum 2 pages

CONTENU REQUIS dans le HTML :

SECTION 1: En-tête
- Nom complet: {$candidate['personal_info']['prenom']} {$candidate['personal_info']['nom']}
- Email: {$candidate['personal_info']['email']}
- Téléphone: {$candidate['personal_info']['telephone']}
- Adresse: {$candidate['personal_info']['adresse']}

SECTION 2: Objectif professionnel
- Adapter pour le poste: {$offer['poste']}
- Mentionner l'entreprise: {$offer['entreprise']['nom']}

SECTION 3: Expériences professionnelles
" . $this->formatExperiencesForPrompt($candidate['experiences']) . "

SECTION 4: Formations
" . $this->formatFormationsForPrompt($candidate['formations']) . "

SECTION 5: Compétences pertinentes pour ce poste
Compétences requises par l'offre: " . implode(', ', $offer['competences_requises']) . "

IMPORTANT: 
- Structure HTML sémantique avec des balises appropriées (h1, h2, p, ul, li)
- Ne pas inclure de commentaires, seulement du HTML
- Le CV doit être prêt pour impression PDF

CODE HTML:";

    return $prompt;
}

private function formatExperiencesForPrompt(array $experiences): string
{
    $formatted = '';
    foreach ($experiences as $exp) {
        $formatted .= "- {$exp['poste']} chez {$exp['entreprise']} ({$exp['periode']})\n";
    }
    return $formatted;
}

private function formatFormationsForPrompt(array $formations): string
{
    $formatted = '';
    foreach ($formations as $formation) {
        $formatted .= "- {$formation['diplome']} à {$formation['etablissement']} ({$formation['periode']})\n";
    }
    return $formatted;
}
    public function testCompleteCVGeneration(int $candidateId, int $offerId): array
    {
        try {
            $cvProfile = CvProfile::with([
                'formations',
                'competences', 
                'experiences',
                'langues',
                'perfectionnements',
                'benevolats'
            ])->where('user_id', $candidateId)->first();

            if (!$cvProfile) {
                return ['error' => 'Profil CV non trouvé'];
            }

            // CORRECTION : Charger 'diplomes' au lieu de 'diplome'
            $offer = Offre::with(['skills.skill', 'diplomes'])->findOrFail($offerId);
            
            // CORRECTION : Passer les IDs au lieu des objets
            $promptData = $this->preparePromptDataForGemini($cvProfile->id, $offer->id);
            
            // Log des données pour inspection
            $debugData = [
                'candidate' => [
                    'name' => $cvProfile->prenom . ' ' . $cvProfile->nom,
                    'formations_count' => count($promptData['candidate']['formations']),
                    'experiences_count' => count($promptData['candidate']['experiences']),
                    'competences_count' => count($promptData['candidate']['competences']['specifiques']) + count($promptData['candidate']['competences']['generales'])
                ],
                'offer' => [
                    'poste' => $offer->poste,
                    'titre' => $offer->titre,
                    'competences_requises' => $offer->competences
                ]
            ];
            
            $cvHtml = $this->generateCVWithGemini($promptData);
            
            if (!$cvHtml) {
                return [
                    'success' => false,
                    'error' => 'Échec génération Gemini',
                    'debug_data' => $debugData
                ];
            }

            return [
                'success' => true,
                'cv_html' => $cvHtml,
                'cv_length' => strlen($cvHtml),
                'debug_data' => $debugData
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Génère un CV personnalisé avec Gemini sans créer de postulation  
     */
    public function generateCVForCandidate(int $candidateId, int $offerId): array
    {
        try {
            $cvProfile = CvProfile::with([
                'formations',
                'competences', 
                'experiences',
                'langues',
                'perfectionnements',
                'benevolats'
            ])->where('user_id', $candidateId)->first();

            if (!$cvProfile) {
                return ['error' => 'Aucun profil CV trouvé'];
            }

            // CORRECTION : Charger 'diplomes' au lieu de 'diplome'
            $offer = Offre::with(['skills.skill', 'diplomes'])->findOrFail($offerId);
            
            // CORRECTION : Passer les IDs au lieu des objets
            $promptData = $this->preparePromptDataForGemini($cvProfile->id, $offer->id);
            $cvHtml = $this->generateCVWithGemini($promptData);
            
            if (!$cvHtml) {
                return ['error' => 'Échec de génération avec Gemini'];
            }

            return [
                'success' => true,
                'candidate_name' => $cvProfile->prenom . ' ' . $cvProfile->nom,
                'offer_title' => $offer->titre,
                'cv_html' => $cvHtml,
                'prompt_data' => $promptData // Pour debug
            ];
            
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Personnalise les données du CV selon l'offre
     */
    private function personalizeCVData(CvProfile $cvProfile, Offre $offer): array
    {
        $data = [
            'nom' => $cvProfile->nom,
            'prenom' => $cvProfile->prenom,
            'email' => $cvProfile->email,
            'telephone' => $cvProfile->telephone,
            'adresse' => $cvProfile->adresse,
            'ville' => $cvProfile->ville,
            'code_postal' => $cvProfile->code_postal,
            'province' => $cvProfile->province,
            'langues_competences' => $cvProfile->langues_competences,
            'logiciels' => $cvProfile->logiciels,
            'formations' => $cvProfile->formations,
            'competences' => $cvProfile->competences,
            'experiences' => $cvProfile->experiences,
            'langues' => $cvProfile->langues,
            'perfectionnements' => $cvProfile->perfectionnements,
            'benevolats' => $cvProfile->benevolats,
            'offer_title' => $offer->titre,
            'personalization_note' => $this->generatePersonalizationNote($cvProfile, $offer)
        ];

        // Réorganiser les compétences selon les besoins de l'offre
        if ($offer->skills->isNotEmpty()) {
            $data['competences'] = $this->prioritizeSkills($data['competences'], $offer);
        }

        // Réorganiser les expériences selon la pertinence
        $data['experiences'] = $this->prioritizeExperiences($data['experiences'], $offer);

        return $data;
    }

    /**
     * Priorise les compétences selon les besoins de l'offre
     */
    private function prioritizeSkills(Collection $competences, Offre $offer): Collection
    {
        $requiredSkillIds = $offer->skills->pluck('skill_id')->toArray();
        
        return $competences->sortByDesc(function ($competence) use ($requiredSkillIds) {
            // Ici vous pouvez implémenter une logique de matching des compétences
            // Pour l'instant, on garde l'ordre original
            return 1;
        });
    }

    /**
     * Priorise les expériences selon la pertinence pour l'offre
     */
    private function prioritizeExperiences(Collection $experiences, Offre $offer): Collection
    {
        return $experiences->sortByDesc(function ($experience) {
            // Prioriser les expériences les plus récentes
            return $experience->created_at;
        });
    }

    /**
     * Génère une note de personnalisation pour le CV
     */
    private function generatePersonalizationNote(CvProfile $cvProfile, Offre $offer): string
    {
        return " " . $offer->titre;
    }

    /**
     * Génère un CV personnalisé pour un utilisateur (hors processus automatique)
     */
    public function generatePersonalizedCVForUser(CvProfile $cvProfile, $virtualOffer, int $userId): array
    {
        try {
            // Utiliser la méthode adaptée pour les données manuelles
            $promptData = $this->preparePromptDataForManualCustomization($cvProfile->id, [
                'offer_title' => $virtualOffer->titre,
                'offer_details' => $virtualOffer->description,
                'key_requirements' => $virtualOffer->competences ?? '',
                'company_name' => $virtualOffer->entreprise->name ?? 'Entreprise Cible',
                'template_style' => 'modern'
            ]);
            
            // Générer le CV avec Gemini
            $cvHtml = $this->generateCVWithGemini($promptData);
            
            if (!$cvHtml) {
                // Fallback sur template classique
                $cvHtml = $this->generateFallbackCV($cvProfile, $virtualOffer);
            }

            // Convertir en PDF
            $pdf = PDF::loadHTML($cvHtml)
                ->setPaper('a4')
                ->setOption('dpi', 150)
                ->setOption('defaultFont', 'Arial');

            $filename = 'cv_perso_' . $userId . '_' . time() . '.pdf';
            $filePath = 'personalized-cvs/' . $filename;
            
            Storage::disk('public')->put($filePath, $pdf->output());

            // Log de l'activité
            Log::info('CV personnalisé généré manuellement', [
                'user_id' => $userId,
                'offer_title' => $virtualOffer->titre,
                'filename' => $filename
            ]);

            return [
                'success' => true,
                'filename' => $filename,
                'file_path' => $filePath,
                'file_url' => Storage::disk('public')->url($filePath)
            ];

        } catch (\Exception $e) {
            Log::error('Erreur génération CV manuel', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'error' => 'Impossible de générer le CV personnalisé. Veuillez réessayer.'
            ];
        }
    }




    /**
 * Génère une lettre de motivation personnalisée avec Gemini
 */
private function generateCoverLetter(int $candidateId, int $offerId): ?string
{
    try {
        $cvProfile = CvProfile::with([
            'formations',
            'experiences',
            'competences'
        ])->where('user_id', $candidateId)->first();

        if (!$cvProfile) {
            Log::warning('Aucun profil CV trouvé pour la lettre de motivation', ['candidate_id' => $candidateId]);
            return null;
        }

        // Charger l'offre avec l'entreprise
        $offer = Offre::with(['entreprise'])->findOrFail($offerId);
        
        // Construire le prompt pour la lettre de motivation avec toutes les informations
        $prompt = $this->buildCoverLetterPrompt($cvProfile, $offer);
        
        // Appeler Gemini
        $response = Prism::text()
            ->using(Provider::Gemini, 'gemini-2.5-flash')
            ->withPrompt($prompt)
            ->generate();

        $coverLetterContent = trim($response->text);
        
        // Nettoyer la réponse
        $coverLetterContent = $this->cleanGeminiResponse($coverLetterContent);
        
        // Vérifier que la réponse contient du HTML
        if (strlen($coverLetterContent) < 200 || !str_contains($coverLetterContent, '<html')) {
            Log::warning('Réponse de lettre de motivation trop courte ou invalide, génération fallback', [
                'length' => strlen($coverLetterContent)
            ]);
            $coverLetterContent = $this->generateBasicCoverLetter($cvProfile, $offer);
        }
        
        // Convertir en PDF
        $pdf = PDF::loadHTML($coverLetterContent)
            ->setPaper('a4')
            ->setOption('dpi', 150);
        
        $filename = 'lettre_motivation_' . $cvProfile->prenom . '_' . $cvProfile->nom . '_' . $offerId . '_' . time() . '.pdf';
        $filePath = 'cover-letters/' . $filename;
        
        Storage::disk('public')->put($filePath, $pdf->output());
        
        Log::info('Lettre de motivation générée avec succès', [
            'candidate_id' => $candidateId,
            'file_path' => $filePath,
            'prompt_length' => strlen($prompt),
            'response_length' => strlen($coverLetterContent)
        ]);
        
        return $filePath;
        
    } catch (\Exception $e) {
        Log::error('Erreur génération lettre de motivation', [
            'candidate_id' => $candidateId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return null;
    }
}

/**
 * Construit le prompt pour la lettre de motivation
 */
private function buildCoverLetterPrompt(CvProfile $cvProfile, Offre $offer): string
{
    $nomComplet = $cvProfile->prenom . ' ' . $cvProfile->nom;
    $entrepriseNom = $offer->entreprise->company_name ?? 'l\'entreprise';
    $poste = $offer->poste ?? $offer->titre;
    
    // Description de l'entreprise
    $entrepriseDescription = $offer->entreprise->description ?? '';
    
    // Description détaillée de l'offre
    $descriptionOffre = $offer->description ?? '';
    $missionsOffre = $offer->missions ?? '';
    $competencesRequises = $offer->competences ?? '';
    
    // Compétences du candidat
    $competencesCandidat = [];
    if ($cvProfile->competences) {
        foreach ($cvProfile->competences as $competence) {
            $competencesCandidat[] = $competence->description;
        }
    }
    
    // Expériences du candidat
    $experiencesCandidat = [];
    if ($cvProfile->experiences) {
        foreach ($cvProfile->experiences as $experience) {
            $experiencesCandidat[] = [
                'poste' => $experience->poste,
                'entreprise' => $experience->entreprise,
                'periode' => $experience->periode,
                'description' => $experience->description
            ];
        }
    }
    
    // Formations du candidat
    $formationsCandidat = [];
    if ($cvProfile->formations) {
        foreach ($cvProfile->formations as $formation) {
            $formationsCandidat[] = [
                'diplome' => $formation->diplome,
                'etablissement' => $formation->etablissement,
                'periode' => $formation->periode
            ];
        }
    }
    
    // Trouver les correspondances entre compétences requises et compétences du candidat
    $correspondances = [];
    if ($competencesRequises && !empty($competencesCandidat)) {
        $competencesRequisesArray = array_map('trim', explode(',', $competencesRequises));
        foreach ($competencesRequisesArray as $competenceRequise) {
            foreach ($competencesCandidat as $competenceCand) {
                if (stripos($competenceCand, $competenceRequise) !== false || 
                    stripos($competenceRequise, $competenceCand) !== false) {
                    $correspondances[] = $competenceCand;
                }
            }
        }
    }
    
    // Formater les informations du candidat
    $formattedCompetences = implode(', ', array_slice($competencesCandidat, 0, 5));
    $formattedCorrespondances = !empty($correspondances) ? 
        "Compétences correspondant aux exigences: " . implode(', ', array_slice($correspondances, 0, 3)) : 
        "Compétences pertinentes: " . $formattedCompetences;
    
    $formattedExperiences = "";
    foreach (array_slice($experiencesCandidat, 0, 2) as $exp) {
        $formattedExperiences .= "• {$exp['poste']} chez {$exp['entreprise']} ({$exp['periode']}) - {$exp['description']}\n";
    }
    
    $formattedFormations = "";
    foreach (array_slice($formationsCandidat, 0, 2) as $formation) {
        $formattedFormations .= "• {$formation['diplome']} - {$formation['etablissement']} ({$formation['periode']})\n";
    }
    
    // Construire le prompt
    $prompt = "Génère une lettre de motivation professionnelle en français pour $nomComplet.

        INFORMATIONS PERSONNELLES:
        - Nom complet: $nomComplet
        - Email: {$cvProfile->email}
        - Téléphone: {$cvProfile->telephone}
        - Adresse: {$cvProfile->adresse}, {$cvProfile->ville} {$cvProfile->code_postal}

        INFORMATIONS SUR LE POSTE VISÉ:
        - Titre du poste: $poste
        - Entreprise: $entrepriseNom
        - Description du poste: $descriptionOffre
        - Missions principales: $missionsOffre
        - Compétences requises: $competencesRequises

        INFORMATIONS SUR L'ENTREPRISE:
        $entrepriseDescription

        PROFIL DU CANDIDAT:
        $formattedCorrespondances

        EXPÉRIENCES PROFESSIONNELLES PERTINENTES:
        $formattedExperiences

        FORMATIONS PERTINENTES:
        $formattedFormations
        
        INSTRUCTIONS SPÉCIFIQUES POUR LA LETTRE:
        1. Format: Lettre formelle professionnelle en français
        2. Structure: 
        - En-tête avec coordonnées du candidat
        - Date et coordonnées de l'entreprise
        - Objet clair mentionnant le poste
        - Salutation formelle
        - Paragraphe d'introduction exprimant l'intérêt pour le poste et l'entreprise
        - Paragraphe sur les compétences et expériences pertinentes
        - Paragraphe sur l'adéquation avec l'entreprise et sa mission
        - Paragraphe de conclusion avec appel à l'entretien
        - Formule de politesse standard
        - Signature
        3. Style: Ton professionnel, convaincant et enthousiaste
        4. Personnalisation: Mentionner spécifiquement le poste ($poste) et l'entreprise ($entrepriseNom)
        5. Intégration: Faire référence à des éléments spécifiques de la description de l'entreprise et du poste
        6. Longueur: Maximum 1 page A4 (environ 400-500 mots)
        7. Originalité: Éviter les phrases génériques, personnaliser en fonction des informations fournies

        IMPORTANT: Utiliser les informations sur l'entreprise ($entrepriseNom) et sa description ($entrepriseDescription) pour montrer une réelle compréhension et intérêt pour l'entreprise.

        Génère uniquement le contenu HTML de la lettre avec un style professionnel, sans commentaires ni ```html. Le HTML doit être complet et prêt pour conversion en PDF.";

            return $prompt;
}

/**
 * Génère une lettre de motivation basique (fallback)
 */
 private function generateBasicCoverLetter(CvProfile $cvProfile, Offre $offer): string
{
    $date = Carbon::now()->format('d/m/Y');
    $nomComplet = $cvProfile->prenom . ' ' . $cvProfile->nom;
    $entrepriseNom = $offer->entreprise->company_name ?? 'Madame, Monsieur';
    $poste = $offer->poste ?? $offer->titre;
    
    // HTML minimaliste pour la lettre de motivation
    $html = <<<HTML
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Lettre de Motivation - {$nomComplet}</title>
                <style>
                    body {
                        font-family: 'Helvetica Neue', Arial, sans-serif;
                        line-height: 1.6;
                        color: #333;
                        margin: 0;
                        padding: 20px;
                        max-width: 800px;
                        margin: 0 auto;
                    }
                    .header {
                        display: flex;
                        justify-content: space-between;
                        margin-bottom: 40px;
                        border-bottom: 2px solid #4a90e2;
                        padding-bottom: 20px;
                    }
                    .sender-info {
                        width: 60%;
                    }
                    .date {
                        width: 35%;
                        text-align: right;
                        color: #666;
                    }
                    .recipient {
                        margin: 40px 0;
                    }
                    .subject {
                        font-weight: bold;
                        margin: 30px 0;
                        color: #4a90e2;
                        font-size: 18px;
                    }
                    .content {
                        margin: 20px 0;
                        text-align: justify;
                    }
                    .paragraph {
                        margin-bottom: 20px;
                    }
                    .signature {
                        margin-top: 60px;
                    }
                    .contact-info {
                        font-size: 12px;
                        color: #666;
                        margin-top: 5px;
                    }
                    .company-name {
                        font-weight: bold;
                        color: #2c3e50;
                    }
                    .job-title {
                        color: #e74c3c;
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <div class="sender-info">
                        <strong>{$nomComplet}</strong><br>
                        <div class="contact-info">
                            {$cvProfile->adresse}<br>
                            {$cvProfile->ville} {$cvProfile->code_postal}<br>
                            Téléphone: {$cvProfile->telephone}<br>
                            Email: {$cvProfile->email}
                        </div>
                    </div>
                    <div class="date">
                        {$date}
                    </div>
                </div>
                
                <div class="recipient">
                    <strong><span class="company-name">{$entrepriseNom}</span></strong><br>
                    Service des Ressources Humaines
                </div>
                
                <div class="subject">
                    Objet : Candidature au poste de <span class="job-title">{$poste}</span>
                </div>
                
                <div class="content">
                    <div class="paragraph">
                        Madame, Monsieur,
                    </div>
                    
                    <div class="paragraph">
                        Je me permets de vous adresser ma candidature pour le poste de {$poste} que vous proposez au sein de votre entreprise {$entrepriseNom}.
                    </div>
                    
                      <div class="paragraph">
                        ' . ($offer->description ?? 'Mon profil correspond aux compétences recherchées pour ce poste.') . '
                      </div>
                    
                    <div class="paragraph">
                        Fort de mes expériences professionnelles et de mes formations, je suis convaincu de pouvoir apporter une réelle valeur ajoutée à votre équipe.
                    </div>
                    
                    <div class="paragraph">
                        Je serais ravi de pouvoir vous exposer plus en détail mes motivations et compétences lors d'un entretien.
                    </div>
                    
                    <div class="paragraph">
                        Dans l'attente de votre réponse, je vous prie d'agréer, Madame, Monsieur, l'expression de mes salutations distinguées.
                    </div>
                </div>
                
                <div class="signature">
                    <strong>{$nomComplet}</strong>
                </div>
            </body>
            </html>
            HTML;

                return $html;
}

    /**
     * Génère un CV de fallback si Gemini échoue
     */
    private function generateFallbackCV(CvProfile $cvProfile, $offer): string
    {
        $data = [
            'cvProfile' => $cvProfile,
            'offer' => $offer,
            'personalized_note' => "CV adapté pour : " . $offer->titre
        ];

        return view('cv.fallback-template', $data)->render();
    }
}