<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CvGenere extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'cv_profile_id',
        'offre_id',
        'nom_fichier',
        'chemin_fichier'
    ];

    protected $casts = [
        'date_generation' => 'datetime',
    ];

    public function cvProfile()
    {
        return $this->belongsTo(CvProfile::class);
    }

    public function getDisplayNameAttribute(): string
    {
        $fullName = trim(($this->cvProfile?->prenom ?? '').' '.($this->cvProfile?->nom ?? ''));

        return trim('CV'.($fullName !== '' ? ' - '.$fullName : ''));
    }

    // Accessor pour l'URL du fichier
    public function getUrlFichierAttribute()
    {
        return asset('storage/' . $this->chemin_fichier);
    }

    // Accessor pour le chemin complet du fichier
    public function getCheminCompletAttribute()
    {
        return storage_path('app/public/' . $this->chemin_fichier);
    }
}
