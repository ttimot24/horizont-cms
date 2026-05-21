<?php

namespace App\Services;

use App\Interfaces\ThemeEngineInterface;

class ThemeEngine implements ThemeEngineInterface
{
    protected ?\App\Services\Theme $theme = null;

    protected string $page_template = 'index';

    public \Illuminate\Http\Request $request;

    public function __construct(\Illuminate\Http\Request $request)
    {
        $this->request = $request;
    }

    public function getTheme(): \App\Services\Theme
    {
        return $this->theme;
    }

    public function setTheme(\App\Services\Theme|string $theme): void
    {
        $this->theme = is_string($theme) ? new \App\Services\Theme($theme) : $theme;
    }

    public function pageTemplate(string $page_template): void
    {
        $this->page_template = $page_template;
    }

    public function defaultTemplateExists(string $template): bool
    {
        return file_exists($this->theme->getPath().$template.'.php');
    }

    public function templateExists(string $template): bool
    {
        return file_exists($this->theme->getPath().'page_templates'.DIRECTORY_SEPARATOR.$template.'.php');
    }

    public function boot(): void
    {
        if ($this->theme === null) {
            throw new \Exception('<b>Theme is not set!</b>');
        }

        Website::initalize($this);
    }

    public function render(?array $data = null): string
    {
        if ($this->theme === null) {
            throw new \Exception('Theme is not set!');
        }

        ob_start();

        $this->require_file('header.php');
        $this->require_file('index.php');
        $this->require_file('footer.php');

        $output = ob_get_contents();
        ob_end_clean();

        return trim($output);
    }

    private function require_file(string $file): void
    {
        $filePath = base_path($this->theme->getPath().$file);
        if (file_exists($filePath)) {
            require_once $filePath;
        }
    }

    public function runScript(string $script_name)
    {

        $script = config('theme:theme.scripts.'.$script_name, null);
        if (is_callable($script)) {
            return call_user_func($script);
        }

        return null;
    }

    public function render404()
    {
        ob_start();

        if ($this->theme->has404Template()) {
            $this->require_file('404.php');
        } else {
            $this->require_file(resource_path('static/404.php'));
        }

        $output = ob_get_contents();
        ob_end_clean();

        return response(trim($output), 404);
    }

    public function renderWebsiteDown()
    {
        ob_start();

        if ($this->theme->hasWebsiteDownTemplate()) {
            $this->require_file('website_down.php');
        } else {
            $this->require_file(resource_path('static/website_down.php'));
        }

        $output = ob_get_contents();
        ob_end_clean();

        return response(trim($output), 503);
    }
}
