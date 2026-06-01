<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Employee Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="bg-gray-50">
    @include('partials.toast')
    @include('employee.partials.sidebar')

    <div class="ml-64">
        <div class="sticky top-0 z-30 border-b border-gray-200 bg-white">
            <div class="flex items-center justify-between px-6 py-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Employee Dashboard</h2>
                    <p class="mt-1 text-sm text-gray-600">Welcome back! Here are your assigned tasks.</p>
                </div>

                <div class="flex items-center gap-4">
                    @include('partials.notifications')

                    @can('create task')
                        <a href="{{ route('tasks.create') }}" class="flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2 text-sm font-medium text-white hover:bg-blue-700">
                            <i class="fas fa-plus"></i>
                            Create Task
                        </a>
                    @endcan

                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-indigo-700">
                        <i class="fas fa-user-circle text-lg"></i>
                        <span>{{ Auth::user()->name }}</span>
                    </a>
                </div>
        </div>

        <div class="space-y-8 p-6">
            @include('employee.partials.stats')
            @include('employee.partials.permissions')
            @include('employee.partials.tasks-table')
        </div>
    </div>
</body>

</html>
