<?php

namespace App\Notifications;

use App\Models\Project;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProjectAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Project $project,
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
            ? route('team-member.projects.index')
            : route('admin.projects.show', $this->project);

        return [
            'project_id' => $this->project->id,
            'project_name' => $this->project->name,
            'assigned_by_id' => $this->assignedBy->id,
            'assigned_by_name' => $this->assignedBy->name,
            'target_url' => $targetUrl,
            'message' => sprintf(
                'You have been assigned to project "%s" by %s.',
                $this->project->name,
                $this->assignedBy->name
            ),
        ];
    }
}
