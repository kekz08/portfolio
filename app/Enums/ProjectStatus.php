<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case PENDING_REVIEW = 'pending_review';
    case APPROVED = 'approved';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING_REVIEW => 'Pending Review',
            self::APPROVED => 'Approved / Granted',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
        };
    }

    public function colorClass(): string
    {
        return match ($this) {
            self::PENDING_REVIEW => 'bg-slate-100/80 text-slate-800 border-slate-300/75',
            self::APPROVED => 'bg-indigo-100/80 text-indigo-800 border-indigo-300/75',
            self::IN_PROGRESS => 'bg-sky-100/80 text-sky-800 border-sky-300/75',
            self::COMPLETED => 'bg-emerald-100/80 text-emerald-800 border-emerald-300/75',
        };
    }
}
