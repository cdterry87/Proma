<?php

namespace App\Http\Controllers;

use App\Project;
use App\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Auth::user()->projects()
            ->orderBy('complete')
            ->orderByRaw('ISNULL(due_date), due_date ASC')
            ->orderByRaw('ISNULL(completed_date), completed_date ASC')
            ->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'client_id' => $request->client_id,
            'team_id' => $request->team_id,
        ]);

        $project->user()->attach(Auth::user()->id);

        $notification = new Notification;
        $notification->createNotification("Project '" . $project->name . "' created.");

        return response()->json([
            'status' => (bool)$project,
            'data' => $project,
            'message' => $project ? 'Project created successfully!' : 'Error adding project!'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        return response()->json($project->with([
            'team',
            'client'
        ])
            ->where('id', $project->id)
            ->first());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
        $status = $project->update(
            $request->only(['name', 'description', 'due_date', 'client_id', 'team_id'])
        );

        $notification = new Notification;
        $notification->createNotification("Project '" . $project->name . "' updated.");

        return response()->json([
            'status' => $status,
            // 'data' => $project,
            'message' => $status ? 'Project updated successfully!' : 'Error updating project!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        $status = $project->delete();

        $notification = new Notification;
        $notification->createNotification("Project '" . $project->name . "' deleted.");

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Project deleted successfully!' : 'Error deleting project!'
        ]);
    }

    /**
     * Set a project as complete.
     *
     * @param  int  $project_id
     * @param  int  $task_id
     * @return \Illuminate\Http\Response
     */
    public function complete(Project $project)
    {
        $project->complete = 1;
        $project->completed_date = date('Y-m-d');
        $status = $project->save();

        $notification = new Notification;
        $notification->createNotification("Project '" . $project->name . "' completed.");

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Project is now complete!' : 'Project could not be completed!'
        ]);
    }

    /**
     * Set a project as complete.
     *
     * @param  int  $project_id
     * @param  int  $task_id
     * @return \Illuminate\Http\Response
     */
    public function incomplete(Project $project)
    {
        $project->complete = 0;
        $status = $project->save();

        $notification = new Notification;
        $notification->createNotification("Project '" . $project->name . "' incomplete.");

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Project is now incomplete!' : 'Project could not be marked as incomplete!'
        ]);
    }
}
