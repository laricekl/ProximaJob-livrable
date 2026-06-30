<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Abonnement;
use App\Models\AbonnementFonctionnalite;
use App\Models\SiteSetting;
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

        Log::info('New public contact message received', $validated);

        return redirect()->route('contact')
            ->with('success', 'Votre message a bien ete envoye. Nous vous recontacterons rapidement.');
    }
    public function abonnement()
    {

         $abonnements = Abonnement::get();
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
