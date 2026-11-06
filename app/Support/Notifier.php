<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\NotificationEvent;
use App\Models\Issue;
use App\Models\NotificationLog;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Notifications\RecordNotification;
use Illuminate\Support\Facades\DB;

final class Notifier
{
    /**
     * Writes a notification the first time an event happens to a record. The
     * unique index on the log decides which run wins, so this is safe to repeat.
     *
     * The email goes out after the commit and cannot undo it, so a mail failure
     * never loses the in-app notification.
     */
    public function once(Project|Issue|Task $subject, NotificationEvent $event, User $user, string $title, string $message): bool
    {
        $created = DB::transaction(function () use ($subject, $event, $user, $title, $message): bool {
            $logged = NotificationLog::query()->insertOrIgnore([
                'user_id' => $user->id,
                'subject_type' => $subject->getMorphClass(),
                'subject_id' => $subject->getKey(),
                'event' => $event->value,
                'created_at' => now(),
            ]);

            if ($logged === 0) {
                return false;
            }

            $user->userNotifications()
                ->make(['title' => $title, 'message' => $message])
                ->subject()->associate($subject)
                ->save();

            return true;
        });

        if ($created && $user->wantsEmailNotifications()) {
            rescue(fn () => $user->notify(new RecordNotification($title, $message, $subject->url())));
        }

        return $created;
    }
}
