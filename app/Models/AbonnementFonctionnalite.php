<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbonnementFonctionnalite extends Model
{
    use HasFactory;

    protected $fillable = [
        'abonnement_id',
        'nom',
        'icone',
        'actif',
        'ordre'
    ];

    protected $casts = [
        'actif' => 'boolean',
        'ordre' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function abonnement()
    {
        return $this->belongsTo(Abonnement::class);
    }
}