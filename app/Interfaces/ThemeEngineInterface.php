<?php

namespace App\Interfaces;

use App\Services\Theme;

interface ThemeEngineInterface
{
    public function getTheme(): Theme;

    public function setTheme(Theme|string $theme): void;

    public function render(array $data);

    public function render404();

    public function renderWebsiteDown();
}
