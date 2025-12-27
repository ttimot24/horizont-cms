<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ThemeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hcms:theme {--set} {theme}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets the selected theme.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }



    public function handle(): void
    {

        $selectedTheme = $this->argument('theme');

        echo PHP_EOL . "Selected theme: " . $selectedTheme . PHP_EOL . PHP_EOL;

        if ($this->option('set')) {
            $this->set($selectedTheme);
        }
    }


    private function set($theme): void
    {
        if (file_exists(base_path('themes' . DIRECTORY_SEPARATOR . $theme))) {

            if (\App\Model\Settings::where('setting', 'theme')->update(['value' => $theme])) {
                echo $theme . " successfully set as current theme!" . PHP_EOL;
            } else {
                echo "Could not set theme!";
            }
        } else {
            echo "The selected theme doesn't exists.";
        }
    }
}
