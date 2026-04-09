<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projects,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $projects = $this->projects->getForUser($request->user())
            ->map(fn (Project $project): array => [
                'id' => $project->id,
                'title' => $project->title,
                'description' => $project->description,
                'status' => $project->status->value,
                'status_label' => $project->status->label(),
                'status_color_class' => $project->status->colorClass(),
                'status_notes' => $project->status_notes,
                'approved_at' => $project->approved_at?->toDateTimeString(),
                'started_at' => $project->started_at?->toDateTimeString(),
                'completed_at' => $project->completed_at?->toDateTimeString(),
                'created_at' => $project->created_at?->toDateTimeString(),
                'activity_logs' => $project->activityLogs->map(fn ($log): array => [
                    'id' => $log->id,
                    'action' => $log->action,
                    'old_status_label' => $log->old_status?->label(),
                    'new_status_label' => $log->new_status?->label(),
                    'notes' => $log->context['notes'] ?? null,
                    'actor' => $log->actor?->name,
                    'created_at' => $log->created_at?->toDateTimeString(),
                ]),
            ])
            ->values();

        return Inertia::render('Dashboard', [
            'projects' => $projects,
        ]);
    }
}
