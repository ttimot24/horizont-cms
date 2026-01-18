<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class SchedulerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
  
        DB::table('schedules')->insert([
            'id' => 1,
            'name' => 'Async Queue Worker',
            'command' => 'queue:work',
            'arguments' => '--stop-when-empty >> /dev/null 2>&1',
            'frequency' => '* * * * *',
            'author_id' => 1,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => NULL,
            'active' => 1
        ]);


    }
}
