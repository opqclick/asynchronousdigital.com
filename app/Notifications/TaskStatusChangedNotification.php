<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Task $task,
        private readonly User $actor,
        private readonly string $fromStatus,
        private readonly string $toStatus,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $targetPath = route('admin.tasks.show', $this->task, false);
        $targetUrl = rtrim((string) config('app.url'), '/').$targetPath;

        return [
            'type' => 'task_status_changed',
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'project_id' => $this->task->project_id,
            'project_name' => optional($this->task->project)->name,
            'actor_id' => $this->actor->id,
            'actor_name' => $this->actor->name,
            'from_status' => $this->fromStatus,
            'to_status' => $this->toStatus,
            'target_url' => $targetUrl,
            'message' => sprintf(
                'Task "%s" moved from %s to %s by %s.',
                $this->task->title,
                str_replace('_', ' ', $this->fromStatus),
                str_replace('_', ' ', $this->toStatus),
                $this->actor->name,
            ),
        ];
    }
}
