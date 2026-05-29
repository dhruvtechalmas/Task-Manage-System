<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function edit(): View
    {
        $employeeRole = Role::findByName(UserRole::Employee->value, 'web');

        return view('super-admin.permissions.edit', [
            'permissions' => Permission::orderBy('name')->get(),
            'employeePermissions' => $employeeRole->permissions->pluck('name')->toArray(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $employeeRole = Role::findByName(UserRole::Employee->value, 'web');
        $employeeRole->syncPermissions($request->permissions ?? []);

        return redirect()
            ->route('super-admin.permissions.edit')
            ->with('status', 'Employee permissions updated.');
    }

    public function editEmployeePermissions(User $employee): View
    {
        abort_unless($employee->hasRole(UserRole::Employee->value), 404);

        return view('super-admin.permissions.user-edit', [
            'employee' => $employee,
            'permissions' => Permission::orderBy('name')->get(),
            'assignedPermissions' => $employee->getPermissionNames()->toArray(),
            'directPermissions' => $employee->getDirectPermissions()->pluck('name')->toArray(),
            'rolePermissions' => $employee->getPermissionsViaRoles()->pluck('name')->toArray(),
        ]);
    }

    public function updateEmployeePermissions(Request $request, User $employee): RedirectResponse
    {
        abort_unless($employee->hasRole(UserRole::Employee->value), 404);

        $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $employee->syncPermissions($request->permissions ?? []);

        return redirect()
            ->route('super-admin.employees.permissions.edit', $employee)
            ->with('status', 'Permissions updated for ' . $employee->name . '.');
    }
}
