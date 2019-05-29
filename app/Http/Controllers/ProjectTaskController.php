<?php

namespace App\Http\Controllers;

use App\Project;
use App\ProjectTask;
use App\Notification;
use Illuminate\Http\Request;

class ProjectTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Project $project)
    {
        return response()->json($project->tasks()->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $task = ProjectTask::create([
            'due_date' => $request->due_date,
            'description' => $request->description,
            'project_id' => $request->project_id,
        ]);

        $notification = new Notification;
        $notification->createNotification("Task '" . $task->description . "' created.");

        return response()->json([
            'status' => (bool)$task,
            'data' => $task,
            'message' => $task ? 'Task created successfully!' : 'Error adding task!'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ProjectTask $task)
    {
        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProjectTask $task)
    {
        $status = $task->update(
            $request->only(['due_date', 'description'])
        );

        $notification = new Notification;
        $notification->createNotification("Task '" . $task->description . "' updated.");

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Task updated successfully!' : 'Error updating task!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProjectTask $task)
    {
        $status = $task->delete();

        $notification = new Notification;
        $notification->createNotification("Task '" . $task->description . "' created.");

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Task deleted successfully!' : 'Error deleting task!'
        ]);
    }

    /**
     * Set a project as complete.
     *
     * @param  int  $project_id
     * @param  int  $task_id
     * @return \Illuminate\Http\Response
     */
    public function complete(Project $project, ProjectTask $task)
    {
        $task->complete = 1;
        $task->completed_date = date('Y-m-d');
        $status = $task->save();

        $notification = new Notification;
        $notification->createNotification("Task '" . $task->description . "' completed.");

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Task is now complete!' : 'Task could not be completed!'
        ]);
    }

    /**
     * Set a project as complete.
     *
     * @param  int  $project_id
     * @param  int  $task_id
     * @return \Illuminate\Http\Response
     */
    public function incomplete(Project $project, ProjectTask $task)
    {
        $task->complete = 0;
        $status = $task->save();

        $notification = new Notification;
        $notification->createNotification("Task '" . $task->description . "' incomplete.");

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Task is now incomplete!' : 'Task could not be marked as incomplete!'
        ]);
    }
}
