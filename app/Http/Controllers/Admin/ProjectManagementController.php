<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\ProjectStatusUpdateRequest;
use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Services\ProjectStatusService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProjectManagementController extends Controller
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projects,
        private readonly ProjectStatusService $statusService,
    ) {
    }

    public function index(): Response
    {
        $this->authorize('manage-projects');

        $projects = $this->projects
            ->paginateForAdmin()
            ->through(fn (Project $project): array => [
                'id' => $project->id,
                'title' => $project->title,
                'description' => $project->description,
                'status' => $project->status->value,
                'status_label' => $project->status->label(),
                'status_color_class' => $project->status->colorClass(),
                'status_notes' => $project->status_notes,
                'created_at' => $project->created_at?->toDateTimeString(),
                'owner' => [
                    'id' => $project->user?->id,
                    'name' => $project->user?->name,
                    'email' => $project->user?->email,
                ],
                'activity_logs' => $project->activityLogs->map(fn ($log): array => [
                    'id' => $log->id,
                    'action' => $log->action,
                    'old_status_label' => $log->old_status?->label(),
                    'new_status_label' => $log->new_status?->label(),
                    'notes' => $log->context['notes'] ?? null,
                    'actor' => $log->actor?->name,
                    'created_at' => $log->created_at?->toDateTimeString(),
                ]),
            ]);

        return Inertia::render('Admin/Projects/Index', [
            'projects' => $projects,
            'statusOptions' => collect(ProjectStatus::cases())
                ->map(fn (ProjectStatus $status): array => [
                    'value' => $status->value,
                    'label' => $status->label(),
                ])
                ->values(),
        ]);
    }

    public function update(ProjectStatusUpdateRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('updateStatus', $project);

        $this->statusService->updateStatus(
            project: $project,
            status: ProjectStatus::from($request->validated('status')),
            actor: $request->user(),
            notes: $request->validated('status_notes'),
        );

        return redirect()
            ->back()
            ->with('success', 'Project status updated successfully.');
    }
}
