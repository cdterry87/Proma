<?php

namespace App\Http\Controllers;

use App\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Auth::user()->teams()->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $team = Team::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $team->user()->attach($request->user_id);

        return response()->json([
            'status' => (bool)$team,
            'data' => $team,
            'message' => $team ? 'Team created successfully!' : 'Error adding team!'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function show(Team $team)
    {
        return response()->json($team);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Team $team)
    {
        $status = $team->update(
            $request->only(['name', 'description'])
        );

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Team updated successfully!' : 'Error updating team!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team)
    {
        $status = $team->delete();

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Team deleted successfully!' : 'Error deleting team!'
        ]);
    }
}
