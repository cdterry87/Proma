<?php

declare(strict_types=1);

use App\Livewire\NotificationBadge;
use App\Livewire\Notifications\Index;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\UserNotification;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('requires authentication', function () {
    auth()->logout();

    $this->get('/notifications')->assertRedirect('/login');
});

it('lists only the user\'s notifications, newest first', function () {
    UserNotification::factory()->for($this->user)->create(['title' => 'Older', 'created_at' => now()->subDay()]);
    UserNotification::factory()->for($this->user)->create(['title' => 'Newer']);
    UserNotification::factory()->create(['title' => 'Theirs']);

    $this->get('/notifications')->assertOk()->assertSeeLivewire(Index::class)->assertDontSee('Theirs');

    expect(Livewire::test(Index::class)->viewData('rows')->pluck('title')->all())->toBe(['Newer', 'Older']);
});

it('searches subjects and messages', function () {
    UserNotification::factory()->for($this->user)->create(['title' => 'Apollo', 'message' => 'One']);
    UserNotification::factory()->for($this->user)->create(['title' => 'Other', 'message' => 'About apollo']);
    UserNotification::factory()->for($this->user)->create(['title' => 'Neither', 'message' => 'Two']);

    expect(Livewire::test(Index::class)->set('search', 'apollo')->viewData('rows')->total())->toBe(2);
});

it('filters by read state without widening the owner scope', function () {
    UserNotification::factory()->for($this->user)->create(['title' => 'Unread']);
    UserNotification::factory()->for($this->user)->read()->create(['title' => 'Read']);
    UserNotification::factory()->create(['title' => 'Theirs']);
    UserNotification::factory()->read()->create(['title' => 'Theirs read']);

    expect(Livewire::test(Index::class)->set('filters.read', 'no')->viewData('rows')->pluck('title')->all())->toBe(['Unread'])
        ->and(Livewire::test(Index::class)->set('filters.read', 'yes')->viewData('rows')->pluck('title')->all())->toBe(['Read']);
});

it('sorts by the allowed columns', function () {
    UserNotification::factory()->for($this->user)->create(['title' => 'B']);
    UserNotification::factory()->for($this->user)->create(['title' => 'A']);

    expect(Livewire::test(Index::class)->call('sortBy', 'title')->viewData('rows')->pluck('title')->all())->toBe(['A', 'B']);
});

it('exports the user\'s notifications', function () {
    UserNotification::factory()->for($this->user)->create(['title' => 'Mine']);

    Livewire::test(Index::class)->call('export', 'csv')->assertFileDownloaded();
});

it('marks a notification as read when it is expanded', function () {
    $notification = UserNotification::factory()->for($this->user)->create();

    Livewire::test(Index::class)
        ->assertSeeHtml('wire:click="markRead('.$notification->id.')"')
        ->call('markRead', $notification->id)
        ->assertDispatched('notifications-read')
        ->assertDontSeeHtml('wire:click="markRead('.$notification->id.')"');

    expect($notification->fresh()->isRead())->toBeTrue();
});

it('keeps the first read time when a read notification is expanded again', function () {
    $this->freezeSecond();
    $notification = UserNotification::factory()->for($this->user)->create(['read_at' => now()->subDay()]);

    Livewire::test(Index::class)
        ->call('markRead', $notification->id)
        ->assertNotDispatched('notifications-read');

    expect($notification->fresh()->read_at->toDateTimeString())->toBe(now()->subDay()->toDateTimeString());
});

it('keeps a notification read on this page in the unread filter', function () {
    $notification = UserNotification::factory()->for($this->user)->create();
    $other = UserNotification::factory()->for($this->user)->read()->create();

    $ids = Livewire::test(Index::class)
        ->set('filters.read', 'no')
        ->call('markRead', $notification->id)
        ->viewData('rows')->pluck('id')->all();

    expect($ids)->toBe([$notification->id])->not->toContain($other->id);
});

it('cannot mark another user\'s notification as read', function () {
    $notification = UserNotification::factory()->create();

    Livewire::test(Index::class)->call('markRead', $notification->id)->assertForbidden();

    expect($notification->fresh()->isRead())->toBeFalse();
});

it('marks all of the user\'s notifications as read and nobody else\'s', function () {
    $mine = UserNotification::factory()->for($this->user)->count(2)->create();
    $theirs = UserNotification::factory()->create();

    Livewire::test(Index::class)
        ->call('markAllRead')
        ->assertDispatched('notifications-read')
        ->assertDispatched('toast');

    expect($mine->every(fn (UserNotification $notification) => $notification->fresh()->isRead()))->toBeTrue()
        ->and($theirs->fresh()->isRead())->toBeFalse();
});

it('links to the record, through the parent for a task, and not once it is gone', function () {
    $project = Project::factory()->for($this->user)->create();
    $task = Task::factory()->forProject($project)->create();

    $linked = UserNotification::factory()->for($this->user)->make(['title' => 'Linked']);
    $linked->subject()->associate($task)->save();

    UserNotification::factory()->for($this->user)->create(['title' => 'Unlinked']);

    Livewire::test(Index::class)
        ->assertSeeHtml('href="'.route('projects.show', $project).'"')
        ->assertSeeHtml('View record for Linked')
        ->assertDontSeeHtml('View record for Unlinked');
});

it('does not lazy load when listing many linked notifications', function () {
    $project = Project::factory()->for($this->user)->create();

    foreach (Task::factory()->forProject($project)->count(2)->create() as $task) {
        UserNotification::factory()->for($this->user)->make()->subject()->associate($task)->save();
    }

    Livewire::test(Index::class)->assertOk();
});

it('escapes stored values', function () {
    UserNotification::factory()->for($this->user)->create(['title' => '<script>alert(1)</script>', 'message' => '<img src=x>']);

    Livewire::test(Index::class)
        ->assertDontSeeHtml('<script>alert(1)</script>')
        ->assertDontSeeHtml('<img src=x>');
});

describe('sidebar badge', function () {
    it('counts only the user\'s unread notifications', function () {
        UserNotification::factory()->for($this->user)->count(3)->create();
        UserNotification::factory()->for($this->user)->read()->create();
        UserNotification::factory()->create();

        Livewire::test(NotificationBadge::class)->assertSeeHtml('class="nav__badge">3<');
    });

    it('caps the count', function () {
        UserNotification::factory()->for($this->user)->count(100)->create();

        Livewire::test(NotificationBadge::class)->assertSeeHtml('class="nav__badge">99+<');
    });

    it('hides at zero', function () {
        Livewire::test(NotificationBadge::class)->assertDontSeeHtml('class="nav__badge"');
    });

    it('updates when notifications are read', function () {
        $notification = UserNotification::factory()->for($this->user)->create();
        $badge = Livewire::test(NotificationBadge::class)->assertSeeHtml('class="nav__badge">1<');

        $notification->update(['read_at' => now()]);

        $badge->dispatch('notifications-read')->assertDontSeeHtml('class="nav__badge"');
    });

    it('appears in the sidebar for a user and not for a guest', function () {
        $this->get('/')->assertSeeLivewire(NotificationBadge::class);

        auth()->logout();

        $this->get('/privacy-policy')->assertDontSeeLivewire(NotificationBadge::class);
    });
});
