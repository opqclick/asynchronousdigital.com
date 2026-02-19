<?php

namespace App\Mail;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskAssigned extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Task $task,
        public User $assignee,
        public User $assignedBy,
    ) {
    }

    public function envelope(): Envelope
    {
        $environment = strtoupper(config('app.env'));
        $prefix = $environment === 'PRODUCTION' ? '' : "[$environment] ";

        return new Envelope(
            subject: $prefix . 'New Task Assigned: ' . $this->task->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.task-assigned',
            with: [
                'task' => $this->task,
                'assignee' => $this->assignee,
                'assignedBy' => $this->assignedBy,
                'projectName' => optional($this->task->project)->name,
                'dashboardUrl' => route('dashboard'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
