<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskActivityNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Task $task,
        private readonly User $actor,
        private readonly string $activity,
        private readonly string $message,
        private readonly array $meta = [],
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $targetUrl = method_exists($notifiable, 'isTeamMember') && $notifiable->isTeamMember()
            ? route('team-member.dashboard', ['open_task' => $this->task->id])
            : route('admin.tasks.show', $this->task);

        return array_merge([
            'type' => 'task_activity',
            'activity' => $this->activity,
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'project_id' => $this->task->project_id,
            'project_name' => optional($this->task->project)->name,
            'actor_id' => $this->actor->id,
            'actor_name' => $this->actor->name,
            'target_url' => $targetUrl,
            'message' => $this->message,
        ], $this->meta);
    }
}
