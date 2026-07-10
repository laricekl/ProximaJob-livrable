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
            'Actif' => 'bg-success-light text-success-dark',
            'Expiré' => 'bg-error-light text-error-dark',
            'En attente' => 'bg-warning-light text-warning-dark',
            default => 'bg-surface-container text-on-surface-variant',
        };
    }
}