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
            ->orderBy('created_at', 'desc');

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
}
