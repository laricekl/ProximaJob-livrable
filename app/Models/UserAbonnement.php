<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAbonnement extends Model
{
    use HasFactory;

    protected $table = 'user_abonnements';

    protected $fillable = [
        'user_id',
        'abonnement_id',
        'date_debut',
        'date_fin',
        'status'
    ];

    protected $casts = [
        'date_debut' => 'datetime:Y-m-d',
        'date_fin' => 'datetime:Y-m-d',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function abonnement()
    {
        return $this->belongsTo(Abonnement::class);
    }

        public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'Actif' => 'bg-green-100 text-green-700',
            'Expiré' => 'bg-red-100 text-red-700',
            'En attente' => 'bg-yellow-100 text-yellow-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}