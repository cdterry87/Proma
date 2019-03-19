<?php

namespace App\Http\Controllers;

use App\Project;
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
        return response()->json(Auth::user()->projects());
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
            'client_id' => $request->client_id,
            'team_id' => $request->team_id,
        ]);

        $project->user()->attach($request->user_id);

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
        return response()->json($project);
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
            $request->only(['name', 'description', 'client_id', 'team_id'])
        );

        return response()->json([
            'status' => $status,
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

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Project deleted successfully!' : 'Error deleting project!'
        ]);
    }
}
