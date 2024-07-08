<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use Livewire\Livewire;
use App\Models\ClientUpload;
use App\Models\ClientContact;
use App\Livewire\Clients\ClientsView;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientsViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_must_be_logged_in_to_see_client_view_page()
    {
        $client = Client::factory()->create();

        $this->get(route('clients.view', $client))
            ->assertRedirect(route('login'));
    }

    public function test_logged_in_user_can_see_client_view_page()
    {
        $user = User::factory()->create();

        $client = Client::factory()->create([
            'user_id' => $user->id
        ]);

        ClientContact::factory(5)->create([
            'client_id' => $client->id
        ]);

        ClientUpload::factory(5)->create([
            'client_id' => $client->id
        ]);

        // Ensure table is shown and issue is present
        $this->actingAs($user)
            ->get(route('clients.view', $client))
            ->assertStatus(200)
            ->assertSeeLivewire('clients.clients-form')
            ->assertSeeLivewire('clients.clients-contacts-form')
            ->assertSeeLivewire('clients.clients-contacts-table')
            ->assertSeeLivewire('clients.clients-uploads-form')
            ->assertSeeLivewire('clients.clients-uploads-table')
            ->assertSee($client->name);

        // Test delete
        Livewire::actingAs($user)
            ->test(ClientsView::class, ['client' => $client])
            ->call('getClient')
            ->call('deleteClient')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('clients', [
            'id' => $client->id
        ]);
    }
}
