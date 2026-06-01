import Alpine from 'alpinejs';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Alpine = Alpine;
window.Pusher = Pusher;

if (import.meta.env.VITE_PUSHER_APP_KEY) {
    const pusherHost = import.meta.env.VITE_PUSHER_HOST || null;
    const pusherPort = Number(import.meta.env.VITE_PUSHER_PORT ?? 443);
    const pusherScheme = import.meta.env.VITE_PUSHER_SCHEME ?? 'https';

    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
        ...(pusherHost ? { wsHost: pusherHost } : {}),
        wsPort: pusherPort,
        wssPort: pusherPort,
        forceTLS: pusherScheme === 'https',
        encrypted: pusherScheme === 'https',
        enabledTransports: ['ws', 'wss'],
    });
}

window.notificationDropdown = ({ userId, initialNotifications, initialUnreadCount, markReadUrl, deleteUrlTemplate }) => ({
    open: false,
    notifications: initialNotifications,
    unreadCount: initialUnreadCount,
    markReadUrl,
    deleteUrlTemplate,

    listen() {
        if (! window.Echo || ! userId) {
            return;
        }

        window.Echo.private(`App.Models.User.${userId}`)
            .notification((notification) => {
                this.notifications = [
                    {
                        id: notification.id ?? `${Date.now()}`,
                        title: notification.title ?? 'Notification',
                        message: notification.message ?? '',
                        url: notification.url ?? window.location.href,
                        read_at: null,
                        created_at: 'Just now',
                    },
                    ...this.notifications,
                ].slice(0, 8);

                this.unreadCount += 1;
            });
    },

    markAllRead() {
        fetch(this.markReadUrl, {
            method: 'PATCH',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        }).then((response) => {
            if (! response.ok) {
                return;
            }

            this.unreadCount = 0;
            this.notifications = this.notifications.map((notification) => ({
                ...notification,
                read_at: notification.read_at ?? new Date().toISOString(),
            }));
        });
    },

    deleteNotification(notification) {
        fetch(this.deleteUrlTemplate.replace('__notification__', notification.id), {
            method: 'DELETE',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        }).then((response) => {
            if (! response.ok) {
                return;
            }

            this.notifications = this.notifications.filter((item) => item.id !== notification.id);

            if (! notification.read_at) {
                this.unreadCount = Math.max(this.unreadCount - 1, 0);
            }
        });
    },
});

Alpine.start();
