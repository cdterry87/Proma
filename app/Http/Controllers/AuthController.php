<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class AuthController extends Controller
{
    public function login()
    {
        $credentials = [
            'email' => request('email'),
            'password' => request('password')
        ];

        if (Auth::attempt($credentials)) {
            $success['id'] = Auth::user()->id;
            $success['name'] = Auth::user()->name;
            $success['email'] = Auth::user()->email;

            return response()->json(['success' => $success]);
        }

        return response()->json(['error' => 'Invalid username/password.'], 401);
    }

    public function register(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validate->fails()) {
            $errorString = implode("<br>", $validate->messages()->all());

            return response()->json(['error' => $errorString], 401);
        }

        $data = $request->all();
        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);

        $credentials = [
            'email' => request('email'),
            'password' => request('password')
        ];

        if (Auth::attempt($credentials)) {
            $success['id'] = Auth::user()->id;
            $success['name'] = Auth::user()->name;
            $success['email'] = Auth::user()->email;

            return response()->json(['success' => $success]);
        }

        return response()->json(['error' => 'Failed to generate user data.'], 500);
    }
}
