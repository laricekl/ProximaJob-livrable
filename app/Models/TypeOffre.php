<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeOffre extends Model
{
    use HasFactory;

    protected $table = 'types_offres';

    protected $fillable = ['nom'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];


        public function offres()
    {
        return $this->hasMany(Offre::class, 'type_id'); // Clé étrangère dans la table offres
    }

}