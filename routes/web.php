<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'redirect'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/notifications/read', [NotificationController::class, 'markAllAsRead'])->name('notifications.read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    Route::middleware('role:SuperAdmin')->group(function () {
        Route::get('/super-admin/dashboard', [DashboardController::class, 'superAdmin'])->name('super-admin.dashboard');
        Route::get('/super-admin/employees/{employee}/work', [DashboardController::class, 'employeeWork'])->name('super-admin.employees.work');
        Route::get('/super-admin/permissions', [RolePermissionController::class, 'edit'])->name('super-admin.permissions.edit');
        Route::patch('/super-admin/permissions', [RolePermissionController::class, 'update'])->name('super-admin.permissions.update');
        Route::get('/super-admin/employees/{employee}/permissions', [RolePermissionController::class, 'editEmployeePermissions'])->name('super-admin.employees.permissions.edit');
        Route::patch('/super-admin/employees/{employee}/permissions', [RolePermissionController::class, 'updateEmployeePermissions'])->name('super-admin.employees.permissions.update');
    });

    Route::middleware('role:Employee')->group(function () {
        Route::get('/employee/dashboard', [DashboardController::class, 'employee'])->name('employee.dashboard');
        Route::get('/employee/tasks/create', [TaskController::class, 'create'])->middleware('can:create task')->name('tasks.create');
        Route::patch('/employee/tasks/{task}/start', [TaskController::class, 'start'])->name('employee.tasks.start');
        Route::patch('/employee/tasks/{task}/complete', [TaskController::class, 'complete'])->name('employee.tasks.complete');
    });

    Route::post('/tasks', [TaskController::class, 'store'])->middleware('can:create task')->name('tasks.store');
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->middleware('can:edit task')->name('tasks.edit');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->middleware('can:edit task')->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->middleware('can:delete task')->name('tasks.destroy');

});

require __DIR__.'/auth.php';
