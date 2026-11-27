<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Traduction automatique des textes libres (offres d'emploi)
    |--------------------------------------------------------------------------
    |
    | Pilote : "auto" choisit DeepL si une clé DeepL est présente, sinon
    | Gemini si une clé Gemini est présente, sinon aucun pilote (les textes
    | restent en français, l'affichage anglais retombe sur le français).
    | Valeurs possibles : auto, deepl, gemini, null.
    |
    */

    'driver' => env('TRANSLATION_DRIVER', 'auto'),

    'deepl' => [
        'api_key' => env('DEEPL_API_KEY'),
        // Clé gratuite : https://api-free.deepl.com — clé Pro : https://api.deepl.com
        'url' => env('DEEPL_API_URL', 'https://api-free.deepl.com/v2/translate'),
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_TRANSLATION_MODEL', 'gemini-2.5-flash'),
        'url' => env('GEMINI_URL', 'https://generativelanguage.googleapis.com/v1beta/models'),
    ],

];
