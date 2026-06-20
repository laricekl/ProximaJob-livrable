<?php

namespace Tests\Feature;

use App\Models\CandidateSector;
use App\Models\CvProfile;
use App\Models\Entreprise;
use App\Models\Offre;
use App\Models\User;
use Database\Seeders\DataSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoDataSeederTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = false;

    public function test_demo_seed_creates_core_public_data(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $this->seed(DataSeeder::class);

        $this->assertGreaterThanOrEqual(3, Entreprise::count());
        $this->assertGreaterThanOrEqual(5, Offre::count());
        $this->assertGreaterThanOrEqual(2, User::role('candidat')->count());
        $this->assertGreaterThanOrEqual(2, CvProfile::count());
        $this->assertGreaterThanOrEqual(2, CandidateSector::count());

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
        $this->assertDatabaseHas('users', ['email' => 'contact@techsolutions.com']);
        $this->assertDatabaseHas('offres', ['titre' => 'Developpeur Fullstack Laravel']);
    }

    public function test_demo_seed_is_idempotent(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $this->seed(DataSeeder::class);
        $this->seed(DataSeeder::class);

        $this->assertSame(3, Entreprise::count());
        $this->assertSame(5, Offre::count());
        $this->assertSame(1, User::where('email', 'test@example.com')->count());
        $this->assertSame(1, User::where('email', 'camille.roy@example.com')->count());
        $this->assertSame(1, CvProfile::where('email', 'test@example.com')->count());
        $this->assertSame(1, CvProfile::where('email', 'camille.roy@example.com')->count());
        $this->assertSame(2, CandidateSector::count());
    }
}
