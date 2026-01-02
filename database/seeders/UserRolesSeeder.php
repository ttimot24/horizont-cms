<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('user_roles')->insert([
            'name' => 'Public',
            'permission' => 0,
            'rights' => NULL,
        ]);

        DB::table('user_roles')->insert([
            'name' => 'User',
            'permission' => 1,
            'rights' => NULL,
        ]);


        DB::table('user_roles')->insert([
            'name' => 'Member',
            'permission' => 2,
            'rights' => NULL,
        ]);

        DB::table('user_roles')->insert([
            'name' => 'Editor',
            'permission' => 3,
            'rights' => json_encode(["admin_area","blogpostcategory.view","blogpostcomment.view","blogpost.view","filemanager.view","headerimage.view","page.view","search.view","user.view",
                                                  "blogpostcategory.create","blogpostcomment.create","blogpost.create","filemanager.create","headerimage.create","page.create","search.create","user.create",
                                                  "blogpostcategory.update","blogpostcomment.update","blogpost.update","filemanager.update","headerimage.update","page.update","search.update","user.update",
                                                  "blogpostcategory.delete","blogpostcomment.delete","blogpost.delete","filemanager.delete","headerimage.delete","page.delete","search.delete","user.delete"]),   
        ]);

        DB::table('user_roles')->insert([
            'name' => 'Manager',
            'permission' => 4,
            'rights' => json_encode(["admin_area","blogpostcategory.view","blogpostcomment.view","blogpost.view","filemanager.view","headerimage.view","page.view","plugin.view","search.view","user.view","userrole.view",
                                                  "blogpostcategory.create","blogpostcomment.create","blogpost.create","filemanager.create","headerimage.create","page.create","plugin.create","search.create","user.create","userrole.create",
                                                  "blogpostcategory.update","blogpostcomment.update","blogpost.update","filemanager.update","headerimage.update","page.update","plugin.update","search.update","user.update","userrole.update",
                                                  "blogpostcategory.delete","blogpostcomment.delete","blogpost.delete","filemanager.delete","headerimage.delete","page.delete","plugin.delete","search.delete","user.delete","userrole.delete"]),
        ]);

         DB::table('user_roles')->insert([
            'name' => 'Admin',
            'permission' => 5,
            'rights' => json_encode(["admin_area","blogpostcategory.view","blogpostcomment.view","blogpost.view","filemanager.view","headerimage.view","page.view","plugin.view", "pluginregistry.view","schedule.view","search.view","settings.view","theme.view","upgrade.view","user.view","userrole.view", "log.view",
                                                  "blogpostcategory.create","blogpostcomment.create","blogpost.create","filemanager.create","headerimage.create","page.create","plugin.create", "pluginregistry.create","schedule.create","search.create","settings.create","theme.create","upgrade.create","user.create","userrole.create",
                                                  "blogpostcategory.update","blogpostcomment.update","blogpost.update","filemanager.update","headerimage.update","page.update","plugin.update", "pluginregistry.update","schedule.update","search.update","settings.update","theme.update","upgrade.update","user.update","userrole.update",
                                                  "blogpostcategory.delete","blogpostcomment.delete","blogpost.delete","filemanager.delete","headerimage.delete","page.delete","plugin.delete", "pluginregistry.delete","schedule.delete","search.delete","settings.delete","theme.delete","upgrade.delete","user.delete","userrole.delete"]),
        ]);


    }
}