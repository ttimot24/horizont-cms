<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class PluginController extends Controller
{


    public function before()
    {
        File::ensureDirectoryExists('plugins');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('plugin.index', [
            'all_plugin' => collect(File::directories(base_path() . DIRECTORY_SEPARATOR . "plugins"))->map(function ($dir) {
                return new \App\Model\Plugin(str_replace(base_path() . DIRECTORY_SEPARATOR . "plugins" . DIRECTORY_SEPARATOR, "", $dir));
            }),
            'zip_enabled' => class_exists('ZipArchive'),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->{$id}();
    }

    private function migrate(\App\Model\Plugin $plugin)
    {

        if ($plugin->getDatabaseFilesPath()) {
            return Artisan::call("migrate", ['--path' => $plugin->getDatabaseFilesPath() . DIRECTORY_SEPARATOR . "migrations", '--no-interaction' => '', '--force' => true]);
        }
        
        return null;
    }

    private function seed(\App\Model\Plugin $plugin)
    {
        if ($plugin->getDatabaseFilesPath()) {

            $seed_class = '\\Plugin\\' . $plugin->root_dir . '\\Database\\Seeds\\PluginSeeder';

            if (class_exists($seed_class)) {
                return Artisan::call('db:seed', ['--class' => $seed_class, '--no-interaction' => '', '--force' => true]);
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        try {

            $plugin = new \App\Model\Plugin($request->input('plugin'));

            if (!$plugin->isCompatibleWithCore()) {
                return redirect()->back()->withMessage(['warning' => trans('plugin.not_compatible_with_core', ['min_core_ver' => $plugin->getRequiredCoreVersion()])]);
            }

            $this->migrate($plugin);

            $this->seed($plugin);

            $plugin->getRegister('onInstall', []);

            $plugin->version = $plugin->getInfo('version');
            unset($plugin->info, $plugin->config, $plugin->default_image, $plugin->image);
            $plugin->area = 0;
            $plugin->permission = 0;
            $plugin->tables = "";
            $plugin->active = 0;


            $plugin->save();

            foreach (\App\Model\UserRole::all() as $role) {
                if ($role->isAdminRole()) {
                    foreach(["view","create","update","delete"] as $action){
                        $role->addRight(str_slug($plugin->root_dir).'.'.$action);
                    }
                    $role->save();
                }
            }

            return redirect()->back()->withMessage(['success' => trans('Succesfully installed ' . $plugin->root_dir)]);
        } catch (\Exception $e) {
            return redirect()->back()->withMessage(['danger' => trans('message.something_went_wrong') . " " . $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, \App\Model\Plugin $plugin)
    {
        $plugin = \App\Model\Plugin::rootDir($request->input('plugin_name'))->firstOrFail();

        if($request->has('active')){
            $plugin->active = $request->input('active');
        }

        if($request->has('upgrade')){
            try{
                $this->migrate($plugin);
                $this->seed($plugin);
                $plugin->getRegister('onUpgrade', [$plugin->version]);
                $plugin->version = $plugin->getInfo('version');
            } catch(\Exception $e){
                \Log::error("Plugin upgrade error for ".$plugin->root_dir.": ".$e->getMessage());
                return redirect()->back()->withMessage(['danger' => "Plugin upgrade error for ".$plugin->root_dir.": ".$e->getMessage()]);
            };
        }


        if ($plugin->save()) {
            return redirect()->back()->withMessage(['success' => trans('Succesfully modified ' . $plugin->root_dir)]);
        } else {
            return redirect()->back()->withMessage(['danger' => trans('message.something_went_wrong')]);
        }
    }

    /**
     * Remove the specified resource from database.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $plugin)
    {

        try {

            \App\Model\Plugin::rootDir($plugin)->delete();

            if (file_exists("plugins/" . $plugin)) {
                Storage::disk('plugins')->deleteDirectory($plugin);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withMessage(['danger' => trans('message.something_went_wrong') . " " . $e->getMessage()]);
        }

        return redirect()->back()->withMessage(['success' => trans('Succesfully deleted the plugin!')]);
    }


    public function upload()
    {

        if (request()->hasFile('up_file')) {

            $file_name = request()->up_file[0]->store('framework/temp');
        }

        $zip = new \ZipArchive;
        if ($zip->open("storage/" . $file_name) === TRUE) {
            $zip->extractTo('plugins/');
            $zip->close();

            Storage::delete("storage/" . $file_name);

            return redirect()->back()->withMessage(['success' => trans('Succesfully uploaded the plugin!')]);
        } else {
            return redirect()->back()->withMessage(['danger' => trans('message.something_went_wrong')]);
        }
    }
}
