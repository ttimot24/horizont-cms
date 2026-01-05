<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Model\BlogpostComment;
use Illuminate\Http\RedirectResponse;

class BlogpostCommentController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        return view();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse | RedirectResponse
    {

        $blogpost_comment = new BlogpostComment($request->all());
        $blogpost_comment->blogpost_id = $request->input('blogpost_id');
        $blogpost_comment->author()->associate($request->user());

        return redirect()->back()->withMessage(
            $blogpost_comment->save() ? ['success' => trans('message.successfully_created_blogpost_comment')]
                : ['danger' => trans('message.something_went_wrong')]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): View | JsonResponse
    {
        return view();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BlogpostComment $blogpost_comment): JsonResponse | RedirectResponse
    {

        $blogpost_comment->blogpost_id = $request->input('blogpost_id');
        $blogpost_comment->comment = $request->input('comment');
        $blogpost_comment->author()->associate($request->user());


        return redirect()->back()->withMessage(
            $blogpost_comment->save() ? ['success' => trans('message.successfully_updated_blogpost_comment')]
                : ['danger' => trans('message.something_went_wrong')]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(BlogpostComment $blogpostcomment): JsonResponse | RedirectResponse
    {
        return redirect()->back()->withMessage(
            $blogpostcomment->delete() ? ['success' => trans('message.successfully_deleted_blogpost_comment')]
                : ['danger' => trans('message.something_went_wrong')]
        );
    }
}
