<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case PENDING = 'pending';
    case PENDING_REVIEW = 'pending_review';
    case APPROVED = 'approved';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING, self::PENDING_REVIEW => 'Pending',
            self::APPROVED => 'Approved / Granted',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
            self::REJECTED => 'Rejected',
        };
    }

    public function colorClass(): string
    {
        return match ($this) {
            self::PENDING, self::PENDING_REVIEW => 'bg-slate-100/80 text-slate-800 border-slate-300/75',
            self::APPROVED => 'bg-indigo-100/80 text-indigo-800 border-indigo-300/75',
            self::IN_PROGRESS => 'bg-sky-100/80 text-sky-800 border-sky-300/75',
            self::COMPLETED => 'bg-emerald-100/80 text-emerald-800 border-emerald-300/75',
            self::REJECTED => 'bg-rose-100/80 text-rose-800 border-rose-300/75',
        };
    }

    public static function workflowCases(): array
    {
        return [
            self::PENDING,
            self::APPROVED,
            self::IN_PROGRESS,
            self::COMPLETED,
            self::REJECTED,
        ];
    }

    public function isPendingLike(): bool
    {
        return $this === self::PENDING || $this === self::PENDING_REVIEW;
    }

    public function allowsCollaboration(): bool
    {
        return match ($this) {
            self::APPROVED, self::IN_PROGRESS, self::COMPLETED => true,
            default => false,
        };
    }

    public function canTransitionTo(self $next): bool
    {
        if ($this->value === $next->value) {
            return true;
        }

        if ($this->isPendingLike()) {
            return in_array($next, [self::APPROVED, self::REJECTED], true);
        }

        return match ($this) {
            self::APPROVED => in_array($next, [self::IN_PROGRESS, self::REJECTED], true),
            self::IN_PROGRESS => $next === self::COMPLETED,
            self::COMPLETED, self::REJECTED => false,
            default => false,
        };
    }
}
