<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Mail\UserInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with(['role', 'roles', 'teams'])->latest()->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        $teams = Team::all();
        return view('admin.users.create', compact('roles', 'teams'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_ids' => ['required', 'array', 'min:1'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
            'active_role_id' => ['nullable', 'integer', 'exists:roles,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'joining_date' => ['nullable', 'date'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'documents.*' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:5120'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_account_holder' => ['nullable', 'string', 'max:255'],
            'bank_routing_number' => ['nullable', 'string', 'max:50'],
            'bank_swift_code' => ['nullable', 'string', 'max:20'],
            'payment_model' => ['nullable', 'in:hourly,fixed,monthly'],
            'monthly_salary' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
            'send_invitation_email' => ['boolean'],
            'teams' => ['nullable', 'array'],
            'teams.*' => ['exists:teams,id'],
        ]);

        $roleIds = array_values(array_unique(array_map('intval', $validated['role_ids'])));
        $selectedRoles = Role::whereIn('id', $roleIds)->get(['id', 'name']);
        $hasClientRole = $selectedRoles->contains(fn (Role $role) => $role->name === Role::CLIENT);

        if ($hasClientRole && count($roleIds) > 1) {
            return back()->withErrors([
                'role_ids' => 'Client role must stay exclusive and cannot be combined with other roles.',
            ])->withInput();
        }

        $activeRoleId = isset($validated['active_role_id']) && in_array((int) $validated['active_role_id'], $roleIds, true)
            ? (int) $validated['active_role_id']
            : $roleIds[0];

        // Store the plain password for the invitation email
        $plainPassword = $validated['password'];

        // Handle profile picture upload
        $profilePicturePath = null;
        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = $request->file('profile_picture')->storePublicly('profile_pictures', 'do_spaces');
        }

        // Handle documents upload
        $documentPaths = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $documentPaths[] = $document->storePublicly('user_documents', 'do_spaces');
            }
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $activeRoleId,
            'active_role_id' => $activeRoleId,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'joining_date' => $validated['joining_date'] ?? null,
            'profile_picture' => $profilePicturePath,
            'documents' => !empty($documentPaths) ? $documentPaths : null,
            'bank_name' => $validated['bank_name'] ?? null,
            'bank_account_number' => $validated['bank_account_number'] ?? null,
            'bank_account_holder' => $validated['bank_account_holder'] ?? null,
            'bank_routing_number' => $validated['bank_routing_number'] ?? null,
            'bank_swift_code' => $validated['bank_swift_code'] ?? null,
            'payment_model' => $validated['payment_model'] ?? null,
            'monthly_salary' => $validated['monthly_salary'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        $user->syncRolesWithRules($roleIds, $activeRoleId);

        // Attach teams if any
        if (!empty($validated['teams'])) {
            $user->teams()->attach($validated['teams'], [
                'joined_at' => now(),
            ]);
        }

        // Send invitation email if checkbox is checked
        if ($request->has('send_invitation_email')) {
            try {
                Mail::to($user->email)->send(new UserInvitation($user, $plainPassword));
            } catch (\Exception $e) {
                // Log the error but don't fail the user creation
                Log::error('Failed to send invitation email: ' . $e->getMessage());
            }
        }

        $successMessage = $request->has('send_invitation_email') 
            ? 'User created successfully and invitation email sent.' 
            : 'User created successfully.';

        return redirect()->route('admin.users.index')
            ->with('success', $successMessage);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['role', 'roles', 'teams', 'tasks', 'salaries']);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $teams = Team::all();
        $userTeams = $user->teams->pluck('id')->toArray();
        $userRoleIds = $user->roles()->pluck('roles.id')->toArray();

        return view('admin.users.edit', compact('user', 'roles', 'teams', 'userTeams', 'userRoleIds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role_ids' => ['required', 'array', 'min:1'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
            'active_role_id' => ['nullable', 'integer', 'exists:roles,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'joining_date' => ['nullable', 'date'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'documents.*' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:5120'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_account_holder' => ['nullable', 'string', 'max:255'],
            'bank_routing_number' => ['nullable', 'string', 'max:50'],
            'bank_swift_code' => ['nullable', 'string', 'max:20'],
            'payment_model' => ['nullable', 'in:hourly,fixed,monthly'],
            'monthly_salary' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
            'teams' => ['nullable', 'array'],
            'teams.*' => ['exists:teams,id'],
        ]);

        $roleIds = array_values(array_unique(array_map('intval', $validated['role_ids'])));
        $selectedRoles = Role::whereIn('id', $roleIds)->get(['id', 'name']);
        $hasClientRole = $selectedRoles->contains(fn (Role $role) => $role->name === Role::CLIENT);

        if ($hasClientRole && count($roleIds) > 1) {
            return back()->withErrors([
                'role_ids' => 'Client role must stay exclusive and cannot be combined with other roles.',
            ])->withInput();
        }

        $activeRoleId = isset($validated['active_role_id']) && in_array((int) $validated['active_role_id'], $roleIds, true)
            ? (int) $validated['active_role_id']
            : ($user->active_role_id && in_array((int) $user->active_role_id, $roleIds, true)
                ? (int) $user->active_role_id
                : $roleIds[0]);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old picture if exists
            if ($user->profile_picture && Storage::disk('do_spaces')->exists($user->profile_picture)) {
                Storage::disk('do_spaces')->delete($user->profile_picture);
            }
            $validated['profile_picture'] = $request->file('profile_picture')->storePublicly('profile_pictures', 'do_spaces');
        }

        // Handle documents upload
        if ($request->hasFile('documents')) {
            $existingDocuments = $user->documents ?? [];
            foreach ($request->file('documents') as $document) {
                $existingDocuments[] = $document->storePublicly('user_documents', 'do_spaces');
            }
            $validated['documents'] = $existingDocuments;
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $activeRoleId,
            'active_role_id' => $activeRoleId,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'joining_date' => $validated['joining_date'] ?? null,
            'profile_picture' => $validated['profile_picture'] ?? $user->profile_picture,
            'documents' => $validated['documents'] ?? $user->documents,
            'bank_name' => $validated['bank_name'] ?? null,
            'bank_account_number' => $validated['bank_account_number'] ?? null,
            'bank_account_holder' => $validated['bank_account_holder'] ?? null,
            'bank_routing_number' => $validated['bank_routing_number'] ?? null,
            'bank_swift_code' => $validated['bank_swift_code'] ?? null,
            'payment_model' => $validated['payment_model'] ?? null,
            'monthly_salary' => $validated['monthly_salary'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        $user->syncRolesWithRules($roleIds, $activeRoleId);

        // Update password if provided
        if (!empty($validated['password'])) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        // Sync teams
        if (isset($validated['teams'])) {
            $teamsWithTimestamp = [];
            foreach ($validated['teams'] as $teamId) {
                $teamsWithTimestamp[$teamId] = ['joined_at' => now()];
            }
            $user->teams()->sync($teamsWithTimestamp);
        } else {
            $user->teams()->detach();
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Send invitation email to user.
     */
    public function sendInvitation(User $user)
    {
        // Generate a temporary password or use a reset token
        $temporaryPassword = Str::random(12);
        
        // Update user's password
        $user->update([
            'password' => Hash::make($temporaryPassword),
        ]);

        // Send invitation email
        try {
            Mail::to($user->email)->send(new UserInvitation($user, $temporaryPassword));
            return redirect()->route('admin.users.index')
                ->with('success', 'Invitation email sent successfully to ' . $user->email);
        } catch (\Exception $e) {
            Log::error('Failed to send invitation email: ' . $e->getMessage());
            return redirect()->route('admin.users.index')
                ->with('error', 'Failed to send invitation email. Please try again.');
        }
    }

    /**
     * Impersonate a user account.
     */
    public function impersonate(Request $request, User $user)
    {
        $admin = $request->user();

        if (!$admin->isAdmin()) {
            abort(403, 'Only admins can impersonate users.');
        }

        if ($admin->id === $user->id) {
            return back()->with('error', 'You are already logged in as this user.');
        }

        if ($user->hasAssignedRole(Role::ADMIN)) {
            return back()->with('error', 'Impersonating another admin is not allowed.');
        }

        if ($request->session()->has('impersonator_id')) {
            return back()->with('error', 'Already impersonating a user. Please return to admin first.');
        }

        Auth::login($user);
        $request->session()->put('impersonator_id', $admin->id);

        return redirect()->route('dashboard')
            ->with('success', 'You are now logged in as ' . $user->name . '.');
    }

    /**
     * Stop impersonation and return to original admin account.
     */
    public function stopImpersonation(Request $request)
    {
        $impersonatorId = $request->session()->pull('impersonator_id');

        if (!$impersonatorId) {
            return redirect()->route('dashboard')
                ->with('error', 'No active impersonation session found.');
        }

        $admin = User::find($impersonatorId);

        if (!$admin || !$admin->isAdmin()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Original admin account is not available. Please log in again.');
        }

        Auth::login($admin);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Returned to admin account.');
    }
}
