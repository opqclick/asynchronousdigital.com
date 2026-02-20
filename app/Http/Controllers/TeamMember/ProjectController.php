<?php

namespace App\Http\Controllers\TeamMember;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display all projects the team member is assigned to.
     */
    public function index()
    {
        $projects = Project::with(['client.user', 'projectManager', 'tasks'])
            ->whereHas('users', fn($q) => $q->where('users.id', Auth::id()))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('team-member.projects.index', compact('projects'));
    }

    /**
     * Display a specific project (only if assigned).
     */
    public function show(Project $project)
    {
        $isAssigned = $project->users()->where('users.id', Auth::id())->exists();

        if (!$isAssigned) {
            abort(403, 'You are not assigned to this project.');
        }

        $project->load(['client.user', 'projectManager', 'tasks', 'teams']);

        return view('team-member.projects.show', compact('project'));
    }
}
