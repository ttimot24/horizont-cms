<?php

namespace App\Services;

use App\Model\Plugin;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Madnest\Madzipper\Madzipper;

class PluginRegistry
{
    private readonly string $apiPath;

    public function __construct()
    {
        $this->apiPath = config('horizontcms.hcms_store_url').'/api/v1/plugins';
        File::ensureDirectoryExists('plugins');
    }

    public function getApiUrl(): string
    {
        return $this->apiPath;
    }

    public function client(): PendingRequest
    {
        $http = Http::timeout(30);

        if ($token = config('horizontcms.hcms_store_token')) {
            $http = $http->withToken($token);
        }

        return $http;
    }

    public function fetchPlugins(): Collection
    {
        $response = $this->client()->get($this->getApiUrl());

        return collect($response->object())->map(function ($plugin) {
            $local = new Plugin($plugin->dir);
            $plugin->local = $local->exists() ? $local : null;

            return $plugin;
        });
    }

    public function downloadPlugin(string $plugin_name): bool
    {

        $tempZip = "framework/temp/{$plugin_name}.zip";

        Storage::disk('local')->makeDirectory('framework/temp');

        $path = Storage::disk('local')->path($tempZip);

        $response = $this->client()->sink($path)->get($this->apiPath."/{$plugin_name}/download");

        if ($response->successful()) {

            $zipper = new Madzipper;
            $zipper->make(storage_path().DIRECTORY_SEPARATOR.$tempZip);

            if ($zipper->contains($plugin_name.'/plugin_info.xml')) {
                $zipper->folder($plugin_name)->extractTo('plugins/'.$plugin_name);
            } else {
                $zipper->extractTo('plugins/'.$plugin_name);
            }

            $zipper->close();

            if (file_exists('plugins/'.$plugin_name)) {
                @Storage::delete('framework'.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.$plugin_name.'.zip');
            }

            return true;
        }

        return false;
    }
}
