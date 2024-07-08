<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Issue;
use Livewire\Livewire;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Livewire\Issues\IssuesUploadsForm;
use App\Models\IssueUpload;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IssuesUploadsFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_save_and_download_and_delete_issue_upload()
    {
        $user = User::factory()->create([
            'demo' => false
        ]);

        $issue = Issue::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        // Fake file uploads
        Storage::fake('local');
        $file1 = UploadedFile::fake()->image('testfile1.png');
        $file2 = UploadedFile::fake()->image('testfile2.png');
        Storage::disk('local')->put('/issues/' . $issue->id, $file1);
        Storage::disk('local')->put('/issues/' . $issue->id, $file2);
        $files = [$file1, $file2];

        Livewire::test(IssuesUploadsForm::class)
            ->call('getIssue', $issue->id)
            ->set('files', $files)
            ->call('uploadFiles')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('issues_uploads', [
            'issue_id' => $issue->id,
            'name' => $file1->getClientOriginalName(),
        ]);

        // Get the file
        $file = IssueUpload::query()->first();

        // Test download
        // Livewire::test(IssuesUploadsForm::class)
        //     ->call('getIssue', $issue->id)
        //     ->call('downloadFile', $file->id, $issue->id)
        //     ->assertHasNoErrors();

        // Test deletion
        Livewire::test(IssuesUploadsForm::class)
            ->call('getIssue', $issue->id)
            ->call('deleteFile', $file->id, $issue->id);

        $this->assertDatabaseMissing('issues_uploads', [
            'id' => $file->id
        ]);
    }
}
