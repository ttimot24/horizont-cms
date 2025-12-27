<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
    
        if(\App\HorizontCMS::isInstalled()){
        
        
            $jobs = \App\Model\ScheduledTask::where('active',1)->get();
        	
            \Settings::where('setting', 'scheduler')->update(['value' => uniqid("running-")."-jobs-".$jobs->count(), 'updated_at' => \Carbon\Carbon::now()]);
        
            foreach($jobs as $task){
                $schedule->command($task->command.' '.$task->arguments)->cron($task->frequency)
                ->before(function() use ($task) {
                   // \Log::info("Scheduled run : ".$task->name." [".$task->command."]");
                })
                ->pingBefore(empty($task->ping_before)? 'google.com' : $task->ping_before)->thenPing(empty($task->ping_after)? 'google.com' : $task->ping_after)->withoutOverlapping();
            }
        }
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {

        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
