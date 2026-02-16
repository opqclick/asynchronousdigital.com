<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatusHistory;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::with(['project', 'users'])->get();
        return view('admin.tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::all();
        $users = User::whereHas('role', function($q) {
            $q->whereIn('name', ['admin', 'team_member']);
        })->get();
        $teams = Team::all();
        
        return view('admin.tasks.create', compact('projects', 'users', 'teams'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'description' => 'nullable|string',
            'status' => 'required|in:to_do,in_progress,review,done',
            'priority' => 'required|in:low,medium,high',
            'estimated_hours' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip|max:10240',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
            'teams' => 'nullable|array',
            'teams.*' => 'exists:teams,id',
        ]);

        // Handle file uploads to S3
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

        $validated['attachments'] = !empty($attachmentPaths) ? $attachmentPaths : null;

        $task = Task::create($validated);

        // Attach users and teams
        if (!empty($validated['users'])) {
            $task->users()->attach($validated['users']);
        }
        if (!empty($validated['teams'])) {
            $task->teams()->attach($validated['teams']);
        }

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $task->load(['project', 'users', 'teams']);
        return view('admin.tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $projects = Project::all();
        $users = User::whereHas('role', function($q) {
            $q->whereIn('name', ['admin', 'team_member']);
        })->get();
        $teams = Team::all();
        $task->load(['users', 'teams']);
        
        return view('admin.tasks.edit', compact('task', 'projects', 'users', 'teams'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'description' => 'nullable|string',
            'status' => 'required|in:to_do,in_progress,review,done',
            'priority' => 'required|in:low,medium,high',
            'estimated_hours' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip|max:10240',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
            'teams' => 'nullable|array',
            'teams.*' => 'exists:teams,id',
        ]);

        // Handle new file uploads to S3
        if ($request->hasFile('attachments')) {
            $existingAttachments = $task->attachments ?? [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->storePublicly('tasks', 'do_spaces');
                $existingAttachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'uploaded_at' => now()->toDateTimeString(),
                ];
            }
            $validated['attachments'] = $existingAttachments;
        }

        $task->update($validated);

        if ($task->wasChanged('status')) {
            $this->logStatusChange(
                $task,
                $task->getOriginal('status'),
                $task->status
            );
        }

        // Sync users and teams
        if (isset($validated['users'])) {
            $task->users()->sync($validated['users']);
        } else {
            $task->users()->detach();
        }
        
        if (isset($validated['teams'])) {
            $task->teams()->sync($validated['teams']);
        } else {
            $task->teams()->detach();
        }

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

    /**
     * Update task status via AJAX
     */
    public function updateStatus(Request $request, Task $task)
    {
        $request->validate([
            'status' => 'required|in:to_do,in_progress,review,done'
        ]);

        $oldStatus = $task->status;
        $task->update(['status' => $request->status]);

        $this->logStatusChange($task, $oldStatus, $request->status);

        return response()->json([
            'success' => true,
            'message' => 'Task status updated successfully'
        ]);
    }

    /**
     * Get task details via AJAX
     */
    public function details(Task $task)
    {
        $task->load(['project', 'users', 'comments', 'statusHistories']);
        
        return view('admin.tasks.details-partial', compact('task'));
    }

    /**
     * Store a comment for a task
     */
    public function storeComment(Request $request, Task $task)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:task_comments,id'
        ]);

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
