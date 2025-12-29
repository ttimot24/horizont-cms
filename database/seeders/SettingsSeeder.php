<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'title',
            'value' => "Your title",
            
        ]);


        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'site_name',
            'value' => "Your site",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'slogan',
            'value' => "Your awesome slogan",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'favicon',
            'value' => "",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'scroll_text',
            'value' => "",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'default_email',
            'value' => "",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'address',
            'value' => "",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'default_phone',
            'value' => "",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'contact',
            'value' => "",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'theme',
            'value' => 'TheWright',
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'language',
            'value' => 'en',
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'date_format',
            'value' => "Y.m.d H:i:s",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'home_page',
            'value' => 1,
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'website_down',
            'value' => "0",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'logo',
            'value' => "",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'favicon',
            'value' => "",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'admin', 
            'setting' => 'admin_logo',
            'value' => "",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'website_debug',
            'value' => "0",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'admin', 
            'setting' => 'admin_debug',
            'value' => "0",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'admin', 
            'setting' => 'admin_broadcast',
            'value' => "",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'website_type',
            'value' => "website",
            
        ]);
        
        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'blogposts_on_page',
            'value' => "5",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'admin', 
            'setting' => 'default_user_role',
            'value' => "2",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'admin', 
            'setting' => 'auto_upgrade_check',
            'value' => "1",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'use_https',
            'value' => "0",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'social_link_facebook',
            'value' => "",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'social_link_youtube',
            'value' => "",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'social_link_twitter',
            'value' => "",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'social_link_instagram',
            'value' => "",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'social_link_google',
            'value' => "",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'social_link_linkedin',
            'value' => "",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'social_link_pinterest',
            'value' => "",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'social_link_github',
            'value' => "",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'social_link_gitlab',
            'value' => "",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'social_link_spotify',
            'value' => "",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'social_link_soundcloud',
            'value' => "",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'social_link_tiktok',
            'value' => "",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'social_link_steam',
            'value' => "",
            
        ]);

        DB::table('settings')->insert([
            'group' => 'website', 
            'setting' => 'social_link_reddit',
            'value' => "",
            
        ]);


        DB::table('settings')->insert([
            'group' => 'admin', 
            'setting' => 'scheduler',
            'value' => "not configured",
            
        ]);

    }
}

