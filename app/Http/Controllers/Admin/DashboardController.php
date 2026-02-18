<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $isProjectManager = Auth::user()->isProjectManager() && !Auth::user()->isAdmin();

        $taskQuery = Task::query();
        $projectQuery = Project::query();

        if ($isProjectManager) {
            $projectQuery->where('project_manager_id', Auth::id());
            $taskQuery->whereHas('project', function ($query) {
                $query->where('project_manager_id', Auth::id());
            });
        }

        // Get all tasks grouped by status for Trello-style board
        $tasksByStatus = [
            'to_do' => (clone $taskQuery)->where('status', 'to_do')->with(['project', 'users', 'teams'])->get(),
            'in_progress' => (clone $taskQuery)->where('status', 'in_progress')->with(['project', 'users', 'teams'])->get(),
            'review' => (clone $taskQuery)->where('status', 'review')->with(['project', 'users', 'teams'])->get(),
            'done' => (clone $taskQuery)->where('status', 'done')->with(['project', 'users', 'teams'])->get(),
        ];

        // Dashboard statistics
        $stats = [
            'active_projects' => (clone $projectQuery)->where('status', 'active')->count(),
            'total_clients' => $isProjectManager ? 0 : Client::where('is_active', true)->count(),
            'pending_tasks' => (clone $taskQuery)->whereIn('status', ['to_do', 'in_progress'])->count(),
            'unpaid_invoices' => $isProjectManager ? 0 : Invoice::where('status', 'sent')->sum('total_amount'),
            'overdue_invoices' => $isProjectManager ? 0 : Invoice::where('status', 'overdue')->count(),
            'team_members' => $isProjectManager ? 0 : User::whereHas('roles', function($q) {
                $q->where('name', 'team_member');
            })->count(),
        ];

        return view('admin.dashboard', compact('tasksByStatus', 'stats'));
    }
}
