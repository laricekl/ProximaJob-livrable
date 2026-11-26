<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Abonnement;
use App\Models\AbonnementFonctionnalite;
use App\Models\SiteSetting;
use App\Mail\PublicContactMessage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ParametresController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return redirect()->route('contact');
    }
    public function contact()
    {
        $infos = SiteSetting::first();

        return view("parametres.contact" , compact('infos'));
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'firstname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $destination = SiteSetting::first()?->email ?: config('mail.from.address');

        try {
            Mail::to($destination)->send(new PublicContactMessage($validated));
        } catch (\Throwable $exception) {
            Log::error('Échec de transmission du formulaire de contact', [
                'destination' => $destination,
                'error' => $exception->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Votre message n’a pas pu être transmis. Veuillez réessayer ou nous écrire directement.');
        }

        return redirect()->route('contact')
            ->with('success', 'Votre message a bien été envoyé. Nous vous recontacterons rapidement.');
    }
    public function abonnement()
    {

         // Vitrine publique : seuls les plans actifs sont présentés (un plan
         // désactivé, ex. l'ancien Pro candidat, ne doit plus y être vendu).
         $abonnements = Abonnement::where('actif', true)->get();
         $fonctionnalites = AbonnementFonctionnalite::get();
        return view("parametres.abonnements", compact('abonnements' , 'fonctionnalites'));
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
        //
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
