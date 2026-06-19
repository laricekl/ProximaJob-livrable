<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CvCompetence extends Model
{
    use HasFactory;

    protected $fillable = [
        'cv_profile_id',
        'description',
        'type',
        'ordre'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function cvProfile()
    {
        return $this->belongsTo(CvProfile::class);
    }

    // Scope pour les types de compétences
    public function scopeSpecifiques($query)
    {
        return $query->where('type', 'specifique');
    }

    public function scopeGenerales($query)
    {
        return $query->where('type', 'generale');
    }
}