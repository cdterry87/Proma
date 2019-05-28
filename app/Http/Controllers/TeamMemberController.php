<?php

namespace App\Http\Controllers;

use App\Team;
use App\TeamMember;
use App\Notification;
use Illuminate\Http\Request;

class TeamMemberController extends Controller
{
    public function index(Team $team)
    {
        return response()->json($team->members()->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $member = TeamMember::create([
            'team_id' => $request->team_id,
            'user_id' => $request->user_id,
        ]);

        $notification = new Notification;
        $notification->createNotification("Team member '" . $member->name . "' created.");

        return response()->json([
            'status' => (bool)$member,
            'data' => $member,
            'message' => $member ? 'Team member created successfully!' : 'Error adding team member!'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(TeamMember $member)
    {
        return response()->json($member);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TeamMember $member)
    {
        $status = $member->update(
            $request->only(['user_id'])
        );

        $notification = new Notification;
        $notification->createNotification("Team member '" . $member->name . "' updated.");

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Team member updated successfully!' : 'Error updating team member!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(TeamMember $member)
    {
        $status = $member->delete();

        $notification = new Notification;
        $notification->createNotification("Team member '" . $member->name . "' deleted.");

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Team member deleted successfully!' : 'Error deleting team member!'
        ]);
    }
}
