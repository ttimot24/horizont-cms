<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchedulerSeeder extends Seeder
{
    /**
     * Run the database seeds.
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
            'updated_at' => null,
            'active' => 1,
        ]);

    }
}
