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

    public function handle(): int
    {

        $selectedTheme = $this->argument('theme');

        $this->line('');
        $this->line("Selected theme: {$selectedTheme}");
        $this->line('');

        if ($this->option('set')) {
            return $this->set($selectedTheme);
        }

        return self::INVALID;
    }

    private function set(string $theme): int
    {
        if (file_exists(base_path('themes'.DIRECTORY_SEPARATOR.$theme))) {

            if (\App\Model\Settings::where('setting', 'theme')->update(['value' => $theme])) {
                $this->line($theme.' successfully set as current theme!');

                return self::SUCCESS;
            } else {
                $this->line('Could not set theme!');

                return self::FAILURE;
            }
        } else {
            $this->line("The selected theme doesn't exists.");

            return self::INVALID;
        }
    }
}
