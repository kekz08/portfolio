<?php

namespace App\Services\Api;

use App\Enums\ProjectStatus;
use App\Exceptions\ProjectAccessDeniedException;
use App\Exceptions\ProjectNotFoundException;
use App\Exceptions\ProjectStatusTransitionException;
use App\Models\Project;
use App\Models\User;
use App\Services\IdObfuscator;
use App\Services\ProjectStatusService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProjectStatusUpdateService
{
    public function __construct(
        private readonly IdObfuscator $ids,
        private readonly ProjectStatusService $statusService,
    ) {
    }

    public function handle(array $payload, User $actor): Project
    {
        return DB::transaction(function () use ($payload, $actor): Project {
            $projectId = $this->ids->decode($payload['project_id']);

            $project = Project::query()
                ->lockForUpdate()
                ->find($projectId);

            if (! $project) {
                throw new ProjectNotFoundException();
            }

            if (! $actor->isAdmin()) {
                throw new ProjectAccessDeniedException();
            }

            try {
                return $this->statusService->updateStatus(
                    project: $project,
                    status: ProjectStatus::from($payload['status']),
                    actor: $actor,
                    notes: $payload['status_notes'] ?? null,
                );
            } catch (ValidationException $exception) {
                throw new ProjectStatusTransitionException();
            }
        });
    }
}
