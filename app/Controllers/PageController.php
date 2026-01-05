<?php

namespace App\Controllers;

use App\Controllers\Trait\UploadsImage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Model\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class PageController extends Controller
{

    use UploadsImage;

    protected $imagePath = 'images/pages';

    public function before()
    {
        File::ensureDirectoryExists($this->imagePath . '/thumbs');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pages = Page::orderBy('queue')->paginateSortAndFilter();

        if ($request->wantsJson()) {
            foreach($request->get('with', []) as $relation) {
                $pages->load($relation);
            }
            return response()->json($pages);
        }

        return view('pages.index', [
            'all_pages' => $pages,
            'visible_pages' => Page::where('visibility', 1)->count(),
            'home_page' => Page::find($request->settings['home_page']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        return view('pages.form', [
            'all_page' => Page::all(),
            'page_templates' => (new \App\Services\Theme($request->settings['theme']))->templates(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(Page::$rules);
        
        $page = new Page($request->all());
        $page->slug = str_slug($request->input('name'), "-");
        $page->parent_id = $request->input('parent_select') == 0 ? null : $request->input('parent_id');
        $page->queue = 99;
        $page->page = clean($request->input('page'));
        $page->author()->associate($request->user());

        $this->uploadImage($page);

        if($page->save()) {
            return redirect(route("page.edit", ['page' => $page]))->withMessage(['success' => trans('message.successfully_created_page')]);
        } 
            
        return redirect()->back()->withMessage(['danger' => trans('message.something_went_wrong')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Page $page)
    {
        if ($request->wantsJson()) {
            foreach($request->get('with', []) as $relation) {
                $page->load($relation);
            }
            return response()->json($page);
        }

        return view('pages.view', ['page' => $page]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Page $page)
    {

        return view('pages.form', [
            'page' => $page,
            'all_page' => Page::all(),
            'page_templates' => (new \App\Services\Theme($request->settings['theme']))->templates(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Page $page)
    {

        $request->validate(Page::$rules);

        $page->fill($request->all());

        $page->slug = str_slug($request->input('name'), "-");
        $page->parent_id = $request->input('parent_select') == 0 ? NULL : $request->input('parent_id');
        $page->page = clean($request->input('page'));

        $this->uploadImage($page);

        if ($page->save()) {
            return redirect(route("page.edit", ['page' => $page]))->withMessage(['success' => trans('message.successfully_updated_page')]);
        } 

        return redirect()->back()->withMessage(['danger' => trans('message.something_went_wrong')]);
    }


    public function setHomePage($id): RedirectResponse
    {

        if (\App\Model\Settings::where("setting", "home_page")->update(['value' => $id])) {
            return redirect()->back()->withMessage(['success' => trans('message.successfully_set_homepage')]);
        } 

        return redirect()->back()->withMessage(['danger' => trans('message.something_went_wrong')]);
    }


    /**
     * Remove the specified resource from database.
     *
     * @param  \App\Model\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function destroy(Page $page): RedirectResponse
    {

        if ($page->delete()) {
            return redirect(route("page.index"))->withMessage(['success' => trans('message.successfully_deleted_page')]);
        }

        return redirect(route("page.index"))->withMessage(['danger' => trans('message.something_went_wrong')]);
    }

    public function reorder(Request $request): JsonResponse
    {

        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:pages,id'
        ]);

        $order = $request->input('order');

        try {

            DB::transaction(function () use ($order) {
                foreach ($order as $index => $pageId) {
                    $page = Page::findOrFail($pageId);
                    $page->queue = $index;
                    $page->save();
                }
            });

            return response()->json([
                'message' => 'Pages reordered successfully.',
                'data' => $order
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to reorder pages.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
