<?php

namespace App\Http\Controllers;

use App\Client;
use App\ClientContact;
use App\Project;
use App\Team;
use App\Notification;
use Illuminate\Http\Request;
use Spatie\Searchable\Search;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function search(Request $request)
    {
        $results = (new Search())
            ->registerModel(Client::class, 'name')
            ->registerModel(Project::class, 'name')
            ->registerModel(Team::class, 'name')
            ->perform($request->input('query'));

        // return view('search', compact('searchResults'));
        return response()->json($results);
    }

    public function notifications()
    {
        return response()->json(Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(25)
            ->get());
    }
}
