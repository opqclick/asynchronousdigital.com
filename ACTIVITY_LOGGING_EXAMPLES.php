<?php

/**
 * USER ACTIVITY LOGGING EXAMPLES
 * 
 * This file shows how to implement activity logging in your controllers.
 * Copy these examples into your actual controller methods.
 */

use App\Models\UserActivity;

// ============================================
// EXAMPLE 1: Logging CREATE actions
// ============================================
public function store(Request $request)
{
    $validated = $request->validate([...]);
    
    $salary = Salary::create($validated);
    
    // Log the creation
    UserActivity::log(
        'create',
        "Created salary for {$salary->user->name} - Month: {$salary->month->format('F Y')}",
        'Salary',
        $salary->id
    );
    
    return redirect()->route('admin.salaries.index')
        ->with('success', 'Salary created successfully.');
}

// ============================================
// EXAMPLE 2: Logging UPDATE actions with changes
// ============================================
public function update(Request $request, Salary $salary)
{
    $validated = $request->validate([...]);
    
    // Capture old values before update
    $oldValues = $salary->only(['base_amount', 'bonus', 'deduction', 'status']);
    
    $salary->update($validated);
    
    // Capture new values after update
    $newValues = $salary->fresh()->only(['base_amount', 'bonus', 'deduction', 'status']);
    
    // Build changes array
    $changes = [];
    foreach ($newValues as $key => $newValue) {
        if ($oldValues[$key] != $newValue) {
            $changes[$key] = [
                'old' => $oldValues[$key],
                'new' => $newValue
            ];
        }
    }
    
    // Log the update
    UserActivity::log(
        'update',
        "Updated salary #{$salary->id} for {$salary->user->name}",
        'Salary',
        $salary->id,
        $changes
    );
    
    return redirect()->route('admin.salaries.index')
        ->with('success', 'Salary updated successfully.');
}

// ============================================
// EXAMPLE 3: Logging DELETE actions
// ============================================
public function destroy(Salary $salary)
{
    $userName = $salary->user->name;
    $salaryId = $salary->id;
    $month = $salary->month->format('F Y');
    
    $salary->delete();
    
    // Log the deletion
    UserActivity::log(
        'delete',
        "Deleted salary #{$salaryId} for {$userName} - Month: {$month}",
        'Salary',
        $salaryId
    );
    
    return redirect()->route('admin.salaries.index')
        ->with('success', 'Salary deleted successfully.');
}

// ============================================
// EXAMPLE 4: Logging VIEW actions (optional)
// ============================================
public function show(Salary $salary)
{
    // Log viewing sensitive information
    UserActivity::log(
        'view',
        "Viewed salary details for {$salary->user->name} - Month: {$salary->month->format('F Y')}",
        'Salary',
        $salary->id
    );
    
    return view('admin.salaries.show', compact('salary'));
}

// ============================================
// EXAMPLE 5: Logging BULK actions
// ============================================
public function bulkApprove(Request $request)
{
    $salaryIds = $request->input('salary_ids');
    $salaries = Salary::whereIn('id', $salaryIds)->get();
    
    foreach ($salaries as $salary) {
        $salary->update(['status' => 'paid']);
    }
    
    // Log the bulk action
    UserActivity::log(
        'update',
        "Bulk approved " . count($salaryIds) . " salaries",
        'Salary',
        null,
        ['salary_ids' => $salaryIds]
    );
    
    return redirect()->back()
        ->with('success', 'Salaries approved successfully.');
}

// ============================================
// EXAMPLE 6: Logging PROJECT actions
// ============================================
public function storeProject(Request $request)
{
    $validated = $request->validate([...]);
    
    $project = Project::create($validated);
    
    // Log project creation
    UserActivity::log(
        'create',
        "Created new project: {$project->name}",
        'Project',
        $project->id
    );
    
    return redirect()->route('admin.projects.index')
        ->with('success', 'Project created successfully.');
}

// ============================================
// EXAMPLE 7: Logging USER management actions
// ============================================
public function storeUser(Request $request)
{
    $validated = $request->validate([...]);
    
    $user = User::create($validated);
    
    // Log user creation
    UserActivity::log(
        'create',
        "Created new user: {$user->name} ({$user->email}) with role: {$user->role->name}",
        'User',
        $user->id
    );
    
    return redirect()->route('admin.users.index')
        ->with('success', 'User created successfully.');
}

// ============================================
// EXAMPLE 8: Logging STATUS changes
// ============================================
public function changeStatus(Salary $salary, $newStatus)
{
    $oldStatus = $salary->status;
    $salary->update(['status' => $newStatus]);
    
    // Log status change
    UserActivity::log(
        'update',
        "Changed salary #{$salary->id} status from {$oldStatus} to {$newStatus}",
        'Salary',
        $salary->id,
        ['status' => ['old' => $oldStatus, 'new' => $newStatus]]
    );
    
    return redirect()->back()
        ->with('success', 'Status updated successfully.');
}

// ============================================
// SIMPLE LOGGING WITHOUT MODEL (e.g., Settings)
// ============================================
public function updateSettings(Request $request)
{
    // Update settings logic...
    
    UserActivity::log(
        'update',
        "Updated system settings",
        null,  // No specific model
        null   // No specific model ID
    );
    
    return redirect()->back()
        ->with('success', 'Settings updated successfully.');
}
