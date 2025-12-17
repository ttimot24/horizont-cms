<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

use App\Model\Settings;

class SettingsController extends Controller
{

    public function before()
    {
        \File::ensureDirectoryExists('images/logos');
        \File::ensureDirectoryExists('images/favicons');
    }


    public function store(Request $request)
    {

        foreach ($request->all() as $key => $value) {
            Settings::updateOrCreate(['setting' => $key], ['value' => $value, 'more' => 1]);
        }

        Cache::forget('settings');

        return redirect()->back()->withMessage(['success' => trans('message.successfully_saved_settings')]);
    }

    private function getSettingsPanels()
    {


        return [
            ['name' => trans('settings.website'), 'link' => route('settings.show', ['setting' => 'website']), 'icon' => 'fa fa-globe'],
            ['name' => trans('settings.admin_area'), 'link' => route('settings.show', ['setting' => 'adminarea']), 'icon' => 'fa fa-desktop'],
            ['name' => trans('settings.update_center'), 'link' => route('upgrade.index'), 'icon' => 'fa fa-arrow-circle-up'],
            ['name' => trans('settings.server'), 'link' => route('settings.show', ['setting' => 'server']), 'icon' => 'fa fa-server'],
            ['name' => trans('settings.social_media'), 'link' => route('settings.show', ['setting' => 'socialmedia']), 'icon' => 'fa fa-thumbs-up'],
            ['name' => trans('Log'), 'link' => route('log.index'), 'icon' => 'fa fa-bug'],
            ['name' => trans('settings.database'), 'link' => route('settings.show', ['setting' => 'database']), 'icon' => 'fa fa-database'],
            ['name' => trans('settings.scheduler'), 'link' => route('schedule.index', ['setting' => 'schedules']), 'icon' => 'fa fa-clock'],
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('settings.index', [
            'panels' => $this->getSettingsPanels(),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->{$id}();
    }

    public function update(Request $request, $id)
    {

        if (\App\Model\Settings::where("setting", $request->input("setting"))->update(['value' => $id])) {
            return redirect()->back()->withMessage(['success' => trans('message.successfully_set_homepage')]);
        } 

        return redirect()->back()->withMessage(['danger' => trans('message.something_went_wrong')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function website()
    {
        return view('settings.website', [
            'available_logos' => array_slice(scandir("storage/images/logos"), 2),
            'user_roles' => \App\Model\UserRole::all(),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function adminarea()
    {
        return view('settings.adminarea', [
            'available_logos' => array_slice(scandir("storage/images/logos"), 2),
            'dateFormats' => ['Y.m.d H:i:s', 'Y-m-d H:i:s', 'Y. M. d H:i:s', 'd-m-Y H:i:s', 'd/m/Y H:i:s', 'm/d/Y H:i:s'],
        ]);
    }

    public function server()
    {
        return view('settings.server', [
            'server' => request()->server(),
        ]);
    }


    public function database()
    {

        switch (\Config::get('database.default')) {

            case 'mysql':
                $tables = \DB::select('SHOW TABLES');
                break;
            case 'pgsql':
                $tables = \DB::select('SELECT table_name FROM information_schema.tables ORDER BY table_name');
                break;
            case 'sqlite':
                $tables = \DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name");
                break;

            default:
                $tables = [['name' => 'Could not get table informations']];
        }

        return view('settings.database', [
            'tables' =>  $tables,

        ]);
    }



    public function socialmedia()
    {
        return view('settings.socialmedia', [
            'all_socialmedia' => \SocialLink::all(),
        ]);
    }

}
