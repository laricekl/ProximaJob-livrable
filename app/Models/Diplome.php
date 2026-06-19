<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Eloquent pour la table diplome
 */
class Diplome extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'nom_diplome',
        'nom_anglais',
        'sigle',
        'niveau_education',
        'duree_annees',
        'statut'
    ];

    protected $casts = [
        'duree_annees' => 'float',
        'date_creation' => 'datetime',
        'date_modification' => 'datetime'
    ];

    // Constantes pour les niveaux d'éducation
    const NIVEAU_SECONDAIRE = 'SECONDAIRE';
    const NIVEAU_COLLEGIAL = 'COLLEGIAL';
    const NIVEAU_UNIVERSITAIRE_1ER_CYCLE = 'UNIVERSITAIRE_1ER_CYCLE';
    const NIVEAU_UNIVERSITAIRE_2E_CYCLE = 'UNIVERSITAIRE_2E_CYCLE';
    const NIVEAU_UNIVERSITAIRE_3E_CYCLE = 'UNIVERSITAIRE_3E_CYCLE';
    const NIVEAU_PROFESSIONNEL = 'PROFESSIONNEL';

    // Constantes pour le statut
    const STATUT_ACTIF = 'ACTIF';
    const STATUT_INACTIF = 'INACTIF';

    // Relation avec CandidateSector
    public function candidateSectors()
    {
        return $this->hasMany(CandidateSector::class, 'diplome_id');
    }

    // Relation avec les candidats via CandidateSector
    public function candidates()
    {
        return $this->hasManyThrough(
            User::class, 
            CandidateSector::class, 
            'diplome_id', 
            'id', 
            'id', 
            'candidate_id'
        );
    }

    // Scopes pour filtrer par niveau
    public function scopeSecondaire($query)
    {
        return $query->where('niveau_education', self::NIVEAU_SECONDAIRE);
    }

    public function scopeCollegial($query)
    {
        return $query->where('niveau_education', self::NIVEAU_COLLEGIAL);
    }

    public function scopeUniversitaire($query)
    {
        return $query->whereIn('niveau_education', [
            self::NIVEAU_UNIVERSITAIRE_1ER_CYCLE,
            self::NIVEAU_UNIVERSITAIRE_2E_CYCLE,
            self::NIVEAU_UNIVERSITAIRE_3E_CYCLE
        ]);
    }

    public function scopeActif($query)
    {
        return $query->where('statut', self::STATUT_ACTIF);
    }

    // Méthode pour obtenir le libellé du niveau d'éducation
    public function getNiveauLibelleAttribute()
    {
        $niveaux = [
            self::NIVEAU_SECONDAIRE => 'Secondaire',
            self::NIVEAU_COLLEGIAL => 'Collégial',
            self::NIVEAU_UNIVERSITAIRE_1ER_CYCLE => 'Universitaire 1er cycle',
            self::NIVEAU_UNIVERSITAIRE_2E_CYCLE => 'Universitaire 2e cycle',
            self::NIVEAU_UNIVERSITAIRE_3E_CYCLE => 'Universitaire 3e cycle',
            self::NIVEAU_PROFESSIONNEL => 'Professionnel'
        ];

        return $niveaux[$this->niveau_education] ?? $this->niveau_education;
    }

    // Timestamps personnalisés
    const CREATED_AT = 'date_creation';
    const UPDATED_AT = 'date_modification';


    

      
public function offres() {
    return $this->belongsToMany(Offre::class, 'offre_diplome')
                ->withPivot('obligatoire')
                ->withTimestamps();
}
}
