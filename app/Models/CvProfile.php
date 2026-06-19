<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CvProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nom',
        'prenom', 
        'email',
        'telephone',
        'adresse',
        'ville',
        'code_postal',
        'province',
        'langues_competences',
        'logiciels'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relations
 
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function formations()
    {
        return $this->hasMany(CvFormation::class)->orderBy('ordre');
    }

    public function competences()
    {
        return $this->hasMany(CvCompetence::class)->orderBy('ordre');
    }

    public function experiences()
    {
        return $this->hasMany(CvExperience::class)->orderBy('ordre');
    }

    public function langues()
    {
        return $this->hasMany(CvLangue::class)->orderBy('ordre');
    }

    public function perfectionnements()
    {
        return $this->hasMany(CvPerfectionnement::class)->orderBy('ordre');
    }

    public function benevolats()
    {
        return $this->hasMany(CvBenevolat::class)->orderBy('ordre');
    }

    public function cvGeneres()
    {
        return $this->hasMany(CvGenere::class);
    }

    // Méthodes utilitaires
    public function getNomCompletAttribute()
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function hasInformationsPersonnelles()
    {
        return !empty($this->nom) && !empty($this->prenom) && !empty($this->email);
    }
}