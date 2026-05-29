<div id="employees" class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-6 py-4">
        <h3 class="text-lg font-semibold text-gray-900">Employees</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="border-b border-gray-200 bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Assigned Tasks</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Work</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($employees as $employee)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $employee->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $employee->email }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $employee->assigned_tasks_count }}</td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('super-admin.employees.work', $employee) }}" class="mr-3 text-sm font-medium text-blue-700 hover:text-blue-900">
                                View Work
                            </a>
                            <a href="{{ route('super-admin.employees.permissions.edit', $employee) }}" class="text-sm font-medium text-indigo-700 hover:text-indigo-900">
                                Permissions
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">No employees found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
