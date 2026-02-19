<?php

namespace App\Http\Controllers\TeamMember;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $taskFilter = request('task_filter', 'assigned_to_me');
        if (!in_array($taskFilter, ['assigned_to_me', 'all_project_tasks'], true)) {
            $taskFilter = 'assigned_to_me';
        }

        $projectIds = Project::query()
            ->where(function ($query) use ($user) {
                $query->whereHas('teams.users', function ($teamUsersQuery) use ($user) {
                    $teamUsersQuery->where('users.id', $user->id);
                })->orWhereHas('tasks.users', function ($taskUsersQuery) use ($user) {
                    $taskUsersQuery->where('users.id', $user->id);
                });
            })
            ->pluck('projects.id');

        $taskScopeQuery = Task::query()
            ->whereIn('project_id', $projectIds)
            ->with(['project', 'users']);

        if ($taskFilter === 'assigned_to_me') {
            $taskScopeQuery->whereHas('users', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            });
        }

        // Get tasks grouped by status for selected filter scope
        $tasksByStatus = [
            'to_do' => (clone $taskScopeQuery)->where('status', 'to_do')->get(),
            'in_progress' => (clone $taskScopeQuery)->where('status', 'in_progress')->get(),
            'review' => (clone $taskScopeQuery)->where('status', 'review')->get(),
            'done' => (clone $taskScopeQuery)->where('status', 'done')->get(),
        ];

        // Personal statistics
        $stats = [
            'tasks_due_today' => $user->tasks()
                ->whereDate('due_date', today())
                ->whereIn('status', ['to_do', 'in_progress'])
                ->count(),
            'overdue_tasks' => $user->tasks()
                ->where('due_date', '<', today())
                ->whereIn('status', ['to_do', 'in_progress'])
                ->count(),
            'completed_this_month' => $user->tasks()
                ->where('status', 'done')
                ->whereMonth('tasks.updated_at', now()->month)
                ->count(),
            'total_assigned' => $user->tasks()->count(),
        ];

        $myCreatedTasks = Task::query()
            ->where('created_by', $user->id)
            ->with(['project', 'users:id'])
            ->latest()
            ->take(10)
            ->get();

        $projects = Project::query()
            ->where(function ($query) use ($user) {
                $query->whereHas('teams.users', function ($teamUsersQuery) use ($user) {
                    $teamUsersQuery->where('users.id', $user->id);
                })->orWhereHas('tasks.users', function ($taskUsersQuery) use ($user) {
                    $taskUsersQuery->where('users.id', $user->id);
                });
            })
            ->distinct()
            ->get();

        return view('team-member.dashboard', compact('tasksByStatus', 'stats', 'myCreatedTasks', 'taskFilter', 'projects'));
    }
}
