<?php

namespace App\Console\Commands;

use App\Model\Plugin;
use App\Services\PluginRegistry;
use Illuminate\Console\Command;

class PluginCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'hcms:plugin {--install} {--uninstall} {--activate} {--deactivate} {--download} {--remove} {plugin} ';

    /**
     * The console command description.
     */
    protected $description = 'Install and activate plugin.';

    private readonly PluginRegistry $pluginRegistry;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(PluginRegistry $pluginRegistry)
    {
        parent::__construct();
        $this->pluginRegistry = $pluginRegistry;
    }

    public function handle(): int
    {

        $selectedPlugin = $this->argument('plugin');

        $this->line('');
        $this->line("Selected plugin: {$selectedPlugin}");
        $this->line('');

        return match (true) {
            $this->option('download') => $this->download($selectedPlugin),
            $this->option('install') => $this->install($selectedPlugin),
            $this->option('activate') => $this->activate($selectedPlugin),
            $this->option('deactivate') => $this->deactivate($selectedPlugin),
            $this->option('uninstall') => $this->uninstall($selectedPlugin),
            $this->option('remove') => $this->remove($selectedPlugin),

            default => self::INVALID,
        };
    }

    private function download(string $selectedPlugin): int
    {

        $this->line('Download: '.$selectedPlugin);

        if ($this->pluginRegistry->downloadPlugin($selectedPlugin)) {
            $this->info('Plugin successfully downloaded!');

            return self::SUCCESS;
        } else {
            $this->error('Could not download the plugin!');

            return self::FAILURE;
        }
    }

    private function remove(string $selectedPlugin): int
    {

        $this->line('Remove...');
        throw new \Exception('This function is not supported yet!');
    }

    private function install(string $selectedPlugin): int
    {

        $this->line('Install...');
        throw new \Exception('This function is not supported yet!');
    }

    private function uninstall(string $selectedPlugin): int
    {

        $this->line('Uninstall...');
        throw new \Exception('This function is not supported yet!');
    }

    private function activate(string $selectedPlugin): int
    {

        $this->line('Activate...');

        if (Plugin::rootDir($selectedPlugin)->update(['active' => 1])) {
            $this->info('Plugin successfully activated!');

            return self::SUCCESS;
        } else {
            $this->error('Could not activate the plugin!');

            return self::FAILURE;
        }
    }

    private function deactivate(string $selectedPlugin): int
    {

        $this->line('Deactivate...');

        if (Plugin::rootDir($selectedPlugin)->update(['active' => 0])) {
            $this->info('Plugin successfully deactivated!');

            return self::SUCCESS;
        } else {
            $this->error('Could not deactivate the plugin!');

            return self::FAILURE;
        }
    }
}
