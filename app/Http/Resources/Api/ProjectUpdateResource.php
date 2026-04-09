<?php

namespace App\Http\Resources\Api;

use App\Services\IdObfuscator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\ProjectUpdate
 */
class ProjectUpdateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $ids = app(IdObfuscator::class);

        $data = [
            'id' => $ids->encode($this->id),
            'project_id' => $ids->encode($this->project_id),
            'title' => $this->title,
            'description' => $this->description,
            'progress_percentage' => (int) $this->progress_percentage,
            'created_by' => $this->creator ? [
                'id' => $ids->encode($this->creator->id),
                'name' => $this->creator->name,
            ] : null,
            'created_at' => $this->created_at?->toIso8601String(),
        ];

        return array_filter($data, static fn ($value) => $value !== null);
    }
}
