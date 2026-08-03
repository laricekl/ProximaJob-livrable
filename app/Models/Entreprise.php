<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entreprise extends Model
{
    use HasFactory, SoftDeletes;

    private const STATUS_ALIASES = [
        'email_verification_pending' => 'pending',
    ];

    protected $fillable = [
        'user_id',
        'company_name',
        'description',
        'website',
        'logo',
        'neq',
        'status',
        'rejection_reason',
        'verified_at',
        'is_featured',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'verified_at' => 'datetime',
        'is_featured' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function offres()
    {
        return $this->hasMany(Offre::class);
    }

    public function candidatures()
    {
        return $this->hasManyThrough(Postulation::class, Offre::class, 'entreprise_id', 'offre_id');
    }

    public static function statusVariants(string $status): array
    {
        return array_merge([$status], array_keys(self::STATUS_ALIASES, $status, true));
    }

    public function getNormalizedStatusAttribute(): string
    {
        return self::STATUS_ALIASES[$this->status] ?? ($this->status ?: 'pending');
    }
}
