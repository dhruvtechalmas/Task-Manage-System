<?php

use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskNotification;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->withoutMiddleware(PreventRequestForgery::class);

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

test('creating a task stores notifications for the employee and super admin', function () {
    $superAdmin = User::factory()->create(['name' => 'Super Admin']);
    $superAdmin->assignRole(UserRole::SuperAdmin->value);

    $employee = User::factory()->create(['name' => 'Employee One']);
    $employee->assignRole(UserRole::Employee->value);

    $this->actingAs($superAdmin)
        ->post(route('tasks.store'), [
            'assigned_to' => $employee->id,
            'title' => 'Create notification dropdown',
            'description' => 'Build realtime notifications.',
            'priority' => 'high',
            'due_date' => now()->addWeek()->toDateString(),
        ])
        ->assertRedirect(route('super-admin.dashboard', absolute: false));

    $employeeNotification = $employee->fresh()->unreadNotifications->first();
    $superAdminNotification = $superAdmin->fresh()->unreadNotifications->first();

    expect($employee->fresh()->unreadNotifications)->toHaveCount(1);
    expect($employeeNotification->data['title'])->toBe('New task assigned');

    expect($superAdmin->fresh()->unreadNotifications)->toHaveCount(1);
    expect($superAdminNotification->data['title'])->toBe('Task created');
});

test('employee status changes notify super admins', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(UserRole::SuperAdmin->value);

    $employee = User::factory()->create(['name' => 'Employee One']);
    $employee->assignRole(UserRole::Employee->value);

    $task = Task::create([
        'assigned_by' => $superAdmin->id,
        'assigned_to' => $employee->id,
        'title' => 'Realtime task',
        'description' => 'Startable task.',
        'priority' => 'medium',
        'status' => TaskStatus::Pending->value,
        'due_date' => now()->addDay()->toDateString(),
    ]);

    $this->actingAs($employee)
        ->patch(route('employee.tasks.start', $task))
        ->assertRedirect(route('employee.dashboard', absolute: false));

    $superAdminNotification = $superAdmin->fresh()->unreadNotifications->first();

    expect($superAdmin->fresh()->unreadNotifications)->toHaveCount(1);
    expect($superAdminNotification->data['title'])->toBe('Task started');
});

test('dashboard renders notification center and existing notifications', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(UserRole::SuperAdmin->value);

    $employee = User::factory()->create();
    $employee->assignRole(UserRole::Employee->value);

    $task = Task::create([
        'assigned_by' => $superAdmin->id,
        'assigned_to' => $employee->id,
        'title' => 'Visible notification task',
        'description' => 'Shown in dashboard notifications.',
        'priority' => 'low',
        'status' => TaskStatus::Pending->value,
        'due_date' => now()->addDay()->toDateString(),
    ]);

    $employee->notify(new TaskNotification(
        task: $task,
        title: 'Dashboard notification',
        message: 'This notification is visible.',
    ));

    $this->actingAs($employee)
        ->get(route('employee.dashboard'))
        ->assertSuccessful()
        ->assertSee('Notifications')
        ->assertSee('Dashboard notification');
});

test('users can mark dashboard notifications as read', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(UserRole::SuperAdmin->value);

    $employee = User::factory()->create();
    $employee->assignRole(UserRole::Employee->value);

    $task = Task::create([
        'assigned_by' => $superAdmin->id,
        'assigned_to' => $employee->id,
        'title' => 'Readable notification task',
        'description' => 'Notification can be marked read.',
        'priority' => 'low',
        'status' => TaskStatus::Pending->value,
        'due_date' => now()->addDay()->toDateString(),
    ]);

    $employee->notify(new TaskNotification(
        task: $task,
        title: 'Read me',
        message: 'Mark this as read.',
    ));

    $this->actingAs($employee)
        ->get(route('employee.dashboard'))
        ->patchJson(route('notifications.read'))
        ->assertNoContent();

    expect($employee->fresh()->unreadNotifications)->toHaveCount(0);
});

test('users can delete their dashboard notifications', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(UserRole::SuperAdmin->value);

    $employee = User::factory()->create();
    $employee->assignRole(UserRole::Employee->value);

    $task = Task::create([
        'assigned_by' => $superAdmin->id,
        'assigned_to' => $employee->id,
        'title' => 'Deletable notification task',
        'description' => 'Notification can be deleted.',
        'priority' => 'low',
        'status' => TaskStatus::Pending->value,
        'due_date' => now()->addDay()->toDateString(),
    ]);

    $employee->notify(new TaskNotification(
        task: $task,
        title: 'Delete me',
        message: 'Remove this notification.',
    ));

    $notification = $employee->fresh()->notifications->first();

    $this->actingAs($employee)
        ->deleteJson(route('notifications.destroy', $notification))
        ->assertNoContent();

    expect($employee->fresh()->notifications)->toHaveCount(0);
});

test('users cannot delete another users notification', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(UserRole::SuperAdmin->value);

    $employee = User::factory()->create();
    $employee->assignRole(UserRole::Employee->value);

    $otherEmployee = User::factory()->create();
    $otherEmployee->assignRole(UserRole::Employee->value);

    $task = Task::create([
        'assigned_by' => $superAdmin->id,
        'assigned_to' => $employee->id,
        'title' => 'Private notification task',
        'description' => 'Only owner can delete.',
        'priority' => 'low',
        'status' => TaskStatus::Pending->value,
        'due_date' => now()->addDay()->toDateString(),
    ]);

    $employee->notify(new TaskNotification(
        task: $task,
        title: 'Private notification',
        message: 'Do not remove this notification.',
    ));

    $notification = $employee->fresh()->notifications->first();

    $this->actingAs($otherEmployee)
        ->deleteJson(route('notifications.destroy', $notification))
        ->assertNotFound();

    expect($employee->fresh()->notifications)->toHaveCount(1);
});

test('task notification broadcasts immediately', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(UserRole::SuperAdmin->value);

    $employee = User::factory()->create();
    $employee->assignRole(UserRole::Employee->value);

    $task = Task::create([
        'assigned_by' => $superAdmin->id,
        'assigned_to' => $employee->id,
        'title' => 'Immediate notification task',
        'description' => 'Broadcast without waiting for the queue worker.',
        'priority' => 'high',
        'status' => TaskStatus::Pending->value,
        'due_date' => now()->addDay()->toDateString(),
    ]);

    $message = (new TaskNotification(
        task: $task,
        title: 'Live notification',
        message: 'This should broadcast immediately.',
    ))->toBroadcast($employee);

    expect($message->connection)->toBe('sync')
        ->and($message->data['title'])->toBe('Live notification');
});
