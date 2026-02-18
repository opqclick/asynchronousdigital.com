<?php

namespace App\Http\Controllers\TeamMember;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get only tasks assigned to this team member
        $tasksByStatus = [
            'to_do' => $user->tasks()->where('status', 'to_do')->with('project')->get(),
            'in_progress' => $user->tasks()->where('status', 'in_progress')->with('project')->get(),
            'review' => $user->tasks()->where('status', 'review')->with('project')->get(),
            'done' => $user->tasks()->where('status', 'done')->with('project')->get(),
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

        return view('team-member.dashboard', compact('tasksByStatus', 'stats', 'myCreatedTasks'));
    }
}
