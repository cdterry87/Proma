<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * The email that accompanies an in-app notification.
 */
class RecordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public readonly string $title,
        public readonly string $body,
        public readonly string $url,
    ) {}

    /**
     * @return list<string>
     */
    public function via(User $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Checked when the queued email is sent, so turning emails off also stops
     * any that are still waiting.
     */
    public function shouldSend(User $notifiable, string $channel): bool
    {
        return $notifiable->wantsEmailNotifications();
    }

    public function toMail(User $notifiable): MailMessage
    {
        $settingsUrl = route('settings');

        return (new MailMessage)
            ->subject($this->title)
            ->view(
                [
                    'html' => 'mail.notification',
                    'raw' => implode("\n\n", [
                        $this->title,
                        $this->body,
                        "View it: {$this->url}",
                        "Turn off these emails in your settings: {$settingsUrl}",
                    ]),
                ],
                // The mailer reserves the name "message" for its own variable.
                [
                    'title' => $this->title,
                    'body' => $this->body,
                    'url' => $this->url,
                    'settingsUrl' => $settingsUrl,
                ],
            );
    }
}
