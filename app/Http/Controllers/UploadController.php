<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;
use App\Upload;

class UploadController extends Controller
{
    public function getProjectUploads(Project $project)
    {
        return response()->json($project->uploads()->get());
    }

    public function storeProjectUpload(Request $request, Project $project)
    {
        $upload = null;

        if ($request->hasFile('fileUpload')) {
            $file = $request->file('fileUpload');
            $originalFileName = $file->getClientOriginalName();
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $filePath = '/storage/projects/' . $project->id . '/' . $filename;
            $file->move(storage_path('app/public/projects/' . $project->id), $filename);

            $upload = new Upload([
                'name' => $originalFileName,
                'filepath' => $filePath
            ]);

            $upload = $project->uploads()->save($upload);
        }

        return response()->json([
            'status' => (bool)$upload,
            'data' => $upload,
            'message' => $upload ? 'File uploaded successfully!' : 'Error uploading file!'
        ]);
    }

    public function destroy(Upload $upload)
    {
        $uploadFolder = '';
        $filePathParts = explode('/', $upload->filepath);
        
        switch ($upload->uploadable_type) {
            case 'App\Project':
                $uploadFolder = 'projects';
                break;
        }

        $fileToDelete = storage_path('app/public/' . $uploadFolder . '/' . $upload->uploadable_id . '/' . end($filePathParts));
        unlink($fileToDelete);

        $status = $upload->delete();

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Upload deleted successfully!' : 'Error deleting upload!'
        ]);
    }
}
