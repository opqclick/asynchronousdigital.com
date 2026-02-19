<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function roleIndex(): View
    {
        $roles = Role::orderBy('display_name')->get();

        return view('admin.permissions.roles-index', compact('roles'));
    }

    public function roleEdit(Role $role): View
    {
        $permissions = Permission::orderBy('name')->get();
        $assignedPermissionIds = $role->permissionItems()->pluck('permissions.id')->all();

        return view('admin.permissions.role-edit', compact('role', 'permissions', 'assignedPermissionIds'));
    }

    public function roleUpdate(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role->permissionItems()->sync($validated['permission_ids'] ?? []);

        return redirect()->route('admin.permissions.roles.index')
            ->with('success', 'Role permissions updated successfully.');
    }

    public function userIndex(): View
    {
        $users = User::with(['roles', 'role'])->latest()->get();

        return view('admin.permissions.users-index', compact('users'));
    }

    public function userEdit(User $user): View
    {
        $permissions = Permission::orderBy('name')->get();

        $roleIds = $user->roles()->pluck('roles.id')->all();
        $rolePermissionIds = Permission::whereHas('roles', function ($query) use ($roleIds) {
            $query->whereIn('roles.id', $roleIds);
        })->pluck('permissions.id')->all();

        $overrides = $user->permissionOverrides()->get();
        $allowedPermissionIds = $overrides->filter(fn ($permission) => (bool) $permission->pivot->allowed)->pluck('id')->all();
        $deniedPermissionIds = $overrides->filter(fn ($permission) => !(bool) $permission->pivot->allowed)->pluck('id')->all();

        return view('admin.permissions.user-edit', compact(
            'user',
            'permissions',
            'rolePermissionIds',
            'allowedPermissionIds',
            'deniedPermissionIds'
        ));
    }

    public function userUpdate(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'allow_permissions' => ['nullable', 'array'],
            'allow_permissions.*' => ['integer', 'exists:permissions,id'],
            'deny_permissions' => ['nullable', 'array'],
            'deny_permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $allowPermissions = array_map('intval', $validated['allow_permissions'] ?? []);
        $denyPermissions = array_map('intval', $validated['deny_permissions'] ?? []);

        if (!empty(array_intersect($allowPermissions, $denyPermissions))) {
            return back()->withErrors([
                'deny_permissions' => 'A permission cannot be set to both allow and deny for the same user.',
            ])->withInput();
        }

        $syncPayload = [];
        foreach ($allowPermissions as $permissionId) {
            $syncPayload[$permissionId] = ['allowed' => true];
        }
        foreach ($denyPermissions as $permissionId) {
            $syncPayload[$permissionId] = ['allowed' => false];
        }

        $user->permissionOverrides()->sync($syncPayload);

        return redirect()->route('admin.permissions.users.index')
            ->with('success', 'User permission overrides updated successfully.');
    }
}
