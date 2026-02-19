<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Http\Request;

class UserActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = UserActivity::with('user')
            ->withTrashed()
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->withoutTrashed();
            } elseif ($request->status === 'deleted') {
                $query->onlyTrashed();
            }
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by model
        if ($request->filled('model')) {
            $query->where('model', $request->model);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activities = $query->paginate(50);
        $users = User::orderBy('name')->get();

        return view('admin.user-activities.index', compact('activities', 'users'));
    }

    public function show(UserActivity $activity)
    {
        $activity->load('user');
        return view('admin.user-activities.show', compact('activity'));
    }

    public function edit(UserActivity $activity)
    {
        $activity->load('user');

        return view('admin.user-activities.edit', compact('activity'));
    }

    public function update(Request $request, UserActivity $activity)
    {
        $validated = $request->validate([
            'action' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
        ]);

        $before = [
            'action' => $activity->action,
            'description' => $activity->description,
        ];

        $activity->update($validated);

        UserActivity::log(
            'update',
            'Updated activity log #' . $activity->id,
            'UserActivity',
            $activity->id,
            [
                'action' => ['old' => $before['action'], 'new' => $activity->action],
                'description' => ['old' => $before['description'], 'new' => $activity->description],
            ]
        );

        return redirect()->route('admin.user-activities.show', $activity)
            ->with('success', 'Activity updated successfully.');
    }

    public function destroy(Request $request, UserActivity $activity)
    {
        $deletedId = $activity->id;
        $deletedAction = $activity->action;
        $deletedDescription = $activity->description;

        $forceDelete = $request->input('delete_mode') === 'force';
        if ($forceDelete && !$request->user()->isAdmin()) {
            return back()->with('error', 'Only admins can permanently delete records.');
        }

        if ($forceDelete) {
            $activity->forceDelete();

            UserActivity::log(
                'delete',
                'Permanently deleted activity log #' . $deletedId,
                'UserActivity',
                $deletedId,
                [
                    'action' => ['old' => $deletedAction, 'new' => null],
                    'description' => ['old' => $deletedDescription, 'new' => null],
                ]
            );

            return redirect()->route('admin.user-activities.index')
                ->with('success', 'Activity permanently deleted successfully.');
        }

        $activity->delete();

        UserActivity::log(
            'delete',
            'Deleted activity log #' . $deletedId,
            'UserActivity',
            $deletedId,
            [
                'action' => ['old' => $deletedAction, 'new' => null],
                'description' => ['old' => $deletedDescription, 'new' => null],
            ]
        );

        return redirect()->route('admin.user-activities.index')
            ->with('success', 'Activity deleted successfully.');
    }

    public function restore(int $activity)
    {
        $record = UserActivity::onlyTrashed()->findOrFail($activity);
        $record->restore();

        UserActivity::log(
            'update',
            'Restored activity log #' . $record->id,
            'UserActivity',
            $record->id,
            [
                'deleted_at' => ['old' => 'deleted', 'new' => null],
            ]
        );

        return redirect()->route('admin.user-activities.index')
            ->with('success', 'Activity restored successfully.');
    }
}
