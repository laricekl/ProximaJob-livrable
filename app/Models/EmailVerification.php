<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'token',
        'created_at'
    ];

    public $timestamps = false;  

    /**
     * Trouver une vérification par token
     */
    public static function findByToken($token)
    {
        return static::where('token', $token)
            ->where('created_at', '>', now()->subHours(24))
            ->first();
    }

    /**
     * Supprimer les tokens expirés
     */
    public static function cleanupExpired()
    {
        return static::where('created_at', '<', now()->subHours(24))->delete();
    }
}