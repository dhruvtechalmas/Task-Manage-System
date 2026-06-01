<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class DashboardController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        if ($request->user()->hasRole(UserRole::SuperAdmin->value)) {
            return redirect()->route('super-admin.dashboard');
        }

        return redirect()->route('employee.dashboard');
    }

    public function superAdmin(): View
    {
        $employees = User::role(UserRole::Employee->value)
            ->withCount('assignedTasks')
            ->latest()
            ->get();

        $tasks = Task::query()
            ->with('assignedTo')
            ->latest()
            ->get();

        return view('super-admin.dashboard', [
            'employees' => $employees,
            'tasks' => $tasks,
            'totalEmployees' => $employees->count(),
            'completedTasks' => $tasks->where('status', 'completed')->count(),
            'pendingTasks' => $tasks->where('status', 'pending')->count(),
            'inProgressTasks' => $tasks->where('status', 'in_progress')->count(),
            ...$this->notificationData(request()->user()),
        ]);
    }

    public function employeeWork(User $employee): View
    {
        abort_unless($employee->hasRole(UserRole::Employee->value), 404);

        $tasks = Task::query()
            ->where('assigned_to', $employee->id)
            ->latest()
            ->get();

        return view('super-admin.employee-work', [
            'employee' => $employee,
            'tasks' => $tasks,
            'totalTasks' => $tasks->count(),
            'completedTasks' => $tasks->where('status', 'completed')->count(),
            'pendingTasks' => $tasks->where('status', 'pending')->count(),
            'inProgressTasks' => $tasks->where('status', 'in_progress')->count(),
        ]);
    }

    public function employee(Request $request): View
    {
        $tasks = Task::query()
            ->where('assigned_to', $request->user()->id)
            ->latest()
            ->get();

        return view('employee.dashboard', [
            'tasks' => $tasks,
            'totalTasks' => $tasks->count(),
            'completedTasks' => $tasks->where('status', 'completed')->count(),
            'inProgressTasks' => $tasks->where('status', 'in_progress')->count(),
            ...$this->notificationData($request->user()),
        ]);
    }

    /**
     * @return array{dashboardNotifications: Collection<int, DatabaseNotification>, unreadNotificationsCount: int}
     */
    private function notificationData(User $user): array
    {
        return [
            'dashboardNotifications' => $user->notifications()
                ->latest()
                ->limit(8)
                ->get(),
            'unreadNotificationsCount' => $user->unreadNotifications()->count(),
        ];
    }
}
