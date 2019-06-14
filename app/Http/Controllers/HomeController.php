<?php

namespace App\Http\Controllers;

use App\Client;
use App\ClientContact;
use App\Project;
use App\ProjectTask;
use App\Issue;
use App\IssueActivity;
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
            ->registerModel(ClientContact::class, 'name')
            ->registerModel(Project::class, 'name')
            ->registerModel(ProjectTask::class, 'description')
            ->registerModel(Issue::class, 'description')
            ->registerModel(IssueActivity::class, 'description')
            ->perform($request->input('query'));
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
