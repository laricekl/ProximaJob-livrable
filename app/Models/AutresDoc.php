<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutresDoc extends Model
{
    use HasFactory;

     protected $table = 'autres_docs';

    protected $fillable = [
        'id_postulation',
        'intitule',
        'description',
        'path',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function postulation()
    {
        return $this->belongsTo(Postulation::class , 'id_postulation');
    }
}