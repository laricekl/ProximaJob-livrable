<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'prenom',
        'email',
        'password',
        'password_changed_at',
        'telephone',
        'adresse',
        'profile_photo_path',
        'salary_expectation_min',
        'provider',
        'provider_id',
        'avatar',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'email_verified_at',
        'last_login_at',
        'status',
        'cv',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'two_factor_confirmed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password_changed_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function hasProvider($provider)
    {
        return $this->provider === $provider && ! empty($this->provider_id);
    }

    public function cvProfile()
    {
        return $this->hasOne(CvProfile::class);
    }

    // Pour accéder directement aux formations via l'utilisateur
    public function formations()
    {
        return $this->hasManyThrough(CvFormation::class, CvProfile::class);
    }

    /**
     * Relation avec le modèle Entreprise
     */
    public function entreprise()
    {
        return $this->hasOne(Entreprise::class);
    }

    /**
     * Relation avec les postulations
     */
    public function postulations()
    {
        return $this->hasMany(Postulation::class);
    }

    /**
     * Relation many-to-many avec les offres via les postulations
     */
    public function offresPostulees()
    {
        return $this->belongsToMany(Offre::class, 'postulations')
            ->withPivot('cv', 'lettre_motivation', 'status', 'created_at', 'updated_at');
    }

    /**
     * Relation avec les abonnements
     */
    public function abonnements()
    {
        return $this->belongsToMany(Abonnement::class, 'user_abonnements')
            ->withPivot('date_debut', 'date_fin', 'status', 'created_at', 'updated_at');
    }

    public function hasCurrentPlan(string ...$names): bool
    {
        $normalizedNames = collect($names)->map(fn ($name) => mb_strtolower($name))->all();

        return $this->abonnements()
            ->wherePivot('status', 'Actif')
            ->wherePivot('date_debut', '<=', now())
            ->wherePivot('date_fin', '>=', now())
            ->whereIn(\DB::raw('LOWER(abonnements.nom)'), $normalizedNames)
            ->exists();
    }

    /**
     * Vérifie si l'utilisateur a un abonnement actif
     */
    public function hasActiveAbonnement()
    {
        return $this->abonnements()
            ->where('date_debut', '<=', now())
            ->where('date_fin', '>=', now())
            ->where('status', 'Actif')
            ->exists();
    }

    public function scopeWithActiveAbonnement($query)
    {
        return $query->whereHas('abonnements', function ($q) {
            $q->where('date_debut', '<=', now())
                ->where('date_fin', '>=', now())
                ->where('status', 'Actif');
        });
    }

    /**
     * Méthodes pratiques pour vérifier les rôles
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isEntreprise(): bool
    {
        return $this->hasRole('entreprise');
    }

    public function isCandidat(): bool
    {
        return $this->hasRole('candidat');
    }

    public function isMarketing(): bool
    {
        return $this->hasRole('Marketing');
    }

    /**
     * Méthodes pratiques pour vérifier les permissions
     * (basées sur votre seeder)
     */
    public function canManageOffres(): bool
    {
        return $this->can('offres.manage') || $this->isAdmin();
    }

    public function canApplyToOffres(): bool
    {
        return $this->can('postulations.apply') && $this->isCandidat();
    }

    public function canManagePostulations(): bool
    {
        return $this->can('postulations.manage') && $this->isEntreprise();
    }

    public function canSubscribe(): bool
    {
        return $this->can('abonnements.subscribe');
    }

    /**
     * Scope pour les administrateurs
     */
    public function scopeAdmins($query)
    {
        return $query->role('admin');
    }

    /**
     * Scope pour les entreprises
     */
    public function scopeEntreprises($query)
    {
        return $query->role('entreprise');
    }

    /**
     * Scope pour les candidats
     */
    public function scopeCandidats($query)
    {
        return $query->role('candidat');
    }

    public function profileCompletionPercentage()
    {
        $this->loadMissing([
            'cvProfile.experiences',
            'cvProfile.formations',
            'cvProfile.competences',
            'cvProfile.langues',
            'candidateSector',
            'skills',
        ]);

        $profile = $this->cvProfile;
        $totalFields = 12;
        $completedFields = 0;

        if (! empty($profile?->nom ?: $this->name)) {
            $completedFields++;
        }
        if (! empty($profile?->prenom ?: $this->prenom)) {
            $completedFields++;
        }
        if (! empty($profile?->email ?: $this->email)) {
            $completedFields++;
        }
        if (! empty($profile?->telephone ?: $this->telephone)) {
            $completedFields++;
        }
        if (! empty($profile?->ville ?: $profile?->adresse ?: $this->adresse)) {
            $completedFields++;
        }
        if (! empty($this->profile_photo_path)) {
            $completedFields++;
        }
        if ($profile || ! empty($this->cv)) {
            $completedFields++;
        }
        if (! empty($this->salary_expectation_min)) {
            $completedFields++;
        }
        if (($profile?->experiences?->count() ?? 0) > 0 || ($this->candidateSector?->experience_years ?? 0) > 0) {
            $completedFields++;
        }
        if (($profile?->formations?->count() ?? 0) > 0 || ! empty($this->candidateSector?->diplome_id)) {
            $completedFields++;
        }
        if ($this->skills->isNotEmpty() || ($profile?->competences?->count() ?? 0) > 0) {
            $completedFields++;
        }
        if (($profile?->langues?->count() ?? 0) > 0 || ! empty($profile?->langues_competences)) {
            $completedFields++;
        }

        return (int) round(($completedFields / $totalFields) * 100);
    }

    public function hasAppliedTo(Offre $offre)
    {
        return $this->postulations()->where('offre_id', $offre->id)->exists();
    }

    public function getAvatarInitials(): string
    {
        return strtoupper(substr((string) $this->name, 0, 1).substr((string) $this->prenom, 0, 1));
    }

    // Accesseur pour compatibilité Blade : $user->initials
    public function getInitialsAttribute(): string
    {
        return $this->getAvatarInitials();
    }

    public function getAvatarColor(): string
    {
        if ($this->hasRole('admin')) {
            return '#F59E0B';
        } elseif ($this->hasRole('entreprise')) {
            return '#3B82F6';
        }

        return '#10B981'; // vert par défaut (candidat)
    }

    /* Relation avec les notifications
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    /**
     * Notifications non lues
     */
    public function unreadNotifications()
    {
        return $this->notifications()->where('is_read', false);
    }

    public function getDisplayStatusAttribute()
    {
        if ($this->status === 'Suspendu' || $this->status === 'inactive') {
            return $this->status;
        }

        if ($this->hasRole('entreprise') && $this->entreprise) {
            return $this->entreprise->status;
        }

        return $this->status;
    }

    public function getDisplayStatusLabelAttribute()
    {
        $status = $this->display_status;

        $labels = [
            'approved' => 'Approuvé',
            'pending' => 'En attente de validation',
            'rejected' => 'Rejeté',
            'suspended' => 'Suspendu',
            'Actif' => 'Actif',
            'Suspendu' => 'Suspendu',
            'inactive' => 'Inactif',
        ];

        return $labels[$status] ?? $status;
    }

    public function getDisplayStatusColorAttribute()
    {
        $status = $this->display_status;

        $colors = [
            'approved' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'rejected' => 'bg-red-100 text-red-800',
            'suspended' => 'bg-red-100 text-red-800',
            'Actif' => 'bg-green-100 text-green-800',
            'Suspendu' => 'bg-red-100 text-red-800',
            'inactive' => 'bg-gray-100 text-gray-800',
        ];

        return $colors[$status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getAdminRoleLabelAttribute(): string
    {
        if ($this->hasRole('admin')) {
            return 'Admin';
        }

        if ($this->hasRole('Marketing')) {
            return 'Marketing';
        }

        if ($this->hasRole('entreprise')) {
            return 'Entreprise';
        }

        return 'Candidat';
    }

    public function getAdminRoleColorAttribute(): string
    {
        if ($this->hasRole('admin')) {
            return 'bg-orange-50 text-orange-700';
        }

        if ($this->hasRole('Marketing')) {
            return 'bg-indigo-50 text-indigo-700';
        }

        if ($this->hasRole('entreprise')) {
            return 'bg-purple-50 text-purple-700';
        }

        return 'bg-blue-50 text-blue-700';
    }

    public function candidateSector(): HasOne
    {
        return $this->hasOne(CandidateSector::class, 'candidate_id');
    }

    // AJOUTEZ CETTE RELATION MANQUANTE POUR CORRIGER L'ERREUR
    public function candidateSkills(): HasMany
    {
        return $this->hasMany(CandidateSkill::class, 'candidate_id');
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'candidate_skills', 'candidate_id', 'skill_id')
            ->withPivot('level', 'years_experience', 'is_validated')
            ->withTimestamps();
    }

    public function getExperienceYearsAttribute()
    {
        return $this->candidateSector?->experience_years ?? 0;
    }
}
