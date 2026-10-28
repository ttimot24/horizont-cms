<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileManagerController extends Controller
{
    private function disk(): string
    {
        return (string) request()->get('disk', 'local');
    }

    /**
     * Turn a user supplied path into a path relative to the disk root.
     *
     * The UI sends paths like "storage/images/pages", so the storage prefix
     * and the surrounding slashes are stripped. Returns null when the path
     * tries to leave the disk root (".."), because Storage refuses to work
     * with such paths.
     */
    private function normalizePath(mixed $path): ?string
    {
        $path = trim(str_replace_first('storage/', '', is_scalar($path) ? (string) $path : ''), '/');

        if (Str::contains($path, ['..', './', '\\'])) {
            return null;
        }

        return $path;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

        $mode = request()->get('mode');

        $current_dir = $this->normalizePath(request()->get('path'));

        if ($current_dir === null) {
            if (request()->wantsJson()) {
                return response()->json(['error' => 'Invalid path!'], 400);
            }

            return redirect()->back()->withMessage(['danger' => trans('message.something_went_wrong')]);
        }

        $storage = Storage::disk($this->disk());

        $data = [
            'old_path' => ($current_dir == '' ? '' : $current_dir.'/'),
            'current_dir' => $current_dir,
            'dirs' => collect($storage->directories($current_dir))
                ->map(fn ($dir) => basename($dir))->values()->toArray(),
            'files' => collect($storage->files($current_dir))
                ->map(fn ($file) => basename($file))->values()->toArray(),
            'allowed_extensions' => [
                'image' => ['jpg', 'png', 'jpeg', 'webp', 'gif'],
            ],
            'mode' => $mode,
        ];

        if (request()->wantsJson()) {
            return response()->json($data);
        }

        return view($mode == 'embed' ? 'media.embed' : 'media.fmframe', $data);
    }

    public function show($id)
    {
        return $this->index();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function store()
    {
        if (request()->isMethod('POST') && auth()->user()->isAdmin()) {

            if (request()->hasFile('up_file')) {

                $dir = $this->normalizePath(request()->input('dir_path'));

                if ($dir === null) {
                    if (request()->wantsJson()) {
                        return response()->json(['error' => 'Could not upload files!', 'uploaded' => 0]);
                    }

                    return redirect()->back()->withMessage(['danger' => 'Could not upload files!']);
                }

                $storage = Storage::disk($this->disk());

                $images = [];

                foreach (request()->up_file as $file) {
                    if (! \Security::isExecutable($file->getClientOriginalName())) {

                        $image = config('horizontcms.upload_file_rename') ?
                            $storage->putFile($dir, $file) :
                            $storage->putFileAs($dir, $file, $file->getClientOriginalName());

                        if ($image) {
                            $images[] = $image;
                        }
                    }
                }

                if (empty($images)) {
                    if (request()->wantsJson()) {
                        return response()->json(['error' => 'Could not upload files!', 'uploaded' => 0]);
                    }

                    return redirect()->back()->withMessage(['danger' => 'Could not upload files!']);
                }

                if (request()->wantsJson()) {
                    return response()->json(['success' => 'Files uploaded successfully!', 'uploadedFileNames' => $images, 'uploaded' => 1, 'url' => asset($images[0])]);
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

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        if (! auth()->user()->isAdmin()) {
            if (request()->wantsJson()) {
                return response()->json(['warning' => 'Unauthorized!']);
            }

            return redirect()->back()->withMessage(['warning' => 'Unauthorized!']);
        }

        if ($id == 'new-folder') {
            return $this->newFolder();
        }

        if ($id == 'new-file') {
            return $this->newFile();
        }

        if ($id == 'rename') {
            $storage = Storage::disk($this->disk());

            $old_file = $this->normalizePath(request()->input('old_file'));
            $new_file = $this->normalizePath(request()->input('new_file'));

            $renamed = $old_file !== null && $new_file !== null &&
                ! \Security::isExecutable($new_file) &&
                $storage->exists($old_file) &&
                $storage->move($old_file, $new_file);

            if ($renamed) {
                if (request()->wantsJson()) {
                    return response()->json(['success' => trans('File successfully renamed!')]);
                }

                return redirect()->back()->withMessage(['success' => trans('File successfully renamed!')]);
            }

            if (request()->wantsJson()) {
                return response()->json(['danger' => trans('message.something_went_wrong')]);
            }

            return redirect()->back()->withMessage(['danger' => trans('message.something_went_wrong')]);
        }

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Invalid action!']);
        }

        return redirect()->back()->withMessage(['danger' => trans('message.something_went_wrong')]);
    }

    public function download()
    {

        if (request()->has('file')) {

            $file = $this->normalizePath(request()->input('file'));

            if ($file !== null) {
                $storage = Storage::disk($this->disk());

                if ($storage->exists($file)) {
                    return $storage->download($file, basename($file));
                }
            }
        }

        return redirect()->back()->withMessage(['warning' => 'Bad request!']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function newFolder()
    {

        if (request()->isMethod('POST') || request()->isMethod('PUT')) {

            $directory = $this->normalizePath(request()->input('dir_path').'/'.request()->input('new_folder_name'));

            if ($directory !== null) {

                $storage = Storage::disk($this->disk());

                if (! $storage->exists($directory)) {

                    $storage->makeDirectory($directory);

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
        }

        if (request()->wantsJson()) {
            return response()->json(['danger' => 'Invalid HTTP method!']);
        }

        return redirect()->back()->withMessage(['danger' => 'Invalid HTTP method!']);
    }

    /**
     * Create a new file.
     *
     * @return Response
     */
    public function newFile()
    {

        if (request()->isMethod('POST') || request()->isMethod('PUT')) {

            $file = $this->normalizePath(request()->input('dir_path').'/'.request()->input('new_file_name', 'new_file.txt'));

            if ($file !== null) {

                $storage = Storage::disk($this->disk());

                if (! $storage->exists($file)) {
                    $storage->put($file, '');

                    if (request()->wantsJson()) {
                        return response()->json(['success' => 'File created successfully!']);
                    }

                    return redirect()->back()->withMessage(['success' => 'File created successfully!']);
                } else {

                    if (request()->wantsJson()) {
                        return response()->json(['danger' => 'File already exists!']);
                    }

                    return redirect()->back()->withMessage(['danger' => 'File already exists!']);
                }
            }
        }

        if (request()->wantsJson()) {
            return response()->json(['danger' => 'Invalid HTTP method!']);
        }

        return redirect()->back()->withMessage(['danger' => 'Invalid HTTP method!']);
    }

    /**
     * Remove the specified resource from database.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy()
    {

        $requested = is_scalar(request()->input('file')) ? (string) request()->input('file') : '';

        $toDelete = $this->normalizePath($requested);

        $label = $toDelete ?? $requested;

        $storage = Storage::disk($this->disk());

        if ($toDelete === null || ! $storage->exists($toDelete)) {
            if (request()->wantsJson()) {
                return response()->json(['warning' => trans('File '.$label." doesn't exists")]);
            }

            return redirect()->back()->withMessage(['warning' => trans('File '.$label." doesn't exists")]);
        }

        if ($storage->fileExists($toDelete) && $storage->delete($toDelete)) {

            if (request()->wantsJson()) {
                return response()->json(['success' => trans('File deleted successfully')]);
            }

            return redirect()->back()->withMessage(['success' => trans('File deleted successfully')]);
        } elseif ($storage->directoryExists($toDelete) && $storage->deleteDirectory($toDelete)) {

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
}
