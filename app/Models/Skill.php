<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'category',
        'cnp_code',
        'importance_level',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'importance_level' => 'integer'
    ];

    // Relations
    public function sectors()
    {
        return $this->belongsToMany(Sector::class, 'sector_skills')
                    ->withPivot('relevance_score', 'is_core_skill')
                    ->withTimestamps();
    }

    public function sectorSkills()
    {
        return $this->hasMany(SectorSkill::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByImportance($query, $level)
    {
        return $query->where('importance_level', '>=', $level);
    }

    // Accessors
    public function getCategoryLabelAttribute()
    {
        $labels = [
            'technique' => 'Technique',
            'transversale' => 'Transversale',
            'numerique' => 'Numérique',
            'linguistique' => 'Linguistique',
            'gestion' => 'Gestion',
            'commercial' => 'Commercial'
        ];

        return $labels[$this->category] ?? ucfirst($this->category);
    }

    public function getImportanceLabelAttribute()
    {
        $labels = [
            1 => 'Basique',
            2 => 'Utile',
            3 => 'Important',
            4 => 'Très important',
            5 => 'Essentiel'
        ];

        return $labels[$this->importance_level] ?? 'Non défini';
    }
}