<?php

namespace App\Services\Web;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;

class AdminProjectQueueViewService
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projects,
        private readonly ProjectViewDataBuilder $builder,
    ) {
    }

    public function handle(): array
    {
        $projects = $this->projects
            ->paginateForAdmin()
            ->through(fn (Project $project): array => $this->builder->forAdmin($project));

        return [
            'projects' => $projects,
            'statusOptions' => collect(ProjectStatus::workflowCases())
                ->map(fn (ProjectStatus $status): array => [
                    'value' => $status->value,
                    'label' => $status->label(),
                ])
                ->values(),
        ];
    }
}
