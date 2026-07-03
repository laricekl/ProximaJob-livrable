<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\Postulation;
use App\Models\Offre;
use App\Models\Notification;
use App\Models\User;
use App\Models\AutresDoc;

class CandidatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'offre_id' => ['required', Rule::exists('offres', 'id')->where('status', 'active')],
            'cv' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'motivation' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'additional_docs' => 'nullable|array',
            'additional_docs.*.intitule' => 'required_if:additional_docs.*.file,!=,null|string|max:255',
            'additional_docs.*.description' => 'nullable|string|max:500',
            'additional_docs.*.file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        try {
            DB::beginTransaction();



        $userId = Auth::id();
        $offreId = $validated['offre_id'];
        $uniqueId = Str::uuid()->toString();
        
        // Chemin dans public/assets/cvs/
        $storagePath = "candidatures/{$userId}/{$offreId}";

        // Gestion des fichiers principaux
        $cvPath = $this->storeFile($request->file('cv'), $storagePath, "cv_{$uniqueId}");
        $motivationPath = $this->storeFile($request->file('motivation'), $storagePath, "motivation_{$uniqueId}");


            // Vérifier si une candidature existe déjà pour cette offre
            $existingCandidature = Postulation::where('user_id', $userId)
                ->where('offre_id', $offreId)
                ->first();

            if ($existingCandidature) {
                // Ne pas écraser une candidature déjà acceptée ou refusée
                if (in_array($existingCandidature->status, ['accepted', 'rejected'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Vous avez déjà une candidature ' . $existingCandidature->status . ' pour cette offre.'
                    ], 422);
                }

                // Mettre à jour la candidature existante
                $existingCandidature->update([
                    'cv' => $cvPath,
                    'lettre_motivation' => $motivationPath,
                    'status' => 'en_attente'
                ]);
                $candidature = $existingCandidature;
            } else {
                $candidature = Postulation::create([
                    'user_id' => $userId,
                    'offre_id' => $offreId,
                    'cv' => $cvPath,
                    'lettre_motivation' => $motivationPath,
                    'status' => 'en_attente',
                    'autopostulation' => false,
                    'application_date' => now(),
                ]);
            }

            // Gestion des documents supplémentaires
            if (!empty($validated['additional_docs'])) {
                foreach ($validated['additional_docs'] as $docData) {
                    if (isset($docData['file']) && $docData['file']->isValid()) {
                        $docUniqueId = Str::uuid()->toString();
                        $docPath = $this->storeFile(
                            $docData['file'],
                            "{$storagePath}/autres_docs",
                            "doc_{$docUniqueId}"
                        );

                        AutresDoc::create([
                            'id_postulation' => $candidature->id,
                            'intitule' => $docData['intitule'],
                            'description' => $docData['description'] ?? null,
                            'path' => $docPath,
                        ]);
                    }
                }
            }

            DB::commit();

            // Récupérer l'entreprise propriétaire de l'offre
            $offre = Offre::with('entreprise.user')->find($offreId);
            $entrepriseUser = $offre->entreprise?->user;

            // Créer la notification (seulement si l'entreprise a un utilisateur)
            if ($entrepriseUser) {
                Notification::create([
                    'user_id' => $entrepriseUser->id,
                    'role' => 'entreprise',
                    'title' => 'Nouvelle candidature reçue',
                    'message' => 'Un candidat a postulé à votre offre "' . $offre->titre . '"',
                    'link' => "/entreprise/offres/{$offreId}/candidatures",
                    'is_read' => false,
                ]);
            }

            $message = $candidature->wasRecentlyCreated
                ? 'Votre candidature a été envoyée avec succès!'
                : 'Votre candidature a été mise à jour avec succès!';

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $candidature->load('autresDocs'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur candidature', [
                'user_id' => Auth::id(),
                'offre_id' => $request->offre_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du traitement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

 
    private function storeFile($file, $path, $namePrefix)
{
    if (!$file || !$file->isValid()) {
        return null;
    }

    $extension = $file->getClientOriginalExtension();
    $filename = "{$namePrefix}.{$extension}";
    
    // Utiliser le dossier public
    $storagePath = public_path("assets/cvs/{$path}");
    
    // Créer le dossier s'il n'existe pas
    if (!File::exists($storagePath)) {
        File::makeDirectory($storagePath, 0755, true);
    }
    
    try {
        // Déplacer le fichier
        $file->move($storagePath, $filename);
        
        // Retourner le chemin relatif pour la base de données
        return "assets/cvs/{$path}/{$filename}";
        
    } catch (\Exception $e) {
        Log::error('Erreur stockage fichier', [
            'path' => $storagePath,
            'filename' => $filename,
            'error' => $e->getMessage()
        ]);
        throw $e;
    }
}

    public function checkEmail(Request $request)
    {
        $exists = User::where('email', $request->email)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function previewCV($candidatureId)
    {
        $candidature = Postulation::findOrFail($candidatureId);

        // Vérification des permissions (ex: seul l'admin ou le propriétaire peut voir)
        if (!auth()->user()->isAdmin() && $candidature->user_id != auth()->id()) {
            abort(403);
        }

        if (!Storage::exists($candidature->cv)) {
            abort(404, 'Fichier introuvable');
        }

        // Retourne une réponse avec l'en-tête PDF
        return response()->file(
            Storage::path($candidature->cv),
            ['Content-Type' => 'application/pdf']
        );
    }


 public function previewletteria($candidatureId)
{
    try {
        $candidature = \App\Models\Postulation::with('user')->findOrFail($candidatureId);
        
        // Vérifications de permission
        $user = auth()->user();
        $isCandidat = $user->hasRole('candidat') && $candidature->user_id == $user->id;
        $isEntreprise = $user->hasRole('entreprise');
        
        if (!$isCandidat && !$isEntreprise) {
            abort(403, 'Accès non autorisé');
        }
        
         
        $filePath = $candidature->cover_letter;
        
        if (!$filePath) {
            abort(404, 'Lettre non trouvé pour cette candidature');
        }
        
        // Déterminer le chemin complet selon le type de chemin
        $fullPath = $this->resolveLetterPath($filePath);
        
        if (!File::exists($fullPath)) {
            abort(404, 'Fichier CV introuvable: ' . $filePath);
        }
        
        // Obtenir le type MIME
        $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];
        
        $mimeType = $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
        
        // Générer un nom d'affichage
        $displayName = "Cover_Letter" . $candidature->user->prenom . "_" . $candidature->user->name . "." . $extension;
        
        // Retourner le fichier
        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $displayName . '"',
            'X-CV-Source' => $candidature->autopostulation ? 'gemini_generated' : 'manual_upload'
        ]);
            
    } catch (\Exception $e) {
        Log::error('Erreur prévisualisation lettre de motivation', [
            'candidature_id' => $candidatureId,
            'error' => $e->getMessage()
        ]);
        abort(500, 'Erreur lors de l\'ouverture de la lettre de motivation');
    }
}
 
 public function previewCVia($candidatureId)
{
    try {
        $candidature = \App\Models\Postulation::with('user')->findOrFail($candidatureId);
        
        // Vérifications de permission
        $user = auth()->user();
        $isCandidat = $user->hasRole('candidat') && $candidature->user_id == $user->id;
        $isEntreprise = $user->hasRole('entreprise');
        
        if (!$isCandidat && !$isEntreprise) {
            abort(403, 'Accès non autorisé');
        }
        
        // CORRECTION : TOUJOURS utiliser le CV de la postulation
        $filePath = $candidature->cv;
        
        if (!$filePath) {
            abort(404, 'CV non trouvé pour cette candidature');
        }
        
        // Déterminer le chemin complet selon le type de chemin
        $fullPath = $this->resolveCVPath($filePath);
        
        if (!File::exists($fullPath)) {
            abort(404, 'Fichier CV introuvable: ' . $filePath);
        }
        
        // Obtenir le type MIME
        $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];
        
        $mimeType = $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
        
        // Générer un nom d'affichage
        $displayName = "CV_" . $candidature->user->prenom . "_" . $candidature->user->name . "." . $extension;
        
        // Retourner le fichier
        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $displayName . '"',
            'X-CV-Source' => $candidature->autopostulation ? 'gemini_generated' : 'manual_upload'
        ]);
            
    } catch (\Exception $e) {
        Log::error('Erreur prévisualisation CV', [
            'candidature_id' => $candidatureId,
            'error' => $e->getMessage()
        ]);
        abort(500, 'Erreur lors de l\'ouverture du CV');
    }
}

/**
 * Résout le chemin complet du CV selon son type
 */
private function resolveCVPath(string $filePath): string
{
    // Si c'est un chemin de storage (CV Gemini)
    if (str_contains($filePath, 'personalized-cvs/')) {
        return Storage::disk('public')->path($filePath);
    }
    
    // Si c'est un chemin storage standard
    if (str_contains($filePath, 'storage/')) {
        return Storage::disk('public')->path(str_replace('storage/', '', $filePath));
    }
    
    // Si c'est un chemin public (CV classique)
    return public_path($filePath);
}



private function resolveLetterPath(string $filePath): string
{
    // Si c'est un chemin de storage (letter Gemini) 
    if (str_contains($filePath, 'cover-letters/')) {
        return Storage::disk('public')->path($filePath);
    }
    
    // Si c'est un chemin storage standard
    if (str_contains($filePath, 'storage/')) {
        return Storage::disk('public')->path(str_replace('storage/', '', $filePath));
    }
    
    // Si c'est un chemin public (letter classique)
    return public_path($filePath);
}

/**
 * Afficher le CV d'une candidature pour les entreprises
 */
public function previewCVForEnterprise($candidatureId)
{
    try {
        // Récupérer la candidature avec l'utilisateur et l'offre associés
        $candidature = \App\Models\Postulation::with(['user', 'offre.entreprise'])
            ->findOrFail($candidatureId);
        
        // Vérifier que l'utilisateur est l'entreprise propriétaire de l'offre
        $user = auth()->user();
        if (!$user->hasRole('entreprise') || $candidature->offre->entreprise->user_id != $user->id) {
            abort(403, 'Accès non autorisé');
        }
        
        // Utiliser la même logique que previewCVia mais avec un logging spécifique
        return $this->previewCVia($candidatureId);
        
    } catch (\Exception $e) {
        Log::error('Erreur entreprise lors de la prévisualisation du CV', [
            'candidature_id' => $candidatureId,
            'entreprise_id' => auth()->id(),
            'error' => $e->getMessage()
        ]);
        
        abort(500, 'Erreur lors de l\'ouverture du CV');
    }
}

/**
 * Servir un fichier privé via Storage
 */
private function servePrivateFile($storagePath, $fullPath)
{
    // Vérifier que le fichier existe dans le storage
    if (!Storage::exists($storagePath)) {
        abort(404, 'Fichier introuvable');
    }
    
    // Obtenir l'extension du fichier
    $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
    
    // Définir le type MIME approprié
    $mimeTypes = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'txt' => 'text/plain'
    ];
    
    $mimeType = $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
    
    // Récupérer le contenu du fichier
    $fileContent = Storage::get($storagePath);
    
    // Retourner la réponse avec les en-têtes appropriés
    return response($fileContent, 200)
        ->header('Content-Type', $mimeType)
        ->header('Content-Disposition', 'inline');
}

/**
 * Servir un fichier public
 */
private function servePublicFile($filePath, $candidature)
{
    // Obtenir l'extension du fichier
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    
    // Définir le type MIME approprié
    $mimeTypes = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'txt' => 'text/plain'
    ];
    
    $mimeType = $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
    
    // Générer un nom de fichier pour l'affichage
    $displayName = "CV_" . $candidature->user->prenom . "_" . $candidature->user->name . "." . $extension;
    
    // Retourner le fichier avec les en-têtes appropriés
    return response()->file($filePath, [
        'Content-Type' => $mimeType,
        'Content-Disposition' => 'inline; filename="' . $displayName . '"'
    ]);
}

    public function candidature($offreId)
    {
        // Validation de l'ID de l'offre
        if (!is_numeric($offreId)) {
            abort(404, 'Offre non trouvée');
        }

        // Récupération de l'offre avec vérification d'existence
        $offre = Offre::findOrFail($offreId);

        // Construction de la requête de base
        $query = Postulation::where('offre_id', $offreId)
                    ->with(['user' => function($query) {
                        $query->select('id', 'name', 'email', 'telephone');  
                    }, 'offre'])
                    ->orderBy('created_at', 'asc'); // Tri par défaut

        // Filtre par statut (avec validation)
        if ($status = request('status')) {
            $allowedStatuses = ['en_attente', 'accepted', 'rejected'];
            if (in_array($status, $allowedStatuses)) {
                $query->where('status', $status);
            }
        }

        // Filtre par date (avec validation du format)
        if ($date = request('date')) {
            try {
                $parsedDate = Carbon::createFromFormat('Y-m-d', $date);
                $query->whereDate('created_at', $parsedDate);
            } catch (\Exception $e) {
                
            }
        }

        // Filtre supplémentaire par nom de candidat
        if ($search = request('search')) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Pagination avec gestion du nombre d'éléments par page
        $perPage = request('per_page', 10);
        $postulations = $query->paginate(min($perPage, 100));  
        return view('entreprise.liste-candidature', [
            'offre' => $offre,
            'postulations' => $postulations,
            'statuses' => ['en_attente', 'accepted', 'rejected'],
            'filters' => request()->only(['status', 'date', 'search', 'per_page'])
        ]);
    }


    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:en_attente,accepted,rejected'
        ]);

        try {
            $postulation = Postulation::whereHas('offre', function ($q) {
                    $entreprise = Auth::user()->entreprise;
                    if (!$entreprise) {
                        throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Entreprise non trouvée');
                    }
                    $q->where('entreprise_id', $entreprise->id);
                })
                ->with(['user', 'offre']) 
                ->findOrFail($id);

            $postulation->update($validated);
            $autopostulation = $postulation->autopostulation;
            if($autopostulation) {

                Notification::create([
                    'user_id' => $postulation->user_id, 
                    'role' => 'candidat',
                    'title' => 'Mise à jour de votre candidature',
                    'message' => 'Votre candidature pour l\'offre "' . $postulation->offre->titre . '" a été ' . $this->getStatusMessage($validated['status']),
                    'link' => "/user/historique-candidature_ia",
                    'is_read' => false,
                ]);
             }else {
                      Notification::create([
                    'user_id' => $postulation->user_id, 
                    'role' => 'candidat',
                    'title' => 'Mise à jour de votre candidature',
                    'message' => 'Votre candidature pour l\'offre "' . $postulation->offre->titre . '" a été ' . $this->getStatusMessage($validated['status']),
                    'link' => "/user/historique-candidature",
                    'is_read' => false,
                ]);
             }


            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'new_status' => $postulation->status
                ]);
            }

            return back()->with('status', 'Le statut de la candidature a été mis à jour.');

        } catch (ModelNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Candidature introuvable'], 404);
            }

            return back()->withErrors(['status' => 'Candidature introuvable.']);
        } catch (\Exception $e) {
            Log::error($e);
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Erreur serveur'], 500);
            }

            return back()->withErrors(['status' => 'Erreur serveur.']);
        }
    }

/**
 * Helper method pour obtenir le message selon le statut
 */
private function getStatusMessage($status)
{
    $messages = [
        'en_attente' => 'mise en attente',
        'accepted' => 'acceptée ',
        'rejected' => 'refusée'
    ];
    
    return $messages[$status] ?? 'mise à jour';
}

   

 public function previewFile($postulationId, $type)
{
    $postulation = Postulation::with('user')->findOrFail($postulationId);

    // Déterminer le chemin du fichier selon le type
    if ($type === 'cv' && $postulation->cv) {
        $filePath = $postulation->cv;
        $fileName = 'CV_' . Str::slug($postulation->user->name) . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
    } elseif ($type === 'motivation' && $postulation->lettre_motivation) {
        $filePath = $postulation->lettre_motivation;
        $fileName = 'Lettre_Motivation_' . Str::slug($postulation->user->name) . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
    } else {
        abort(404, 'Fichier non trouvé');
    }

    // Chemin complet dans public
    $fullPath = public_path($filePath);
    
    // Vérifier que le fichier existe
    if (!File::exists($fullPath)) {
        abort(404, 'Fichier introuvable');
    }

    // Vérification de sécurité
    if (!Str::startsWith($filePath, 'assets/cvs/')) {
        abort(403, 'Chemin de fichier non autorisé');
    }

    // Obtenir le type MIME
    $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
    $mimeTypes = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];
    
    $mimeType = $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';

    // Retourner le fichier
    return response()->file($fullPath, [
        'Content-Type' => $mimeType,
        'Content-Disposition' => 'inline; filename="' . $fileName . '"'
    ]);
}



    // Méthode optionnelle pour envoyer une notification
    protected function sendStatusNotification($postulation)
    {
        // Implémentez l'envoi d'email ici
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
