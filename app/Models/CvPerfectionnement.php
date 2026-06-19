<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CvPerfectionnement extends Model
{
    use HasFactory;

    protected $fillable = [
        'cv_profile_id',
        'annee',
        'formation',
        'etablissement',
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
}