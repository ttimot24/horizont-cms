<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(SettingsSeeder::class);
        $this->call(UserRolesSeeder::class);
        $this->call(ContentSeeder::class);
    }
}
