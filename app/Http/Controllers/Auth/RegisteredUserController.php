<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use App\Models\Entreprise;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use App\Models\EmailVerification;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail;


class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }


    public function entreprisecreate(): View
    {
        return view('auth.entreprise-register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'telephone' => ['required', 'string',  'unique:'.User::class],
           // 'adresse' => ['required', 'string',  'unique:'.User::class],
            'adresse' => ['required', 'string', ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'password' => Hash::make($request->password),
            'email_verified_at' => null,  
            'is_active' => false,
        ]);

        $candidateRole = Role::where('name', 'candidat')->first();
        if ($candidateRole) {
            $user->assignRole($candidateRole);
        }

        // Générer et sauvegarder le token AVEC LE MODÈLE
        $token = Str::random(60);
        
        EmailVerification::create([
            'email' => $user->email,
            'token' => $token,
            'created_at' => now()
        ]);

        // Nettoyer les anciens tokens
        EmailVerification::cleanupExpired();

        // En local/staging, le service mail peut etre indisponible; l'inscription ne doit pas tomber en 500.
        try {
            Mail::to($user->email)->send(new EmailVerificationMail($token, $user));
        } catch (\Throwable $e) {
            \Log::warning('Email de verification candidat non envoye', [
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('register')
            ->with('success', 'Un lien de vérification a été envoyé à votre adresse email.');

       /* Notification::create([
        'user_id' => $user->id,
        'role' => 'user',
        'title' => 'Bienvenue sur ProximaJob ! ',
        'message' => 'Complétez votre profil pour optimiser votre expérience et augmenter vos chances de succès.',
        'link' => 'javascript:void(0)',  
        'is_read' => false,
    ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('user.home', absolute: false));*/
    }



/*public function entrepreisestore(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'prenom' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users',
        'telephone' => 'required|string|max:20',
        'adresse' => 'required|string|max:255',
        'password' => 'required|confirmed|min:8',
        'company_name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'website' => 'nullable|url|max:255',
        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'neq' => 'required|string|max:255',
        
    ]);

    $user = User::create([
        'name' => $request->name,
        'prenom' => $request->prenom,
        'email' => $request->email,
        'telephone' => $request->telephone,
        'adresse' => $request->adresse,
        'password' => Hash::make($request->password),
        'is_active' => true,  
        'status' => 'pending', 
    ]);

    $role = Role::where('name', 'entreprise')->first();
    $user->assignRole($role);

    // Gestion du logo
    $logoPath = null;
    if ($request->hasFile('logo')) {
        $logo = $request->file('logo');
        $logoName = Str::slug($request->company_name) . '-logo-' . time() . '.' . $logo->getClientOriginalExtension();
        $logo->move(public_path('assets/images/entreprises'), $logoName);
        $logoPath = 'assets/images/entreprises/' . $logoName;
    }

    

    // Création de l'entreprise
    Entreprise::create([
        'user_id' => $user->id,
        'company_name' => $request->company_name,
        'description' => $request->description,
        'website' => $request->website,
        'logo' => $logoPath,
        'neq' => $request->neq,
        'status' => 'pending',  
        'verified_at' => null,
    ]);


     // Notification pour l'administrateur
    $adminUsers = User::whereHas('roles', function($q) {
        $q->where('name', 'admin');
    })->get();

    foreach ($adminUsers as $admin) {
        Notification::create([
            'user_id' => $admin->id,
            'role' => 'admin',
            'title' => 'Nouvelle entreprise en attente de validation',
            'message' => 'L\'entreprise "' . $request->company_name . '" a demandé une inscription et attend votre validation.',
            'link' => '/admin/users-details/'. $user->id,
            'is_read' => false,
        ]);
    }


    


    event(new Registered($user));
     $candidatRole = Role::where('name', 'candidat')->first();
    if ($candidatRole && $user->hasRole('candidat')) {
        $user->removeRole($candidatRole);
    }
   // Auth::login($user);

   // return redirect()->route('offres.publies')->with('success', 'Compte entreprise créé avec succès !');

       return redirect()->route('login')->with([
        'success' => 'Votre inscription a été soumise avec succès ! Votre compte est en attente de validation par nos administrateurs. Vous recevrez un email une fois votre compte activé.'
    ]);
}*/


public function entrepreisestore(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'prenom' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users',
        'telephone' => 'required|string|max:20',
        'adresse' => 'required|string|max:255',
        'password' => 'required|confirmed|min:8',
        'company_name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'website' => 'nullable|url|max:255',
        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'neq' => 'required|string|max:255',
    ]);

    $user = User::create([
        'name' => $request->name,
        'prenom' => $request->prenom,
        'email' => $request->email,
        'telephone' => $request->telephone,
        'adresse' => $request->adresse,
        'password' => Hash::make($request->password),
        'is_active' => false,  // Inactif tant que email non vérifié
        'status' => 'email_verification_pending',
        'email_verified_at' => null,
    ]);

    $role = Role::where('name', 'entreprise')->first();
    $user->assignRole($role);

    // Gestion du logo
    $logoPath = null;
    if ($request->hasFile('logo')) {
        $logo = $request->file('logo');
        $logoName = Str::slug($request->company_name) . '-logo-' . time() . '.' . $logo->getClientOriginalExtension();
        $logo->move(public_path('assets/images/entreprises'), $logoName);
        $logoPath = 'assets/images/entreprises/' . $logoName;
    }

    // Création de l'entreprise
    Entreprise::create([
        'user_id' => $user->id,
        'company_name' => $request->company_name,
        'description' => $request->description,
        'website' => $request->website,
        'logo' => $logoPath,
        'neq' => $request->neq,
        'status' => 'email_verification_pending',
        'verified_at' => null,
    ]);

    // Générer et sauvegarder le token AVEC LE MODÈLE
    $token = Str::random(60);
    
    EmailVerification::create([
        'email' => $user->email,
        'token' => $token,
        'created_at' => now()
    ]);

    // Nettoyer les anciens tokens
    EmailVerification::cleanupExpired();

    // Nettoyage du rôle candidat si existant
    $candidatRole = Role::where('name', 'candidat')->first();
    if ($candidatRole && $user->hasRole('candidat')) {
        $user->removeRole($candidatRole);
    }

    // En local/staging, le service mail peut etre indisponible; l'inscription ne doit pas tomber en 500.
    try {
        Mail::to($user->email)->send(new EmailVerificationMail($token, $user));
    } catch (\Throwable $e) {
        \Log::warning('Email de verification entreprise non envoye', [
            'email' => $user->email,
            'company_name' => $request->company_name,
            'error' => $e->getMessage(),
        ]);
    }

    // Redirection vers la page de confirmation d'envoi d'email
    return redirect()->route('enterprise.verification.notice')->with([
        'email' => $user->email,
        'company_name' => $request->company_name
    ]);
}


}
