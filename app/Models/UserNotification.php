<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'link',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function markAsRead(): void
    {
        if ($this->read_at === null) {
            $this->update(['read_at' => now()]);
        }
    }

    public function getIconAttribute(): string
    {
        return match ($this->type) {
            'application_submitted' => 'fa-file-circle-plus',
            'application_status' => 'fa-clipboard-check',
            'interview_invited' => 'fa-calendar-check',
            'job_approved' => 'fa-circle-check',
            'job_rejected' => 'fa-circle-xmark',
            default => 'fa-bell',
        };
    }

    public static function notifyUser(?int $userId, string $type, string $title, string $message, ?string $link = null): ?self
    {
        if (! $userId) {
            return null;
        }

        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
        ]);
    }
}
