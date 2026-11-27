<?php

namespace App\Services\Translation;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Traduit un lot de textes FR -> EN via le pilote configuré
 * (config/translation.php). Retourne un tableau associatif
 * clé => traduction, ou un tableau vide si aucun pilote n'est
 * disponible ou si l'appel échoue. Ne lève jamais d'exception :
 * une panne de traduction ne doit jamais bloquer le site.
 */
class AutoTranslator
{
    public function driver(): ?string
    {
        $driver = config('translation.driver', 'auto');

        if ($driver === 'deepl' && filled(config('translation.deepl.api_key'))) {
            return 'deepl';
        }

        if ($driver === 'gemini' && filled(config('translation.gemini.api_key'))) {
            return 'gemini';
        }

        if ($driver === 'auto') {
            if (filled(config('translation.deepl.api_key'))) {
                return 'deepl';
            }
            if (filled(config('translation.gemini.api_key'))) {
                return 'gemini';
            }
        }

        return null;
    }

    public function available(): bool
    {
        return $this->driver() !== null;
    }

    /**
     * @param  array<string, string>  $texts  Textes français, par clé.
     * @return array<string, string> Traductions anglaises, mêmes clés (partiel accepté).
     */
    public function translateMany(array $texts): array
    {
        $texts = array_filter($texts, fn ($text) => filled($text));

        if ($texts === []) {
            return [];
        }

        try {
            return match ($this->driver()) {
                'deepl' => $this->viaDeepl($texts),
                'gemini' => $this->viaGemini($texts),
                default => [],
            };
        } catch (\Throwable $e) {
            Log::warning('Traduction automatique échouée : ' . $e->getMessage());

            return [];
        }
    }

    /**
     * DeepL accepte plusieurs paramètres "text" en un seul appel et
     * retourne les traductions dans le même ordre.
     */
    private function viaDeepl(array $texts): array
    {
        $response = Http::timeout(30)
            ->asForm()
            ->withHeaders([
                'Authorization' => 'DeepL-Auth-Key ' . config('translation.deepl.api_key'),
            ])
            ->post(config('translation.deepl.url'), [
                'text' => array_values($texts),
                'source_lang' => 'FR',
                'target_lang' => 'EN',
            ]);

        if (! $response->successful()) {
            Log::warning('DeepL a retourné le statut ' . $response->status());

            return [];
        }

        $translations = $response->json('translations', []);
        $result = [];
        $keys = array_keys($texts);

        foreach ($translations as $i => $translation) {
            if (isset($keys[$i], $translation['text'])) {
                $result[$keys[$i]] = $translation['text'];
            }
        }

        return $result;
    }

    /**
     * Gemini traduit tout le lot en un seul appel, en JSON structuré.
     */
    private function viaGemini(array $texts): array
    {
        $model = config('translation.gemini.model');
        $url = rtrim(config('translation.gemini.url'), '/') . '/' . $model . ':generateContent';

        $prompt = "Translate each of the following French job posting texts into English. "
            . "Preserve line breaks and formatting exactly. Do not add comments. "
            . "Return ONLY a JSON object with the same keys and the translated strings as values.\n\n"
            . json_encode($texts, JSON_UNESCAPED_UNICODE);

        $response = Http::timeout(60)
            ->withHeaders(['x-goog-api-key' => config('translation.gemini.api_key')])
            ->post($url, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]],
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                ],
            ]);

        if (! $response->successful()) {
            Log::warning('Gemini (traduction) a retourné le statut ' . $response->status());

            return [];
        }

        $raw = $response->json('candidates.0.content.parts.0.text', '');
        $decoded = json_decode((string) $raw, true);

        if (! is_array($decoded)) {
            Log::warning('Gemini (traduction) a retourné un JSON illisible.');

            return [];
        }

        return array_filter(
            array_intersect_key($decoded, $texts),
            fn ($value) => is_string($value) && filled($value)
        );
    }
}
