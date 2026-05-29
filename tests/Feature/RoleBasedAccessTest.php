<?php

use App\Enums\UserRole;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
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

    Role::findOrCreate(UserRole::SuperAdmin->value, 'web')
        ->syncPermissions($permissions);

    Role::findOrCreate(UserRole::Employee->value, 'web')
        ->syncPermissions([
            'view dashboard',
            'start task',
            'complete task',
        ]);
});

test('super admin is redirected to the super admin dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::SuperAdmin->value);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('super-admin.dashboard', absolute: false));
});

test('employee is redirected to the employee dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::Employee->value);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('employee.dashboard', absolute: false));
});

test('super admin dashboard is restricted to super admins', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::Employee->value);

    $this->actingAs($user)
        ->get(route('super-admin.dashboard'))
        ->assertForbidden();
});

test('employee dashboard is restricted to employees', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::SuperAdmin->value);

    $this->actingAs($user)
        ->get(route('employee.dashboard'))
        ->assertForbidden();
});

test('super admin dashboard includes account navigation', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::SuperAdmin->value);

    $this->actingAs($user)
        ->get(route('super-admin.dashboard'))
        ->assertSuccessful()
        ->assertSee(route('profile.edit', absolute: false), false)
        ->assertSee(route('logout', absolute: false), false);
});

test('employee dashboard includes account navigation', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::Employee->value);

    $this->actingAs($user)
        ->get(route('employee.dashboard'))
        ->assertSuccessful()
        ->assertSee(route('profile.edit', absolute: false), false)
        ->assertSee(route('logout', absolute: false), false);
});

test('super admin dashboard shows employees and all tasks', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(UserRole::SuperAdmin->value);

    $employee = User::factory()->create(['name' => 'Employee One']);
    $employee->assignRole(UserRole::Employee->value);

    Task::create([
        'assigned_by' => $superAdmin->id,
        'assigned_to' => $employee->id,
        'title' => 'Prepare report',
        'description' => 'Prepare the monthly report.',
        'priority' => 'high',
        'status' => TaskStatus::Pending->value,
        'due_date' => now()->addDay()->toDateString(),
    ]);

    $this->actingAs($superAdmin)
        ->get(route('super-admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('Employee One')
        ->assertSee('Prepare report')
        ->assertSee('Total Employees')
        ->assertSee('Pending Tasks');
});

test('employee dashboard only shows assigned tasks', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(UserRole::SuperAdmin->value);

    $employee = User::factory()->create();
    $employee->assignRole(UserRole::Employee->value);

    $otherEmployee = User::factory()->create();
    $otherEmployee->assignRole(UserRole::Employee->value);

    Task::create([
        'assigned_by' => $superAdmin->id,
        'assigned_to' => $employee->id,
        'title' => 'Visible employee task',
        'description' => 'This task belongs to the employee.',
        'priority' => 'medium',
        'status' => TaskStatus::InProgress->value,
        'due_date' => now()->addDay()->toDateString(),
    ]);

    Task::create([
        'assigned_by' => $superAdmin->id,
        'assigned_to' => $otherEmployee->id,
        'title' => 'Hidden employee task',
        'description' => 'This task belongs to another employee.',
        'priority' => 'low',
        'status' => TaskStatus::Pending->value,
        'due_date' => now()->addDays(2)->toDateString(),
    ]);

    $this->actingAs($employee)
        ->get(route('employee.dashboard'))
        ->assertSuccessful()
        ->assertSee('Visible employee task')
        ->assertDontSee('Hidden employee task');
});

test('super admin can create a task for an employee', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(UserRole::SuperAdmin->value);

    $employee = User::factory()->create();
    $employee->assignRole(UserRole::Employee->value);

    $this->actingAs($superAdmin)
        ->post(route('tasks.store'), [
            'assigned_to' => $employee->id,
            'title' => 'Create login page',
            'description' => 'Build the employee login page.',
            'priority' => 'high',
            'due_date' => now()->addWeek()->toDateString(),
        ])
        ->assertRedirect(route('super-admin.dashboard', absolute: false));

    $this->assertDatabaseHas('tasks', [
        'assigned_by' => $superAdmin->id,
        'assigned_to' => $employee->id,
        'title' => 'Create login page',
        'status' => 'pending',
    ]);
});

test('super admin can assign permissions to an individual employee', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(UserRole::SuperAdmin->value);

    $employee = User::factory()->create();
    $employee->assignRole(UserRole::Employee->value);

    $this->actingAs($superAdmin)
        ->patch(route('super-admin.employees.permissions.update', $employee), [
            'permissions' => ['create task', 'start task'],
        ])
        ->assertRedirect(route('super-admin.employees.permissions.edit', $employee, absolute: false));

    expect($employee->fresh()->hasPermissionTo('create task'))->toBeTrue();
    expect($employee->fresh()->hasPermissionTo('start task'))->toBeTrue();
});

test('employee dashboard shows assigned permissions', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(UserRole::SuperAdmin->value);

    $employee = User::factory()->create();
    $employee->assignRole(UserRole::Employee->value);
    $employee->givePermissionTo('create task');

    Task::create([
        'assigned_by' => $employee->id,
        'assigned_to' => $employee->id,
        'title' => 'Pending permission task',
        'description' => 'This task is pending and should show Start.',
        'priority' => 'medium',
        'status' => TaskStatus::Pending->value,
        'due_date' => now()->addDay()->toDateString(),
    ]);

    $this->actingAs($employee)
        ->get(route('employee.dashboard'))
        ->assertSuccessful()
        ->assertSee('Create Task')
        ->assertSee('Start');
});

test('employee dashboard does not show create edit delete when permission is absent', function () {
    $employee = User::factory()->create();
    $employee->assignRole(UserRole::Employee->value);

    Task::create([
        'assigned_by' => $employee->id,
        'assigned_to' => $employee->id,
        'title' => 'No permission task',
        'description' => 'Task should only show allowed actions.',
        'priority' => 'low',
        'status' => TaskStatus::Pending->value,
        'due_date' => now()->addDay()->toDateString(),
    ]);

    $this->actingAs($employee)
        ->get(route('employee.dashboard'))
        ->assertSuccessful()
        ->assertDontSee('Create Task')
        ->assertDontSee('Edit')
        ->assertDontSee('Delete')
        ->assertSee('Start');
});

test('super admin can view task edit page', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(UserRole::SuperAdmin->value);

    $employee = User::factory()->create();
    $employee->assignRole(UserRole::Employee->value);

    $task = Task::create([
        'assigned_by' => $superAdmin->id,
        'assigned_to' => $employee->id,
        'title' => 'Original task title',
        'description' => 'Description before edit.',
        'priority' => 'low',
        'status' => TaskStatus::Pending->value,
        'due_date' => now()->addDay()->toDateString(),
    ]);

    $this->actingAs($superAdmin)
        ->get(route('tasks.edit', $task))
        ->assertSuccessful()
        ->assertSee('Edit Task')
        ->assertSee('Original task title');
});

test('super admin can update a task', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(UserRole::SuperAdmin->value);

    $employee = User::factory()->create();
    $employee->assignRole(UserRole::Employee->value);

    $task = Task::create([
        'assigned_by' => $superAdmin->id,
        'assigned_to' => $employee->id,
        'title' => 'Original title',
        'description' => 'Original description.',
        'priority' => 'low',
        'status' => TaskStatus::Pending->value,
        'due_date' => now()->addDay()->toDateString(),
    ]);

    $this->actingAs($superAdmin)
        ->put(route('tasks.update', $task), [
            'assigned_to' => $employee->id,
            'title' => 'Updated title',
            'description' => 'Updated description.',
            'priority' => 'high',
            'due_date' => now()->addWeek()->toDateString(),
        ])
        ->assertRedirect(route('super-admin.dashboard', absolute: false));

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'Updated title',
        'priority' => 'high',
    ]);
});

test('super admin can delete a task', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(UserRole::SuperAdmin->value);

    $employee = User::factory()->create();
    $employee->assignRole(UserRole::Employee->value);

    $task = Task::create([
        'assigned_by' => $superAdmin->id,
        'assigned_to' => $employee->id,
        'title' => 'Task to delete',
        'description' => 'This task will be deleted.',
        'priority' => 'medium',
        'status' => TaskStatus::Pending->value,
        'due_date' => now()->addDay()->toDateString(),
    ]);

    $this->actingAs($superAdmin)
        ->delete(route('tasks.destroy', $task))
        ->assertRedirect(route('super-admin.dashboard', absolute: false));

    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
    ]);
});

test('super admin can view one employee work page', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(UserRole::SuperAdmin->value);

    $employee = User::factory()->create(['name' => 'Employee Work User']);
    $employee->assignRole(UserRole::Employee->value);

    Task::create([
        'assigned_by' => $superAdmin->id,
        'assigned_to' => $employee->id,
        'title' => 'Employee visible work',
        'description' => 'Visible on employee work page.',
        'priority' => 'medium',
        'status' => TaskStatus::InProgress->value,
        'due_date' => now()->addDay()->toDateString(),
    ]);

    $this->actingAs($superAdmin)
        ->get(route('super-admin.employees.work', $employee))
        ->assertSuccessful()
        ->assertSee('Employee Work User')
        ->assertSee('Employee visible work')
        ->assertSee('In Progress');
});

test('employee can start their pending task', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(UserRole::SuperAdmin->value);

    $employee = User::factory()->create();
    $employee->assignRole(UserRole::Employee->value);

    $task = Task::create([
        'assigned_by' => $superAdmin->id,
        'assigned_to' => $employee->id,
        'title' => 'Startable task',
        'description' => 'This task can be started.',
        'priority' => 'high',
        'status' => TaskStatus::Pending->value,
        'due_date' => now()->addDay()->toDateString(),
    ]);

    $this->actingAs($employee)
        ->patch(route('employee.tasks.start', $task))
        ->assertRedirect(route('employee.dashboard', absolute: false));

    expect($task->refresh()->status)->toBe(TaskStatus::InProgress->value);
});

test('employee can complete their in progress task', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(UserRole::SuperAdmin->value);

    $employee = User::factory()->create();
    $employee->assignRole(UserRole::Employee->value);

    $task = Task::create([
        'assigned_by' => $superAdmin->id,
        'assigned_to' => $employee->id,
        'title' => 'Completable task',
        'description' => 'This task can be completed.',
        'priority' => 'high',
        'status' => TaskStatus::InProgress->value,
        'due_date' => now()->addDay()->toDateString(),
    ]);

    $this->actingAs($employee)
        ->patch(route('employee.tasks.complete', $task))
        ->assertRedirect(route('employee.dashboard', absolute: false));

    expect($task->refresh()->status)->toBe(TaskStatus::Completed->value);
});

test('employee cannot update another employees task', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(UserRole::SuperAdmin->value);

    $employee = User::factory()->create();
    $employee->assignRole(UserRole::Employee->value);

    $otherEmployee = User::factory()->create();
    $otherEmployee->assignRole(UserRole::Employee->value);

    $task = Task::create([
        'assigned_by' => $superAdmin->id,
        'assigned_to' => $otherEmployee->id,
        'title' => 'Other employee task',
        'description' => 'This task belongs to someone else.',
        'priority' => 'low',
        'status' => TaskStatus::Pending->value,
        'due_date' => now()->addDay()->toDateString(),
    ]);

    $this->actingAs($employee)
        ->patch(route('employee.tasks.start', $task))
        ->assertForbidden();

    expect($task->refresh()->status)->toBe(TaskStatus::Pending->value);
});

test('super admin can update employee permissions', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(UserRole::SuperAdmin->value);

    $this->actingAs($superAdmin)
        ->patch(route('super-admin.permissions.update'), [
            'permissions' => ['view dashboard', 'start task'],
        ])
        ->assertRedirect(route('super-admin.permissions.edit', absolute: false));

    $employeeRole = Role::findByName(UserRole::Employee->value, 'web');

    expect($employeeRole->hasPermissionTo('start task'))->toBeTrue();
    expect($employeeRole->hasPermissionTo('complete task'))->toBeFalse();
});

test('employee cannot start task when permission is removed', function () {
    Role::findByName(UserRole::Employee->value, 'web')
        ->syncPermissions(['view dashboard']);

    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(UserRole::SuperAdmin->value);

    $employee = User::factory()->create();
    $employee->assignRole(UserRole::Employee->value);

    $task = Task::create([
        'assigned_by' => $superAdmin->id,
        'assigned_to' => $employee->id,
        'title' => 'Blocked task',
        'description' => 'Employee has no start permission.',
        'priority' => 'low',
        'status' => TaskStatus::Pending->value,
        'due_date' => now()->addDay()->toDateString(),
    ]);

    $this->actingAs($employee)
        ->patch(route('employee.tasks.start', $task))
        ->assertForbidden();

    expect($task->refresh()->status)->toBe(TaskStatus::Pending->value);
});
