<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminOperationsController;
use Illuminate\Support\Facades\Route;
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
use App\Http\Controllers\Auth\ResendVerificationEmailController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\StripeWebhookController;

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
Route::post('/set-language', [App\Http\Controllers\LocaleController::class, 'setLanguage'])
    ->middleware('throttle:30,1')
    ->name('set.language');

// Health check
Route::get('/health', [App\Http\Controllers\HealthController::class, 'check'])->name('health');



Route::get('/', [HomeController::class, 'index'])->name("welcome");
Route::view('/terms', 'terms')->name('terms');
Route::view('/policy', 'policy')->name('policy');
Route::view('/cookies', 'cookies')->name('cookies.policy');


// Routes de vérification d'email
Route::get('/verify-email/{token}', [VerifyEmailController::class, 'verify'])
    ->name('verification.custom.verify');

Route::get('/resend-verification', [VerifyEmailController::class, 'showResendForm'])
    ->name('verification.custom.resend-form');

Route::post('/resend-verification', [VerifyEmailController::class, 'resend'])
    ->middleware('throttle:3,1')
    ->name('verification.custom.resend');

    

//Route::get("/login", function() {
   // return view("login");
//})->name("loginForm");


// Routes guest

Route::get('/offres', [OffresController::class, 'index'])->name('offres');
Route::redirect('/offres/detail', '/offres')->name('details.offre');
Route::redirect('/app-form', '/offres')->name('app_form');
Route::post('/check-email', [UserController::class, 'checkEmail'])
    ->middleware('throttle:10,1')
    ->name('check.email');
Route::get('/offres/{offre:slug}', [UserController::class, 'jobdetails'])->name('job_infos');
Route::get('/entreprises', [HomeController::class, 'entreprises'])->name('entreprises.index');
Route::get('/entreprise/{entreprise}', [HomeController::class, 'entreprise'])
    ->whereNumber('entreprise')
    ->name('entreprise.profil');
//Route::post('/register/user', [RegisteredUserController::class, 'registerJobSeeker'])->name('register.jobseeker');
//Route::post('/register/entreprise', [RegisteredUserController::class, 'registerCompany'])->name('register.company');


//Paramettes du site
Route::get('/contact', [ParametresController::class, 'contact'])->name('contact');
Route::post('/contact', [ParametresController::class, 'submitContact'])
    ->middleware('throttle:5,1')
    ->name('contact.submit');
Route::get('/abonnement', [ParametresController::class, 'abonnement'])->name('abonnement');

// Paiement des abonnements (Stripe Checkout) — l'activation arrive par webhook.
Route::middleware('auth')->group(function () {
    Route::post('/paiement/checkout/{userAbonnement}', [PaiementController::class, 'checkout'])->name('paiement.checkout');
    Route::get('/paiement/succes', [PaiementController::class, 'succes'])->name('paiement.succes');
    Route::get('/paiement/annule', [PaiementController::class, 'annule'])->name('paiement.annule');
});
Route::post('/stripe/webhook', StripeWebhookController::class)->name('stripe.webhook');

//Ressources
Route::get('/ressources', [RessourcesController::class, 'index'])->name('ressources');


//utilisateurs



// Routes OAuth
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirectToProvider'])
    ->whereIn('provider', ['google', 'facebook'])
    ->name('auth.social.redirect');
    
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback'])
    ->whereIn('provider', ['google', 'facebook'])
    ->name('auth.social.callback');


Route::prefix('user')
    ->middleware(['auth', 'verified' , 'candidate.access', 'user.status' ])
    ->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user.home');
        Route::get('/offres/{offre:slug}', [UserController::class, 'jobdetails'])->name('job_details');
        Route::post('/candidature/store', [CandidatureController::class, 'store'])->name('candidatures.store');
        Route::post('/profile/update', [UserController::class, 'update'])->name('user.profile.update'); 
        Route::post('/plan-souscrire', [UserController::class, 'souscrire'])->name('abonnements.souscrire');
        Route::post('/abonnement/annuler', [UserController::class, 'cancelSubscription'])->name('abonnements.cancel');
       // Route::get('/preview-cv/{candidature}', [CandidatureController::class, 'previewCV'])->name('preview.cv');
    Route::get('/historique-candidatures', [UserController::class, 'historique'])->name('user.historiques');
    Route::get('/historique-candidatures_ia', [UserController::class, 'historique_ia'])->name('user.historiques_ia');

    // Redirections rétrocompatibles (anciens noms de routes)
    Route::redirect('/historique-candidature', '/user/historique-candidatures')->name('candidature.historique');
    Route::redirect('/historique-candidature_ia', '/user/historique-candidatures_ia')->name('candidature_ia.historique');
    Route::get('/abonnement', [UserController::class, 'abonnement'])->name('user.abonnement');
    Route::get('/plan-abonnement', [UserController::class, 'planabonnement'])->name('plan.abonnement');
    Route::get('/infos-cv', [UserController::class, 'infoscv'])->name('infos.cv');
    Route::get('/profil-public', [UserController::class, 'publicProfile'])->name('user.profil-public');
    Route::get('/detail-candidature', [UserController::class, 'detailCandidature'])->name('user.detail-candidature');
    Route::get('/preview-cv-ia/{candidature}', [CandidatureController::class, 'previewCVia'])->name('preview.cv-ia'); 
    Route::get('/preview-letter-ia/{candidature}', [CandidatureController::class, 'previewletteria'])->name('preview.letter-ia');


    Route::middleware('candidate.premium')->group(function () {
        Route::get('/personnaliser-cv', [CvPersonalizationController::class, 'showForm'])->name('cv.personalization.form');
        Route::post('/generer-cv-personnalise', [CvPersonalizationController::class, 'generateCV'])->name('cv.personalization.generate');
        Route::get('/previsualiser-cv/{filename}', [CvPersonalizationController::class, 'previewCV'])->name('cv.personalization.preview');
        Route::get('/afficher-cv/{filename}', [CvPersonalizationController::class, 'inlineCV'])->name('cv.personalization.inline');
        Route::get('/telecharger-cv/{filename}', [CvPersonalizationController::class, 'downloadCV'])->name('cv.personalization.download');
    });


   });

   
    Route::middleware(['auth', 'candidate.access'])->group(function () {
    Route::redirect('/cv/create', '/user/infos-cv')->name('cv.create');
    Route::post('/cv/store', [CvProfileController::class, 'store'])->name('cv.store');
    Route::post('/cv/upload-source', [CvProfileController::class, 'uploadSourceCv'])->name('cv.upload-source');
    Route::post('/cv/import-uploaded', [CvProfileController::class, 'importFromUploadedCv'])->name('cv.import-uploaded');
    Route::get('/cv/principal/afficher', [CvProfileController::class, 'inlinePrincipalPdf'])->name('cv.principal.inline');
    Route::get('/cv/principal/telecharger', [CvProfileController::class, 'downloadPrincipalPdf'])->name('cv.principal.download');
    Route::post('/cv/principal/design', [CvProfileController::class, 'updatePrincipalDesign'])->name('cv.principal.design');
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
    Route::post("/abonnements/souscrire",[EntreprisesController::class, 'souscrire'])->name("entreprise.abonnements.souscrire");
    Route::get("/promotion",[EntreprisesController::class, 'promotion'])->name("entreprise.promotion");
    Route::get('/candidatures', [CandidatureController::class, 'entrepriseIndex'])
        ->name('entreprise.candidatures.index');
   // Route::get("/candidat-details",[EntreprisesController::class, 'candidat'])->name("entreprise.connected_candidate_details");
    Route::get('/candidatures/{postulation}', [EntreprisesController::class, 'candidatureDetail'])
        ->whereNumber('postulation')
        ->name('entreprise.candidatures.show');
    Route::get('/candidat/{id}', [EntreprisesController::class, 'candidat'])
        ->whereNumber('id')
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
    Route::patch('/parametres', [ProfileController::class, 'updateEnterprise'])
        ->name('entreprise.settings.update');
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


Route::prefix('/admin')->middleware(['auth', 'verified', 'admin.security', 'admin.audit'])->group(function () {
    Route::middleware('role:admin|Marketing')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/Gestion/statistiques', [AdminController::class, 'statistiques'])->name('admin.statistiques');
        Route::get('/Gestion/newsletters', [AdminController::class, 'newsletters'])->name('admin.newsletters');
        Route::post('/Gestion/newsletters', [AdminController::class, 'newsletterStore'])->name('admin.newsletters.store');
        Route::put('/Gestion/newsletters/{campagne}', [AdminOperationsController::class, 'updateNewsletter'])->name('admin.newsletters.update');
        Route::delete('/Gestion/newsletters/{campagne}', [AdminOperationsController::class, 'destroyNewsletter'])->name('admin.newsletters.destroy');
        Route::get('/chart-data', [AdminController::class, 'getChartData'])->name('admin.chart-data');
    });

    Route::middleware('role:admin')->group(function () {
    Route::get('/utilisateurs', fn () => redirect()->route('admin.users'));
    Route::get('/offres', fn () => redirect()->route('admin.offres'));
    Route::get('/abonnements', fn () => redirect()->route('admin.abonnements'));
    Route::get('/statistiques', fn () => redirect()->route('admin.statistiques'));
    Route::get('/newsletters', fn () => redirect()->route('admin.newsletters'));
    Route::get('/parametres', fn () => redirect()->route('admin.parametres'));
    Route::get("/Gestion/utilisateurs",[AdminController::class, 'users'])->name("admin.users");
    Route::get("/Gestion/offres",[AdminController::class, 'offres'])->name("admin.offres");
    Route::get("/Gestion/abonnements",[AdminController::class, 'abonnements'])->name("admin.abonnements");
    Route::get("/Gestion/parametres",[AdminController::class, 'parametres'])->name("admin.parametres");
    Route::get('/Gestion/entreprises', [AdminOperationsController::class, 'entreprises'])->name('admin.entreprises.index');
    Route::patch('/Gestion/entreprises/{entreprise}/statut', [AdminOperationsController::class, 'updateEntrepriseStatus'])->name('admin.entreprises.status');
    Route::patch('/Gestion/entreprises/{entreprise}/vedette', [AdminOperationsController::class, 'toggleEntrepriseFeatured'])->name('admin.entreprises.featured');
    Route::get('/Gestion/candidatures', [AdminOperationsController::class, 'candidatures'])->name('admin.candidatures.index');
    Route::get('/Gestion/candidatures/{postulation}', [AdminOperationsController::class, 'showCandidature'])->name('admin.candidatures.show');
    Route::get('/Gestion/candidatures/{postulation}/documents/{type}', [AdminOperationsController::class, 'candidatureDocument'])
        ->whereIn('type', ['cv', 'motivation'])->name('admin.candidatures.document');
    Route::get('/Gestion/candidatures/{postulation}/documents/autres/{document}', [AdminOperationsController::class, 'candidatureOtherDocument'])
        ->whereNumber('document')->name('admin.candidatures.other-document');
    Route::patch('/Gestion/candidatures/{postulation}/statut', [AdminOperationsController::class, 'updateCandidatureStatus'])->name('admin.candidatures.status');
    Route::patch('/Gestion/candidatures/{postulation}/notes', [AdminOperationsController::class, 'updateCandidatureNotes'])->name('admin.candidatures.notes');
    Route::get('/Gestion/ressources', [AdminOperationsController::class, 'ressources'])->name('admin.ressources.index');
    Route::post('/Gestion/ressources', [AdminOperationsController::class, 'storeRessource'])->name('admin.ressources.store');
    Route::put('/Gestion/ressources/{ressource}', [AdminOperationsController::class, 'updateRessource'])->name('admin.ressources.update');
    Route::delete('/Gestion/ressources/{ressource}', [AdminOperationsController::class, 'destroyRessource'])->name('admin.ressources.destroy');
    Route::get('/Gestion/pilotage', [AdminOperationsController::class, 'pilotage'])->name('admin.pilotage');
    Route::patch('/Gestion/pilotage/securite', [AdminOperationsController::class, 'updateSecurity'])->name('admin.security.update');
    Route::patch('/Gestion/pilotage/roles/{role}', [AdminOperationsController::class, 'updateRole'])->name('admin.roles.update');
    Route::post('/Gestion/pilotage/recalculer-matching', [AdminOperationsController::class, 'recalculateMatching'])->name('admin.matching.recalculate');
    Route::put('/users/{user}', [AdminController::class, 'update'])->name('admin.users.update');
    Route::post('/users/{id}/delete', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::post('/users/{id}/suspend', [AdminController::class, 'suspendUser']) ->name('admin.users.suspend');
    Route::post('/users/{id}/reactivate', [AdminController::class, 'reactivateUser'])->name('admin.users.reactivate');


    Route::post('/verify-password', function (Illuminate\Http\Request $request) {
        if (\Hash::check($request->password, auth()->user()->password)) {
            return response()->json(['valid' => true]);
        }
        return response()->json(['valid' => false]);
    })->middleware('throttle:5,1');

    Route::post('/users', [AdminController::class, 'store'])->name('admin.users.store');
    Route::patch('/offres/{id}/deactivate', [AdminController::class, 'deactivateOffer'])->name('offres.deactivate');
    Route::patch('/offres/{id}/reactivate', [AdminController::class, 'reactivateOffer'])->name('offres.reactivate');

    // Routes pour la gestion des paramètres généraux
    Route::post('/parametres/general', [AdminController::class, 'updateGeneral'])->name('parametres.update-general');
    Route::delete('/parametres/remove-logo', [AdminController::class, 'removeLogo'])->name('parametres.remove-logo');
    Route::delete('/parametres/remove-favicon', [AdminController::class, 'removeFavicon'])->name('parametres.remove-favicon');

    Route::get('/users-details/{user}', [AdminController::class, 'show'])->name('admin.users.show');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'adminmarkAsRead'])->name('admin.notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('admin.allnotifications.read');


    // abonnement

       // Routes pour les abonnements
    Route::post('/abonnements', [AdminController::class, 'abonstore'])->name('admin.abonnements.store');
    Route::get('/abonnements/{abonnement}/fonctionnalites', [AdminController::class, 'getFonctionnalites'])->name('admin.abonnements.fonctionnalites');
    Route::put('/abonnements/{abonnement}', [AdminController::class, 'abonupdate'])->name('admin.abonnements.update');
    Route::delete('/abonnements/{abonnement}', [AdminController::class, 'abondestroy'])->name('admin.abonnements.destroy');
    Route::patch('/abonnements-utilisateurs/{subscription}/activate', [AdminController::class, 'activateSubscription'])->name('admin.user-subscriptions.activate');
    Route::patch('/abonnements-utilisateurs/{subscription}/cancel', [AdminController::class, 'cancelSubscription'])->name('admin.user-subscriptions.cancel');

    // Routes pour les référentiels (catégories, types d'offres, secteurs, skills, diplômes)
    Route::resource('categories', \App\Http\Controllers\Admin\CategorieController::class)
        ->except(['show', 'create', 'edit'])
        ->parameters(['categories' => 'categorie'])
        ->names([
            'index'   => 'admin.categories.index',
            'store'   => 'admin.categories.store',
            'update'  => 'admin.categories.update',
            'destroy' => 'admin.categories.destroy',
        ]);
    Route::resource('types-offres', \App\Http\Controllers\Admin\TypeOffreController::class)
        ->except(['show', 'create', 'edit'])
        ->parameters(['types-offres' => 'typeOffre'])
        ->names([
            'index'   => 'admin.types-offres.index',
            'store'   => 'admin.types-offres.store',
            'update'  => 'admin.types-offres.update',
            'destroy' => 'admin.types-offres.destroy',
        ]);
    Route::resource('secteurs', \App\Http\Controllers\Admin\SectorController::class)
        ->except(['show', 'create', 'edit'])
        ->names([
            'index'   => 'admin.secteurs.index',
            'store'   => 'admin.secteurs.store',
            'update'  => 'admin.secteurs.update',
            'destroy' => 'admin.secteurs.destroy',
        ]);
    Route::resource('skills', \App\Http\Controllers\Admin\SkillController::class)
        ->except(['show', 'create', 'edit'])
        ->names([
            'index'   => 'admin.skills.index',
            'store'   => 'admin.skills.store',
            'update'  => 'admin.skills.update',
            'destroy' => 'admin.skills.destroy',
        ]);
    Route::resource('diplomes', \App\Http\Controllers\Admin\DiplomeController::class)
        ->except(['show', 'create', 'edit'])
        ->names([
            'index'   => 'admin.diplomes.index',
            'store'   => 'admin.diplomes.store',
            'update'  => 'admin.diplomes.update',
            'destroy' => 'admin.diplomes.destroy',
        ]);
    });
});


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

Route::get('/dashboard', [App\Http\Controllers\DashboardRedirectController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


Route::get('/email/verify', function () {
    return view('auth.verify-email-entreprise');
})->name('enterprise.verification.notice');


 

Route::post('/email/resend-verification', [ResendVerificationEmailController::class, 'store'])
    ->middleware(['throttle:3,1'])
    ->name('enterprise.verification.resend');
