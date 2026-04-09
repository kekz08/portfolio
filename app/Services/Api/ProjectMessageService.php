<?php

namespace App\Services\Api;

use App\Exceptions\ProjectAccessDeniedException;
use App\Exceptions\ProjectCollaborationNotAllowedException;
use App\Exceptions\ProjectNotFoundException;
use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\ProjectMessage;
use App\Models\User;
use App\Events\ProjectMessageSent;
use App\Services\IdObfuscator;
use Illuminate\Support\Facades\DB;

class ProjectMessageService
{
    public function __construct(
        private readonly IdObfuscator $ids,
    ) {
    }

    public function handle(array $payload, User $actor): ProjectMessage
    {
        return DB::transaction(function () use ($payload, $actor): ProjectMessage {
            $project = $this->getProject($payload['project_id'], $actor);

            if (! $project->canCollaborate()) {
                throw new ProjectCollaborationNotAllowedException();
            }

            $message = $project->messages()->create([
                'sender_id' => $actor->id,
                'message' => $payload['message'],
            ]);

            $activity = ActivityLog::query()->create([
                'project_id' => $project->id,
                'user_id' => $project->user_id,
                'actor_id' => $actor->id,
                'action' => 'message_posted',
                'old_status' => $project->status,
                'new_status' => $project->status,
                'context' => [
                    'notes' => mb_substr($payload['message'], 0, 140),
                ],
            ]);

            $message->load('sender');
            $activity->load('actor');

            event(new ProjectMessageSent($project, $message, $activity));

            return $message;
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

        if (! $actor->isAdmin() && $project->user_id !== $actor->id) {
            throw new ProjectAccessDeniedException();
        }

        return $project;
    }
}
