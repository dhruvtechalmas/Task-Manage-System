<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Permissions</title>
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
                    <h2 class="text-2xl font-bold text-gray-900">Employee Permissions</h2>
                    <p class="mt-1 text-sm text-gray-600">Select what Employee role can do.</p>
                </div>

                <a href="{{ route('super-admin.dashboard') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Back
                </a>
            </div>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('super-admin.permissions.update') }}" class="max-w-2xl rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                @csrf
                @method('PATCH')

                <div class="space-y-3">
                    @foreach ($permissions as $permission)
                        <label class="flex items-center gap-3 rounded-lg border border-gray-200 px-4 py-3">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" @checked(in_array($permission->name, $employeePermissions, true)) class="rounded border-gray-300">
                            <span class="text-sm font-medium text-gray-800">{{ ucwords($permission->name) }}</span>
                        </label>
                    @endforeach
                </div>

                <button type="submit" class="mt-6 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
                    Save Permissions
                </button>
            </form>
        </div>
    </div>
</body>

</html>
