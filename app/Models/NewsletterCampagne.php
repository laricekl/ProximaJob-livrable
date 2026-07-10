<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterCampagne extends Model
{
    use HasFactory;

    protected $table = 'newsletter_campagnes';

    protected $fillable = [
        'sujet', 'contenu', 'audience', 'statut',
        'envoyee_le', 'programmee_pour', 'destinataires_count',
    ];

    protected $casts = [
        'envoyee_le'     => 'datetime',
        'programmee_pour' => 'datetime',
    ];
}
