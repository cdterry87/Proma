<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Issue;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IssuesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_must_be_logged_in_to_see_issues_page()
    {
        $this->get(route('issues'))
            ->assertRedirect(route('login'));
    }

    public function test_logged_in_user_can_see_issues_page()
    {
        $user = User::factory()->create();

        Issue::factory(20)->create([
            'user_id' => $user->id
        ]);

        // Get a random issue
        $issue = Issue::where('user_id', $user->id)->first();

        // Ensure table is shown and issue is present
        $this->actingAs($user)
            ->get(route('issues'))
            ->assertStatus(200)
            ->assertSeeLivewire('issues.issues-table')
            ->assertSeeLivewire('issues.issues-form')
            ->assertSee($issue->name);
    }
}
