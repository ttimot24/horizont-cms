<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PluginCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hcms:plugin {--install} {--uninstall} {--activate} {--deactivate} {--download} {--remove} {plugin} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install and activate plugin.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
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

        $this->line('Download...');
        throw new \Exception('This function is not supported yet!');
    }

    private function remove(string $selectedPlugin): int
    {

        $this->line('Remove...');
        throw new \Exception('This function is not supported yet!');
    }

    private function install(string $selectedPlugin): int
    {

        echo 'Install...'.PHP_EOL;
        throw new \Exception('This function is not supported yet!');
    }

    private function uninstall(string $selectedPlugin): int
    {

        echo 'Uninstall...'.PHP_EOL;
        throw new \Exception('This function is not supported yet!');
    }

    private function activate(string $selectedPlugin): int
    {

        echo 'Activate...'.PHP_EOL;

        if (\App\Model\Plugin::where('root_dir', $selectedPlugin)->update(['active' => 1])) {
            echo 'Plugin successfully activated!'.PHP_EOL;

            return self::SUCCESS;
        } else {
            echo 'Could not activate the plugin!'.PHP_EOL;

            return self::FAILURE;
        }
    }

    private function deactivate(string $selectedPlugin): int
    {

        echo 'Deactivate...'.PHP_EOL;

        if (\App\Model\Plugin::where('root_dir', $selectedPlugin)->update(['active' => 0])) {
            echo 'Plugin successfully deactivated!'.PHP_EOL;

            return self::SUCCESS;
        } else {
            echo 'Could not deactivate the plugin!'.PHP_EOL;

            return self::FAILURE;
        }
    }
}
