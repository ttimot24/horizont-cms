<?php

namespace App\Controllers;

use App\Controllers\Trait\UploadsImage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use \Illuminate\Http\Response;
use App\Model\BlogpostCategory;

class BlogpostCategoryController extends Controller
{

    use UploadsImage;

    protected $itemPerPage = 25;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $all_categories = BlogpostCategory::paginate($request->input('per_page', $this->itemPerPage));


        if ($request->wantsJson()) {
            foreach($request->get('with', []) as $relation) {
                $all_categories->load($relation);
            }
            return response()->json($all_categories);
        }

        return view('blogposts.category.index', [
            'all_category' => $all_categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        return $this->index($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        $blogpost_category = new BlogpostCategory($request->all());
        $blogpost_category->author()->associate($request->user());

        $this->uploadImage($blogpost_category);

        if ($blogpost_category->save()) {
            return redirect()->back()->withMessage(['success' => trans('message.successfully_created_blogpost_category')]);
        } else {
            return redirect()->back()->withMessage(['danger' => trans('message.something_went_wrong')]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, BlogpostCategory $blogpostcategory)
    {

        if ($request->wantsJson()) {
            foreach($request->get('with', []) as $relation) {
                $blogpostcategory->load($relation);
            }
            return response()->json($blogpostcategory);
        }

        return view('blogposts.category.view', ['category' => $blogpostcategory]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(BlogpostCategory $blogpostcategory)
    {

        return view('blogposts.category.edit', [
            'category' => $blogpostcategory,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BlogpostCategory $blogpostcategory)
    {

        $blogpostcategory->name = $request->input('name');

        $this->uploadImage($blogpostcategory);

        if ($blogpostcategory->save()) {
            return redirect()->back()->withMessage(['success' => trans('message.successfully_updated_blogpost_category')]);
        } else {
            return redirect()->back()->withMessage(['danger' => trans('message.something_went_wrong')]);
        }
    }

    /**
     * Remove the specified resource from database.
     *
     * @param  \App\Model\BlogpostCategory  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(BlogpostCategory $blogpostcategory)
    {

        if ($blogpostcategory->delete()) {
            return redirect()->back()->withMessage(['success' => trans('message.successfully_deleted_blogpost_category')]);
        }

        return redirect()->back()->withMessage(['danger' => trans('message.something_went_wrong')]);
    }
}
