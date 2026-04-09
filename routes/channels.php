<?php

use App\Models\Project;
use App\Services\IdObfuscator;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('project.{project}', function ($user, string $project): bool {
    try {
        $projectId = app(IdObfuscator::class)->decode($project);
    } catch (\Throwable) {
        return false;
    }

    $model = Project::query()->find($projectId);

    return $model !== null && ($user->isAdmin() || $model->user_id === $user->id);
});
