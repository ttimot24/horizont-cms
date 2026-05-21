<?php

namespace App\Interfaces;

interface ThemeEngineInterface
{
    public function getTheme(): \App\Services\Theme;

    public function setTheme(\App\Services\Theme|string $theme): void;

    public function render(array $data);

    public function render404();

    public function renderWebsiteDown();
}
