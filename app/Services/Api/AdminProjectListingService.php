<?php

namespace App\Services\Api;

use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;

class AdminProjectListingService
{
    public function handle(): Collection
    {
        return Project::query()
            ->with([
                'user:id,name,email',
                'activityLogs' => fn ($builder) => $builder->with('actor:id,name')->latest('created_at'),
                'updates' => fn ($builder) => $builder->with('creator:id,name')->latest('created_at'),
                'messages' => fn ($builder) => $builder->with('sender:id,name,role')->latest('created_at'),
            ])
            ->latest('created_at')
            ->get();
    }
}
