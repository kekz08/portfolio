<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Project
 */
class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'budget' => $this->budget,
            'deadline' => $this->deadline?->toDateString(),
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'status_color_class' => $this->status->colorClass(),
            'status_notes' => $this->status_notes,
            'progress_percentage' => (int) ($this->progress_percentage ?? 0),
            'can_collaborate' => $this->canCollaborate(),
            'timeline' => [
                'submitted_at' => $this->created_at?->toIso8601String(),
                'approved_at' => $this->approved_at?->toIso8601String(),
                'in_progress_at' => ($this->start_date ?? $this->started_at)?->toIso8601String(),
                'completed_at' => $this->completed_at?->toIso8601String(),
            ],
            'owner' => $this->whenLoaded('user', function (): array {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'activity_logs' => ActivityLogResource::collection($this->whenLoaded('activityLogs')),
            'updates' => $this->whenLoaded('updates', function () {
                return $this->updates->map(fn ($update): array => [
                    'id' => $update->id,
                    'title' => $update->title,
                    'description' => $update->description,
                    'progress_percentage' => (int) $update->progress_percentage,
                    'created_by' => $update->creator?->name,
                    'created_at' => $update->created_at?->toIso8601String(),
                ])->values();
            }),
            'messages' => $this->whenLoaded('messages', function () {
                return $this->messages->map(fn ($message): array => [
                    'id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $message->sender?->name,
                    'sender_role' => $message->sender?->role?->value,
                    'message' => $message->message,
                    'created_at' => $message->created_at?->toIso8601String(),
                ])->values();
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
