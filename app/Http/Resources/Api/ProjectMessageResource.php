<?php

namespace App\Http\Resources\Api;

use App\Services\IdObfuscator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\ProjectMessage
 */
class ProjectMessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $ids = app(IdObfuscator::class);

        $data = [
            'id' => $ids->encode($this->id),
            'project_id' => $ids->encode($this->project_id),
            'sender' => $this->sender ? [
                'id' => $ids->encode($this->sender->id),
                'name' => $this->sender->name,
                'role' => $this->sender->role?->value,
            ] : null,
            'message' => $this->message,
            'created_at' => $this->created_at?->toIso8601String(),
        ];

        return array_filter($data, static fn ($value) => $value !== null);
    }
}
