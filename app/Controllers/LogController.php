<?php

namespace App\Controllers;

use Illuminate\Routing\Controller;
use \Jackiedo\LogReader\Facades\LogReader;


class LogController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($file = null)
    {
        LogReader::setLogPath(dirname(\Config::get('logging.channels.' . \Config::get('logging.default') . '.path')));

        $entries = collect();
        $files = collect(LogReader::getLogFilenameList());

        if ($files->isNotEmpty()) {

            $current_file = empty($file)?  basename($files->last()) : $file;

            $entries = LogReader::filename($current_file)->orderBy('date', 'desc')->paginate(250);
        }

        // dd($entries);
        return view('settings.log', [
            'all_files' => $files->reverse(),
            'entries' => $entries,
            'current_file' => isset($current_file) ? $current_file : null,
            'max_files' => 15
        ]);

    }

    public function show($log){
        return $this->index($log);
    }


}
