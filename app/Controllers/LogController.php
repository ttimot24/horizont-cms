<?php

namespace App\Controllers;

use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Jackiedo\LogReader\Facades\LogReader;

class LogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($file = null)
    {
        LogReader::setLogPath(dirname(Config::get('logging.channels.'.Config::get('logging.default').'.path')));

        $files = collect(LogReader::getLogFilenameList());

        $current_file = null;
        $entries = new LengthAwarePaginator([], 0, 250);

        if ($files->isNotEmpty()) {

            $current_file = empty($file) ? basename($files->last()) : $file;

            if (! $files->has($current_file)) {
                $current_file = basename($files->last());
            }

            $entries = LogReader::filename($current_file)->orderBy('date', 'desc')->paginate(250);
        }

        if (request()->wantsJson()) {
            return response()->json($entries);
        }

        return view('settings.log', [
            'all_files' => $files->reverse(),
            'entries' => $entries,
            'current_file' => $current_file,
            'max_files' => 15,
        ]);

    }

    public function show($log)
    {
        return $this->index($log);
    }
}
