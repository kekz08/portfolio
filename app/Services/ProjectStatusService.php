<?php

namespace App\Services;

use App\Enums\ProjectStatus;
use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\User;
use App\Notifications\ProjectStatusUpdatedNotification;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ProjectStatusService
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projects,
    ) {
    }

    public function updateStatus(Project $project, ProjectStatus $status, User $actor, ?string $notes = null): Project
    {
        return DB::transaction(function () use ($project, $status, $actor, $notes): Project {
            $oldStatus = $project->status;

            $project->status = $status;
            $project->status_notes = $notes;
            $project->reviewed_by = $actor->id;

            if ($status === ProjectStatus::APPROVED && $project->approved_at === null) {
                $project->approved_at = now();
            }

            if ($status === ProjectStatus::IN_PROGRESS && $project->started_at === null) {
                $project->started_at = now();
            }

            if ($status === ProjectStatus::COMPLETED && $project->completed_at === null) {
                $project->completed_at = now();
            }

            $project = $this->projects->save($project);

            ActivityLog::query()->create([
                'project_id' => $project->id,
                'user_id' => $project->user_id,
                'actor_id' => $actor->id,
                'action' => 'status_updated',
                'old_status' => $oldStatus,
                'new_status' => $status,
                'context' => [
                    'notes' => $notes,
                ],
            ]);

            $project->user->notify(new ProjectStatusUpdatedNotification($project, $oldStatus, $status, $notes));

            return $project;
        });
    }
}

