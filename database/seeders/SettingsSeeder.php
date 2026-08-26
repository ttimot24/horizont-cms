<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('settings')->insertOrIgnore([
            [
                'group' => 'website',
                'setting' => 'title',
                'value' => 'Your title',
            ],
            [
                'group' => 'website',
                'setting' => 'site_name',
                'value' => 'Your site',
            ],
            [
                'group' => 'website',
                'setting' => 'slogan',
                'value' => 'Your awesome slogan',
            ],
            [
                'group' => 'website',
                'setting' => 'favicon',
                'value' => '',
            ],
            [
                'group' => 'website',
                'setting' => 'scroll_text',
                'value' => '',
            ],
            [
                'group' => 'website',
                'setting' => 'default_email',
                'value' => '',
            ],
            [
                'group' => 'website',
                'setting' => 'address',
                'value' => '',
            ],
            [
                'group' => 'website',
                'setting' => 'default_phone',
                'value' => '',
            ],
            [
                'group' => 'website',
                'setting' => 'contact',
                'value' => '',
            ],
            [
                'group' => 'website',
                'setting' => 'theme',
                'value' => 'TheWright',
            ],
            [
                'group' => 'website',
                'setting' => 'language',
                'value' => 'en',
            ],
            [
                'group' => 'website',
                'setting' => 'date_format',
                'value' => 'Y.m.d H:i:s',
            ],
            [
                'group' => 'website',
                'setting' => 'home_page',
                'value' => '1',
            ],
            [
                'group' => 'website',
                'setting' => 'website_down',
                'value' => '0',
            ],
            [
                'group' => 'website',
                'setting' => 'logo',
                'value' => '',
            ],
            [
                'group' => 'admin',
                'setting' => 'admin_logo',
                'value' => '',
            ],
            [
                'group' => 'website',
                'setting' => 'website_debug',
                'value' => '0',
            ],
            [
                'group' => 'admin',
                'setting' => 'admin_debug',
                'value' => '0',
            ],
            [
                'group' => 'admin',
                'setting' => 'admin_broadcast',
                'value' => '',
            ],
            [
                'group' => 'website',
                'setting' => 'website_type',
                'value' => 'website',
            ],
            [
                'group' => 'website',
                'setting' => 'blogposts_on_page',
                'value' => '5',
            ],
            [
                'group' => 'admin',
                'setting' => 'default_user_role',
                'value' => '2',
            ],
            [
                'group' => 'admin',
                'setting' => 'auto_upgrade_check',
                'value' => '1',
            ],
            [
                'group' => 'website',
                'setting' => 'use_https',
                'value' => '0',
            ],
            [
                'group' => 'website',
                'setting' => 'social_link_facebook',
                'value' => '',
            ],
            [
                'group' => 'website',
                'setting' => 'social_link_youtube',
                'value' => '',
            ],
            [
                'group' => 'website',
                'setting' => 'social_link_twitter',
                'value' => '',
            ],
            [
                'group' => 'website',
                'setting' => 'social_link_instagram',
                'value' => '',
            ],
            [
                'group' => 'website',
                'setting' => 'social_link_google',
                'value' => '',
            ],
            [
                'group' => 'website',
                'setting' => 'social_link_linkedin',
                'value' => '',
            ],
            [
                'group' => 'website',
                'setting' => 'social_link_pinterest',
                'value' => '',
            ],
            [
                'group' => 'website',
                'setting' => 'social_link_github',
                'value' => '',
            ],
            [
                'group' => 'website',
                'setting' => 'social_link_gitlab',
                'value' => '',
            ],
            [
                'group' => 'website',
                'setting' => 'social_link_spotify',
                'value' => '',
            ],
            [
                'group' => 'website',
                'setting' => 'social_link_soundcloud',
                'value' => '',
            ],
            [
                'group' => 'website',
                'setting' => 'social_link_tiktok',
                'value' => '',
            ],
            [
                'group' => 'website',
                'setting' => 'social_link_steam',
                'value' => '',
            ],
            [
                'group' => 'website',
                'setting' => 'social_link_reddit',
                'value' => '',
            ],
            [
                'group' => 'admin',
                'setting' => 'scheduler',
                'value' => 'not configured',
            ],
        ]);

    }
}
