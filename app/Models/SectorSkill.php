<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectorSkill extends Model
{
    use HasFactory;
protected $table = 'sector_skills';
    protected $fillable = [
        'sector_id',
        'skill_id',
        'relevance_score',
        'is_core_skill',
    ];

    protected $casts = [
        'relevance_score' => 'integer',
        'is_core_skill' => 'boolean',
    ];

    // Relations
    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    // Scopes
    public function scopeCoreSkills($query)
    {
        return $query->where('is_core_skill', true);
    }

    public function scopeByRelevance($query, $minScore = 3)
    {
        return $query->where('relevance_score', '>=', $minScore);
    }

    public function scopeForSector($query, $sectorId)
    {
        return $query->where('sector_id', $sectorId);
    }

    // Accessors
    public function getRelevanceLabelAttribute()
    {
        $labels = [
            1 => 'Peu pertinent',
            2 => 'Utile',
            3 => 'Important',
            4 => 'Très important',
            5 => 'Essentiel'
        ];

        return $labels[$this->relevance_score] ?? 'Non défini';
    }

    public function getCoreSkillLabelAttribute()
    {
        return $this->is_core_skill ? 'Compétence clé' : 'Compétence secondaire';
    }
}