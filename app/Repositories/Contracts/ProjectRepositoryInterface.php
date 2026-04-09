<?php

namespace App\Repositories\Contracts;

use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ProjectRepositoryInterface
{
    public function createForUser(User $user, array $attributes): Project;

    /**
     * @return Collection<int, Project>
     */
    public function getForUser(User $user): Collection;

    public function paginateForAdmin(int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): ?Project;

    public function save(Project $project): Project;
}

