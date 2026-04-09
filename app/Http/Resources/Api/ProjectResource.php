<?php

namespace App\Http\Resources\Api;

use App\Services\IdObfuscator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Project
 */
class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $ids = app(IdObfuscator::class);

        $data = [
            'id' => $ids->encode($this->id),
            'title' => $this->title,
            'description' => $this->description,
            'budget' => $this->budget,
            'deadline' => $this->deadline?->toDateString(),
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'status_notes' => $this->status_notes,
            'progress_percentage' => (int) ($this->progress_percentage ?? 0),
            'can_collaborate' => $this->canCollaborate(),
            'timeline' => [
                'submitted_at' => $this->created_at?->toIso8601String(),
                'approved_at' => $this->approved_at?->toIso8601String(),
                'in_progress_at' => ($this->start_date ?? $this->started_at)?->toIso8601String(),
                'completed_at' => $this->completed_at?->toIso8601String(),
            ],
            'owner' => $this->whenLoaded('user', function () use ($ids) {
                return [
                    'id' => $ids->encode($this->user->id),
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'activity_logs' => $this->whenLoaded('activityLogs', function () {
                return ActivityLogResource::collection($this->activityLogs);
            }),
            'updates' => $this->whenLoaded('updates', function () {
                return ProjectUpdateResource::collection($this->updates);
            }),
            'messages' => $this->whenLoaded('messages', function () {
                return ProjectMessageResource::collection($this->messages);
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];

        $data['timeline'] = array_filter($data['timeline'], static fn ($value) => $value !== null);

        return array_filter($data, static fn ($value) => $value !== null);
    }
}
