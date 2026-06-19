<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CvExperience extends Model
{
    use HasFactory;

    protected $fillable = [
        'cv_profile_id',
        'periode',
        'poste',
        'entreprise',
        'description',
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