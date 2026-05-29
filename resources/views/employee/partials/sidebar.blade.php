<div class="fixed left-0 top-0 z-40 h-screen w-64 border-r border-gray-200 bg-gradient-to-b from-indigo-50 to-blue-50 pt-6">
    <div class="mb-8 px-6">
        <h3 class="text-lg font-bold text-gray-900">Employee</h3>
    </div>

    <nav class="space-y-2 px-4">
        <a href="{{ route('employee.dashboard') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 text-gray-700 transition hover:bg-indigo-100">
            <i class="fas fa-home w-5"></i> <span>Dashboard</span>
        </a>
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 text-gray-700 transition hover:bg-indigo-100">
            <i class="fas fa-user w-5"></i> <span>Profile</span>
        </a>
        <a href="#my-tasks" class="flex items-center gap-3 rounded-lg px-4 py-3 text-gray-700 transition hover:bg-indigo-100">
            <i class="fas fa-tasks w-5"></i> <span>My Tasks</span>
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
