<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Offre extends Model
{
    use HasFactory, SoftDeletes;
 
    protected $fillable = [
    'entreprise_id',
    'titre',
    'poste',
    'description',
    'localisation',
    'categorie_id',
    'sector_id',
    'type_id',
    'status',
    'experience',
    'salaire_min',
    'salaire_max',
    'slug',
    'competences',
    'langues',
    'annee_experience',
    'criteres',
    'missions',
    'objectif',
    'avantages',
    'date_fin',
    
     
    'employment_type',
    'remote_work',
    'job_category',
    'salary_type',
    'start_date',
    'required_experience',
    'education_level',
    'responsibilities',
];

    protected $casts = [
        'salaire' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
         
    ];

    protected static function booted()
    {
        static::creating(function ($offre) {
            $offre->slug = Str::slug($offre->poste . '-' . uniqid());
        });
    }

    // Relations
    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }

 public function diplomes() {
    return $this->belongsToMany(Diplome::class, 'offre_diplome')
                ->withPivot('obligatoire')
                ->withTimestamps();
}

    public function sector()
    {
        return $this->belongsTo(Sector::class, 'sector_id');
    }

    public function type()
    {
        return $this->belongsTo(TypeOffre::class, 'type_id');
    }

    public function postulations()
    {
        return $this->hasMany(Postulation::class, 'offre_id');
    }

    public function candidats()
    {
        return $this->belongsToMany(User::class, 'postulations')
            ->withPivot('cv', 'lettre_motivation', 'status')
            ->withTimestamps();
    }


    public function skills()
    {
        return $this->hasMany(JobOfferSkill::class, 'job_offer_id');
    }

    // Accesseurs pour le statut avec couleur
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'bg-green-100 text-green-800',
            'expire' => 'bg-red-100 text-red-800',
            'desactive' => 'bg-yellow-100 text-yellow-800',
            'brouillon' => 'bg-slate-100 text-slate-700',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getAdminStatusAttribute(): string
    {
        if ($this->status === 'desactive') {
            return 'desactive';
        }

        if ($this->status === 'brouillon') {
            return 'brouillon';
        }

        if ($this->is_expired || $this->status === 'expire') {
            return 'expire';
        }

        return 'active';
    }

    public function getAdminStatusLabelAttribute(): string
    {
        return match ($this->admin_status) {
            'desactive' => 'Désactivée',
            'brouillon' => 'Brouillon',
            'expire' => 'Expirée',
            default => 'Publiée',
        };
    }

    public function getAdminStatusColorAttribute(): string
    {
        return match ($this->admin_status) {
            'desactive' => 'bg-yellow-100 text-yellow-800',
            'brouillon' => 'bg-amber-50 text-amber-700',
            'expire' => 'bg-red-100 text-red-700',
            default => 'bg-green-100 text-green-700',
        };
    }

    // Vérifier si l'offre est expirée
    public function getIsExpiredAttribute()
    {
        return $this->date_fin && Carbon::parse($this->date_fin)->isPast();
    }

    // Récupérer les compétences du secteur associé
    public function getSectorSkillsAttribute()
    {
        return $this->sector ? $this->sector->skills : collect();
    }

    // Récupérer les compétences clés du secteur
    public function getSectorCoreSkillsAttribute()
    {
        return $this->sector ? $this->sector->getCoreSkills() : collect();
    }

    // Scopes pour les filtres
    public function scopeByEntreprise($query, $entrepriseId)
    {
        if ($entrepriseId) {
            return $query->where('entreprise_id', $entrepriseId);
        }
        return $query;
    }

    public function scopeByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    public function scopeBySector($query, $sectorId)
    {
        if ($sectorId) {
            return $query->where('sector_id', $sectorId);
        }
        return $query;
    }

    public function apply(User $user, Offre $offre)
    {
        return $user->hasRole('candidat');
    } 
    
   
}
