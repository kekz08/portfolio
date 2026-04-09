<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'project_id',
    'user_id',
    'actor_id',
    'action',
    'old_status',
    'new_status',
    'context',
])]
class ActivityLog extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'old_status' => ProjectStatus::class,
            'new_status' => ProjectStatus::class,
            'context' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}

