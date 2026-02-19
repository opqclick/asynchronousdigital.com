<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamContent;
use Illuminate\Http\Request;

class TeamContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teamContents = TeamContent::withTrashed()->orderBy('display_order')->get();

        return view('admin.team-contents.index', compact('teamContents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.team-contents.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role_title' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'image_url' => 'nullable|url|max:500',
            'display_order' => 'nullable|integer|min:0',
            'is_published' => 'boolean',
        ]);

        $validated['display_order'] = $validated['display_order'] ?? 0;
        $validated['is_published'] = $request->has('is_published');

        TeamContent::create($validated);

        return redirect()->route('admin.team-contents.index')
            ->with('success', 'Team content created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TeamContent $teamContent)
    {
        return view('admin.team-contents.show', compact('teamContent'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TeamContent $teamContent)
    {
        return view('admin.team-contents.edit', compact('teamContent'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TeamContent $teamContent)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role_title' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'image_url' => 'nullable|url|max:500',
            'display_order' => 'nullable|integer|min:0',
            'is_published' => 'boolean',
        ]);

        $validated['display_order'] = $validated['display_order'] ?? $teamContent->display_order;
        $validated['is_published'] = $request->has('is_published');

        $teamContent->update($validated);

        return redirect()->route('admin.team-contents.index')
            ->with('success', 'Team content updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, TeamContent $teamContent)
    {
        $forceDelete = $request->input('delete_mode') === 'force';
        if ($forceDelete && !$request->user()->isAdmin()) {
            return back()->with('error', 'Only admins can permanently delete records.');
        }

        if ($forceDelete) {
            try {
                $teamContent->forceDelete();

                return redirect()->route('admin.team-contents.index')
                    ->with('success', 'Team content permanently deleted successfully.');
            } catch (\Illuminate\Database\QueryException $exception) {
                return redirect()->route('admin.team-contents.index')
                    ->with('error', 'Permanent delete blocked due to dependent data. Please use soft delete.');
            }
        }

        $teamContent->delete();

        return redirect()->route('admin.team-contents.index')
            ->with('success', 'Team content deleted successfully.');
    }
}
