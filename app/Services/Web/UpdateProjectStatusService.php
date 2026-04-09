<?php

namespace App\Services\Web;

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

class UpdateProjectStatusService
{
    public function __construct(
        private readonly IdObfuscator $ids,
        private readonly ProjectStatusService $statusService,
    ) {
    }

    public function handle(array $payload, User $actor): Project
    {
        return DB::transaction(function () use ($payload, $actor): Project {
            if (! $actor->isAdmin()) {
                throw new ProjectAccessDeniedException();
            }

            $project = Project::query()
                ->lockForUpdate()
                ->find($this->ids->decode($payload['project_id']));

            if (! $project) {
                throw new ProjectNotFoundException();
            }

            try {
                return $this->statusService->updateStatus(
                    project: $project,
                    status: ProjectStatus::from($payload['status']),
                    actor: $actor,
                    notes: $payload['status_notes'] ?? null,
                );
            } catch (ValidationException) {
                throw new ProjectStatusTransitionException();
            }
        });
    }
}
