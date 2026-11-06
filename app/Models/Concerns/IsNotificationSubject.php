<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\NotificationLog;
use App\Models\UserNotification;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Deleting a subject clears its log and keeps its notifications, which no
 * longer link anywhere.
 */
trait IsNotificationSubject
{
    protected static function bootIsNotificationSubject(): void
    {
        static::deleting(function (self $subject): void {
            $subject->notificationLogs()->delete();
            $subject->userNotifications()->update(['subject_type' => null, 'subject_id' => null]);
        });
    }

    /**
     * @return MorphMany<NotificationLog, $this>
     */
    public function notificationLogs(): MorphMany
    {
        return $this->morphMany(NotificationLog::class, 'subject');
    }

    /**
     * @return MorphMany<UserNotification, $this>
     */
    public function userNotifications(): MorphMany
    {
        return $this->morphMany(UserNotification::class, 'subject');
    }
}
