<?php

namespace App\Http\Controllers;

use App\Client;
use App\ClientContact;
use Illuminate\Http\Request;

class ClientContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Client $client)
    {
        return response()->json($client->contacts()->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $contact = ClientContact::create([
            'name' => $request->name,
            'title' => $request->title,
            'email' => $request->email,
            'phone' => $request->phone,
            'client_id' => $request->client_id,
        ]);

        return response()->json([
            'status' => (bool)$contact,
            'data' => $contact,
            'message' => $contact ? 'Contact created successfully!' : 'Error adding contact!'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ClientContact $contact)
    {
        return response()->json($contact);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClientContact $contact)
    {
        $status = $contact->update(
            $request->only(['name', 'title', 'email', 'phone'])
        );

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Contact updated successfully!' : 'Error updating contact!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClientContact $contact)
    {
        $status = $contact->delete();

        return response()->json([
            'status' => $status,
            'message' => $status ? 'Contact deleted successfully!' : 'Error deleting contact!'
        ]);
    }
}
