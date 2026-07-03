<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    protected $signature = 'email:test {email? : Adresse email de destination}';
    protected $description = 'Teste l\'envoi d\'email';

    public function handle()
    {
        $email = $this->argument('email') ?: 'admin@proximajob.fr';

        $this->info("Envoi d'un email de test à : {$email}");

        try {
            Mail::raw("Test email envoyé depuis ProximaJob à " . now()->format('d/m/Y H:i'), function ($message) use ($email) {
                $message->to($email)->subject('Test ProximaJob');
            });

            $this->info('✅ Email envoyé avec succès !');
            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Erreur lors de l'envoi : " . $e->getMessage());
            return 1;
        }
    }
}
