<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->renameOldRole('super-admin', UserRole::SuperAdmin->value);
        $this->renameOldRole('employee', UserRole::Employee->value);

        Role::findOrCreate(UserRole::SuperAdmin->value, 'web');
        Role::findOrCreate(UserRole::Employee->value, 'web');

        $permissions = [
            'view dashboard',
            'view employees',
            'view employee work',
            'create task',
            'edit task',
            'delete task',
            'start task',
            'complete task',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        Role::findByName(UserRole::SuperAdmin->value, 'web')
            ->syncPermissions($permissions);

        Role::findByName(UserRole::Employee->value, 'web')
            ->syncPermissions([
                'view dashboard',
                'start task',
                'complete task',
            ]);

        User::doesntHave('roles')->each(function (User $user): void {
            $user->syncRoles([UserRole::Employee->value]);
        });

        $superAdmins = [
            ['name' => 'Super Admin', 'email' => 'superadmin@gmail.com'],
        ];

        foreach ($superAdmins as $superAdminData) {
            $superAdmin = User::updateOrCreate([
                'email' => $superAdminData['email'],
            ], [
                'name' => $superAdminData['name'],
                'password' => Hash::make('12345678'),
            ]);

            $superAdmin->syncRoles([UserRole::SuperAdmin->value]);
        }

        $employees = [
            ['name' => 'Employee', 'email' => 'employee@gmail.com'],
        ];

        foreach ($employees as $employeeData) {
            $employee = User::updateOrCreate([
                'email' => $employeeData['email'],
            ], [
                'name' => $employeeData['name'],
                'password' => Hash::make('12345678'),
            ]);

            $employee->syncRoles([UserRole::Employee->value]);
        }
    }

    private function renameOldRole(string $oldName, string $newName): void
    {
        $oldRole = Role::where('name', $oldName)
            ->where('guard_name', 'web')
            ->first();

        if (! $oldRole) {
            return;
        }

        $newRole = Role::where('name', $newName)
            ->where('guard_name', 'web')
            ->first();

        if (! $newRole) {
            $oldRole->update(['name' => $newName]);

            return;
        }

        $oldAssignments = DB::table('model_has_roles')
            ->where('role_id', $oldRole->id)
            ->get();

        foreach ($oldAssignments as $assignment) {
            $alreadyAssigned = DB::table('model_has_roles')
                ->where('role_id', $newRole->id)
                ->where('model_type', $assignment->model_type)
                ->where('model_id', $assignment->model_id)
                ->exists();

            if (! $alreadyAssigned) {
                DB::table('model_has_roles')->insert([
                    'role_id' => $newRole->id,
                    'model_type' => $assignment->model_type,
                    'model_id' => $assignment->model_id,
                ]);
            }
        }

        DB::table('model_has_roles')
            ->where('role_id', $oldRole->id)
            ->delete();

        $oldRole->delete();
    }
}
