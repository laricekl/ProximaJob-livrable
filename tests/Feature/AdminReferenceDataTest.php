<?php

namespace Tests\Feature;

use App\Http\Middleware\ContentSecurityPolicy;
use App\Http\Middleware\SetLocale;
use App\Models\Categorie;
use App\Models\Diplome;
use App\Models\Sector;
use App\Models\Skill;
use App\Models\TypeOffre;
use App\Models\User;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminReferenceDataTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create([
            'email_verified_at' => now(),
            'status' => 'Actif',
        ]);
        $admin->syncRoles(['admin']);

        return $admin;
    }

    public function test_web_group_keeps_laravel_security_and_binding_middleware(): void
    {
        $middleware = Route::getMiddlewareGroups()['web'];

        $this->assertContains(EncryptCookies::class, $middleware);
        $this->assertContains(ValidateCsrfToken::class, $middleware);
        $this->assertContains(SubstituteBindings::class, $middleware);
        $this->assertContains(ContentSecurityPolicy::class, $middleware);
        $this->assertContains(SetLocale::class, $middleware);
    }

    public function test_admin_can_create_update_and_delete_categories(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.categories.store'), ['nom' => 'Administration QA'])
            ->assertRedirect()->assertSessionHasNoErrors();
        $category = Categorie::where('nom', 'Administration QA')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.categories.update', $category), ['nom' => 'Administration vérifiée'])
            ->assertRedirect()->assertSessionHasNoErrors();
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'nom' => 'Administration vérifiée']);

        $this->actingAs($admin)->delete(route('admin.categories.destroy', $category))->assertRedirect();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_admin_can_create_update_and_delete_offer_types(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.types-offres.store'), ['nom' => 'Mandat QA'])
            ->assertRedirect()->assertSessionHasNoErrors();
        $type = TypeOffre::where('nom', 'Mandat QA')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.types-offres.update', $type), ['nom' => 'Mandat vérifié'])
            ->assertRedirect()->assertSessionHasNoErrors();
        $this->assertDatabaseHas('types_offres', ['id' => $type->id, 'nom' => 'Mandat vérifié']);

        $this->actingAs($admin)->delete(route('admin.types-offres.destroy', $type))->assertRedirect();
        $this->assertDatabaseMissing('types_offres', ['id' => $type->id]);
    }

    public function test_admin_can_create_update_and_delete_sectors(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.secteurs.store'), [
            'name' => 'Secteur QA',
            'scian_code' => '5415',
        ])->assertRedirect()->assertSessionHasNoErrors();
        $sector = Sector::where('name', 'Secteur QA')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.secteurs.update', $sector), [
            'name' => 'Secteur vérifié',
            'scian_code' => '5416',
        ])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertDatabaseHas('sectors', ['id' => $sector->id, 'name' => 'Secteur vérifié']);

        $this->actingAs($admin)->delete(route('admin.secteurs.destroy', $sector))->assertRedirect();
        $this->assertDatabaseMissing('sectors', ['id' => $sector->id]);
    }

    public function test_admin_can_create_update_and_delete_skills(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.skills.store'), [
            'name' => 'Compétence QA',
            'category' => 'technique',
            'description' => 'Validation automatisée',
            'importance_level' => 4,
        ])->assertRedirect()->assertSessionHasNoErrors();
        $skill = Skill::where('name', 'Compétence QA')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.skills.update', $skill), [
            'name' => 'Compétence vérifiée',
            'category' => 'transversale',
            'description' => 'Validation complète',
            'importance_level' => 5,
        ])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertDatabaseHas('skills', ['id' => $skill->id, 'name' => 'Compétence vérifiée']);

        $this->actingAs($admin)->delete(route('admin.skills.destroy', $skill))->assertRedirect();
        $this->assertDatabaseMissing('skills', ['id' => $skill->id]);
    }

    public function test_admin_can_create_update_and_delete_diplomas(): void
    {
        $admin = $this->admin();
        $payload = [
            'nom_diplome' => 'Diplôme QA',
            'nom_anglais' => 'QA Diploma',
            'sigle' => 'DQA',
            'niveau_education' => 'PROFESSIONNEL',
            'duree_annees' => 1.5,
        ];

        $this->actingAs($admin)->post(route('admin.diplomes.store'), $payload)
            ->assertRedirect()->assertSessionHasNoErrors();
        $diploma = Diplome::where('nom_diplome', 'Diplôme QA')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.diplomes.update', $diploma), array_merge($payload, [
            'nom_diplome' => 'Diplôme vérifié',
        ]))->assertRedirect()->assertSessionHasNoErrors();
        $this->assertDatabaseHas('diplomes', ['id' => $diploma->id, 'nom_diplome' => 'Diplôme vérifié']);

        $this->actingAs($admin)->delete(route('admin.diplomes.destroy', $diploma))->assertRedirect();
        $this->assertDatabaseMissing('diplomes', ['id' => $diploma->id]);
    }
}
