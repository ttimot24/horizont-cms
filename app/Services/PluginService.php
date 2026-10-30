<?php

namespace App\Services;

use App\Model\Plugin;
use App\Model\UserRole;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class PluginService
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    public function migrate(Plugin $plugin)
    {

        if ($plugin->getDatabaseFilesPath()) {
            return Artisan::call('migrate', ['--path' => $plugin->getDatabaseFilesPath().DIRECTORY_SEPARATOR.'migrations', '--no-interaction' => '', '--force' => true]);
        }

        return null;
    }

    public function seed(Plugin $plugin)
    {
        if ($plugin->getDatabaseFilesPath()) {

            $seed_class = '\\Plugin\\'.$plugin->root_dir.'\\Database\\Seeds\\PluginSeeder';

            if (class_exists($seed_class)) {
                return Artisan::call('db:seed', ['--class' => $seed_class, '--no-interaction' => '', '--force' => true]);
            }
        }
    }

    public function assignPermissions(Plugin $plugin)
    {

        foreach (UserRole::all() as $role) {
            if ($role->isAdminRole()) {
                foreach (['view', 'create', 'update', 'delete'] as $action) {
                    $role->addRight(str_slug($plugin->root_dir).'.'.$action);
                }
                $role->save();
            }
        }

    }

    public function install(Plugin $plugin)
    {

        if (! $plugin->isCompatibleWithCore()) {
            return redirect()->back()->withMessage(['warning' => trans('plugin.not_compatible_with_core', ['min_core_ver' => $plugin->getRequiredCoreVersion()])]);
        }

        $this->migrate($plugin);

        $this->seed($plugin);

        $plugin->getRegister('onInstall', []);

        $plugin->version = $plugin->getInfo('version');
        unset($plugin->info, $plugin->config, $plugin->default_image, $plugin->image);
        $plugin->area = 0;
        $plugin->permission = 0;
        $plugin->tables = '';
        $plugin->active = 0;

        $plugin->save();

        $this->assignPermissions($plugin);

    }

    public function activate(Plugin $plugin)
    {
        return $plugin->update(['active' => 1]);
    }

    public function deactivate(Plugin $plugin)
    {
        return $plugin->update(['active' => 0]);
    }

    public function uninstall(Plugin $plugin)
    {
        // TODO: Add uninstall logic here, like removing tables, permissions, etc.

        $this->deactivate($plugin);

        Plugin::rootDir($plugin->root_dir)->delete();

        $this->remove($plugin);
    }

    public function remove(Plugin $plugin)
    {

        if (file_exists('plugins/'.$plugin->root_dir)) {
            Storage::disk('plugins')->deleteDirectory($plugin->root_dir);

            return true;
        }

        return false;
    }
}
