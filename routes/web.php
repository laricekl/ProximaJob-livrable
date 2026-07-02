<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\OffresController;
use App\Http\Controllers\ParametresController;
use App\Http\Controllers\RessourcesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EntreprisesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CandidatureController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\HomeController;
use App\Http\Middleware\CheckUserStatus;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\CvPersonalizationController ; 
use App\Http\Controllers\CvProfileController;
use App\Services\JobMatchingService; 
use App\Http\Controllers\Auth\ResendVerificationEmailController;
use App\Models\Offre;
use App\Models\User;
use App\Models\Notification;  
use App\Models\CandidateSector;
use App\Models\CandidateSkill;
use App\Models\JobOfferSkill;
use App\Models\Postulation;
use App\Models\CvProfile;
use App\Models\Sector;
use App\Models\Diplome;


 

  use Prism\Prism\Prism;
use Prism\Prism\Enums\Provider;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route pour changer la langue
Route::post('/set-language', function () {
    $locale = request('locale');
    $supportedLocales = ['fr', 'en'];

    if (in_array($locale, $supportedLocales)) {
        Session::put('locale', $locale);
        App::setLocale($locale);

        return response()->json([
            'success' => true,
            'locale' => $locale,
            'message' => __('Langue changée avec succès')
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => __('Langue non supportée')
    ], 400);
})->name('set.language');



Route::get('/', [HomeController::class, 'index'])->name("welcome");
Route::view('/terms', 'terms')->name('terms');
Route::view('/policy', 'policy')->name('policy');
Route::view('/cookies', 'cookies')->name('cookies.policy');


// Routes de vérification d'email
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');

Route::get('/verify-email/{token}', [VerifyEmailController::class, 'verify'])
    ->name('verification.custom.verify');

Route::get('/resend-verification', [VerifyEmailController::class, 'showResendForm'])
    ->name('verification.custom.resend-form');

Route::post('/resend-verification', [VerifyEmailController::class, 'resend'])
    ->name('verification.custom.resend');

    

//Route::get("/login", function() {
   // return view("login");
//})->name("loginForm");


// Routes guest

Route::get('/offres', [OffresController::class, 'index'])->name('offres');
Route::redirect('/offres/detail', '/offres')->name('details.offre');
Route::redirect('/app-form', '/offres')->name('app_form');
Route::post('/check-email', [UserController::class, 'checkEmail'])->name('check.email');
Route::get('/offres/{offre:slug}', [UserController::class, 'jobdetails'])->name('job_infos');
//Route::post('/register/user', [RegisteredUserController::class, 'registerJobSeeker'])->name('register.jobseeker');
//Route::post('/register/entreprise', [RegisteredUserController::class, 'registerCompany'])->name('register.company');


//Paramettes du site
Route::get('/contact', [ParametresController::class, 'contact'])->name('contact');
Route::post('/contact', [ParametresController::class, 'submitContact'])->name('contact.submit');
Route::get('/abonnement', [ParametresController::class, 'abonnement'])->name('abonnement');

//Ressources
Route::get('/ressources', [RessourcesController::class, 'index'])->name('ressources');


//utilisateurs



// Routes OAuth
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirectToProvider'])
    ->name('auth.social.redirect');
    
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback'])
    ->name('auth.social.callback');


Route::prefix('user')
    ->middleware(['auth', 'verified' , 'candidate.access', 'user.status' ])
    ->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user.home');
        Route::get('/historique-candidature', [UserController::class, 'historique'])->name('candidature.historique');
        Route::get('/historique-candidature_ia', [UserController::class, 'historique_ia'])->name('candidature_ia.historique');
        Route::get('/offres/{offre:slug}', [UserController::class, 'jobdetails'])->name('job_details');
        Route::post('/candidature/store', [CandidatureController::class, 'store'])->name('candidatures.store');
        Route::post('/profile/update', [UserController::class, 'update'])->name('user.profile.update'); 
        Route::post('/plan-souscrire', [UserController::class, 'souscrire'])->name('abonnements.souscrire');
       // Route::get('/preview-cv/{candidature}', [CandidatureController::class, 'previewCV'])->name('preview.cv');
    Route::get('/historique-candidatures', [UserController::class, 'historique'])->name('user.historiques');
    Route::get('/historique-candidatures_ia', [UserController::class, 'historique_ia'])->name('user.historiques_ia');
    Route::get('/abonnement', [UserController::class, 'abonnement'])->name('user.bonnement');
    Route::get('/plan-abonnement', [UserController::class, 'planabonnement'])->name('plan.abonnement');
    Route::get('/infos-cv', [UserController::class, 'infoscv'])->name('infos.cv');
    Route::get('/profil-public', [UserController::class, 'publicProfile'])->name('user.profil-public');
    Route::get('/detail-candidature', [UserController::class, 'detailCandidature'])->name('user.detail-candidature');
    Route::get('/preview-cv-ia/{candidature}', [CandidatureController::class, 'previewCVia'])->name('preview.cv-ia'); 
    Route::get('/preview-letter-ia/{candidature}', [CandidatureController::class, 'previewletteria'])->name('preview.letter-ia');


    Route::get('/personnaliser-cv', [CvPersonalizationController::class, 'showForm'])->name('cv.personalization.form');
    Route::post('/generer-cv-personnalise', [CvPersonalizationController::class, 'generateCV'])->name('cv.personalization.generate');
    Route::get('/previsualiser-cv/{filename}', [CvPersonalizationController::class, 'previewCV'])->name('cv.personalization.preview');
    Route::get('/afficher-cv/{filename}', [CvPersonalizationController::class, 'inlineCV'])->name('cv.personalization.inline');
    Route::get('/telecharger-cv/{filename}', [CvPersonalizationController::class, 'downloadCV'])->name('cv.personalization.download');


   });

   
    Route::middleware(['auth'])->group(function () {
    Route::redirect('/cv/create', '/user/infos-cv')->name('cv.create');
    Route::post('/cv/store', [CvProfileController::class, 'store'])->name('cv.store');
    Route::post('/cv/upload-source', [CvProfileController::class, 'uploadSourceCv'])->name('cv.upload-source');
    Route::post('/cv/import-uploaded', [CvProfileController::class, 'importFromUploadedCv'])->name('cv.import-uploaded');
    Route::get('/cv/principal/afficher', [CvProfileController::class, 'inlinePrincipalPdf'])->name('cv.principal.inline');
    Route::get('/cv/principal/telecharger', [CvProfileController::class, 'downloadPrincipalPdf'])->name('cv.principal.download');
    Route::redirect('/cv/{id}', '/user/infos-cv')->name('cv.show');
    Route::redirect('/cv/{id}/edit', '/user/infos-cv')->name('cv.edit');
    Route::put('/cv/{id}/update', [CvProfileController::class, 'update'])->name('cv.update');
});

//Routes entreprises

Route::prefix("/entreprise") ->middleware(['auth', 'verified', 'entreprise.access', 'user.status'])->group(function() {
    Route::get("/",[EntreprisesController::class, 'index'])->name("offres.publies");
    Route::get("/offres/create",[OffresController::class, 'create'])->name("entreprise.offres.create");
    Route::get("/historique",[EntreprisesController::class, 'historique'])->name("entreprise.historique");
    Route::get("/candidatures-ia",[EntreprisesController::class, 'historique_ia'])->name("entreprise.candidatures_ia");
    Route::get("/abonnements",[EntreprisesController::class, 'abonnements'])->name("entreprise.abonnements");
    Route::get("/promotion",[EntreprisesController::class, 'promotion'])->name("entreprise.promotion");
   // Route::get("/candidat-details",[EntreprisesController::class, 'candidat'])->name("entreprise.connected_candidate_details");
   Route::get('/candidat/{id}', [EntreprisesController::class, 'candidat'])
     ->name('entreprise.connected_candidate_details');
   // Route::get('/historique-candidatures', [UserController::class, 'historique'])->name('user.historique');
   // Route::get('/', [OffresController::class, 'index'])->name('index');
    Route::post('/', [OffresController::class, 'store'])->name('offres.store');
    Route::get('/search', [OffresController::class, 'search'])->name('offres.search');
    Route::get('/searchia', [OffresController::class, 'searchia'])->name('offres.searchia');
    Route::get('/offres/{id}/edit/', [OffresController::class, 'edit'])->name('edit.offres');
    Route::put('/offres/update/{id}', [OffresController::class, 'update'])->name('offres.update');
    Route::delete('/offres/{id}', [OffresController::class, 'destroy'])->name('offres.destroy');
    Route::post('/profile/update', [UserController::class, 'update'])->name('entreprise.profile.update');
    Route::get('/offres/{offre}/candidatures', [CandidatureController::class, 'candidature']) ->name('entreprise.offres.candidatures');
    Route::put('/candidature/{postulation}/update-status', [CandidatureController::class, 'updateStatus'])->name('candidature.updateStatus');
    Route::get('/preview-cv/{candidature}', [CandidatureController::class, 'previewCV'])->name('previewcv');
    Route::get('/candidatures/{postulationId}/preview/{type}', [CandidatureController::class, 'previewFile'])->name('entreprise.candidature.preview');
    Route::get('/preview-cv-entreprise/{candidature}', [CandidatureController::class, 'previewCVForEnterprise'])->name('preview.cv-ia.ep');


});


//notifications
Route::get('/notifications', [NotificationController::class, 'index'])
    ->middleware(['auth', 'verified', 'candidate.access', 'user.status'])
    ->name('notifications.index');

Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])
    ->middleware('auth');

Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])
    ->middleware('auth');

Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])
    ->middleware('auth');


//Routes admin


Route::prefix("/admin") ->middleware(['auth', 'verified' , 'role:admin|Marketing'  ])->group(function() {
    Route::get("/",[AdminController::class, 'index'])->name("admin.dashboard");
    Route::redirect('/utilisateurs', '/admin/Gestion/utilisateurs');
    Route::redirect('/offres', '/admin/Gestion/offres');
    Route::redirect('/abonnements', '/admin/Gestion/abonnements');
    Route::redirect('/statistiques', '/admin/Gestion/statistiques');
    Route::redirect('/newsletters', '/admin/Gestion/newsletters');
    Route::redirect('/parametres', '/admin/Gestion/parametres');
    Route::get("/Gestion/utilisateurs",[AdminController::class, 'users'])->name("admin.users");
    Route::get("/Gestion/offres",[AdminController::class, 'offres'])->name("admin.offres");
    Route::get("/Gestion/abonnements",[AdminController::class, 'abonnements'])->name("admin.abonnements");
    Route::get("/Gestion/statistiques",[AdminController::class, 'statistiques'])->name("admin.statistiques");
    Route::get("/Gestion/newsletters",[AdminController::class, 'newsletters'])->name("admin.newsletters");
    Route::get("/Gestion/parametres",[AdminController::class, 'parametres'])->name("admin.parametres");
    Route::get('/chart-data', [AdminController::class, 'getChartData'])->name('admin.chart-data');
    Route::get('/users/{user}/edit', [AdminController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{user}', [AdminController::class, 'update'])->name('admin.users.update');
    Route::post('/users/{id}/delete', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::post('/users/{id}/suspend', [AdminController::class, 'suspendUser']) ->name('admin.users.suspend');
    Route::post('/users/{id}/reactivate', [AdminController::class, 'reactivateUser'])->name('admin.users.reactivate');


    Route::post('/verify-password', function (Illuminate\Http\Request $request) {
    if (\Hash::check($request->password, auth()->user()->password)) {
        return response()->json(['valid' => true]);
    }
    return response()->json(['valid' => false]);

   
        }) ;

    Route::post('/users', [AdminController::class, 'store'])->name('admin.users.store');
    Route::patch('/offres/{id}/deactivate', [AdminController::class, 'deactivateOffer'])->name('offres.deactivate');
    Route::patch('/offres/{id}/reactivate', [AdminController::class, 'reactivateOffer'])->name('offres.reactivate');

    // Routes pour la gestion des paramètres généraux
    Route::post('/parametres/general', [AdminController::class, 'updateGeneral'])->name('parametres.update-general');
    Route::delete('/parametres/remove-logo', [AdminController::class, 'removeLogo'])->name('parametres.remove-logo');
    Route::delete('/parametres/remove-favicon', [AdminController::class, 'removeFavicon'])->name('parametres.remove-favicon');

    Route::get('/users-details/{user}', [AdminController::class, 'show'])->name('admin.users.show');
    Route::post('entreprises/validate', [AdminController::class, 'validateEntreprise']) ->name('entreprises.validate');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'adminmarkAsRead'])->name('admin.notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('admin.allnotifications.read');
    Route::post('/users/{user}/activate', [AdminController::class, 'activate'])->name('admin.users.activate');


    // abonnement

       // Routes pour les abonnements
    Route::post('/abonnements', [AdminController::class, 'abonstore'])->name('admin.abonnements.store');
    Route::get('/abonnements/{abonnement}/fonctionnalites', [AdminController::class, 'getFonctionnalites'])->name('admin.abonnements.fonctionnalites');
    Route::put('/abonnements/{abonnement}', [AdminController::class, 'abonupdate'])->name('admin.abonnements.update');
    Route::delete('/abonnements/{abonnement}', [AdminController::class, 'abondestroy'])->name('admin.abonnements.destroy');
});


    Route::get('/candidatures/{user_id}/{offre_id}/{filename}', function ($offre_id, $user_id, $filename) {
    $path = storage_path("app/private/candidatures/$user_id/$offre_id/$filename");

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="'.$filename.'"'
    ]);
})->name('candidature.files');



Route::prefix('cv-generator')->middleware(['web', 'auth'])->group(function () {
    Route::redirect('/form', '/user/personnaliser-cv')->name('cv.form');
    Route::post('/generate', function () {
        return redirect()
            ->route('cv.personalization.form')
            ->with('info', 'Le generateur CV historique a ete remplace par le parcours CV personnalise.');
    })->name('cv.generate');
});

//Route::get('/', function () {
  //  return view('welcome');
//});

 
Route::middleware(['auth'])->group(function () {
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])
        ->name('profile.change-password');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->hasRole('admin') || $user->hasRole('Marketing')) {
        return redirect()->route('admin.dashboard');
    }

    if ($user->hasRole('entreprise')) {
        return redirect()->route('offres.publies');
    }

    return redirect()->route('user.home');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';



Route::get('/ai', function(){
$response = Prism::text()
->using(Provider::Gemini, 'gemini-2.5-flash')
->withPrompt('possible de me generer un cv ?')
->generate();

echo $response->text;
});

 
Route::get('/test-gemini-cv/{candidateId}/{offerId}', function($candidateId, $offerId) {
    $matchingService = new JobMatchingService();  
    $result = $matchingService->generateCVForCandidate($candidateId, $offerId);
    
    if (isset($result['cv_html'])) {
        return response($result['cv_html'])->header('Content-Type', 'text/html');
    }
    
    return response()->json($result);
});

Route::get('/test-prism', function(){
    try {
        $response = Prism::text()
            ->using(Provider::Gemini, 'gemini-2.5-flash')
            ->withPrompt('Test de connexion - réponds "OK" si tout fonctionne')
            ->generate();

        return response()->json([
            'success' => true,
            'response' => $response->text
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});


Route::get('/test-gemini-fast', function(){
    try {
        $prompt = "CV HTML simple pour Marie Martin, développeuse web. HTML seulement.";
        
        $response = Prism::text()
            ->using(Provider::Gemini, 'gemini-2.5-flash')
            ->withPrompt($prompt)
            ->generate();

        return response($response->text)->header('Content-Type', 'text/html');
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'type' => get_class($e)
        ], 500);
    }
});


Route::get('/test-complete-cv/{candidateId}/{offerId}', function($candidateId, $offerId) {
    $matchingService = new JobMatchingService();
    $result = $matchingService->testCompleteCVGeneration($candidateId, $offerId);
    
    if (isset($result['cv_html'])) {
        return response($result['cv_html'])->header('Content-Type', 'text/html');
    }
    
    return response()->json($result);
});

Route::get('/test-gemini-step-by-step/{candidateId}/{offerId}', function($candidateId, $offerId) {
    try {
        $matchingService = new JobMatchingService();
        
        // 1. Récupérer les données
        $cvProfile = CvProfile::with([
            'formations',
            'competences', 
            'experiences'
        ])->where('user_id', $candidateId)->first();

        if (!$cvProfile) {
            return response()->json(['error' => 'Profil CV non trouvé'], 404);
        }

        $offer = Offre::findOrFail($offerId);
        
        // 2. Préparer les données
        $promptData = [
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
                    'specifiques' => $cvProfile->competences->where('type', 'specifique')->pluck('description')->toArray(),
                    'generales' => $cvProfile->competences->where('type', 'generale')->pluck('description')->toArray(),
                ]
            ],
            'offer' => [
                'titre' => $offer->titre,
                'poste' => $offer->poste,
                'description' => $offer->description,
                'competences_requises' => $offer->competences,
            ]
        ];
        
        // 3. Afficher les données pour debug
        $debugInfo = [
            'candidate' => [
                'name' => $cvProfile->prenom . ' ' . $cvProfile->nom,
                'email' => $cvProfile->email,
                'phone' => $cvProfile->telephone,
                'formations' => $promptData['candidate']['formations'],
                'experiences' => $promptData['candidate']['experiences'],
            ],
            'offer' => [
                'poste' => $offer->poste,
                'titre' => $offer->titre,
            ]
        ];
        
        // 4. Tester avec un prompt très simple d'abord
        $simplePrompt = "Génère un CV HTML simple pour {$cvProfile->prenom} {$cvProfile->nom} - {$offer->poste}. HTML seulement.";
        
        Log::info('=== TEST SIMPLE ===');
        $simpleResponse = Prism::text()
            ->using(Provider::Gemini, 'gemini-2.5-flash')
            ->withPrompt($simplePrompt)
            ->generate();
            
        $simpleResult = trim($simpleResponse->text);
        
        return response()->json([
            'step_1_simple_test' => [
                'success' => !empty($simpleResult),
                'response_length' => strlen($simpleResult),
                'response_preview' => substr($simpleResult, 0, 200),
            ],
            'debug_data' => $debugInfo,
            'prompt_data_sample' => [
                'formations' => $promptData['candidate']['formations'],
                'experiences' => $promptData['candidate']['experiences'],
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});


Route::get('/test-final-cv/{candidateId}/{offerId}', function($candidateId, $offerId) {
    $matchingService = new JobMatchingService();
    $result = $matchingService->testCompleteCVGeneration($candidateId, $offerId);
    
    if (isset($result['cv_html'])) {
        // Nettoyer la réponse pour l'affichage
        $cleanHtml = preg_replace('/```html\s*/', '', $result['cv_html']);
        $cleanHtml = preg_replace('/```\s*/', '', $cleanHtml);
        $cleanHtml = trim($cleanHtml);
        
        return response($cleanHtml)->header('Content-Type', 'text/html');
    }
    
    return response()->json($result);
});


 Route::get('/debug-offre/{offerId}', function($offerId) {
    $offer =  Offre::with(['diplome', 'sector'])->findOrFail($offerId);
    
    return response()->json([
        'offre' => [
            'id' => $offer->id,
            'titre' => $offer->titre,
            'sector_id' => $offer->sector_id,
            'sector_name' => $offer->sector->name ?? 'NULL',
            'diplome_id' => $offer->diplome_id,
            'diplome_name' => $offer->diplome->nom_diplome  ,
            'annee_experience' => $offer->annee_experience
        ]
    ]);
});


Route::get('/diagnose-diplome-relation/{offerId}', function($offerId) {
    try {
        $offer =  Offre::findOrFail($offerId);
        
        // Test 1: Vérifier le diplôme directement
        $directDiplome =  Diplome::find($offer->diplome_id);
        
        // Test 2: Vérifier la relation via Eloquent
        $relationDiplome = $offer->diplome;
        
        // Test 3: Vérifier la structure de la table
        $tableColumns = \Schema::getColumnListing('offres');
        
        return response()->json([
            'tests' => [
                'test_1_direct_diplome' => $directDiplome ? [
                    'exists' => true,
                    'id' => $directDiplome->id,
                    'nom_diplome' => $directDiplome->nom_diplome
                ] : ['exists' => false],
                
                'test_2_relation_diplome' => $relationDiplome ? [
                    'exists' => true,
                    'id' => $relationDiplome->id,
                    'nom_diplome' => $relationDiplome->nom_diplome
                ] : ['exists' => false, 'problem' => 'RELATION CASSÉE'],
                
                'test_3_table_columns' => $tableColumns,
                'has_diplome_id_column' => in_array('diplome_id', $tableColumns)
            ],
            'diagnostic' => (!$directDiplome ? 'DIPLÔME NEXISTE PAS EN BASE' : 
                           !$relationDiplome )? 'RELATION ELoquent CASSÉE' : 'PROBLÈME INCONNU'
        ]);
        
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});



 
Route::get('/api/diplomes', function() {  

    $diplomes = Diplome::all();
        return response()->json($diplomes);
    
});


 
Route::get('/test-email', function () {
    Mail::raw('Test email', function ($message) {
        $message->to('freddymerlinaikpe@gmail.com')
                ->subject('Test');
    });
    return 'Email sent';
});


Route::get('/email/verify', function () {
    return view('auth.verify-email-entreprise');
})->name('enterprise.verification.notice');


 

Route::post('/email/resend-verification', [ResendVerificationEmailController::class, 'store'])
    ->middleware(['throttle:3,1'])
    ->name('enterprise.verification.resend');

/*use Illuminate\Http\Request;
use Twilio\TwiML\MessagingResponse;

Route::post('api/twilio/webhook', function (Request $request) {

    $dipome= Dipome::get();
    $body = strtolower(trim($request->input('Body')));
    $reply = "Hello cest ok !";

    $response = Prism::text()
->using(Provider::Gemini, 'gemini-2.5-flash')
->withPrompt('possible de me generer un cv ?')
->generate();

echo $response->text;

    $response = new MessagingResponse();
    $response->message($reply);

    return response($response)->header('Content-Type', 'text/xml');
})->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);*/

 

 
