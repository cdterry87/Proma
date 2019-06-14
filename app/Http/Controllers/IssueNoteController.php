<?php

namespace App\Http\Controllers;

use App\Issue;
use App\IssueNote;
use App\Notification;
use Illuminate\Http\Request;

class IssueNoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Issue $issue)
    {
        return response()->json($issue->notes()
            ->orderBy('created_at', 'desc')
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
        $note = IssueNote::create([
            'description' => $request->description,
            'issue_id' => $request->issue_id,
        ]);

        $notification = new Notification;
        $notification->createNotification("Note '" . $note->description . "' created.");

        return response()->json([
            'status' => (bool)$note,
            'data' => $note,
            'message' => $note ? 'Note created successfully!' : 'Error adding note!'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(IssueNote $note)
    {
        return response()->json($note);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, IssueNote $note)
    {
        $status = $note->update(
            $request->only(['description'])
        );

        $notification = new Notification;
        $notification->createNotification("Note '" . $note->description . "' updated.");

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Note updated successfully!' : 'Error updating note!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(IssueNote $note)
    {
        $status = $note->delete();

        $notification = new Notification;
        $notification->createNotification("Note '" . $note->description  . "' created.");

        return response()->json([
            'status' => $status,
            'message' => $status  ? 'Note deleted successfully!' : 'Error deleting note!'
        ]);
    }
}
