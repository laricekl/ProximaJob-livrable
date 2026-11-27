<?php

namespace App\Services\Translation;

use App\Models\Offre;

/**
 * Traduit les textes libres d'une offre une seule fois et garde le
 * résultat en base (colonnes *_en). Le hash des textes sources
 * permet de ne retraduire que si le français a changé : c'est le
 * « cache » qui évite de payer une traduction à chaque affichage.
 */
class OffreTranslationService
{
    /** Champs libres traduits automatiquement. */
    public const FIELDS = [
        'titre',
        'poste',
        'description',
        'missions',
        'responsibilities',
        'objectif',
        'criteres',
    ];

    public function __construct(private AutoTranslator $translator)
    {
    }

    public function sourceHash(Offre $offre): string
    {
        $parts = [];
        foreach (self::FIELDS as $field) {
            $parts[] = (string) ($offre->{$field} ?? '');
        }

        return hash('sha256', implode("\x1F", $parts));
    }

    /** Vrai si une traduction à jour existe déjà en cache. */
    public function isCached(Offre $offre): bool
    {
        return filled($offre->titre_en)
            && $offre->traduction_hash === $this->sourceHash($offre);
    }

    /**
     * Traduit l'offre si nécessaire. Retourne vrai si une traduction
     * est en cache après l'appel. Ne lève jamais d'exception.
     */
    public function translate(Offre $offre): bool
    {
        if ($this->isCached($offre)) {
            return true;
        }

        if (! $this->translator->available()) {
            return false;
        }

        $texts = [];
        foreach (self::FIELDS as $field) {
            if (filled($offre->{$field})) {
                $texts[$field] = (string) $offre->{$field};
            }
        }

        if ($texts === []) {
            return false;
        }

        $translations = $this->translator->translateMany($texts);

        if ($translations === []) {
            return false;
        }

        $updates = [
            'traduction_hash' => $this->sourceHash($offre),
            'traduit_le' => now(),
        ];

        foreach ($translations as $field => $text) {
            $updates[$field . '_en'] = $text;
        }

        // Sauvegarde silencieuse : ne pas redéclencher l'événement "saved"
        // (qui redemanderait une traduction en boucle).
        Offre::withoutEvents(fn () => $offre->forceFill($updates)->save());
        $offre->refresh();

        return true;
    }
}
