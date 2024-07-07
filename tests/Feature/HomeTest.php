<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Issue;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_must_be_logged_in_to_see_home_page()
    {
        $this->get(route('home'))
            ->assertRedirect(route('login'));
    }

    public function test_logged_in_user_can_see_home_page()
    {
        $user = User::factory()->create();

        // Create projects
        Project::factory(15)->create([
            'user_id' => $user->id
        ]);

        // Create issues
        Issue::factory(100)->create([
            'user_id' => $user->id
        ]);

        $this->actingAs($user)
            ->get(route('home'))
            ->assertStatus(200);
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->assertTrue(Auth::check());

        $this->post(route('logout'))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login'));

        $this->assertFalse(Auth::check());
    }
}
