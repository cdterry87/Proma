<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use Livewire\Livewire;
use App\Livewire\Clients\ClientsForm;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientsFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_save_client()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(ClientsForm::class)
            ->set('name', 'Client Name')
            ->set('description', 'Client Description')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('clients', [
            'user_id' => $user->id,
            'name' => 'Client Name',
            'description' => 'Client Description',
        ]);
    }

    public function test_user_can_edit_client()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $client = Client::factory()->create([
            'user_id' => $user->id,
        ]);

        Livewire::test(ClientsForm::class)
            ->call('edit', $client->id)
            ->assertSet('model_id', $client->id)
            ->assertSet('user_id', $client->user_id)
            ->assertSet('name', $client->name)
            ->assertSet('description', $client->description)
            ->assertSet('active', !!$client->active)
            ->call('closeModal');
    }
}
