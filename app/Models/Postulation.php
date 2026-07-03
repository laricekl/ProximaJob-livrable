<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Postulation extends Model
{
    use HasFactory, SoftDeletes;

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
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'autopostulation' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function offre()
    {
        return $this->belongsTo(Offre::class , 'offre_id');
    }
    public function autresDocs()
    {
        return $this->hasMany(AutresDoc::class, 'id_postulation');
    }
}