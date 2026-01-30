<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('role', 'teams')->latest()->get();
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
            'role_id' => ['required', 'exists:roles,id'],
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

        // Handle profile picture upload
        $profilePicturePath = null;
        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'do_spaces');
        }

        // Handle documents upload
        $documentPaths = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $documentPaths[] = $document->store('user_documents', 'do_spaces');
            }
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
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

        // Attach teams if any
        if (!empty($validated['teams'])) {
            $user->teams()->attach($validated['teams'], [
                'joined_at' => now(),
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('role', 'teams', 'tasks', 'salaries');
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
        return view('admin.users.edit', compact('user', 'roles', 'teams', 'userTeams'));
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
            'role_id' => ['required', 'exists:roles,id'],
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

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old picture if exists
            if ($user->profile_picture && Storage::disk('do_spaces')->exists($user->profile_picture)) {
                Storage::disk('do_spaces')->delete($user->profile_picture);
            }
            $validated['profile_picture'] = $request->file('profile_picture')->store('profile_pictures', 'do_spaces');
        }

        // Handle documents upload
        if ($request->hasFile('documents')) {
            $existingDocuments = $user->documents ?? [];
            foreach ($request->file('documents') as $document) {
                $existingDocuments[] = $document->store('user_documents', 'do_spaces');
            }
            $validated['documents'] = $existingDocuments;
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
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
}
