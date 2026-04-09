<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\ActivityLog
 */
class ActivityLogResource extends JsonResource
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
            'action' => $this->action,
            'old_status' => $this->old_status?->value,
            'old_status_label' => $this->old_status?->label(),
            'new_status' => $this->new_status?->value,
            'new_status_label' => $this->new_status?->label(),
            'notes' => $this->context['notes'] ?? null,
            'actor' => $this->actor ? [
                'id' => $this->actor->id,
                'name' => $this->actor->name,
            ] : null,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
