<?php

namespace App\Services\Api;

use App\Events\ProjectUpdated;
use App\Exceptions\ProjectAccessDeniedException;
use App\Exceptions\ProjectCollaborationNotAllowedException;
use App\Exceptions\ProjectNotFoundException;
use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\ProjectUpdate;
use App\Models\User;
use App\Services\IdObfuscator;
use App\Services\ProjectStatusService;
use App\Enums\ProjectStatus;
use Illuminate\Support\Facades\DB;

class ProjectUpdateService
{
    public function __construct(
        private readonly IdObfuscator $ids,
        private readonly ProjectStatusService $statusService,
    ) {
    }

    public function handle(array $payload, User $actor): ProjectUpdate
    {
        return DB::transaction(function () use ($payload, $actor): ProjectUpdate {
            $project = $this->getProject($payload['project_id'], $actor);

            if (! $project->canCollaborate()) {
                throw new ProjectCollaborationNotAllowedException();
            }

            $update = $project->updates()->create([
                'title' => $payload['title'],
                'description' => $payload['description'],
                'progress_percentage' => $payload['progress_percentage'],
                'created_by' => $actor->id,
            ]);

            $project->progress_percentage = $payload['progress_percentage'];
            $project->save();

            $activity = ActivityLog::query()->create([
                'project_id' => $project->id,
                'user_id' => $project->user_id,
                'actor_id' => $actor->id,
                'action' => 'progress_updated',
                'old_status' => $project->status,
                'new_status' => $project->status,
                'context' => [
                    'title' => $payload['title'],
                    'notes' => $payload['description'],
                    'new_progress' => $payload['progress_percentage'],
                ],
            ]);

            if ($project->status === ProjectStatus::APPROVED && $payload['progress_percentage'] > 0 && $payload['progress_percentage'] < 100) {
                $this->statusService->updateStatus(
                    project: $project,
                    status: ProjectStatus::IN_PROGRESS,
                    actor: $actor,
                    notes: 'Development started based on progress update.',
                );
            }

            if ($payload['progress_percentage'] >= 100 && $project->status !== ProjectStatus::COMPLETED) {
                $this->statusService->updateStatus(
                    project: $project,
                    status: ProjectStatus::COMPLETED,
                    actor: $actor,
                    notes: 'Marked completed from progress update.',
                );
            }

            $project->refresh();
            $update->load('creator');
            $activity->load('actor');

            event(new ProjectUpdated($project, $update, $activity));

            return $update;
        });
    }

    private function getProject(string $encryptedId, User $actor): Project
    {
        $projectId = $this->ids->decode($encryptedId);

        $project = Project::query()
            ->lockForUpdate()
            ->find($projectId);

        if (! $project) {
            throw new ProjectNotFoundException();
        }

        if (! $actor->isAdmin()) {
            throw new ProjectAccessDeniedException();
        }

        return $project;
    }
}
