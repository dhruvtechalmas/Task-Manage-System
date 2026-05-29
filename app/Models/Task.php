<?php

namespace App\Models;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['assigned_by', 'assigned_to', 'title', 'description', 'priority', 'status', 'due_date'])]
class Task extends Model
{
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function belongsToUser(User $user): bool
    {
        return $this->assigned_to === $user->id;
    }

    public function start(): void
    {
        $this->update([
            'status' => TaskStatus::InProgress->value,
        ]);
    }

    public function complete(): void
    {
        $this->update([
            'status' => TaskStatus::Completed->value,
        ]);
    }

    public function isPending(): bool
    {
        return $this->status === TaskStatus::Pending->value;
    }

    public function isInProgress(): bool
    {
        return $this->status === TaskStatus::InProgress->value;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }
}
