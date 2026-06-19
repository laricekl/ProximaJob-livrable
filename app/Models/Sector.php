<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Sector extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'scian_code',
        'parent_id',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation parent : un secteur peut avoir un parent
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Sector::class, 'parent_id');
    }

    /**
     * Relation enfants : un secteur peut avoir plusieurs sous-secteurs
     */
    public function children(): HasMany
    {
        return $this->hasMany(Sector::class, 'parent_id');
    }

    /**
     * Relation many-to-many avec les compétences via la table pivot sector_skills
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'sector_skills')
                    ->withPivot('relevance_score', 'is_core_skill')
                    ->withTimestamps();
    }

    /**
     * Relation directe avec la table pivot sector_skills
     */
    public function sectorSkills(): HasMany
    {
        return $this->hasMany(SectorSkill::class);
    }

    /**
     * Scope pour les secteurs actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les secteurs principaux (sans parent)
     */
    public function scopeMain($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope pour les sous-secteurs (avec parent)
     */
    public function scopeSub($query)
    {
        return $query->whereNotNull('parent_id');
    }

    /**
     * Boot du modèle pour générer automatiquement le slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sector) {
            if (empty($sector->slug)) {
                $sector->slug = Str::slug($sector->name);
            }
        });

        static::updating(function ($sector) {
            if ($sector->isDirty('name') && empty($sector->slug)) {
                $sector->slug = Str::slug($sector->name);
            }
        });
    }

    /**
     * Vérifie si le secteur est un secteur principal
     */
    public function isMain(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Vérifie si le secteur est un sous-secteur
     */
    public function isSub(): bool
    {
        return !is_null($this->parent_id);
    }

    /**
     * Récupère le chemin complet du secteur (parent > enfant)
     */
    public function getFullPathAttribute(): string
    {
        $path = $this->name;
        $parent = $this->parent;

        while ($parent) {
            $path = $parent->name . ' > ' . $path;
            $parent = $parent->parent;
        }

        return $path;
    }

    /**
     * Récupère uniquement les compétences clés du secteur
     */
    public function getCoreSkills()
    {
        return $this->skills()->wherePivot('is_core_skill', true)->get();
    }

    /**
     * Récupère les compétences par niveau de pertinence minimum
     */
    public function getSkillsByRelevance(int $minScore = 3)
    {
        return $this->skills()
                    ->wherePivot('relevance_score', '>=', $minScore)
                    ->orderByPivot('relevance_score', 'desc')
                    ->get();
    }

    /**
     * Récupère les compétences par catégorie
     */
    public function getSkillsByCategory(string $category)
    {
        return $this->skills()->where('skills.category', $category)->get();
    }

    /**
     * Compte le nombre de compétences clés
     */
    public function getCoreSkillsCountAttribute(): int 
    {
        return $this->skills()->wherePivot('is_core_skill', true)->count();
    }

    /**
     * Compte le nombre total de compétences associées
     */
    public function getTotalSkillsCountAttribute(): int
    {
        return $this->skills()->count();
    }
}