<?php

namespace Database\Seeders;

use App\Enums\ProjectStatus;
use App\Enums\UserRole;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

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

        $user = User::query()->updateOrCreate(
            ['email' => 'client@example.com'],
            [
                'name' => 'Client User',
                'phone' => null,
                'company' => 'Portfolio Client',
                'password' => 'password',
                'role' => UserRole::CLIENT,
                'email_verified_at' => Carbon::now(),
            ]
        );

        Project::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'title' => 'Portfolio Revamp',
            ],
            [
                'description' => 'Modernize the public portfolio and tracking dashboard.',
                'budget' => '$1,000 - $5,000',
                'deadline' => Carbon::now()->addWeeks(3)->toDateString(),
                'status' => ProjectStatus::PENDING,
                'progress_percentage' => 0,
                'reviewed_by' => $admin->id,
                'status_notes' => 'Awaiting initial review.',
            ]
        );
    }
}
