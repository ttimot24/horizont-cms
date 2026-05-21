<?php

namespace App\Interfaces;

interface PluginInterface
{
    public function webRouteOptions(): array;

    public function apiRouteOptions(): array;

    public function navigation(): array;

    public function eventHooks(): array;

    public function widget(): string;

    public function injectWebsiteJs(): array;

    public function injectAdminJs(): array;

    public function onInstall(): void;

    public function addProviders(): array;

    public function addMiddlewares(): array;

    public function addAliases(): array;

    public function cliCommands(): array;
}
