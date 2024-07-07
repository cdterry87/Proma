<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PrivacyPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_privacy_policy_renders_correctly_while_not_logged_in()
    {
        $this->get(route('privacy-policy'))
            ->assertStatus(200);
    }

    public function test_privacy_policy_renders_correctly_while_logged_in()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->get(route('privacy-policy'))
            ->assertStatus(200);
    }
}
