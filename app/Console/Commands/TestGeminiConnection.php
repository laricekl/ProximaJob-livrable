<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Prism\Prism\Text\Request as TextRequest;

class TestGeminiConnection extends Command
{
    protected $signature = 'gemini:test';
    protected $description = 'Test Gemini connection with Prism';

    public function handle()
    {
        $this->info('🧪 Test de connexion Gemini avec Prism...');

        $apiKey = env('GEMINI_API_KEY');
        
        if (empty($apiKey)) {
            $this->error('❌ Clé API Gemini non trouvée');
            return 1;
        }

        $this->info('✅ Clé API trouvée: ' . substr($apiKey, 0, 10) . '...');

        try {
            $this->info('🔄 Initialisation de Prism...');
            
            $prism = new \Prism\Prism\Prism();
            
            $this->info('📡 Envoi de la requête à Gemini...');
            
            // CRÉATION DE L'OBJET TextRequest
            $request = new TextRequest(
                model: 'gemini-pro',
                messages: [
                    ['role' => 'user', 'content' => 'Réponds simplement par "SUCCES" en français']
                ]
            );
            
            // UTILISATION AVEC L'OBJET TextRequest
            $response = $prism->provider('gemini')->text($request);
            
            $content = $response->getContent();
            $this->info('🎉 CONNEXION RÉUSSIE!');
            $this->info('📝 Réponse: ' . $content);

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Erreur Prism: ' . $e->getMessage());
            $this->error('Type: ' . get_class($e));
            
            // Fallback vers HTTP direct
            return $this->tryDirectHttp();
        }
    }
    
    protected function tryDirectHttp()
    {
        $this->info('🔄 Tentative avec requête HTTP directe...');
        
        $apiKey = env('GEMINI_API_KEY');
        
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => 'Réponds simplement par "SUCCES" en français']
                            ]
                        ]
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $content = $data['candidates'][0]['content']['parts'][0]['text'];
                    $this->info('🎉 SUCCÈS AVEC HTTP DIRECT!');
                    $this->info('📝 Réponse: ' . $content);
                    return 0;
                } else {
                    $this->error('❌ Structure de réponse inattendue');
                    return 1;
                }
            } else {
                $this->error('❌ Erreur HTTP: ' . $response->status());
                $this->error('Message: ' . $response->body());
                return 1;
            }

        } catch (\Exception $e) {
            $this->error('❌ Erreur HTTP: ' . $e->getMessage());
            return 1;
        }
    }
}