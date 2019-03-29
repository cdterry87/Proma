<?php

namespace App\Http\Controllers;

use App\Project;
use App\ProjectTask;
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
            'description' => $request->description,
            'project_id' => $request->project_id,
        ]);

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
            $request->only(['description'])
        );

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

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Team deleted successfully!' : 'Error deleting team!'
        ]);
    }
}
