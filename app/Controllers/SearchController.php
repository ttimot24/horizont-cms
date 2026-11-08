<?php

namespace App\Controllers;

use App\Model\Blogpost;
use App\Model\Page;
use App\Model\User;
use App\Services\SearchEngine;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class SearchController extends Controller
{
    public function __construct(private SearchEngine $search_engine) {}

    public function index(Request $request) {}

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function show(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:3|max:100',
        ]);

        // if(auth()->check() && Gate::allows('view', 'blogpost')){
        $this->search_engine->registerModel(Blogpost::class);
        // }
        if (auth()->check() && Gate::allows('view', 'user')) {
            $this->search_engine->registerModel(User::class);
        }
        if (auth()->check() && Gate::allows('view', 'page')) {
            $this->search_engine->registerModel(Page::class);
        }

        $this->search_engine->executeSearch($request->input('search'));

        if ($request->wantsJson()) {
            return response()->json($this->search_engine->getAllResults());
        }

        return view('search.index', [
            'search_for' => $request->input('search'),
            'search_engine' => $this->search_engine,
            'files' => [],
        ]);
    }
}
