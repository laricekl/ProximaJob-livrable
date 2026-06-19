<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CvFormation extends Model
{
    use HasFactory;

    protected $fillable = [
        'cv_profile_id',
        'periode',
        'diplome',           
        'diplome_id',       
        'etablissement',
        'ordre'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function cvProfile()
    {
        return $this->belongsTo(CvProfile::class, 'diplome_id');
    }

     
    public function diplome()
    {
        return $this->belongsTo(Diplome::class, 'diplome_id');
    }

    // Relation indirecte vers User (inchangée)
    public function user()
    {
        return $this->hasOneThrough(User::class, CvProfile::class, 'id', 'id', 'cv_profile_id', 'user_id');
    }
}