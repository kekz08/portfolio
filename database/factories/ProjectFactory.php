<?php

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => 'Website Project Request',
            'description' => $this->faker->paragraph(),
            'budget' => '$1,000 - $5,000',
            'deadline' => now()->addWeeks(3)->toDateString(),
            'status' => ProjectStatus::PENDING,
            'progress_percentage' => 0,
            'status_notes' => 'Awaiting staff review.',
        ];
    }
}
