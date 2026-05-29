<div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
    <div class="rounded-xl border border-blue-200 bg-blue-50 p-6">
        <p class="text-sm font-medium text-gray-600">Total Employees</p>
        <h3 class="mt-2 text-3xl font-bold text-blue-900">{{ $totalEmployees }}</h3>
    </div>

    <div class="rounded-xl border border-green-200 bg-green-50 p-6">
        <p class="text-sm font-medium text-gray-600">Completed Tasks</p>
        <h3 class="mt-2 text-3xl font-bold text-green-900">{{ $completedTasks }}</h3>
    </div>

    <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-6">
        <p class="text-sm font-medium text-gray-600">Pending Tasks</p>
        <h3 class="mt-2 text-3xl font-bold text-yellow-900">{{ $pendingTasks }}</h3>
    </div>

    <div class="rounded-xl border border-purple-200 bg-purple-50 p-6">
        <p class="text-sm font-medium text-gray-600">In Progress</p>
        <h3 class="mt-2 text-3xl font-bold text-purple-900">{{ $inProgressTasks }}</h3>
    </div>
</div>
