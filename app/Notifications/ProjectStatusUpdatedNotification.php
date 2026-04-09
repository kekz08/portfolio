<?php

namespace App\Notifications;

use App\Enums\ProjectStatus;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Project $project,
        private readonly ?ProjectStatus $oldStatus,
        private readonly ProjectStatus $newStatus,
        private readonly ?string $notes = null,
    ) {
    }

    /**
     * @param mixed $notifiable
     * @return array<int, string>
     */
    public function via(mixed $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * @param mixed $notifiable
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        $subject = sprintf('Project "%s" status updated', $this->project->title);

        return (new MailMessage)
            ->subject($subject)
            ->line(sprintf(
                'Your project status changed from %s to %s.',
                $this->oldStatus?->label() ?? 'N/A',
                $this->newStatus->label(),
            ))
            ->when($this->notes, fn (MailMessage $mail) => $mail->line('Note: '.$this->notes))
            ->action('View Dashboard', route('dashboard'))
            ->line('Thank you for working with us.');
    }

    /**
     * @param mixed $notifiable
     * @return array<string, mixed>
     */
    public function toArray(mixed $notifiable): array
    {
        return [
            'project_id' => $this->project->id,
            'project_title' => $this->project->title,
            'old_status' => $this->oldStatus?->value,
            'new_status' => $this->newStatus->value,
            'old_status_label' => $this->oldStatus?->label(),
            'new_status_label' => $this->newStatus->label(),
            'notes' => $this->notes,
        ];
    }
}

