<?php

namespace App\Events;

use App\Enums\ProjectStatus;
use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\ProjectUpdate;
use App\Services\IdObfuscator;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectUpdated implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public Project $project,
        public ?ProjectUpdate $update = null,
        public ?ActivityLog $activity = null,
    ) {
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('project.' . app(IdObfuscator::class)->encode($this->project->id));
    }

    public function broadcastAs(): string
    {
        return 'project.updated';
    }

    public function broadcastWith(): array
    {
        $ids = app(IdObfuscator::class);

        return [
            'project_id' => $ids->encode($this->project->id),
            'status' => $this->project->status->value,
            'status_label' => $this->project->status->label(),
            'status_color_class' => $this->project->status->colorClass(),
            'status_notes' => $this->project->status_notes,
            'progress_percentage' => (int) ($this->project->progress_percentage ?? 0),
            'start_date' => ($this->project->start_date ?? $this->project->started_at)?->toDateTimeString(),
            'completed_at' => $this->project->completed_at?->toDateTimeString(),
            'allowed_statuses' => collect(ProjectStatus::workflowCases())
                ->filter(fn (ProjectStatus $status): bool => $this->project->status->canTransitionTo($status))
                ->map(fn (ProjectStatus $status): array => [
                    'value' => $status->value,
                    'label' => $status->label(),
                ])
                ->values()
                ->all(),
            'activity' => $this->activity ? [
                'id' => $ids->encode($this->activity->id),
                'action' => $this->activity->action,
                'old_status_label' => $this->activity->old_status?->label(),
                'new_status_label' => $this->activity->new_status?->label(),
                'notes' => $this->activity->context['notes'] ?? null,
                'actor' => $this->activity->actor?->name,
                'created_at' => $this->activity->created_at?->toDateTimeString(),
            ] : null,
            'update' => $this->update ? [
                'id' => $ids->encode($this->update->id),
                'title' => $this->update->title,
                'description' => $this->update->description,
                'progress_percentage' => (int) $this->update->progress_percentage,
                'created_by' => $this->update->creator?->name,
                'created_at' => $this->update->created_at?->toDateTimeString(),
            ] : null,
        ];
    }
}
