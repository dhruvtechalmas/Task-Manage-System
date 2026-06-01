<?php

namespace App\Notifications;

use App\Enums\UserRole;
use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class TaskNotification extends Notification
{
    use Queueable;

    public int $taskId;

    public int $assignedTo;

    public string $taskTitle;

    public string $status;

    public string $priority;

    public function __construct(
        Task $task,
        public string $title,
        public string $message,
    ) {
        $this->taskId = $task->id;
        $this->assignedTo = $task->assigned_to;
        $this->taskTitle = $task->title;
        $this->status = $task->status;
        $this->priority = $task->priority;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return (new BroadcastMessage($this->toArray($notifiable)))
            ->onConnection('sync');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'id' => $this->id,
            'task_id' => $this->taskId,
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->notificationUrl($notifiable),
            'status' => $this->status,
            'priority' => $this->priority,
        ];
    }

    public function databaseType(object $notifiable): string
    {
        return 'task.notification';
    }

    public function broadcastType(): string
    {
        return 'task.notification';
    }

    private function notificationUrl(object $notifiable): string
    {
        if ($notifiable instanceof User && $notifiable->hasRole(UserRole::SuperAdmin->value)) {
            return route('super-admin.employees.work', $this->assignedTo);
        }

        return route('employee.dashboard');
    }
}
