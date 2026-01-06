<?php

namespace App\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class InstallController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        File::ensureDirectoryExists('framework/upgrade/cache');

        return view("install.index", ['enable_continue' => true]);
    }

    public function show($id){
        return $this->{$id}();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function step1()
    {
        // TODO Read language file list
        $languages = ['English', 'Magyar', 'Deutsch'];

        return view("install.step1", ['languages' => $languages]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function step2()
    {

        foreach (Config::get('database.connections') as $database) {
            $db_drivers[$database['alias']] = $database['driver'];
        }

        return view("install.step2", ['db_drivers' => $db_drivers]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function checkConnection()
    {
        request()->flash();

        try {

            switch (request()->input('db_driver', 'mysql')) {

                case 'mysql':

                    new \PDO(
                        'mysql:host=' . request()->input('server') . ';database=' . request()->input('database'),
                        request()->input('username'),
                        request()->input('password')
                    );

                    break;

                case 'pgsql':

                    new \PDO(
                        "pgsql:dbname=" . request()->input('database') . ";host=" . request()->input('server'),
                        request()->input('username'),
                        request()->input('password')
                    );

                    break;

                case 'sqlite':

                    $database = request()->input('database');

                    (substr($database, -3) === ".db") ?: $database .= '.db';


                    $sqlite = base_path('database' . DIRECTORY_SEPARATOR . $database);

                    request()->merge(['database' => $sqlite]);

                    new \PDO('sqlite:' . $sqlite);

                    break;
            }

            Session::put('step2', request()->all());

            return redirect(route('install.show', 'step3'))->withMessage(['success' => trans('Connection to database established!')]);
        } catch (\PDOException $except) {
            return redirect()->back()->withMessage(['danger' => trans('Can not establish the connection: ' . $except->getMessage())]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function step3()
    {

        request()->flash();

        return view("install.step3");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function migrate()
    {

        request()->flash();

        try {
            Config::set('database.default', Session::get('step2.db_driver'));
            Config::set('database.connections.' . Session::get('step2.db_driver') . '.host', Session::get('step2.server'));
            Config::set('database.connections.' . Session::get('step2.db_driver') . '.username', Session::get('step2.username'));
            Config::set('database.connections.' . Session::get('step2.db_driver') . '.password', Session::get('step2.password'));
            Config::set('database.connections.' . Session::get('step2.db_driver') . '.database', Session::get('step2.database'));
            Config::set('database.connections.' . Session::get('step2.db_driver') . '.prefix', Session::get('step2.prefix'));

            Artisan::call("migrate", ["--force" => true]);
            Artisan::call("db:seed", ["--force" => true]);

            $administrator = new \App\Model\User();
            $administrator->name = 'Administrator';
            $administrator->username = request()->input('ad_username');
            $administrator->slug = str_slug(request()->input('ad_username'));
            $administrator->password = request()->input('ad_password');
            $administrator->email = request()->input('ad_email');
            $administrator->role_id = 6;
            $administrator->active = 1;

            if (!$administrator->save()) {
                return redirect(route('install.show', 'step4'))->withMessage(['danger' => 'Something went wrong!'])->withError(true);
            }
        } catch (\Exception $e) {

            return redirect(route('install.show', 'step4'))->withMessage(['danger' => $e->getMessage()])->withError(true);
        }

        return redirect(route('install.show', 'step4'))->withMessage(['success' => 'Succesfully installed!']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function step4()
    {

        if (!Session::has('error')) {
            $dotenv = new \App\Services\DotEnvGenerator(); //TODO Inject
            $dotenv->addEnvVar('DB_HOST', Session::get('step2.server'));
            $dotenv->addEnvVar('DB_CONNECTION',  Session::get('step2.db_driver'));
            $dotenv->addEnvVar('DB_USERNAME',  Session::get('step2.username'));
            $dotenv->addEnvVar('DB_PASSWORD',  Session::get('step2.password'));
            $dotenv->addEnvVar('DB_DATABASE',  Session::get('step2.database'));
            $dotenv->addEnvVar('DB_TABLE_PREFIX',  Session::get('step2.prefix'));
            $dotenv->generate();
        }

        return view("install.step4");
    }
}
