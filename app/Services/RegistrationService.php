<?php

namespace App\Services;

use App\Enums\ProjectStatus;
use App\Enums\UserRole;
use App\Models\ActivityLog;
use App\Models\User;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegistrationService
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projects,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function registerWithProject(array $payload): User
    {
        return DB::transaction(function () use ($payload): User {
            $user = User::query()->create([
                'name' => $payload['name'],
                'email' => $payload['email'],
                'phone' => $payload['phone'] ?? null,
                'company' => $payload['company'] ?? null,
                'password' => Hash::make((string) $payload['password']),
                'role' => UserRole::CLIENT,
            ]);

            $project = $this->projects->createForUser($user, [
                'title' => $payload['project_title'],
                'description' => $payload['project_description'],
                'budget' => $payload['budget'],
                'deadline' => $payload['deadline'] ?? null,
                'status' => ProjectStatus::PENDING,
                'progress_percentage' => 0,
                'status_notes' => 'Your request is queued for review.',
            ]);

            ActivityLog::query()->create([
                'project_id' => $project->id,
                'user_id' => $user->id,
                'actor_id' => $user->id,
                'action' => 'registration_submitted',
                'new_status' => ProjectStatus::PENDING,
                'context' => [
                    'source' => 'hire_me_form',
                    'budget' => $payload['budget'],
                    'deadline' => $payload['deadline'] ?? null,
                ],
            ]);

            return $user;
        });
    }
}
