<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::disk('do_spaces')->delete($user->profile_picture);
            }

            $path = $request->file('profile_picture')->store('profile-pictures', 'do_spaces');
            $validated['profile_picture'] = $path;
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Switch active role context for current user.
     */
    public function switchRole(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'active_role_id' => ['required', 'integer', 'exists:roles,id'],
        ]);

        $user = $request->user();

        if (!$user->roles()->where('roles.id', $validated['active_role_id'])->exists()) {
            return Redirect::back()->with('error', 'Selected role is not assigned to your account.');
        }

        $user->switchActiveRole((int) $validated['active_role_id']);

        return Redirect::route('dashboard')->with('success', 'Active role switched successfully.');
    }

    /**
     * Delete the user's account (Admin only, soft delete).
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Only admins can delete accounts
        if (!$request->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        // Use soft delete instead of hard delete
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
