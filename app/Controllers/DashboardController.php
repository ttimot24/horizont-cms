<?php

namespace App\Controllers;

use App\Model\Blogpost;
use App\Model\User;
use App\Model\Visits;
use App\Services\DashboardWidget;
use Codedge\Updater\UpdaterManager;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

        $updater = new UpdaterManager(app());

        $admin_logo = request()->settings['admin_logo'];

        $widgets = collect([
            DashboardWidget::builder()
                ->setName(trans('dashboard.posted_news_count'))
                ->setIcon('fa-solid fa-newspaper')
                ->setValue(Blogpost::count())
                ->setLink(route('blogpost.index'))
                ->setColor('primary')
                ->build(),
            DashboardWidget::builder()
                ->setName(trans('dashboard.registered_users_count'))
                ->setIcon('fa-solid fa-users')
                ->setValue(User::count())
                ->setLink(route('user.index'))
                ->setColor('primary')
                ->build(),
            DashboardWidget::builder()
                ->setName(trans('dashboard.visits_count'))
                ->setIcon('fa-solid fa-eye')
                ->setValue(Visits::count())
                ->setLink('#')
                ->setColor('primary')
                ->build(),
        ]);

        return view('dashboard.index', [
            'domain' => request()->getHost(),
            'server_ip' => isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : 'unknown',
            'client_ip' => request()->ip(),
            'widgets' => $widgets,
            'admin_logo' => ($admin_logo != '' && file_exists('storage/images/logos/'.$admin_logo)) ? 'storage/images/logos/'.$admin_logo : \Config::get('horizontcms.admin_logo'),
            'disk_space' => @((disk_free_space('/') ?: 1) / (disk_total_space('/') ?: 1)) * 100,
            'upgrade' => request()->settings['auto_upgrade_check'] == 1 && Gate::allows('access', 'upgrade') ? $updater->source() : null,
        ]);
    }

    public function show($method)
    {
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
