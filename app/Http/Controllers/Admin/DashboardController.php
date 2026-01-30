<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get all tasks grouped by status for Trello-style board
        $tasksByStatus = [
            'to_do' => Task::where('status', 'to_do')->with(['project', 'users', 'teams'])->get(),
            'in_progress' => Task::where('status', 'in_progress')->with(['project', 'users', 'teams'])->get(),
            'review' => Task::where('status', 'review')->with(['project', 'users', 'teams'])->get(),
            'done' => Task::where('status', 'done')->with(['project', 'users', 'teams'])->get(),
        ];

        // Dashboard statistics
        $stats = [
            'active_projects' => Project::where('status', 'active')->count(),
            'total_clients' => Client::where('is_active', true)->count(),
            'pending_tasks' => Task::whereIn('status', ['to_do', 'in_progress'])->count(),
            'unpaid_invoices' => Invoice::where('status', 'sent')->sum('total_amount'),
            'overdue_invoices' => Invoice::where('status', 'overdue')->count(),
            'team_members' => User::whereHas('role', function($q) {
                $q->where('name', 'team_member');
            })->count(),
        ];

        return view('admin.dashboard', compact('tasksByStatus', 'stats'));
    }
}
