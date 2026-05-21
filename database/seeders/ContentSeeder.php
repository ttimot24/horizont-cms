<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pages')->insert([
            'id' => 1,
            'name' => 'Home',
            'slug' => 'home',
            'url' => '',
            'visibility' => 1,
            'parent_id' => null,
            'queue' => 1,
            'page' => 'Welcome on the homepage of your site!',
            'author_id' => 1,
            'image' => '',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => null,
            'active' => 1,
        ]);

        DB::table('pages')->insert([
            'id' => 2,
            'name' => 'Blog',
            'slug' => 'blog',
            'language' => 'en',
            'url' => 'blog.php',
            'visibility' => 1,
            'parent_id' => null,
            'queue' => 1,
            'page' => '',
            'author_id' => 1,
            'image' => '',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => null,
            'active' => 1,
        ]);

        DB::table('blogposts')->insert([
            'id' => 1,
            'title' => 'Welcome to HorizontCMS!',
            'slug' => 'welcome-to-horizontcms',
            'language' => 'en',
            'summary' => 'Your very first post.',
            'text' => 'If you see this, the install was successfull!',
            'comments_enabled' => 1,
            'author_id' => 1,
            'image' => '',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => null,
            'active' => 1,
        ]);

        DB::table('blogpost_categories')->insert([
            'id' => 1,
            'name' => 'default',
            'language' => 'en',
            'author_id' => 1,
            'image' => '',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => null,
        ]);

        DB::table('blogpost_categories_pivot')->insert([
            'id' => 1,
            'blogpost_id' => 1,
            'blogpost_category_id' => 1,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => null,
        ]);

        DB::table('header_images')->insert([
            'id' => 1,
            'title' => 'default',
            'type' => 'image',
            'image' => 'abovethecity.jpg',
            'author_id' => 1,
            'active' => 1,
        ]);

    }
}
