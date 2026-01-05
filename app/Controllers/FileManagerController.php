<?php

namespace App\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;

class FileManagerController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): View | JsonResponse
    {

        $mode = request()->get('mode');

        $disk = request()->get('disk', 'local');

        $current_dir = str_replace_first("storage/", "", request()->has('path')? ltrim(request()->get('path'), "/") : "");

        $data = [
            'old_path' => ($current_dir == "" ? "" : $current_dir . "/"),
            'current_dir' => $current_dir,
            'dirs' => collect(Storage::disk($disk)->directories($current_dir))
                        ->map(fn ($dir) => basename($dir))->values()->toArray(),
            'files' =>  collect(Storage::disk($disk)->files($current_dir))
                        ->map(fn ($dir) => basename($dir))->values()->toArray(),
            'allowed_extensions' => [
                'image' => ['jpg', 'png', 'jpeg', 'webp', 'gif']
            ],
            'mode' => $mode,
        ];


        if (request()->wantsJson()) {
            return response()->json($data);
        }

        return view($mode == 'embed' ? 'media.embed' : 'media.fmframe', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(): JsonResponse | RedirectResponse
    {
        if (request()->isMethod('POST') && auth()->user()->isAdmin()) {

            if (request()->hasFile('up_file')) {

                $dir = str_replace("storage/", "", request()->input('dir_path'));

                foreach (request()->up_file as $file) {
                    if (!\Security::isExecutable($file)) {
                        $images[] = $file->store($dir);
                    }
                }

                if (request()->wantsJson()) {
                    return response()->json(['success' => 'Files uploaded successfully!', 'uploadedFileNames' => $images, "uploaded" => 1, "url" => asset('storage/'. $images[0])]);
                }

                return redirect()->back()->withMessage(['success' => 'Files uploaded successfully!']);
            } else {

                if (request()->wantsJson()) {
                    return response()->json(['error' => 'Could not upload files!', 'uploaded' => 0]);
                }

                return redirect()->back()->withMessage(['danger' => 'Could not upload files!']);
            }
        }

        if (request()->wantsJson()) {
            return response()->json(['warning' => 'Only POST method allowed!']);
        }

        return redirect()->back()->withMessage(['warning' => 'Only POST method allowed!']);
    }

    // TODO: return type and storage
    public function download()
    {

        if (request()->has('file')) {

            $file = request()->input('file');

            $headers = [
                'Content-Type' => 'application/*',
            ];

            return response()->download($file, basename($file), $headers);
        }

        return redirect()->back()->withMessage(['warning' => 'Bad request!']);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function newFolder(): JsonResponse | RedirectResponse
    {

        if (request()->isMethod('POST')) {

            $directory = "storage/" . str_replace("storage/", "", request()->input('dir_path')) . "/" . request()->input('new_folder_name');

            if (!file_exists($directory)) {
                File::makeDirectory($directory, $mode = 0777, true, true);

                if (request()->wantsJson()) {
                    return response()->json(['success' => 'Folder created successfully!']);
                }

                return redirect()->back()->withMessage(['success' => 'Folder created successfully!']);
            } else {

                if (request()->wantsJson()) {
                    return response()->json(['danger' => 'Folder already exists!']);
                }

                return redirect()->back()->withMessage(['danger' => 'Folder already exists!']);
            }
        }

        return redirect()->back()->withMessage(['danger' => 'Invalid HTTP method!']);
    }


    /**
     * Remove the specified resource from database.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(): JsonResponse | RedirectResponse
    {

        $toDelete = str_replace("storage/", "", request()->input('file'));

        if (!file_exists('storage/' . $toDelete)) {
            if (request()->wantsJson()) {
                return response()->json(['warning' => trans("File " . $toDelete . " doesn't exists")]);
            }

            return redirect()->back()->withMessage(['warning' => trans("File " . $toDelete . " doesn't exists")]);
        }


        if (!is_dir('storage/' . $toDelete) && Storage::delete($toDelete)) {

            if (request()->wantsJson()) {
                return response()->json(['success' => trans('File deleted successfully')]);
            }

            return redirect()->back()->withMessage(['success' => trans('File deleted successfully')]);
        } else if (is_dir('storage/' . $toDelete) && Storage::deleteDirectory($toDelete)) {

            if (request()->wantsJson()) {
                return response()->json(['success' => trans('Directory deleted successfully')]);
            }

            return redirect()->back()->withMessage(['success' => trans('Directory deleted successfully')]);
        } else {

            if (request()->wantsJson()) {
                return response()->json(['danger' => trans('message.something_went_wrong')]);
            }

            return redirect()->back()->withMessage(['danger' => trans('message.something_went_wrong')]);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function rename(): JsonResponse | RedirectResponse
    {

        $new_file = trim(request()->input('new_file'), "/");

        if (!\Security::isExecutable($new_file) && Storage::move(request()->input('old_file'), $new_file)) {
            if (request()->wantsJson()) {
                return response()->json(['success' => trans('File successfully renamed!')]);
            }
        } else {
            if (request()->wantsJson()) {
                return response()->json(['danger' => trans('message.something_went_wrong')]);
            }
        }

        return redirect()->back()->withMessage(['danger' => trans('message.something_went_wrong')]);
    }
}
