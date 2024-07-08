<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use Livewire\Livewire;
use App\Models\ClientUpload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Livewire\Clients\ClientsUploadsForm;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientsUploadsFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_save_and_download_and_delete_client_upload()
    {
        $user = User::factory()->create([
            'demo' => false
        ]);

        $client = Client::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        // Fake file uploads
        Storage::fake('local');
        $file1 = UploadedFile::fake()->image('testfile1.png');
        $file2 = UploadedFile::fake()->image('testfile2.png');
        Storage::disk('local')->put('/clients/' . $client->id, $file1);
        Storage::disk('local')->put('/clients/' . $client->id, $file2);
        $files = [$file1, $file2];

        Livewire::test(ClientsUploadsForm::class)
            ->call('getClient', $client->id)
            ->set('files', $files)
            ->call('uploadFiles')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('clients_uploads', [
            'client_id' => $client->id,
            'name' => $file1->getClientOriginalName(),
        ]);

        // Get the file
        $file = ClientUpload::query()->first();

        // Test download
        // Livewire::test(ClientsUploadsForm::class)
        //     ->call('getClient', $client->id)
        //     ->call('downloadFile', $file->id, $client->id)
        //     ->assertHasNoErrors();

        // Test deletion
        Livewire::test(ClientsUploadsForm::class)
            ->call('getClient', $client->id)
            ->call('deleteFile', $file->id, $client->id);

        $this->assertDatabaseMissing('clients_uploads', [
            'id' => $file->id
        ]);
    }
}
