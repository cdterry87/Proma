<?php

namespace App\Http\Controllers;

use App\Client;
use App\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Auth::user()->clients()->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $client = Client::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => Auth::user()->id
        ]);

        $notification = new Notification;
        $notification->createNotification("Client '" . $client->name . "' created.");

        return response()->json([
            'status' => (bool) $client,
            'data' => $client,
            'message' => $client ? 'Client created successfully!' : 'Error adding client!'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function show(Client $client)
    {
        return response()->json($client);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Client $client)
    {
        $status = $client->update(
            $request->only(['name', 'description'])
        );

        $notification = new Notification;
        $notification->createNotification("Client '" . $client->name . "' updated.");

        return response()->json([
            'status' => $status,
            // 'data'  => $client,
            'message' => $status ? 'Client updated successfully!' : 'Error updating client!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client)
    {
        $status = $client->delete();

        $notification = new Notification;
        $notification->createNotification("Client '" . $client->name . "' deleted.");

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Client deleted successfully!' : 'Error deleting task!'
        ]);
    }

    /**
     * Get a client's projects.
     *
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function projects(Client $client)
    {
        return response()->json($client->projects()->get());
    }
}
