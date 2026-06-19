<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abonnement extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'duree',
        'montant',
        'description',
        'couleur',
        'populaire',
        'actif'
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'populaire' => 'boolean',
        'actif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'features' =>'array',
        'is_premium' =>'boolean'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_abonnements')
                    ->withPivot('date_debut', 'date_fin', 'status')
                    ->withTimestamps();
    }

    public function fonctionnalites()
    {
        return $this->hasMany(AbonnementFonctionnalite::class)->orderBy('actif', 'desc');
    }

    public function fonctionnalitesActives()
    {
        return $this->hasMany(AbonnementFonctionnalite::class)->where('actif', true)->orderBy('ordre');
    }

    // Scope pour obtenir les abonnements actifs
    public function scopeActifs($query)
    {
        return $query->where('actif', true);
    }

    // Méthode pour formater le prix
    public function getPrixFormatteAttribute()
    {
        return number_format($this->montant, 2, ',', ' ') . '$ CAD';
    }

    // Méthode pour obtenir la période formatée
    public function getPeriodeFormatteeAttribute()
    {
        $periodes = [
            'mensuel' => '/mois',
            'trimestriel' => '/trimestre',
            'annuel' => '/an'
        ];

        return $periodes[$this->duree] ?? '/' . $this->duree;
    }
}
