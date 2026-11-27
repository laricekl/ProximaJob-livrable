<?php

namespace Tests\Feature;

use App\Jobs\TranslateOffreJob;
use App\Models\Offre;
use App\Services\Translation\OffreTranslationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\Feature\Concerns\CreatesTestAccounts;
use Tests\TestCase;

class OffreTranslationTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestAccounts;

    private function fakeDeepl(): void
    {
        config([
            'translation.driver' => 'deepl',
            'translation.deepl.api_key' => 'cle-de-test',
            'translation.deepl.url' => 'https://api-free.deepl.com/v2/translate',
        ]);

        Http::fake([
            'api-free.deepl.com/*' => function ($request) {
                $texts = $request->data()['text'] ?? [];
                $translations = array_map(
                    fn ($text) => ['text' => 'EN: ' . $text, 'detected_source_language' => 'FR'],
                    is_array($texts) ? $texts : [$texts]
                );

                return Http::response(['translations' => $translations], 200);
            },
        ]);
    }

    public function test_migration_adds_translation_cache_columns(): void
    {
        foreach (['titre_en', 'poste_en', 'description_en', 'missions_en', 'responsibilities_en', 'objectif_en', 'criteres_en', 'traduction_hash', 'traduit_le'] as $column) {
            $this->assertTrue(Schema::hasColumn('offres', $column), "Colonne manquante : {$column}");
        }
    }

    public function test_offer_is_translated_once_and_cached(): void
    {
        $this->fakeDeepl();

        $offre = $this->createOfferFor(null, [
            'titre' => 'Développeur Laravel',
            'poste' => 'Développeur Laravel',
            'description' => 'Nous cherchons un développeur.',
        ]);

        // La file est synchrone en test : la traduction a lieu à la création.
        $offre->refresh();
        $this->assertSame('EN: Développeur Laravel', $offre->titre_en);
        $this->assertSame('EN: Nous cherchons un développeur.', $offre->description_en);
        $this->assertNotNull($offre->traduit_le);
        $this->assertTrue(app(OffreTranslationService::class)->isCached($offre));

        // Deuxième passage : le cache évite tout nouvel appel au pilote.
        Http::fake(['*' => Http::response([], 500)]);
        (new TranslateOffreJob($offre->id))->handle(app(OffreTranslationService::class));
        Http::assertNothingSent();
    }

    public function test_offer_is_retranslated_when_french_text_changes(): void
    {
        $this->fakeDeepl();

        $offre = $this->createOfferFor(null, ['titre' => 'Ancien titre']);
        $offre->refresh();
        $this->assertSame('EN: Ancien titre', $offre->titre_en);

        $offre->update(['titre' => 'Nouveau titre']);
        $offre->refresh();
        $this->assertSame('EN: Nouveau titre', $offre->titre_en);
    }

    public function test_display_fields_use_english_cache_only_in_english(): void
    {
        $offre = $this->createOfferFor(null, [
            'titre' => 'Titre français',
            'poste' => 'Poste français',
            'description' => 'Description française.',
        ]);

        Offre::withoutEvents(fn () => $offre->forceFill([
            'titre_en' => 'English title',
            'poste_en' => 'English position',
            'description_en' => 'English description.',
        ])->save());
        $offre->refresh();

        app()->setLocale('fr');
        $this->assertSame('Titre français', $offre->display_titre);
        $this->assertSame('Description française.', $offre->display_description);

        app()->setLocale('en');
        $this->assertSame('English title', $offre->display_titre);
        $this->assertSame('English position', $offre->display_poste);
        $this->assertSame('English description.', $offre->display_description);
    }

    public function test_display_fields_fall_back_to_french_without_cache(): void
    {
        $offre = $this->createOfferFor(null, ['titre' => 'Titre seulement en français']);

        app()->setLocale('en');
        $this->assertSame('Titre seulement en français', $offre->display_titre);
    }

    public function test_no_driver_means_no_translation_and_no_error(): void
    {
        config(['translation.driver' => 'null']);

        $offre = $this->createOfferFor(null, ['titre' => 'Offre sans pilote']);

        $offre->refresh();
        $this->assertNull($offre->titre_en);
        $this->assertFalse(app(OffreTranslationService::class)->translate($offre));
    }
}
