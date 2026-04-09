<?php

namespace App\Repositories\Eloquent;

use App\Models\Project;
use App\Models\User;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentProjectRepository implements ProjectRepositoryInterface
{
    public function createForUser(User $user, array $attributes): Project
    {
        return $user->projects()->create($attributes);
    }

    public function getForUser(User $user): Collection
    {
        return $user
            ->projects()
            ->with(['activityLogs' => fn ($query) => $query->with('actor:id,name')->latest('created_at')])
            ->latest('created_at')
            ->get();
    }

    public function paginateForAdmin(int $perPage = 15): LengthAwarePaginator
    {
        return Project::query()
            ->with([
                'user:id,name,email',
                'activityLogs' => fn ($query) => $query->with('actor:id,name')->latest('created_at'),
            ])
            ->latest('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int $id): ?Project
    {
        return Project::query()->find($id);
    }

    public function save(Project $project): Project
    {
        $project->save();

        return $project->refresh();
    }
}
