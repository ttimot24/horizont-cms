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

    public function handle(): int
    {

        try {

            if ($this->updateManager->source()->isNewVersionAvailable()) {

                $latestVersion = $this->updateManager->source()->getVersionAvailable();

                // Install new update
                $this->line('New Version: '.$latestVersion);
                $this->line('Installing Updates: ');

                $release = $this->updateManager->source()->fetch($latestVersion);

                $result = $this->updateManager->source()->update($release);

                if ($result) {
                    $this->line('Update successful!');

                    return self::SUCCESS;
                }

                $this->error('Update failed: '.$result.'!');

                return self::FAILURE;
            } else {
                $this->line('Current Version is up to date');

                return self::SUCCESS;
            }
        } catch (\Exception $e) {
            $this->error('Could not check for updates! '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
