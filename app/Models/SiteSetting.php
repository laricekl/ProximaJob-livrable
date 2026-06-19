<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    /**
     * Le nom de la table associée au modèle.
     *
     * @var string
     */
    protected $table = 'site_settings';

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'site_nom',
        'email', 
        'timezone',
        'logo',
        'favicon'
    ];

    /**
     * Les attributs qui devraient être castés.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Valeurs par défaut pour les attributs.
     *
     * @var array
     */
    protected $attributes = [
        'timezone' => 'UTC',
    ];

    /**
     * Récupère le chemin complet du logo
     *
     * @return string|null
     */
    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/'.$this->logo) : null;
    }

    /**
     * Récupère le chemin complet du favicon
     *
     * @return string|null
     */
    public function getFaviconUrlAttribute()
    {
        return $this->favicon ? asset('storage/'.$this->favicon) : null;
    }
}