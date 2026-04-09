<?php

namespace App\Services\Api;

use App\Exceptions\ProjectAccessDeniedException;
use App\Exceptions\ProjectNotFoundException;
use App\Models\Project;
use App\Models\User;
use App\Services\IdObfuscator;

class ProjectDetailService
{
    public function __construct(
        private readonly IdObfuscator $ids,
    ) {
    }

    public function handle(array $payload, User $actor): Project
    {
        $projectId = $this->ids->decode($payload['project_id']);

        $project = Project::query()
            ->with([
                'user:id,name,email',
                'activityLogs' => fn ($builder) => $builder->with('actor:id,name')->latest('created_at'),
                'updates' => fn ($builder) => $builder->with('creator:id,name')->latest('created_at'),
                'messages' => fn ($builder) => $builder->with('sender:id,name,role')->latest('created_at'),
            ])
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
