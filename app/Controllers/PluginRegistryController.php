<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Madnest\Madzipper\Madzipper;

class PluginRegistryController extends Controller
{


    public function before()
    {
        File::ensureDirectoryExists('plugins');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $repo_status = true;

        try {
            $response = Http::get(config('horizontcms.sattelite_url') . '/api/v1/plugins');

            $plugins = collect($response->object())->map(function ($plugin) { 

                $local = new \App\Model\Plugin($plugin->dir);

                $plugin->local = $local->exists() ? $local : null;
                
                return $plugin; 
            });

        } catch (\Exception $e) {
            \Log::warning($e);
            $plugins = collect([]);
            $repo_status = false;
        }

        return view('plugin.store', ['online_plugins' => $plugins, 'repo_status' => $repo_status]);
    }

    public function store(Request $request)
    {
        $plugin_name = $request->input('plugin_name');

        $tempZip = "framework/temp/{$plugin_name}.zip";

        Storage::disk('local')->makeDirectory('framework/temp');

        $path = Storage::disk('local')->path($tempZip);

        $response = Http::sink($path)->get(
            config('horizontcms.sattelite_url') . "/api/v1/plugins/{$plugin_name}/download"
        );

        if ($response->successful()) {

            $zipper = new Madzipper();

            $zipper->make(storage_path() . DIRECTORY_SEPARATOR . $tempZip)->folder($plugin_name)->extractTo('plugins' . DIRECTORY_SEPARATOR . $plugin_name);

            if (file_exists("plugins/" . $plugin_name)) {
                @Storage::delete("framework" . DIRECTORY_SEPARATOR . "temp" . DIRECTORY_SEPARATOR . $plugin_name . ".zip");
                return redirect()->back()->withMessage(['success' => trans('Succesfully downloaded ' . $plugin_name)]);
            } else {
                return redirect()->back()->withMessage(['danger' => trans('Could not extract the plugin: ' . $plugin_name . "")]);
            }
        } else {
            return redirect()->back()->withMessage(['danger' => trans('Could not download the plugin: ' . $plugin_name . "")]);
        }
    }

}
