<?php

namespace App\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;


class DashboardController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $updater = new \Codedge\Updater\UpdaterManager(app());

        $admin_logo = request()->settings['admin_logo'];

        $widgets = collect([
            \App\Services\DashboardWidget::builder()
                ->setName(trans('dashboard.posted_news_count'))
                ->setIcon("fa-solid fa-newspaper")
                ->setValue(\App\Model\Blogpost::count())
                ->setLink(route("blogpost.index"))
                ->setColor("primary")
                ->build(),
            \App\Services\DashboardWidget::builder()
                ->setName(trans('dashboard.registered_users_count'))
                ->setIcon("fa-solid fa-users")
                ->setValue(\App\Model\User::count())
                ->setLink(route("user.index"))
                ->setColor("primary")
                ->build(),
            \App\Services\DashboardWidget::builder()
                ->setName(trans('dashboard.visits_count'))
                ->setIcon("fa-solid fa-eye")
                ->setValue(\App\Model\Visits::count())
                ->setLink("#")
                ->setColor("primary")
                ->build(),
        ]);

        return view("dashboard.index", [
            'domain' => request()->getHost(),
            'server_ip' => isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : "unknown",
            'client_ip' => request()->ip(),
            'widgets' => $widgets,
            'admin_logo' => ($admin_logo != "" && file_exists("storage/images/logos/" . $admin_logo)) ? "storage/images/logos/" . $admin_logo : \Config::get('horizontcms.admin_logo'),
            'disk_space' => @((disk_free_space("/") ?: 1) / (disk_total_space("/") ?: 1)) * 100,
            'upgrade' => request()->settings['auto_upgrade_check'] == 1 && Gate::allows('access', 'upgrade')? $updater->source() : null,
        ]);
    }

    public function show($method){
        return $this->{$method}();
    }


    public function unauthorized()
    {
        return view('errors.unauthorized');
    }

    public function notfound()
    {
        return view('errors.404');
    }
}
