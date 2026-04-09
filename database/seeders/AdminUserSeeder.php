<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed an admin account with deterministic credentials.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'phone' => null,
                'company' => 'Portfolio Admin',
                'password' => 'password',
                'role' => UserRole::ADMIN,
                'email_verified_at' => now(),
            ]
        );
    }
}
