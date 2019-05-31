<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Auth::user());
    }

    public function update(Request $request)
    {
        $status = Auth::user()->update(
            $request->only(['name', 'email'])
        );

        $user = Auth::user()->get();

        return response()->json([
            'status' => $status,
            'data' => $user,
            'message' => $status ? 'Account updated successfully!' : 'Error updating account!'
        ]);
    }

    public function teams()
    {

    }

    public function clients()
    {

    }

    public function projects()
    {

    }


}
