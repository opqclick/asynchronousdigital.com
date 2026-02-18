<?php

namespace App\Http\Controllers\TeamMember;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function create()
    {
        $projects = $this->assignableProjectsQuery()->get();

        return view('team-member.tasks.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'estimated_hours' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip|max:10240',
        ]);

        $project = Project::findOrFail($validated['project_id']);
        $this->authorizeProjectTaskCreation($project);

        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->storePublicly('tasks', 'do_spaces');
                $attachmentPaths[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'uploaded_at' => now()->toDateTimeString(),
                ];
            }
        }

        $task = Task::create([
            'title' => $validated['title'],
            'project_id' => $project->id,
            'created_by' => Auth::id(),
            'description' => $validated['description'] ?? null,
            'status' => 'to_do',
            'priority' => $validated['priority'],
            'estimated_hours' => $validated['estimated_hours'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'attachments' => !empty($attachmentPaths) ? $attachmentPaths : null,
        ]);

        $task->users()->syncWithoutDetaching([
            Auth::id() => ['assigned_at' => now()],
        ]);

        return redirect()->route('team-member.dashboard')
            ->with('success', 'Task created successfully.');
    }

    public function updateStatus(Request $request, Task $task)
    {
        $request->validate([
            'status' => 'required|in:to_do,in_progress,review,done'
        ]);

        $this->authorizeTaskInteraction($task);

        $oldStatus = $task->status;
        $task->update(['status' => $request->status]);

        $this->logStatusChange($task, $oldStatus, $request->status);

        return response()->json([
            'success' => true,
            'message' => 'Task status updated successfully'
        ]);
    }

    public function details(Task $task)
    {
        $this->authorizeTaskInteraction($task);

        $task->load(['project', 'users', 'comments', 'statusHistories']);

        return view('admin.tasks.details-partial', compact('task'));
    }

    public function storeComment(Request $request, Task $task)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:task_comments,id'
        ]);

        $this->authorizeTaskInteraction($task);

        $comment = $task->comments()->create([
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'comment' => $request->comment,
        ]);

        $comment->load('user', 'replies');

        return response()->json([
            'success' => true,
            'comment' => $comment,
            'html' => view('admin.tasks.comment-item', compact('comment'))->render()
        ]);
    }

    private function authorizeTaskInteraction(Task $task): void
    {
        $isAssigned = $task->users()
            ->where('users.id', Auth::id())
            ->exists();

        abort_unless($isAssigned, 403, 'You can only interact with tasks assigned to you.');
    }

    private function authorizeProjectTaskCreation(Project $project): void
    {
        $isAssignedToProject = $this->assignableProjectsQuery()
            ->where('projects.id', $project->id)
            ->exists();

        abort_unless($isAssignedToProject, 403, 'You can only create tasks for projects assigned to your teams.');
    }

    private function assignableProjectsQuery()
    {
        return Project::query()->whereHas('teams.users', function ($query) {
            $query->where('users.id', Auth::id());
        });
    }

    private function logStatusChange(Task $task, string $fromStatus, string $toStatus): void
    {
        if ($fromStatus === $toStatus) {
            return;
        }

        TaskStatusHistory::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
        ]);
    }
}
