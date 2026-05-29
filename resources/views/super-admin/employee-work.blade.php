<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $employee->name }} Work</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="bg-gray-50">
    @include('partials.toast')
    <div class="fixed left-0 top-0 z-40 h-screen w-64 border-r border-gray-200 bg-gradient-to-b from-blue-50 to-indigo-50 pt-6">
        <div class="mb-8 px-6">
            <h3 class="text-lg font-bold text-gray-900">Super Admin</h3>
        </div>

        <nav class="space-y-2 px-4">
            <a href="{{ route('super-admin.dashboard') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 text-gray-700 transition hover:bg-blue-100">
                <i class="fas fa-home w-5"></i> <span>Dashboard</span>
            </a>
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 text-gray-700 transition hover:bg-blue-100">
                <i class="fas fa-user w-5"></i> <span>Profile</span>
            </a>
        </nav>

        <div class="absolute bottom-6 w-full px-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-left text-gray-700 transition hover:bg-red-100">
                    <i class="fas fa-sign-out-alt w-5"></i> <span>Logout</span>
                </button>
            </form>
        </div>
    </div>

    <div class="ml-64">
        <div class="sticky top-0 z-30 border-b border-gray-200 bg-white">
            <div class="flex items-center justify-between px-6 py-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $employee->name }} Work</h2>
                    <p class="mt-1 text-sm text-gray-600">{{ $employee->email }}</p>
                </div>

                <a href="{{ route('super-admin.dashboard') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Back to Dashboard
                </a>
            </div>
        </div>

        <div class="space-y-8 p-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
                <div class="rounded-xl border border-blue-200 bg-blue-50 p-6">
                    <p class="text-sm font-medium text-gray-600">Total Tasks</p>
                    <h3 class="mt-2 text-3xl font-bold text-blue-900">{{ $totalTasks }}</h3>
                </div>

                <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-6">
                    <p class="text-sm font-medium text-gray-600">Pending</p>
                    <h3 class="mt-2 text-3xl font-bold text-yellow-900">{{ $pendingTasks }}</h3>
                </div>

                <div class="rounded-xl border border-orange-200 bg-orange-50 p-6">
                    <p class="text-sm font-medium text-gray-600">In Progress</p>
                    <h3 class="mt-2 text-3xl font-bold text-orange-900">{{ $inProgressTasks }}</h3>
                </div>

                <div class="rounded-xl border border-green-200 bg-green-50 p-6">
                    <p class="text-sm font-medium text-gray-600">Completed</p>
                    <h3 class="mt-2 text-3xl font-bold text-green-900">{{ $completedTasks }}</h3>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Assigned Tasks</h3>
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
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($tasks as $task)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $task->id }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $task->title }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ ucwords(str_replace('_', ' ', $task->priority)) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ ucwords(str_replace('_', ' ', $task->status)) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $task->due_date->format('d-m-Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">No tasks assigned to this employee.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
