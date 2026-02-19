<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Salary;
use App\Models\User;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filterMonth = $request->input('month', now()->format('Y-m'));
        
        $salaries = Salary::withTrashed()->with([
                'user' => fn ($query) => $query->withTrashed(),
                'project' => fn ($query) => $query->withTrashed(),
            ])
            ->whereYear('month', '=', substr($filterMonth, 0, 4))
            ->whereMonth('month', '=', substr($filterMonth, 5, 2))
            ->orderBy('month', 'desc')
            ->get();
            
        return view('admin.salaries.index', compact('salaries', 'filterMonth'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['admin', 'team_member']);
        })->get();
        $projects = Project::all();
        
        return view('admin.salaries.create', compact('users', 'projects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|date_format:Y-m',
            'project_id' => 'nullable|exists:projects,id',
            'base_amount' => 'required|numeric|min:0',
            'bonus_amount' => 'nullable|numeric|min:0',
            'deduction_amount' => 'nullable|numeric|min:0',
            'payment_date' => 'nullable|date',
            'status' => 'required|in:pending,paid,cancelled',
            'notes' => 'nullable|string',
        ]);

        // Convert month to first day of month
        $validated['month'] = $validated['month'] . '-01';
        $validated['bonus_amount'] = $validated['bonus_amount'] ?? 0;
        $validated['deduction_amount'] = $validated['deduction_amount'] ?? 0;

        Salary::create($validated);

        return redirect()->route('admin.salaries.index')
            ->with('success', 'Salary record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Salary $salary)
    {
        $salary->load(['user.role', 'user.roles', 'project.client.user']);
        return view('admin.salaries.show', compact('salary'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Redirect to show page, salary editing handled via quick actions
        return redirect()->route('admin.salaries.show', $id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Salary $salary)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|date_format:Y-m',
            'project_id' => 'nullable|exists:projects,id',
            'base_amount' => 'required|numeric|min:0',
            'bonus_amount' => 'nullable|numeric|min:0',
            'deduction_amount' => 'nullable|numeric|min:0',
            'payment_date' => 'nullable|date',
            'status' => 'required|in:pending,paid,cancelled',
            'notes' => 'nullable|string',
        ]);

        // Convert month to first day of month
        $validated['month'] = $validated['month'] . '-01';
        $validated['bonus_amount'] = $validated['bonus_amount'] ?? 0;
        $validated['deduction_amount'] = $validated['deduction_amount'] ?? 0;

        $salary->update($validated);

        return redirect()->route('admin.salaries.show', $salary)
            ->with('success', 'Salary record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Salary $salary)
    {
        $forceDelete = $request->input('delete_mode') === 'force';
        if ($forceDelete && !$request->user()->isAdmin()) {
            return back()->with('error', 'Only admins can permanently delete records.');
        }

        if ($forceDelete) {
            try {
                $salary->forceDelete();

                return redirect()->route('admin.salaries.index')
                    ->with('success', 'Salary record permanently deleted successfully.');
            } catch (\Illuminate\Database\QueryException $exception) {
                return redirect()->route('admin.salaries.index')
                    ->with('error', 'Permanent delete blocked due to dependent data. Please use soft delete.');
            }
        }

        $salary->delete();
        return redirect()->route('admin.salaries.index')
            ->with('success', 'Salary record deleted successfully.');
    }
}
