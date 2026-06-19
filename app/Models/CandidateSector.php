<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateSector extends Model
{
    use HasFactory;

    protected $table = 'candidate_sectors';

    protected $fillable = [
        'candidate_id',
        'sector_id',
        'diplome_id',
        'experience_years',
    ];

    protected $casts = [
         'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relations
     */
    public function candidate()
    {
        
        return $this->belongsTo(User::class, 'candidate_id');
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class, 'sector_id');
    }
    // Relation avec Diplome
    public function diplome()
    {
        return $this->belongsTo(Diplome::class, 'diplome_id');
    }
}
