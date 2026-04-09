<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\ProjectStatusUpdateRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Services\ProjectStatusService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectManagementController extends Controller
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projects,
        private readonly ProjectStatusService $statusService,
    ) {
    }

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('manage-projects');

        $projects = $this->projects->paginateForAdmin();

        return ProjectResource::collection($projects);
    }

    public function updateStatus(ProjectStatusUpdateRequest $request, Project $project): ProjectResource
    {
        $this->authorize('updateStatus', $project);

        $project = $this->statusService->updateStatus(
            project: $project,
            status: ProjectStatus::from($request->validated('status')),
            actor: $request->user(),
            notes: $request->validated('status_notes'),
        );

        $project->loadMissing([
            'user:id,name,email',
            'activityLogs' => fn ($query) => $query->with('actor:id,name')->latest('created_at'),
        ]);

        return new ProjectResource($project);
    }
}
