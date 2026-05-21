<?php

namespace App\Services;

use App\Interfaces\ThemeEngineInterface;

class BladeThemeEngine implements ThemeEngineInterface
{
    protected \App\Services\Theme $theme;

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
        return file_exists($this->theme->getPath().$template.'.blade.php');
    }

    public function templateExists(string $template): bool
    {
        return file_exists(
            $this->theme->getPath().'page_templates'.DIRECTORY_SEPARATOR.$template
        );
    }

    public function render(array $data): \Illuminate\View\View
    {
        $default_data = [
            '_THEME_PATH' => str_replace(DIRECTORY_SEPARATOR, '/', $this->theme->getPath()),
        ];

        \View::addNamespace('theme', base_path($this->theme->getPath()));

        return view(
            'theme::'.str_replace('.blade.php', '', $this->page_template),
            array_merge($default_data, $data)
        );
    }

    public function runScript(string $script_name)
    {
        $script = config('theme:theme.scripts.'.$script_name, null);
        if (is_callable($script)) {
            return call_user_func($script);
        }

        return null;
    }

    public function render404(): void
    {
        $this->page_template = $this->theme->has404Template() ? '404' : 'default.404';
    }

    public function renderWebsiteDown(): void
    {
        $this->page_template = $this->theme->hasWebsiteDownTemplate() ? 'website_down' : 'default.website_down';
    }
}
