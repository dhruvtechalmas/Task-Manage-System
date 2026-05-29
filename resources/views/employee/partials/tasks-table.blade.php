<div id="my-tasks" class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-6 py-4">
        <h3 class="text-lg font-semibold text-gray-900">My Tasks</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="border-b border-gray-200 bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Task</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Priority</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Due Date</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($tasks as $task)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $task->id }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $task->title }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ ucwords(str_replace('_', ' ', $task->priority)) }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ ucwords(str_replace('_', ' ', $task->status)) }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $task->due_date->format('d-m-Y') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex justify-center gap-2">
                            @php
                                $hasAction = false;
                            @endphp

                            @can('start task')
                                @if ($task->isPending())
                                    @php $hasAction = true; @endphp
                                    <form method="POST" action="{{ route('employee.tasks.start', $task) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-blue-700">
                                            Start
                                        </button>
                                    </form>
                                @endif
                            @endcan

                            @can('complete task')
                                @if ($task->isInProgress())
                                    @php $hasAction = true; @endphp
                                    <form method="POST" action="{{ route('employee.tasks.complete', $task) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700">
                                            Complete
                                        </button>
                                    </form>
                                @endif
                            @endcan

                            @if ($task->belongsToUser(Auth::user()))
                                @can('edit task')
                                    @php $hasAction = true; @endphp
                                    <a href="{{ route('tasks.edit', $task) }}"
                                        class="rounded-lg bg-yellow-500 px-3 py-1.5 text-sm font-medium text-white hover:bg-yellow-600">
                                        Edit
                                    </a>
                                @endcan

                                @can('delete task')
                                    @php $hasAction = true; @endphp
                                    <form action="{{ route('tasks.destroy', $task) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700"
                                            onclick="return confirm('Delete this task?')">
                                            Delete
                                        </button>
                                    </form>
                                @endcan
                            @endif

                            @if (! $hasAction)
                                @if ($task->status === 'completed')
                                    <span class="text-sm font-medium text-green-700">Done</span>
                                @else
                                    <span class="text-sm text-gray-500">No Action</span>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">No tasks assigned yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>