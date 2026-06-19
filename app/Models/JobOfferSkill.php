<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOfferSkill extends Model
{
    use HasFactory;

    protected $table = 'job_offer_skills';

    protected $fillable = [
        'job_offer_id',
        'skill_id',
        'is_required',
        'weight',
        'skill_type'
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'weight' => 'integer',
    ];

    /**
     * Relations
     */
    public function jobOffer()
    {
        
        return $this->belongsTo(Offre::class, 'job_offer_id');
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class, 'skill_id');
    }
}
