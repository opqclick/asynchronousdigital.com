<?php

namespace App\Http\Controllers\TeamMember;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
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
