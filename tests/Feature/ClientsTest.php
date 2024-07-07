<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_must_be_logged_in_to_see_clients_page()
    {
        $this->get(route('clients'))
            ->assertRedirect(route('login'));
    }

    public function test_logged_in_user_can_see_clients_page()
    {
        $user = User::factory()->create();

        Client::factory(20)->create([
            'user_id' => $user->id
        ]);

        // Get a random client
        $client = Client::where('user_id', $user->id)->first();

        // Ensure table is shown and client is present
        $this->actingAs($user)
            ->get(route('clients'))
            ->assertStatus(200)
            ->assertSeeLivewire('clients.clients-table')
            ->assertSeeLivewire('clients.clients-form')
            ->assertSee($client->name);
    }
}
