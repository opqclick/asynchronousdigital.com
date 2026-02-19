<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Task $task,
        private readonly User $assignedBy,
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

        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'project_id' => $this->task->project_id,
            'project_name' => optional($this->task->project)->name,
            'assigned_by_id' => $this->assignedBy->id,
            'assigned_by_name' => $this->assignedBy->name,
            'target_url' => $targetUrl,
            'message' => sprintf('You have been assigned to task "%s" by %s.', $this->task->title, $this->assignedBy->name),
        ];
    }
}
