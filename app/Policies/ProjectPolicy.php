<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        return $user->isAdmin() || $project->user_id === $user->id;
    }

    /**
     * Determine whether the user can update a project's status.
     */
    public function updateStatus(User $user, Project $project): bool
    {
        return $user->isAdmin();
    }

    public function createProgressUpdate(User $user, Project $project): bool
    {
        return $user->isAdmin() && $project->canCollaborate();
    }

    public function createMessage(User $user, Project $project): bool
    {
        if (! $project->canCollaborate()) {
            return false;
        }

        return $user->isAdmin() || $project->user_id === $user->id;
    }
}
