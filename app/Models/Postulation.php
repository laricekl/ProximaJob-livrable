<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Postulation extends Model
{
    use HasFactory, SoftDeletes;

    private const STATUS_ALIASES = [
        'acceptee' => 'accepted',
        'refusee' => 'rejected',
    ];

    protected $fillable = [
        'user_id',
        'offre_id',
        'cv',
        'autopostulation',
        'lettre_motivation',
        'cover_letter',
        'status',
        'match_score',
        'application_date',
        'algorithm_version',
        'match_details',
        'admin_notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'autopostulation' => 'boolean',
        'match_details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function offre()
    {
        return $this->belongsTo(Offre::class, 'offre_id');
    }

    public static function normalizeStatus(?string $status): string
    {
        return self::STATUS_ALIASES[$status] ?? ($status ?: 'en_attente');
    }

    public static function statusVariants(string $status): array
    {
        return array_merge([$status], array_keys(self::STATUS_ALIASES, $status, true));
    }

    public function getNormalizedStatusAttribute(): string
    {
        return self::normalizeStatus($this->status);
    }

    public function autresDocs()
    {
        return $this->hasMany(AutresDoc::class, 'id_postulation');
    }
}
