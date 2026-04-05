<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use App\Services\DashboardWidget;

use App\Model\Settings;

class SettingsController extends Controller
{

    public function before()
    {
        File::ensureDirectoryExists('images/logos');
        File::ensureDirectoryExists('images/favicons');
    }


    public function store(Request $request)
    {

        foreach ($request->all() as $key => $value) {
            Settings::updateOrCreate(['setting' => $key], ['value' => $value]);
        }

        Cache::forget('settings');

        return redirect()->back()->withMessage(['success' => trans('message.successfully_saved_settings')]);
    }

    private function getSettingsPanels()
    {

        return collect([
            DashboardWidget::builder()
                ->setName(trans('settings.website'))
                ->setIcon("fa fa-globe")
                ->setLink(route('settings.show', ['setting' => 'website']))
                ->build(),
            DashboardWidget::builder()
                ->setName(trans('settings.admin_area'))
                ->setIcon("fa fa-desktop")
                ->setLink(route('settings.show', ['setting' => 'adminarea']))
                ->build(),
            DashboardWidget::builder()
                ->setName(trans('settings.update_center'))
                ->setIcon("fa fa-arrow-circle-up")
                ->setLink(route('upgrade.index'))
                ->build(),
            DashboardWidget::builder()
                ->setName(trans('settings.server'))
                ->setIcon("fa fa-server")
                ->setLink(route('settings.show', ['setting' => 'server']))
                ->build(),
            DashboardWidget::builder()
                ->setName(trans('settings.social_media'))
                ->setIcon("fa fa-thumbs-up")
                ->setLink(route('settings.show', ['setting' => 'socialmedia']))
                ->build(),
            DashboardWidget::builder()
                ->setName(trans('Log'))
                ->setIcon("fa fa-bug")
                ->setLink(route('log.index'))
                ->build(),
            DashboardWidget::builder()
                ->setName(trans('settings.database'))
                ->setIcon("fa fa-database")
                ->setLink(route('settings.show', ['setting' => 'database']))
                ->build(),
            DashboardWidget::builder()
                ->setName(trans('settings.scheduler'))
                ->setIcon("fa fa-clock")
                ->setLink(route('schedule.index'))
                ->build()
            ]);
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

        switch (Config::get('database.default')) {

            case 'mysql':
                $tables = DB::select('SHOW TABLES');
                break;
            case 'pgsql':
                $tables = DB::select('SELECT table_name FROM information_schema.tables ORDER BY table_name');
                break;
            case 'sqlite':
                $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name");
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
