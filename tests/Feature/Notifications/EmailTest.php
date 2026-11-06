<?php

declare(strict_types=1);

use App\Enums\NotificationEvent;
use App\Jobs\SendProjectNotifications;
use App\Jobs\SendTaskNotifications;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Notifications\RecordNotification;
use App\Support\Notifier;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\Mime\Email;

function sentEmails(): array
{
    return app('mailer')->getSymfonyTransport()->messages()
        ->map(fn ($message): Email => $message->getOriginalMessage())
        ->all();
}

it('emails each notification once with a link to its record', function () {
    Notification::fake();
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create(['due_date' => today()->subDay()]);

    SendProjectNotifications::dispatchSync();
    SendProjectNotifications::dispatchSync();

    Notification::assertSentToTimes($user, RecordNotification::class, 1);
    Notification::assertSentTo($user, fn (RecordNotification $mail) => $mail->title === 'Project Overdue'
        && $mail->url === route('projects.show', $project));
});

it('links a task email to the parent project', function () {
    Notification::fake();
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create();
    Task::factory()->forProject($project)->create(['due_date' => today()->subDay()]);

    SendTaskNotifications::dispatchSync();

    Notification::assertSentTo($user, fn (RecordNotification $mail) => $mail->url === route('projects.show', $project));
});

it('queues the email', function () {
    expect(new RecordNotification('Title', 'Body', 'https://example.com'))->toBeInstanceOf(ShouldQueue::class);
});

it('respects the opt-out and still writes the in-app notification', function () {
    Notification::fake();
    $user = User::factory()->create(['email_notifications' => false]);
    Project::factory()->for($user)->create(['due_date' => today()->subDay()]);

    SendProjectNotifications::dispatchSync();

    Notification::assertNothingSent();
    expect($user->userNotifications()->count())->toBe(1);
});

it('never emails demo users, whatever the preference says', function () {
    Notification::fake();
    $user = User::factory()->demo()->create(['email_notifications' => true]);
    Project::factory()->for($user)->create(['due_date' => today()->subDay()]);

    SendProjectNotifications::dispatchSync();

    Notification::assertNothingSent();
    expect($user->userNotifications()->count())->toBe(1);
});

it('stops a waiting email when the user opts out before it is sent', function () {
    $user = User::factory()->create();
    $mail = new RecordNotification('Title', 'Body', 'https://example.com');

    expect($mail->shouldSend($user, 'mail'))->toBeTrue();

    $user->update(['email_notifications' => false]);

    expect($mail->shouldSend($user, 'mail'))->toBeFalse();
});

it('keeps the in-app notification when the email fails', function () {
    Notification::shouldReceive('send')->once()->andThrow(new RuntimeException('Mail server down'));
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create();

    $created = app(Notifier::class)->once($project, NotificationEvent::ProjectDue, $user, 'Title', 'Body');

    expect($created)->toBeTrue()
        ->and($user->userNotifications()->count())->toBe(1);
});

it('sends html and plain text with the record and settings links', function () {
    $user = User::factory()->create();
    $project = Project::factory()->for($user)->create(['name' => "Bob's <b>Launch</b>", 'due_date' => today()->subDay()]);

    SendProjectNotifications::dispatchSync();

    [$email] = sentEmails();

    expect(sentEmails())->toHaveCount(1)
        ->and($email->getSubject())->toBe('Project Overdue')
        ->and($email->getTo()[0]->getAddress())->toBe($user->email)
        ->and($email->getHtmlBody())
        ->toContain('&lt;b&gt;Launch&lt;/b&gt;')
        ->not->toContain('<b>Launch</b>')
        ->toContain(route('projects.show', $project))
        ->toContain(route('settings'))
        ->and($email->getTextBody())
        ->toContain("Bob's <b>Launch</b>")
        ->toContain(route('projects.show', $project))
        ->toContain(route('settings'));
});
