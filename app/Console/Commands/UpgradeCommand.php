<?php

namespace App\Console\Commands;

use Codedge\Updater\UpdaterManager;
use Illuminate\Console\Command;

class UpgradeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hcms:upgrade';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upgrades the system core.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    private ?UpdaterManager $updateManager = null;

    public function __construct(UpdaterManager $updater)
    {
        parent::__construct();

        $this->updateManager = $updater;
    }

    public function handle(): void
    {

        try {

            if ($this->updateManager->source()->isNewVersionAvailable()) {

                $latestVersion = $this->updateManager->source()->getVersionAvailable();

                // Install new update
                echo 'New Version: '.$latestVersion.'<br>';
                echo 'Installing Updates: <br>';

                $release = $this->updateManager->source()->fetch($latestVersion);

                $result = $this->updateManager->source()->update($release);

                echo $result ? 'Update successful<br>' : 'Update failed: '.$result.'!<br>';
            } else {
                echo 'Current Version is up to date<br>';
            }
        } catch (\Exception $e) {
            exit('Could not check for updates! '.$e->getMessage());
        }
    }
}
