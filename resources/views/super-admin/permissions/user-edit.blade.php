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
                    <h2 class="text-2xl font-bold text-gray-900">Permissions for {{ $employee->name }}</h2>
                    <p class="mt-1 text-sm text-gray-600">Assign permissions directly to this employee.</p>
                </div>

                <a href="{{ route('super-admin.dashboard') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Back to dashboard
                </a>
            </div>
        </div>

        <div class="p-6">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="mb-4 rounded-lg bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
                    Direct permissions here are only for this employee. Permissions inherited from the Employee role are shown below and still grant access.
                </div>

                <form method="POST" action="{{ route('super-admin.employees.permissions.update', $employee) }}">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-3">
                        @foreach ($permissions as $permission)
                            <label class="flex items-center gap-3 rounded-lg border border-gray-200 px-4 py-3">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" @checked(in_array($permission->name, $directPermissions, true)) class="rounded border-gray-300">
                                <span class="text-sm font-medium text-gray-800">{{ ucwords(str_replace(['-', '_'], ' ', $permission->name)) }}</span>

                                @if (in_array($permission->name, $rolePermissions, true))
                                    <span class="ml-auto rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-800">
                                        Inherited
                                    </span>
                                @endif
                            </label>
                        @endforeach
                    </div>

                    <button type="submit" class="mt-6 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
                        Save Permissions
                    </button>
                </form>
            </div>

            <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900">Current Direct Permissions</h3>
                <p class="mt-1 text-sm text-gray-600">These permissions apply directly to {{ $employee->name }}.</p>

                <div class="mt-4 flex flex-wrap gap-2">
                    @forelse ($directPermissions as $permissionName)
                        <span class="rounded-full bg-indigo-100 px-3 py-1 text-sm font-medium text-indigo-700">
                            {{ ucwords(str_replace(['-', '_'], ' ', $permissionName)) }}
                        </span>
                    @empty
                        <p class="text-sm text-gray-500">No direct permissions assigned.</p>
                    @endforelse
                </div>
            </div>

            <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900">Inherited Role Permissions</h3>
                <p class="mt-1 text-sm text-gray-600">These permissions come from the Employee role.</p>

                <div class="mt-4 flex flex-wrap gap-2">
                    @forelse ($rolePermissions as $permissionName)
                        <span class="rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-700">
                            {{ ucwords(str_replace(['-', '_'], ' ', $permissionName)) }}
                        </span>
                    @empty
                        <p class="text-sm text-gray-500">No inherited role permissions.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</body>

</html>
