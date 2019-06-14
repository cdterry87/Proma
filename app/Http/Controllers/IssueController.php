<?php

namespace App\Http\Controllers;

use App\Issue;
use App\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IssueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Auth::user()->issues()->with('project')
            ->orderBy('priority')
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
        $issue = Issue::create([
            'description' => $request->description,
            'priority' => $request->priority,
            'project_id' => $request->project_id,
            'user_id' => Auth::user()->id
        ]);

        $notification = new Notification;
        $notification->createNotification("Issue #" . $issue->id . " created.");

        return response()->json([
            'status' => (bool)$issue,
            'data' => $issue,
            'message' => $issue ? 'Issue created successfully!' : 'Error adding issue!'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Issue  $issue
     * @return \Illuminate\Http\Response
     */
    public function show(Issue $issue)
    {
        return response()->json($issue->with([
            'client'
        ])->where('id', $issue->id)->first());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Issue  $issue
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Issue $issue)
    {
        $status = $issue->update(
            $request->only(['description', 'priority', 'project_id'])
        );

        $notification = new Notification;
        $notification->createNotification("Issue #" . $issue->name . " updated.");

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Issue updated successfully!' : 'Error updating issue!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Issue  $issue
     * @return \Illuminate\Http\Response
     */
    public function destroy(Issue $issue)
    {
        $status = $issue->delete();

        $notification = new Notification;
        $notification->createNotification("Issue '" . $issue->name . "' deleted.");

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Issue deleted successfully!' : 'Error deleting issue!'
        ]);
    }
}
