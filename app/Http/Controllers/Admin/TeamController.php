<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teams = Team::withCount(['users', 'projects', 'tasks'])->get();
        return view('admin.teams.index', compact('teams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['admin', 'team_member']);
        })->get();
        
        return view('admin.teams.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ]);

        $team = Team::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        // Attach members if provided
        if (!empty($validated['members'])) {
            $team->users()->attach($validated['members']);
        }

        return redirect()->route('admin.teams.index')
            ->with('success', 'Team created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        $team->load(['users.roles', 'users.role', 'projects.client.user', 'tasks.project']);
        return view('admin.teams.show', compact('team'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        $users = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['admin', 'team_member']);
        })->get();
        
        $team->load('users');
        
        return view('admin.teams.edit', compact('team', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ]);

        $memberIds = $validated['members'] ?? [];
        $projectManagerIds = Project::whereHas('teams', function ($query) use ($team) {
            $query->where('teams.id', $team->id);
        })->pluck('project_manager_id')->filter()->unique()->all();

        $conflictingIds = array_values(array_intersect($memberIds, $projectManagerIds));
        if (!empty($conflictingIds)) {
            return back()->withErrors([
                'members' => 'Role conflict: one or more selected members are already Project Managers for projects assigned to this team. A user cannot be both Project Manager and Team Member under the same project.',
            ])->withInput();
        }

        $team->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        // Sync members
        if (isset($validated['members'])) {
            $team->users()->sync($validated['members']);
        } else {
            $team->users()->detach();
        }

        return redirect()->route('admin.teams.index')
            ->with('success', 'Team updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        $team->delete();
        return redirect()->route('admin.teams.index')
            ->with('success', 'Team deleted successfully.');
    }
}
