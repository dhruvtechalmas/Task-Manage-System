<div class="grid grid-cols-1 gap-6 md:grid-cols-3">
    <div class="rounded-xl border border-blue-200 bg-blue-50 p-6">
        <p class="text-sm font-medium text-gray-600">Total Tasks</p>
        <h3 class="mt-2 text-3xl font-bold text-blue-900">{{ $totalTasks }}</h3>
    </div>

    <div class="rounded-xl border border-green-200 bg-green-50 p-6">
        <p class="text-sm font-medium text-gray-600">Completed</p>
        <h3 class="mt-2 text-3xl font-bold text-green-900">{{ $completedTasks }}</h3>
    </div>

    <div class="rounded-xl border border-orange-200 bg-orange-50 p-6">
        <p class="text-sm font-medium text-gray-600">In Progress</p>
        <h3 class="mt-2 text-3xl font-bold text-orange-900">{{ $inProgressTasks }}</h3>
    </div>
</div>
