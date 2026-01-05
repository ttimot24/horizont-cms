<?php

namespace App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Model\UserRole;

class UserRoleController extends Controller
{

    public function index()
    {
        return view('users.roles.index', [
            'all_user_roles' => \App\Model\UserRole::all(),
            'permission_list' => $this->getPermissionList(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.roles.create', [
            'permission_list' => $this->getPermissionList(),
        ]);
    }

    private function getEscapedRights(Request $request){
        $actions = ['view', 'create', 'update', 'delete'];

        $rights = array_map(function ($item) use ($actions) {
            foreach ($actions as $action) {
                if (str_ends_with($item, '_' . $action)) {
                    return preg_replace('/_(?=' . $action . '$)/', '.', $item);
                }
            }
            return $item;
        }, array_keys($request->except(['_token', 'name', '_method'])));

        return $rights;
    }

    private function getPermissionList()
    {

        $controllers = array_slice(scandir(app_path('Controllers')), 3);

        $permission_list = [];

        foreach ($controllers as $controller) {
            
            if (!str_ends_with($controller, 'Controller.php') || in_array($controller, ['WebsiteController.php', 'InstallController.php', 'DashboardController.php'])) {
                continue;
            }

            $name = str_replace("Controller.php", "", $controller);
            $permission_list[str_slug($name)] = $name;
            
        }

        foreach (\App\Model\Plugin::all() as $plugin) {

            try {
                if ($plugin->isActive()) {
                    $permission_list[str_slug($plugin->root_dir)] = $plugin->getName();
                }
            } catch (\Exception $e) {
            }
        }


        return $permission_list;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $role = new \App\Model\UserRole($request->all());
        $role->rights = $this->getEscapedRights($request);
        $role->permission = 0;

        if ($role->save()) {
            return redirect(route('userrole.index'))->withMessage(['success' => trans('User role created succesfully!')]);
        } else {
            return redirect()->back()->withMessage(['danger' => trans('message.something_went_wrong')]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return null;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('users.roles.edit', [
            'userrole' => \App\Model\UserRole::findOrFail($id),
            'permission_list' => $this->getPermissionList(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRole $userrole, Request $request)
    {

        $userrole->rights = $this->getEscapedRights($request);

        if ($userrole->save()) {
            return redirect()->back()->withMessage(['success' => trans('Rights saved succesfully!')]);
        } else {
            return redirect()->back()->withMessage(['danger' => trans('message.something_went_wrong')]);
        }
    }

    /**
     * Remove the specified resource from database.
     *
     * @param  \App\Model\UserRole  $userrole
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserRole $userrole)
    {

        if ($userrole->delete()) {
            return redirect()->back()->withMessage(['success' => trans('User role deleted succesfully!')]);
        }

        return redirect()->back()->withMessage(['danger' => trans('message.something_went_wrong')]);
    }
}
