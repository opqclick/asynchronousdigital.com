<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

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

        $users = $this->getAssignableUsers();
        $selectedAssigneeId = request('assignee_id', 'all');
        $allowedStringFilters = ['all', 'me'];
        if (!in_array($selectedAssigneeId, $allowedStringFilters, true)) {
            $selectedAssigneeId = (int) $selectedAssigneeId;
        }

        if ($selectedAssigneeId === 'me') {
            $taskQuery->whereHas('users', function ($query) {
                $query->where('users.id', Auth::id());
            });
        } elseif (is_int($selectedAssigneeId) && $selectedAssigneeId > 0) {
            $taskQuery->whereHas('users', function ($query) use ($selectedAssigneeId) {
                $query->where('users.id', $selectedAssigneeId);
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

        $projects = $projectQuery->get();
        $assignableUserIdsByProject = $this->buildAssignableUserMapByProject($projects, $users);

        return view('admin.dashboard', compact('tasksByStatus', 'stats', 'projects', 'users', 'assignableUserIdsByProject', 'selectedAssigneeId'));
    }

    private function getAssignableUsers(): Collection
    {
        return User::with('role')
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', [Role::ADMIN, Role::TEAM_MEMBER, Role::PROJECT_MANAGER]);
            })
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', Role::CLIENT);
            })
            ->get();
    }

    private function buildAssignableUserMapByProject(Collection $projects, Collection $users): array
    {
        $map = [];

        foreach ($projects as $project) {
            $map[(string) $project->id] = $this->getAssignableUserIdsForProject($project, $users);
        }

        return $map;
    }

    private function getAssignableUserIdsForProject(Project $project, Collection $users): array
    {
        if (Auth::user()->isAdmin()) {
            return $users->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();
        }

        $allowedRoleUserIds = $users
            ->filter(fn (User $user) => $user->hasAnyAssignedRole([Role::ADMIN, Role::TEAM_MEMBER]))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if ($project->project_manager_id) {
            $allowedRoleUserIds[] = (int) $project->project_manager_id;
        }

        return array_values(array_unique($allowedRoleUserIds));
    }
}
