<div id="createTaskModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div class="max-h-screen w-full max-w-lg overflow-y-auto rounded-xl bg-white shadow-2xl">
        <div class="sticky top-0 flex items-center justify-between border-b border-gray-200 bg-blue-50 px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900">Create New Task</h3>
            <button onclick="document.getElementById('createTaskModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('tasks.store') }}" class="space-y-5 p-6">
            @csrf

            <div>
                <label for="title" class="mb-2 block text-sm font-semibold text-gray-700">Task Title</label>
                <input id="title" name="title" type="text" value="{{ old('title') }}" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="mb-2 block text-sm font-semibold text-gray-700">Description</label>
                <textarea id="description" name="description" rows="4" class="w-full resize-none rounded-lg border border-gray-300 px-4 py-2.5 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="assigned_to" class="mb-2 block text-sm font-semibold text-gray-700">Assign To</label>
                    <select id="assigned_to" name="assigned_to" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Employee</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}" @selected(old('assigned_to') == $employee->id)>{{ $employee->name }}</option>
                        @endforeach
                    </select>
                    @error('assigned_to')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="priority" class="mb-2 block text-sm font-semibold text-gray-700">Priority</label>
                    <select id="priority" name="priority" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Priority</option>
                        <option value="low" @selected(old('priority') === 'low')>Low</option>
                        <option value="medium" @selected(old('priority') === 'medium')>Medium</option>
                        <option value="high" @selected(old('priority') === 'high')>High</option>
                    </select>
                    @error('priority')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="due_date" class="mb-2 block text-sm font-semibold text-gray-700">Due Date</label>
                <input id="due_date" name="due_date" type="date" value="{{ old('due_date') }}" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('due_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3 border-t border-gray-200 pt-4">
                <button type="button" onclick="document.getElementById('createTaskModal').classList.add('hidden')" class="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 font-medium text-gray-700 transition hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="flex-1 rounded-lg bg-blue-600 px-4 py-2.5 font-medium text-white transition hover:bg-blue-700">
                    Create Task
                </button>
            </div>
        </form>
    </div>
</div>
