<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Livewire\Settings;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_must_be_logged_in_to_see_settings_page()
    {
        $this->get(route('home'))
            ->assertRedirect(route('login'));
    }

    public function test_logged_in_user_can_see_settings_page()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('settings'))
            ->assertStatus(200);
    }

    public function test_user_can_update_settings()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->assertSet('name', $user->name)
            ->assertSet('email', $user->email)
            ->assertSet('title', $user->title)
            ->set('name', 'John Doe')
            ->set('email', 'jdoe@example.com')
            ->set('title', 'Developer')
            ->call('saveName')
            ->assertHasNoErrors()
            ->call('saveEmail')
            ->assertHasNoErrors()
            ->call('saveTitle')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'John Doe',
            'email' => 'jdoe@example.com',
            'title' => 'Developer'
        ]);
    }
}
