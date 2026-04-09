<?php

namespace App\Services\Web;

use App\Models\Project;
use App\Models\User;
use App\Repositories\Contracts\ProjectRepositoryInterface;

class DashboardViewService
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projects,
        private readonly ProjectViewDataBuilder $builder,
    ) {
    }

    public function handle(User $user): array
    {
        $projects = $this->projects
            ->getForUser($user)
            ->map(fn (Project $project): array => $this->builder->forClient($project))
            ->values();

        return [
            'projects' => $projects,
        ];
    }
}
