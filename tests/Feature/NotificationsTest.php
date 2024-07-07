<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Livewire\Notifications;
use App\Livewire\NotificationsTable;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_must_be_logged_in_to_see_notifications_page()
    {
        $this->get(route('notifications'))
            ->assertRedirect(route('login'));
    }

    public function test_logged_in_user_can_see_notifications_page_and_mark_as_read()
    {
        $user = User::factory()->create();

        UserNotification::factory(20)->create([
            'user_id' => $user->id
        ]);

        // Get a random user notification
        $notification = UserNotification::where('user_id', $user->id)->first();

        // Ensure table is shown and notification is present
        $this->actingAs($user)
            ->get(route('notifications'))
            ->assertStatus(200)
            ->assertSeeLivewire('notifications-table')
            ->assertSee($notification->subject);

        // Toggle detail shows message and marks as read
        Livewire::test(NotificationsTable::class)
            ->call('toggleDetail', $notification->id)
            ->assertSee($notification->message);

        // Assert marked as read
        $this->assertDatabaseHas('users_notifications', [
            'id' => $notification->id,
            'read' => true
        ]);

        // Mark all as read
        Livewire::test(Notifications::class)
            ->call('markAllAsRead');

        // Assert all marked as read
        $unreadNotifications = UserNotification::query()
            ->where('user_id', $user->id)
            ->where('read', false)
            ->get();

        // Assert no unread notifications
        $this->assertCount(0, $unreadNotifications);
    }
}
