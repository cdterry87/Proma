<?php

namespace App\Http\Controllers;

use App\Issue;
use App\Project;
use App\Upload;
use App\Client;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function getProjectUploads(Project $project)
    {
        return response()->json($project->uploads()->get());
    }

    public function storeProjectUpload(Request $request, Project $project)
    {
        return $this->storeUploadedFile($request, $project);
    }

    public function getIssueUploads(Issue $issue)
    {
        return response()->json($issue->uploads()->get());
    }

    public function storeIssueUpload(Request $request, Issue $issue)
    {
        return $this->storeUploadedFile($request, $issue);
    }

    public function getClientUploads(Client $client)
    {
        return response()->json($client->uploads()->get());
    }

    public function storeClientUpload(Request $request, Client $client)
    {
        return $this->storeUploadedFile($request, $client);
    }

    public function destroy(Upload $upload)
    {
        $uploadFolder = '';
        $filePathParts = explode('/', $upload->filepath);
        $uploadFolder = $this->getUploadFolder($upload->uploadable_type);

        $fileToDelete = storage_path('app/public/' . $uploadFolder . '/' . $upload->uploadable_id . '/' . end($filePathParts));
        unlink($fileToDelete);

        $status = $upload->delete();

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Upload deleted successfully!' : 'Error deleting upload!'
        ]);
    }

    protected function storeUploadedFile($request, $model)
    {
        $upload = null;

        $uploadFolder = $this->getUploadFolder($model);

        if ($request->hasFile('fileUpload')) {
            $uploadFolder = $this->getUploadFolder(get_class($model));
            $file = $request->file('fileUpload');
            $originalFileName = $file->getClientOriginalName();
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $filePath = '/storage/' . $uploadFolder . '/' . $model->id . '/' . $filename;
            $file->move(storage_path('app/public/' . $uploadFolder . '/' . $model->id), $filename);

            $upload = new Upload([
                'name' => $originalFileName,
                'filepath' => $filePath
            ]);

            $upload = $model->uploads()->save($upload);
        }

        return response()->json([
            'status' => (bool) $upload,
            'data' => $upload,
            'message' => $upload ? 'File uploaded successfully!' : 'Error uploading file!'
        ]);
    }

    protected function getUploadFolder($className)
    {
        switch ($className) {
            case 'App\Project':
                return 'projects';
                break;
            case 'App\Issue':
                return 'issues';
                break;
            case 'App\Client':
                return 'clients';
                break;
        }
    }
}
