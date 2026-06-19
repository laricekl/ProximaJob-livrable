<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateSkill extends Model
{
    use HasFactory;

    protected $table = 'candidate_skills';

    protected $fillable = [
        'candidate_id',
        'skill_id',
        'level',
        'years_experience',
        'is_validated',
    ];

    protected $casts = [
        'is_validated' => 'boolean',
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

    public function skill()
    {
        return $this->belongsTo(Skill::class, 'skill_id');
    }
}
