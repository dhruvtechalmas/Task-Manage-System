<div id="tasks" class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-6 py-4">
        <h3 class="text-lg font-semibold text-gray-900">All Tasks</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="border-b border-gray-200 bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Task</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Assigned To</th>
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
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $task->assignedTo?->name ?? 'Unassigned' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ ucwords(str_replace('_', ' ', $task->priority)) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ ucwords(str_replace('_', ' ', $task->status)) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $task->due_date->format('d-m-Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('tasks.edit', $task) }}" class="rounded-lg bg-yellow-500 px-3 py-1.5 text-sm font-medium text-white hover:bg-yellow-600">
                                    Edit
                                </a>

                                <form action="{{ route('tasks.destroy', $task) }}" method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700" onclick="return confirm('Delete this task?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">No tasks created yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
