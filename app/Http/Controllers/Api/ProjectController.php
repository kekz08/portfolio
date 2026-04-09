<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projects,
    ) {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $projects = $this->projects
            ->getForUser($request->user())
            ->loadMissing('user');

        return ProjectResource::collection($projects);
    }

    public function show(Project $project): ProjectResource
    {
        $this->authorize('view', $project);

        $project->loadMissing([
            'user:id,name,email',
            'activityLogs' => fn ($query) => $query->with('actor:id,name')->latest('created_at'),
        ]);

        return new ProjectResource($project);
    }
}
