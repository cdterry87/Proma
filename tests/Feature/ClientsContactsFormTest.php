<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use Livewire\Livewire;
use App\Models\ClientContact;
use App\Livewire\Clients\ClientsContactsForm;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientsContactsFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_save_client_contact()
    {
        $user = User::factory()->create();

        $client = Client::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        Livewire::test(ClientsContactsForm::class)
            ->call('getClient', $client->id)
            ->set('client_id', $client->id)
            ->set('name', 'Contact Name')
            ->set('title', 'Primary Contact')
            ->set('email', 'client@example.com')
            ->set('phone', '555-555-5555')
            ->set('phone_ext', '1234')
            ->call('saveContact')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('clients_contacts', [
            'client_id' => $client->id,
            'name' => 'Contact Name',
            'title' => 'Primary Contact',
            'email' => 'client@example.com',
            'phone' => '555-555-5555',
            'phone_ext' => '1234',
        ]);
    }

    public function test_user_can_edit_and_delete_client_contact()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $client = Client::factory()->create([
            'user_id' => $user->id,
        ]);

        $contact = ClientContact::factory()->create([
            'client_id' => $client->id,
        ]);

        Livewire::test(ClientsContactsForm::class)
            ->call('editContact', $contact->id)
            ->assertSet('contact_id', $contact->id)
            ->assertSet('client_id', $contact->client_id)
            ->assertSet('name', $contact->name)
            ->assertSet('title', $contact->title)
            ->assertSet('email', $contact->email)
            ->assertSet('phone', $contact->phone)
            ->assertSet('phone_ext', $contact->phone_ext)
            ->assertSet('active', !!$contact->active)
            ->call('deleteContact', $contact->id, $client->id);

        $this->assertDatabaseMissing('clients_contacts', [
            'id' => $contact->id,
        ]);
    }
}
