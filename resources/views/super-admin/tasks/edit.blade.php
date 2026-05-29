<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="bg-gray-50">
    @include('partials.toast')
    @include('super-admin.partials.sidebar')

    <div class="ml-64">
        <div class="sticky top-0 z-30 border-b border-gray-200 bg-white">
            <div class="flex items-center justify-between px-6 py-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Edit Task</h2>
                    <p class="mt-1 text-sm text-gray-600">Update task details and assignment.</p>
                </div>

                <a href="{{ route('super-admin.dashboard') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Back
                </a>
            </div>
        </div>

        <div class="p-6">
            <div class="max-w-3xl overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-blue-50 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Task Information</h3>
                </div>

                <form method="POST" action="{{ route('tasks.update', $task) }}" class="space-y-5 p-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="title" class="mb-2 block text-sm font-semibold text-gray-700">Task Title</label>
                        <input id="title" type="text" name="title" value="{{ old('title', $task->title) }}" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="description" class="mb-2 block text-sm font-semibold text-gray-700">Description</label>
                        <textarea id="description" name="description" rows="4" class="w-full resize-none rounded-lg border border-gray-300 px-4 py-2.5 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $task->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="assigned_to" class="mb-2 block text-sm font-semibold text-gray-700">Assign To</label>
                            <select id="assigned_to" name="assigned_to" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" @selected(old('assigned_to', $task->assigned_to) == $employee->id)>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="priority" class="mb-2 block text-sm font-semibold text-gray-700">Priority</label>
                            <select id="priority" name="priority" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="low" @selected(old('priority', $task->priority) === 'low')>Low</option>
                                <option value="medium" @selected(old('priority', $task->priority) === 'medium')>Medium</option>
                                <option value="high" @selected(old('priority', $task->priority) === 'high')>High</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="due_date" class="mb-2 block text-sm font-semibold text-gray-700">Due Date</label>
                        <input id="due_date" type="date" name="due_date" value="{{ old('due_date', $task->due_date->format('Y-m-d')) }}" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="flex gap-3 border-t border-gray-200 pt-4">
                        <a href="{{ route('super-admin.dashboard') }}" class="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-center font-medium text-gray-700 transition hover:bg-gray-50">
                            Cancel
                        </a>

                        <button type="submit" class="flex-1 rounded-lg bg-blue-600 px-4 py-2.5 font-medium text-white transition hover:bg-blue-700">
                            Update Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
