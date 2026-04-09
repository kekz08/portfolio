<?php

namespace App\Services\Api;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ProjectListingService
{
    public function handle(User $actor): Collection
    {
        return Project::query()
            ->where('user_id', $actor->id)
            ->with([
                'activityLogs' => fn ($builder) => $builder->with('actor:id,name')->latest('created_at'),
                'updates' => fn ($builder) => $builder->with('creator:id,name')->latest('created_at'),
                'messages' => fn ($builder) => $builder->with('sender:id,name,role')->latest('created_at'),
            ])
            ->get();
    }
}
