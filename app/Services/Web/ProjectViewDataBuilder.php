<?php

namespace App\Services\Web;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Services\IdObfuscator;

class ProjectViewDataBuilder
{
    public function __construct(
        private readonly IdObfuscator $ids,
    ) {
    }

    public function forClient(Project $project): array
    {
        return [
            'id' => $this->ids->encode($project->id),
            'reference_code' => sprintf('PRJ-%04d', $project->id),
            'title' => $project->title,
            'description' => $project->description,
            'budget' => $project->budget,
            'deadline' => $project->deadline?->toDateString(),
            'status' => $project->status->value,
            'status_label' => $project->status->label(),
            'status_color_class' => $project->status->colorClass(),
            'status_notes' => $project->status_notes,
            'progress_percentage' => (int) ($project->progress_percentage ?? 0),
            'can_collaborate' => $project->canCollaborate(),
            'approved_at' => $project->approved_at?->toDateTimeString(),
            'start_date' => ($project->start_date ?? $project->started_at)?->toDateTimeString(),
            'completed_at' => $project->completed_at?->toDateTimeString(),
            'created_at' => $project->created_at?->toDateTimeString(),
            'activity_logs' => $project->activityLogs->map(function ($log): array {
                return [
                    'id' => $this->ids->encode($log->id),
                    'action' => $log->action,
                    'old_status_label' => $log->old_status?->label(),
                    'new_status_label' => $log->new_status?->label(),
                    'notes' => $log->context['notes'] ?? null,
                    'actor' => $log->actor?->name,
                    'created_at' => $log->created_at?->toDateTimeString(),
                ];
            })->values(),
            'updates' => $project->updates->map(function ($update): array {
                return [
                    'id' => $this->ids->encode($update->id),
                    'title' => $update->title,
                    'description' => $update->description,
                    'progress_percentage' => (int) $update->progress_percentage,
                    'created_by' => $update->creator?->name,
                    'created_at' => $update->created_at?->toDateTimeString(),
                ];
            })->values(),
            'messages' => $project->messages->map(function ($message): array {
                return [
                    'id' => $this->ids->encode($message->id),
                    'sender_id' => $this->ids->encode($message->sender_id),
                    'sender_name' => $message->sender?->name,
                    'sender_role' => $message->sender?->role?->value,
                    'message' => $message->message,
                    'created_at' => $message->created_at?->toDateTimeString(),
                ];
            })->values(),
        ];
    }

    public function forAdmin(Project $project): array
    {
        return [
            ...$this->forClient($project),
            'owner' => [
                'id' => $project->user ? $this->ids->encode($project->user->id) : null,
                'name' => $project->user?->name,
                'email' => $project->user?->email,
            ],
            'allowed_statuses' => collect(ProjectStatus::workflowCases())
                ->filter(fn (ProjectStatus $status): bool => $project->status->canTransitionTo($status))
                ->map(fn (ProjectStatus $status): array => [
                    'value' => $status->value,
                    'label' => $status->label(),
                ])
                ->values(),
        ];
    }
}
