@php
    $notifications = $dashboardNotifications->map(fn ($notification) => [
        'id' => $notification->id,
        'title' => data_get($notification->data, 'title', 'Notification'),
        'message' => data_get($notification->data, 'message', ''),
        'url' => data_get($notification->data, 'url', route('dashboard')),
        'read_at' => $notification->read_at?->toISOString(),
        'created_at' => $notification->created_at?->diffForHumans(),
    ])->values();
@endphp

<div
    x-data="notificationDropdown({
        userId: @js(Auth::id()),
        initialNotifications: @js($notifications),
        initialUnreadCount: @js($unreadNotificationsCount),
        markReadUrl: @js(route('notifications.read')),
        deleteUrlTemplate: @js(route('notifications.destroy', ['notification' => '__notification__'])),
    })"
    x-init="listen()"
    class="relative"
>
    <button
        type="button"
        x-on:click="open = ! open"
        class="relative inline-flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 transition hover:border-blue-200 hover:text-blue-700"
        aria-label="Notifications">

        <i class="fas fa-bell text-base"></i>
        <span
            x-cloak
            x-show="unreadCount > 0"
            x-text="unreadCount"
            class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-xs font-bold text-white"
        ></span>
    </button>

    <div
        x-cloak
        x-show="open"
        x-transition.origin.top.right
        x-on:click.outside="open = false"
        class="absolute right-0 z-50 mt-3 w-80 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-xl"
    >
        <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-4 py-3">
            <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
            <button
                type="button"
                x-on:click="markAllRead()"
                x-show="unreadCount > 0"
                class="text-xs font-semibold text-blue-700 hover:text-blue-900">
                Mark all read
            </button>
        </div>

        <div class="max-h-96 overflow-y-auto">
            <template x-if="notifications.length === 0">
                <div class="px-4 py-6 text-sm text-gray-500">No notifications yet.</div>
            </template>

            <template x-for="notification in notifications" :key="notification.id  ">
                <div
                     x-on:click="markAllRead()"
                    class="block border-b border-gray-100 px-4 py-3 transition hover:bg-gray-50"
                    :class="{ 'bg-blue-50/70': ! notification.read_at }">
                    
                    <div class="flex items-start justify-between gap-3">
                        <a :href="notification.url" class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <p class="text-sm font-semibold text-gray-900" x-text="notification.title"></p>
                                <span class="shrink-0 text-xs text-gray-500" x-text="notification.created_at"></span>
                            </div>
                            <p class="mt-1 text-sm leading-5 text-gray-600" x-text="notification.message"></p>
                        </a>

                        <button
                            type="button"
                           C="deleteNotification(notification)"
                            class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md text-gray-400 transition hover:bg-red-50 hover:text-red-600"
                            aria-label="Delete notification"
                            title="Delete notification"
                        >
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
