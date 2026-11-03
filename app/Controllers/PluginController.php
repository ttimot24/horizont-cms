<?php

namespace App\Controllers;

use App\Model\Plugin;
use App\Services\PluginService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PluginController extends Controller
{
    public function before()
    {
        File::ensureDirectoryExists('plugins');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('plugin.index', [
            'all_plugin' => collect(File::directories(base_path().DIRECTORY_SEPARATOR.'plugins'))->map(function ($dir) {
                return new Plugin(str_replace(base_path().DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR, '', $dir));
            }),
            'zip_enabled' => class_exists('ZipArchive'),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return $this->{$id}();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request, PluginService $pluginService)
    {

        try {

            $plugin = new Plugin($request->input('plugin'));

            $pluginService->install($plugin);

            return redirect()->back()->withMessage(['success' => trans('Succesfully installed '.$plugin->root_dir)]);
        } catch (\Exception $e) {
            return redirect()->back()->withMessage(['danger' => trans('message.something_went_wrong').' '.$e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, Plugin $plugin, PluginService $pluginService)
    {
        $plugin = Plugin::rootDir($request->input('plugin_name'))->firstOrFail();

        if ($request->has('active')) {
            $plugin->active = $request->input('active');
        }

        if ($request->has('upgrade')) {
            try {
                $pluginService->migrate($plugin);
                $pluginService->seed($plugin);
                $plugin->getRegister('onUpgrade', [$plugin->version]);
                $plugin->version = $plugin->getInfo('version');
            } catch (\Exception $e) {
                Log::error('Plugin upgrade error for '.$plugin->root_dir.': '.$e->getMessage());

                return redirect()->back()->withMessage(['danger' => 'Plugin upgrade error for '.$plugin->root_dir.': '.$e->getMessage()]);
            }
        }

        if ($plugin->save()) {
            return redirect()->back()->withMessage(['success' => trans('Succesfully modified '.$plugin->root_dir)]);
        } else {
            return redirect()->back()->withMessage(['danger' => trans('message.something_went_wrong')]);
        }
    }

    /**
     * Remove the specified resource from database.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(string $plugin, PluginService $pluginService)
    {

        try {
            $pluginService->remove(new Plugin($plugin));
        } catch (\Exception $e) {
            return redirect()->back()->withMessage(['danger' => trans('message.something_went_wrong').' '.$e->getMessage()]);
        }

        return redirect()->back()->withMessage(['success' => trans('Succesfully deleted the plugin!')]);
    }

    public function upload()
    {

        if (request()->hasFile('up_file')) {

            $file_name = request()->up_file[0]->store('framework/temp');

            $zip = new \ZipArchive;
            if ($zip->open('storage/'.$file_name) === true) {
                $zip->extractTo('plugins/');
                $zip->close();

                Storage::delete('storage/'.$file_name);

                return redirect()->back()->withMessage(['success' => trans('Succesfully uploaded the plugin!')]);
            } else {
                return redirect()->back()->withMessage(['danger' => trans('message.something_went_wrong')]);
            }

        }

        return redirect()->back()->withMessage(['danger' => trans('No file uploaded!')]);
    }
}
