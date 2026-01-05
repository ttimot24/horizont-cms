<?php

namespace App;

use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;

class HorizontCMS extends Application
{

    // TODO Settings should be added here
    public Collection $plugins;

    public function __construct($basePath = null)
    {
        parent::__construct($basePath);

        $this->plugins = new Collection();
    }

    public static function isInstalled(): bool
    {
        return file_exists(base_path(".env")) || config('horizontcms.installed', false);
    }

    public function publicPath($path = ''): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR;
    }

    public function setPlugins(Collection $plugins): void {
        $this->plugins = $plugins;
    }

    public function getPlugins(): Collection {
        return $this->plugins;
    }

}
