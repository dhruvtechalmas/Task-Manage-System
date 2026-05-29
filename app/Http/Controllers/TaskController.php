<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function create(): View
    {
        return view('employee.tasks.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->can('create task'), 403);

        if ($request->user()->hasRole(UserRole::Employee->value)) {
            $request->merge(['assigned_to' => $request->user()->id]);
        }

        $request->validate([
            'title' => ['required', 'string', 'max:191'],
            'description' => ['required', 'string'],
            'assigned_to' => ['required', Rule::exists(User::class, 'id')],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'due_date' => ['required', 'date'],
        ]);

        Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
            'priority' => $request->priority,
            'due_date' => $request->due_date,
            'assigned_by' => $request->user()->id,
            'status' => TaskStatus::Pending->value,
        ]);

        $redirectRoute = $request->user()->hasRole(UserRole::Employee->value)
            ? 'employee.dashboard'
            : 'super-admin.dashboard';

        return redirect()
            ->route($redirectRoute)
            ->with('status', 'Task created successfully.');
    }

    public function start(Request $request, Task $task): RedirectResponse
    {
        abort_unless($request->user()->can('start task'), 403);
        abort_unless($task->belongsToUser($request->user()), 403);
        abort_unless($task->isPending(), 403);

        $task->start();

        return redirect()
            ->route('employee.dashboard')
            ->with('status', 'Task started.');
    }

    public function complete(Request $request, Task $task): RedirectResponse
    {
        abort_unless($request->user()->can('complete task'), 403);
        abort_unless($task->belongsToUser($request->user()), 403);
        abort_unless($task->isInProgress(), 403);

        $task->complete();

        return redirect()
            ->route('employee.dashboard')
            ->with('status', 'Task completed.');
    }

    public function edit(Request $request, Task $task): View
    {
        abort_unless($request->user()->can('edit task'), 403);

        if ($request->user()->hasRole(UserRole::Employee->value)) {
            abort_unless($task->belongsToUser($request->user()), 403);

            return view('employee.tasks.edit', [
                'task' => $task,
            ]);
        }

        $employees = User::role(UserRole::Employee->value)->get();

        return view('super-admin.tasks.edit', [
            'task' => $task,
            'employees' => $employees,
        ]);
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        abort_unless($request->user()->can('edit task'), 403);

        if ($request->user()->hasRole(UserRole::Employee->value)) {
            abort_unless($task->belongsToUser($request->user()), 403);
            $request->merge(['assigned_to' => $task->assigned_to]);
        }

        $request->validate([
            'title' => ['required', 'string', 'max:191'],
            'description' => ['required', 'string'],
            'assigned_to' => ['required', Rule::exists(User::class, 'id')],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'due_date' => ['required', 'date'],
        ]);

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
            'priority' => $request->priority,
            'due_date' => $request->due_date,
        ]);

        $redirectRoute = $request->user()->hasRole(UserRole::Employee->value)
            ? 'employee.dashboard'
            : 'super-admin.dashboard';

        return redirect()
            ->route($redirectRoute)
            ->with('status', 'Task updated successfully.');
    }

    public function destroy(Request $request, Task $task): RedirectResponse
    {
        abort_unless($request->user()->can('delete task'), 403);

        if ($request->user()->hasRole(UserRole::Employee->value)) {
            abort_unless($task->belongsToUser($request->user()), 403);
        }

        $task->delete();

        $redirectRoute = $request->user()->hasRole(UserRole::Employee->value)
            ? 'employee.dashboard'
            : 'super-admin.dashboard';

        return redirect()
            ->route($redirectRoute)
            ->with('status', 'Task deleted successfully.');
    }
}
