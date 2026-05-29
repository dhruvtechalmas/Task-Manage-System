<div id="permissions" class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-200 px-6 py-4">
        <h3 class="text-lg font-semibold text-gray-900">Your Permissions</h3>
    </div>

    <div class="p-6">
        @php
            $permissions = Auth::user()->getPermissionNames();
        @endphp

        @if ($permissions->isEmpty())
            <p class="text-sm text-gray-500">You do not currently have any extra task permissions. Ask your Super Admin to grant you access.</p>
        @else
            <div class="flex flex-wrap gap-2">
                @foreach ($permissions as $permission)
                    <span class="rounded-full bg-indigo-100 px-3 py-1 text-sm font-medium text-indigo-700">
                        {{ ucwords(str_replace(['-', '_'], ' ', $permission)) }}
                    </span>
                @endforeach
            </div>

            @unless (Auth::user()->can('create task'))
                <p class="mt-4 text-sm text-gray-500">The Create Task button will appear once you have been granted the permission.</p>
            @endunless
        @endif
    </div>
</div>
