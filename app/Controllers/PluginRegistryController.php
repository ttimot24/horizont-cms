<?php

namespace App\Controllers;

use App\Services\PluginRegistry;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PluginRegistryController extends Controller
{
    private readonly PluginRegistry $pluginRegistry;

    public function __construct(PluginRegistry $pluginRegistry)
    {
        File::ensureDirectoryExists('plugins');
        $this->pluginRegistry = $pluginRegistry;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function index()
    {
        $repo_status = true;

        try {

            $plugins = $this->pluginRegistry->fetchPlugins();

        } catch (\Exception $e) {
            Log::warning($e);
            $plugins = collect([]);
            $repo_status = false;
        }

        return view('plugin.store', ['online_plugins' => $plugins, 'repo_status' => $repo_status]);
    }

    public function store(Request $request)
    {
        $plugin_name = $request->input('plugin_name');

        if ($this->pluginRegistry->downloadPlugin($plugin_name)) {
            return redirect()->back()->withMessage(['success' => trans('Plugin downloaded successfully: '.$plugin_name.'')]);
        }

        return redirect()->back()->withMessage(['danger' => trans('Could not download the plugin: '.$plugin_name.'')]);
    }
}
