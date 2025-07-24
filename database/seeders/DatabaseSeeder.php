<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create users with different roles for testing

        // Admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        // Supervisor user
        User::factory()->create([
            'name' => 'Supervisor User',
            'email' => 'supervisor@example.com',
            'role' => 'supervisor',
        ]);

        // Editor user
        User::factory()->create([
            'name' => 'Editor User',
            'email' => 'editor@example.com',
            'role' => 'editor',
        ]);

        // Regular user
        User::factory()->create([
            'name' => 'Regular User',
            'email' => 'regular@example.com',
            'role' => 'regular',
        ]);

        // Keep the original test user as admin for backward compatibility
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'admin',
        ]);
    }
}
