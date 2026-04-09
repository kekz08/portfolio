<?php

namespace App\Events;

use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\ProjectMessage;
use App\Services\IdObfuscator;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectMessageSent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public Project $project,
        public ProjectMessage $message,
        public ?ActivityLog $activity = null,
    ) {
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('project.' . app(IdObfuscator::class)->encode($this->project->id));
    }

    public function broadcastAs(): string
    {
        return 'project.message.sent';
    }

    public function broadcastWith(): array
    {
        $ids = app(IdObfuscator::class);

        return [
            'project_id' => $ids->encode($this->project->id),
            'message' => [
                'id' => $ids->encode($this->message->id),
                'sender_id' => $ids->encode($this->message->sender_id),
                'sender_name' => $this->message->sender?->name,
                'sender_role' => $this->message->sender?->role?->value,
                'message' => $this->message->message,
                'created_at' => $this->message->created_at?->toDateTimeString(),
            ],
            'activity' => $this->activity ? [
                'id' => $ids->encode($this->activity->id),
                'action' => $this->activity->action,
                'old_status_label' => $this->activity->old_status?->label(),
                'new_status_label' => $this->activity->new_status?->label(),
                'notes' => $this->activity->context['notes'] ?? null,
                'actor' => $this->activity->actor?->name,
                'created_at' => $this->activity->created_at?->toDateTimeString(),
            ] : null,
        ];
    }
}
