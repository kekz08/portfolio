<?php

namespace Database\Seeders;

use App\Enums\ProjectStatus;
use App\Enums\UserRole;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(HomeContentSeeder::class);
        $this->call(AdminUserSeeder::class);

        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $user = User::factory()->create([
            'name' => 'Client User',
            'email' => 'client@example.com',
            'role' => UserRole::USER,
        ]);

        Project::factory()->create([
            'user_id' => $user->id,
            'title' => 'Portfolio Revamp',
            'description' => 'Modernize the public portfolio and tracking dashboard.',
            'status' => ProjectStatus::PENDING_REVIEW,
            'reviewed_by' => $admin->id,
            'status_notes' => 'Awaiting initial review.',
        ]);
    }
}
