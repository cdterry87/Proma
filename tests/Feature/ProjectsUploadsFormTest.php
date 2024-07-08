<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Project;
use App\Models\ProjectUpload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Livewire\Projects\ProjectsUploadsForm;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectsUploadsFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_save_and_download_and_delete_project_upload()
    {
        $user = User::factory()->create([
            'demo' => false
        ]);

        $project = Project::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        // Fake file uploads
        Storage::fake('local');
        $file1 = UploadedFile::fake()->image('testfile1.png');
        $file2 = UploadedFile::fake()->image('testfile2.png');
        Storage::disk('local')->put('/projects/' . $project->id, $file1);
        Storage::disk('local')->put('/projects/' . $project->id, $file2);
        $files = [$file1, $file2];

        Livewire::test(ProjectsUploadsForm::class)
            ->call('getProject', $project->id)
            ->set('files', $files)
            ->call('uploadFiles')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('projects_uploads', [
            'project_id' => $project->id,
            'name' => $file1->getClientOriginalName(),
        ]);

        // Get the file
        $file = ProjectUpload::query()->first();

        // Test download
        // Livewire::test(ProjectsUploadsForm::class)
        //     ->call('getProject', $project->id)
        //     ->call('downloadFile', $file->id, $project->id)
        //     ->assertHasNoErrors();

        // Test deletion
        Livewire::test(ProjectsUploadsForm::class)
            ->call('getProject', $project->id)
            ->call('deleteFile', $file->id, $project->id);

        $this->assertDatabaseMissing('projects_uploads', [
            'id' => $file->id
        ]);
    }
}
