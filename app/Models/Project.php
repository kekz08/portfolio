<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'title',
    'description',
    'budget',
    'deadline',
    'status',
    'progress_percentage',
    'reviewed_by',
    'approved_at',
    'start_date',
    'started_at',
    'completed_at',
    'status_notes',
])]
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
            'deadline' => 'date',
            'progress_percentage' => 'integer',
            'approved_at' => 'datetime',
            'start_date' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::get(fn (): string => $this->status->label());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function updates(): HasMany
    {
        return $this->hasMany(ProjectUpdate::class)->latest('created_at');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ProjectMessage::class)->latest('created_at');
    }

    public function canCollaborate(): bool
    {
        return $this->status->allowsCollaboration();
    }
}
