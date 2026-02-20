<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Notifications\ProjectAssignedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $projectsQuery = Project::withTrashed()->with([
            'client' => fn($query) => $query->withTrashed()->with([
                'user' => fn($userQuery) => $userQuery->withTrashed(),
            ]),
            'projectManager' => fn($query) => $query->withTrashed(),
            'tasks' => fn($query) => $query->withTrashed(),
        ])->withCount(['teams', 'users']);

        if ($user->isProjectManager() && !$user->isAdmin()) {
            $projectsQuery->where('project_manager_id', Auth::id());
        }

        $projects = $projectsQuery->get();

        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $this->authorizeProjectWrite();

        $clients = Client::with('user')->get();
        $teams = Team::with('users:id')->get();
        $teamMembers = User::with(['role', 'roles'])
            ->whereHas('roles', function ($query) {
                $query->where('name', Role::TEAM_MEMBER);
            })
            ->get();
        $projectManagers = User::with(['role', 'roles'])
            ->whereHas('roles', function ($query) {
                $query->where('name', Role::PROJECT_MANAGER);
            })
            ->get();

        if ($user->isProjectManager() && !$user->isAdmin()) {
            $projectManagers = $projectManagers->where('id', Auth::id());
        }

        return view('admin.projects.create', compact('clients', 'teams', 'teamMembers', 'projectManagers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorizeProjectWrite();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client_id' => 'nullable|exists:clients,id',
            'project_manager_id' => [
                'nullable',
                Rule::exists('users', 'id'),
            ],
            'description' => 'nullable|string',
            'status' => 'required|in:active,paused,completed,cancelled',
            'billing_model' => 'required|in:task_based,monthly,fixed_price',
            'project_value' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'repository_url' => 'nullable|url',
            'tech_stack' => 'nullable|string',
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip|max:10240',
            'teams' => 'nullable|array',
            'teams.*' => 'exists:teams,id',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        $selectedTeamIds = collect($validated['teams'] ?? [])->map(fn($id) => (int) $id)->unique()->values()->all();
        $selectedUserIds = collect($validated['users'] ?? [])->map(fn($id) => (int) $id)->unique()->values()->all();
        $this->ensureAssignableTeamMembers($selectedUserIds);

        if (!empty($validated['project_manager_id'])) {
            $pmUser = User::with('roles')->findOrFail($validated['project_manager_id']);
            if (!$pmUser->hasAssignedRole(Role::PROJECT_MANAGER)) {
                return back()->withErrors([
                    'project_manager_id' => 'Selected user must have the Project Manager role.',
                ])->withInput();
            }

            if ($this->hasProjectRoleConflict((int) $validated['project_manager_id'], $selectedTeamIds, $selectedUserIds)) {
                return back()->withErrors([
                    'project_manager_id' => 'Role conflict: this user cannot be Project Manager and Team Member in the same project.',
                    'teams' => 'Role conflict: selected teams include the chosen Project Manager. Remove the user from those teams or choose a different Project Manager.',
                    'users' => 'Role conflict: selected members include the chosen Project Manager. Remove that member or choose a different Project Manager.',
                ])->withInput();
            }
        }

        // Handle file uploads to S3
        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->storePublicly('projects', 'do_spaces');
                $attachmentPaths[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'uploaded_at' => now()->toDateTimeString(),
                ];
            }
        }

        // Convert tech_stack string to array
        if (!empty($validated['tech_stack'])) {
            $validated['tech_stack'] = array_map('trim', explode(',', $validated['tech_stack']));
        } else {
            $validated['tech_stack'] = null;
        }

        $validated['attachments'] = !empty($attachmentPaths) ? $attachmentPaths : null;

        unset($validated['teams'], $validated['users']);

        $project = Project::create($validated);

        // Attach teams if provided
        if (!empty($selectedTeamIds)) {
            $project->teams()->attach($selectedTeamIds);
        }

        if (!empty($selectedUserIds)) {
            $project->users()->attach($selectedUserIds);

            // Notify newly assigned team members
            $assignedBy = Auth::user();
            User::whereIn('id', $selectedUserIds)->each(function ($member) use ($project, $assignedBy) {
                $member->notify(new ProjectAssignedNotification($project, $assignedBy));
            });
        }

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $this->authorizeProjectAccess($project);

        $project->load(['client.user', 'projectManager', 'teams.users', 'users', 'tasks', 'invoices']);
        return view('admin.projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $user = Auth::user();
        $this->authorizeProjectWrite();
        $this->authorizeProjectAccess($project);

        $clients = Client::with('user')->get();
        $teams = Team::with('users:id')->get();
        $teamMembers = User::with(['role', 'roles'])
            ->whereHas('roles', function ($query) {
                $query->where('name', Role::TEAM_MEMBER);
            })
            ->get();
        $project->load(['teams', 'users']);
        $projectManagers = User::with(['role', 'roles'])
            ->whereHas('roles', function ($query) {
                $query->where('name', Role::PROJECT_MANAGER);
            })
            ->get();

        if ($user->isProjectManager() && !$user->isAdmin()) {
            $projectManagers = $projectManagers->where('id', Auth::id());
        }

        return view('admin.projects.edit', compact('project', 'clients', 'teams', 'teamMembers', 'projectManagers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $this->authorizeProjectWrite();
        $this->authorizeProjectAccess($project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'project_manager_id' => [
                'nullable',
                Rule::exists('users', 'id'),
            ],
            'description' => 'nullable|string',
            'status' => 'required|in:active,paused,completed,cancelled',
            'billing_model' => 'required|in:task_based,monthly,fixed_price',
            'project_value' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'repository_url' => 'nullable|url',
            'tech_stack' => 'nullable|string',
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip|max:10240',
            'teams' => 'nullable|array',
            'teams.*' => 'exists:teams,id',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        $selectedTeamIds = collect($validated['teams'] ?? [])->map(fn($id) => (int) $id)->unique()->values()->all();
        $selectedUserIds = collect($validated['users'] ?? [])->map(fn($id) => (int) $id)->unique()->values()->all();
        $this->ensureAssignableTeamMembers($selectedUserIds);

        if (!empty($validated['project_manager_id'])) {
            $pmUser = User::with('roles')->findOrFail($validated['project_manager_id']);
            if (!$pmUser->hasAssignedRole(Role::PROJECT_MANAGER)) {
                return back()->withErrors([
                    'project_manager_id' => 'Selected user must have the Project Manager role.',
                ])->withInput();
            }

            if ($this->hasProjectRoleConflict((int) $validated['project_manager_id'], $selectedTeamIds, $selectedUserIds)) {
                return back()->withErrors([
                    'project_manager_id' => 'Role conflict: this user cannot be Project Manager and Team Member in the same project.',
                    'teams' => 'Role conflict: selected teams include the chosen Project Manager. Remove the user from those teams or choose a different Project Manager.',
                    'users' => 'Role conflict: selected members include the chosen Project Manager. Remove that member or choose a different Project Manager.',
                ])->withInput();
            }
        }

        // Handle new file uploads to S3
        if ($request->hasFile('attachments')) {
            $existingAttachments = $project->attachments ?? [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->storePublicly('projects', 'do_spaces');
                $existingAttachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'uploaded_at' => now()->toDateTimeString(),
                ];
            }
            $validated['attachments'] = $existingAttachments;
        }

        // Convert tech_stack string to array
        if (!empty($validated['tech_stack'])) {
            $validated['tech_stack'] = array_map('trim', explode(',', $validated['tech_stack']));
        } else {
            $validated['tech_stack'] = null;
        }

        unset($validated['teams'], $validated['users']);

        $project->update($validated);

        $project->teams()->sync($selectedTeamIds);

        // Detect newly added users and notify them
        $previousUserIds = $project->users()->pluck('users.id')->map(fn($id) => (int) $id)->all();
        $project->users()->sync($selectedUserIds);
        $newUserIds = array_diff($selectedUserIds, $previousUserIds);
        if (!empty($newUserIds)) {
            $assignedBy = Auth::user();
            User::whereIn('id', $newUserIds)->each(function ($member) use ($project, $assignedBy) {
                $member->notify(new ProjectAssignedNotification($project, $assignedBy));
            });
        }

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Project $project)
    {
        $this->authorizeProjectWrite();
        $this->authorizeProjectAccess($project);

        $forceDelete = $request->input('delete_mode') === 'force';
        if ($forceDelete && !$request->user()->isAdmin()) {
            return back()->with('error', 'Only admins can permanently delete records.');
        }

        if ($forceDelete) {
            $dependencies = $this->collectProjectDependencies($project);
            $activeDependencies = array_filter($dependencies, fn(int $count) => $count > 0);

            if (!empty($activeDependencies)) {
                $dependencySummary = collect($activeDependencies)
                    ->map(fn(int $count, string $key) => ucfirst(str_replace('_', ' ', $key)) . ': ' . $count)
                    ->implode(', ');

                return redirect()->route('admin.projects.index')
                    ->with('error', 'Permanent delete blocked. This project has dependent data. ' . $dependencySummary . '. Please use soft delete.');
            }

            try {
                $project->forceDelete();

                return redirect()->route('admin.projects.index')
                    ->with('success', 'Project permanently deleted successfully.');
            } catch (\Illuminate\Database\QueryException $exception) {
                return redirect()->route('admin.projects.index')
                    ->with('error', 'Permanent delete blocked due to dependent data. Please use soft delete.');
            }
        }

        $project->delete();
        return redirect()->route('admin.projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    private function authorizeProjectAccess(Project $project): void
    {
        if (Auth::user()->isProjectManager() && !Auth::user()->isAdmin() && $project->project_manager_id !== Auth::id()) {
            abort(403, 'You can only manage your assigned projects.');
        }
    }

    private function authorizeProjectWrite(): void
    {
        if (Auth::user()->isProjectManager() && !Auth::user()->isAdmin()) {
            abort(403, 'Project managers can only view assigned projects.');
        }
    }

    private function hasProjectRoleConflict(int $projectManagerId, array $teamIds, array $memberUserIds = []): bool
    {
        $inAssignedTeams = !empty($teamIds) && Team::whereIn('id', $teamIds)
            ->whereHas('users', function ($query) use ($projectManagerId) {
                $query->where('users.id', $projectManagerId);
            })
            ->exists();

        if ($inAssignedTeams) {
            return true;
        }

        return in_array($projectManagerId, array_map('intval', $memberUserIds), true);
    }

    private function ensureAssignableTeamMembers(array $userIds): void
    {
        if (empty($userIds)) {
            return;
        }

        $validTeamMemberIds = User::whereIn('id', $userIds)
            ->whereHas('roles', function ($query) {
                $query->where('name', Role::TEAM_MEMBER);
            })
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();

        if (count($validTeamMemberIds) !== count($userIds)) {
            throw ValidationException::withMessages([
                'users' => 'Only users with Team Member role can be assigned directly to a project.',
            ]);
        }
    }

    private function collectProjectDependencies(Project $project): array
    {
        return [
            'tasks' => $project->tasks()->withTrashed()->count(),
            'invoices' => $project->invoices()->withTrashed()->count(),
            'salaries' => $project->salaries()->withTrashed()->count(),
            'team_assignments' => DB::table('project_team')->where('project_id', $project->id)->count(),
            'member_assignments' => DB::table('project_user')->where('project_id', $project->id)->count(),
            'payments' => Payment::withTrashed()
                ->whereHas('invoice', function ($query) use ($project) {
                    $query->withTrashed()->where('project_id', $project->id);
                })->count(),
        ];
    }
}
