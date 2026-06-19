<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CvLangue extends Model
{
    use HasFactory;

    protected $fillable = [
        'cv_profile_id',
        'nom',
        'niveau',
        'ordre'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function cvProfile()
    {
        return $this->belongsTo(CvProfile::class);
    }

    // Méthodes utilitaires pour les niveaux
    public static function getNiveauxOptions()
    {
        return [
            'Langue maternelle' => 'Langue maternelle',
            'Courant' => 'Courant',
            'Intermédiaire' => 'Intermédiaire',
            'Notions de base' => 'Notions de base',
            'Connaissances de base' => 'Connaissances de base',
        ];
    }
}