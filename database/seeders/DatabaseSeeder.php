<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        $this->call([
            RolePermissionSeeder::class,
            DataSeeder::class,
        ]);

       
        \App\Models\User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ])->assignRole('candidat'); 
    }
}
