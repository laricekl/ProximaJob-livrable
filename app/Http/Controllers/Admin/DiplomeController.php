<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Diplome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiplomeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $niveau = $request->query('niveau');

        $diplomes = Diplome::query()
            ->withCount('offres')
            ->when($search, fn($q) => $q->where('nom_diplome', 'LIKE', '%'.$search.'%')->orWhere('sigle', 'LIKE', '%'.$search.'%'))
            ->when($niveau, fn($q) => $q->where('niveau_education', $niveau))
            ->orderBy('niveau_education')->orderBy('nom_diplome')
            ->paginate(20)
            ->withQueryString();

        $niveaux = [
            'SECONDAIRE'               => 'Secondaire',
            'COLLEGIAL'                => 'Collégial',
            'UNIVERSITAIRE_1ER_CYCLE'  => 'Universitaire 1er cycle',
            'UNIVERSITAIRE_2E_CYCLE'   => 'Universitaire 2e cycle',
            'UNIVERSITAIRE_3E_CYCLE'   => 'Universitaire 3e cycle',
            'PROFESSIONNEL'            => 'Professionnel',
        ];

        return view('admin.diplomes.index', compact('diplomes', 'niveaux', 'search', 'niveau'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom_diplome'      => 'required|string|max:255|unique:diplome,nom_diplome',
            'nom_anglais'      => 'nullable|string|max:255',
            'sigle'            => 'nullable|string|max:50',
            'niveau_education' => 'required|string|in:SECONDAIRE,COLLEGIAL,UNIVERSITAIRE_1ER_CYCLE,UNIVERSITAIRE_2E_CYCLE,UNIVERSITAIRE_3E_CYCLE,PROFESSIONNEL',
            'duree_annees'     => 'nullable|numeric|min:0|max:15',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.diplomes.index')->withErrors($validator)->withInput();
        }

        Diplome::create([
            'nom_diplome'      => $request->nom_diplome,
            'nom_anglais'      => $request->nom_anglais,
            'sigle'            => $request->sigle,
            'niveau_education' => $request->niveau_education,
            'duree_annees'     => $request->duree_annees,
            'statut'           => 'ACTIF',
        ]);

        return redirect()->route('admin.diplomes.index')->with('success', 'Diplôme « '.$request->nom_diplome.' » créé.');
    }

    public function update(Request $request, Diplome $diplome)
    {
        $validator = Validator::make($request->all(), [
            'nom_diplome'      => 'required|string|max:255|unique:diplome,nom_diplome,'.$diplome->id,
            'nom_anglais'      => 'nullable|string|max:255',
            'sigle'            => 'nullable|string|max:50',
            'niveau_education' => 'required|string|in:SECONDAIRE,COLLEGIAL,UNIVERSITAIRE_1ER_CYCLE,UNIVERSITAIRE_2E_CYCLE,UNIVERSITAIRE_3E_CYCLE,PROFESSIONNEL',
            'duree_annees'     => 'nullable|numeric|min:0|max:15',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.diplomes.index')->withErrors($validator)->withInput();
        }

        $diplome->update([
            'nom_diplome'      => $request->nom_diplome,
            'nom_anglais'      => $request->nom_anglais,
            'sigle'            => $request->sigle,
            'niveau_education' => $request->niveau_education,
            'duree_annees'     => $request->duree_annees,
        ]);

        return redirect()->route('admin.diplomes.index')->with('success', 'Diplôme mis à jour.');
    }

    public function destroy(Diplome $diplome)
    {
        if ($diplome->offres()->count() > 0) {
            return redirect()->route('admin.diplomes.index')->with('error', 'Diplôme lié à '.$diplome->offres()->count().' offre(s).');
        }
        $nom = $diplome->nom_diplome;
        $diplome->delete();
        return redirect()->route('admin.diplomes.index')->with('success', 'Diplôme « '.$nom.' » supprimé.');
    }
}
