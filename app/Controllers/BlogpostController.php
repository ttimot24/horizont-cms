<?php

namespace App\Controllers;

use App\Controllers\Trait\UploadsImage;
use \Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use App\Model\Blogpost;
use Illuminate\Support\Facades\Gate;

class BlogpostController extends Controller
{

    use UploadsImage;

    //TODO Use model path
    protected $imagePath = 'images/blogposts';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View | JsonResponse
    {
        
        if ($request->wantsJson()) {

            $blogposts = Blogpost::paginateSortAndFilter();

            foreach($request->get('with', []) as $relation) {
                $blogposts->load($relation);
            }
            return response()->json($blogposts);
        }

        return view('blogposts.index', [
            'all_blogposts' =>  Blogpost::paginateSortAndFilter([
                'sort' => $request->input('sort', 'id,desc'),
            ]),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {

        return view('blogposts.form', [
            'users' => \App\Model\User::whereHas('role', function ($query) {
                $query->whereJsonContains('rights', 'user.update');
            })->get(),
            'categories' => \App\Model\BlogpostCategory::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse | RedirectResponse
    {
        $request->validate(Blogpost::$rules);

        $blogpost = new Blogpost($request->all());
        $blogpost->slug = str_slug($request->input('title'), "-");
        $blogpost->comments_enabled = 1;

        $blogpost->author()->associate(
            Gate::allows('update','user') && $request->has('author_id')? 
            \App\Model\User::findOrFail($request->input('author_id')) : $request->user()
        );

        $this->uploadImage($blogpost);

        $blogpost->save();
    
        $blogpost->categories()->attach($request->input('category_ids', []));

        return $blogpost->save() ? redirect(route("blogpost.edit", ['blogpost' => $blogpost]))->withMessage(['success' => trans('message.successfully_created_blogpost')])
            : redirect()->back()->withInput()->withMessage(['danger' => trans('message.something_went_wrong')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Blogpost $blogpost): View | JsonResponse
    {

        if ($request->wantsJson()) {
            foreach($request->get('with', []) as $relation) {
                $blogpost->load($relation);
            }
            return response()->json($blogpost);
        }


        return view('blogposts.view', [
            'blogpost' => $blogpost,
            'previous_blogpost' => Blogpost::where('id', '<', $blogpost->id)->max('id'),
            'next_blogpost' =>  Blogpost::where('id', '>', $blogpost->id)->min('id'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Blogpost $blogpost): View
    {

        return view('blogposts.form', [
            'blogpost' => $blogpost,
            'categories' => \App\Model\BlogpostCategory::all(),
            'users' => \App\Model\User::whereHas('role', function ($query) {
                $query->whereJsonContains('rights', 'user.update');
            })->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Blogpost $blogpost): JsonResponse | RedirectResponse
    {

        $blogpost->fill($request->all());

        $blogpost->slug = str_slug($request->input('title', $blogpost->title), "-");
        $blogpost->categories()->sync($request->input('category_ids', $blogpost->categories->pluck('id')->toArray()));

        $blogpost->author()->associate(
            Gate::allows('update','user') && $request->has('author_id')? 
            \App\Model\User::findOrFail($request->input('author_id')) : $request->user()
        );

        $this->uploadImage($blogpost);

        if ($blogpost->save()) {
            return redirect()->back()->with('blogpost', $blogpost)->withMessage(['success' => trans('message.successfully_updated_blogpost')]);
        }
        
        return redirect()->back()->withInput()->withMessage(['danger' => trans('message.something_went_wrong')]);
    }

    /**
     * Remove the specified resource from database.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Blogpost $blogpost): JsonResponse | RedirectResponse
    {

        if ($blogpost->delete()) {
            return redirect(route("blogpost.index"))->withMessage(['success' => trans('message.successfully_deleted_blogpost')]);
        }


        return redirect(route("blogpost.index"))->withMessage(['danger' => trans('message.something_went_wrong')]);
    }
}
