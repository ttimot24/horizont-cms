<?php

namespace App\Controllers;

use App\Controllers\Trait\UploadsImage;
use App\Model\HeaderImage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class HeaderImageController extends Controller
{
    use UploadsImage;

    protected $imagePath = 'images/header_images';

    public function before()
    {
        \File::ensureDirectoryExists($this->imagePath.'/thumbs');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $activeImages = HeaderImage::active()->orderBy('order', 'ASC')->get();

        if ($request->wantsJson()) {
            return response()->json($activeImages);
        }

        return view('media.header_images', [
            'slider_images' => $activeImages,
            'slider_disabled' => HeaderImage::inactive()->orderBy('order', 'ASC')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return null;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $header_image = new HeaderImage($request->all());
        $header_image->author()->associate($request->user());

        if ($request->hasFile($this->form_field_name)) {
            $header_image->type = explode('/', request()->{$this->form_field_name}->getMimeType())[0];
        }

        $this->uploadImage($header_image);

        if ($header_image->save()) {
            return redirect()->back()->withMessage(['success' => trans('message.successfully_added_headerimage')]);
        }

        return redirect()->back()->withMessage(['danger' => trans('message.something_went_wrong')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return null;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return null;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HeaderImage $headerimage)
    {

        $headerimage->fill($request->all());
        $headerimage->author()->associate($request->user());

        if ($headerimage->save()) {
            return redirect()->back()->withMessage(['success' => trans('message.successfully_added_headerimage')]);
        }

        return redirect()->back()->withMessage(['danger' => trans('message.something_went_wrong')]);

    }

    /**
     * Remove the specified resource from database.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(HeaderImage $headerimage)
    {

        if ($headerimage->delete()) {
            return redirect()->back()->withMessage(['success' => trans('message.successfully_deleted_blogpost')]);
        }

        return redirect()->back()->withMessage(['danger' => trans('message.something_went_wrong')]);
    }
}
