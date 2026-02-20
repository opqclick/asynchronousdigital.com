<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskCommentedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Task $task,
        private readonly TaskComment $comment,
        private readonly User $commentedBy,
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

        $action = $this->comment->parent_id ? 'replied on' : 'commented on';

        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'comment_id' => $this->comment->id,
            'commented_by_id' => $this->commentedBy->id,
            'commented_by_name' => $this->commentedBy->name,
            'target_url' => $targetUrl,
            'message' => sprintf(
                '%s %s task "%s".',
                $this->commentedBy->name,
                $action,
                $this->task->title
            ),
        ];
    }
}
