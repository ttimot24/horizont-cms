<?php

namespace App\Controllers;

use App\Controllers\Trait\UploadsImage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

use App\Model\HeaderImage;

class HeaderImageController extends Controller
{

    use UploadsImage;

    protected $imagePath = 'images/header_images';

    public function before(): void
    {
        File::ensureDirectoryExists($this->imagePath . '/thumbs');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View | JsonResponse
    {
        $activeImages = HeaderImage::active()->orderBy('order', 'ASC')->get();

        if($request->wantsJson()){
            return response()->json($activeImages);
        }

        return view('media.header_images', [
            'slider_images' => $activeImages,
            'slider_disabled' => HeaderImage::inactive()->orderBy('order','ASC')->get(),
        ]);
    }

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
        $header_image = new HeaderImage($request->all());
        $header_image->author()->associate($request->user());

        if($request->hasFile($this->form_field_name)){
            $header_image->type = explode('/',request()->{$this->form_field_name}->getMimeType())[0];
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
    public function show($id): View | JsonResponse
    {
        return view();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id): View
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
    public function update(Request $request, HeaderImage $headerimage): JsonResponse | RedirectResponse
    {

        $headerimage->fill($request->all());
        $headerimage->author()->associate($request->user());

        if($headerimage->save()) {
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
    public function destroy(HeaderImage $headerimage): JsonResponse | RedirectResponse
    {

        if ($headerimage->delete()) {
            return redirect()->back()->withMessage(['success' => trans('message.successfully_deleted_blogpost')]);
        }

        return redirect()->back()->withMessage(['danger' => trans('message.something_went_wrong')]);
    }
}
