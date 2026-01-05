<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Lavary\Menu\Facade as Menu;

class NavbarPluginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Closure
    {
        if (app()->plugins != null && !app()->plugins->isEmpty()) {

            $main_menu = Menu::get('MainMenu');
            $right_menu = Menu::get('RightMenu');

            foreach (app()->plugins as $plugin) {
                try {
                    $permissionKey = strtolower($plugin->root_dir);

                    // Használjuk a Gate-et a hozzáférés ellenőrzésére
                    if (!Gate::allows('view', $permissionKey)) {
                        continue;
                    }

                    $plugin_nav = $plugin->getRegister('navigation', []);

                    foreach ($plugin_nav as $key => $item) {

                        $item['url'] = $item['url'] ?? rescue(
                            fn() => route('plugin.' . str_slug($plugin->root_dir) . '.start.index'),
                            fn() => plugin_link(namespace_to_slug($plugin->root_dir))
                        );

                        if (!isset($item['menu']) || $item['menu'] === 'main') {
                            if (isset($item['submenu_of'])) {
                                $main_menu->find($item['submenu_of'])->add($item['label'], $item['url'])->id($key);
                            } else {
                                $main_menu->add($item['label'], $item['url'])->id($key);
                            }
                        } elseif ($item['menu'] === 'right') {
                            if (isset($item['submenu_of'])) {
                                $right_menu->find($item['submenu_of'])->add($item['label'], $item['url'])->id($key);
                            } else {
                                $right_menu->add($item['label'], $item['url'])->id($key);
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    // Log the error or handle it as needed
                   /* \Log::warning('Error processing plugin navigation: ' . $e->getMessage(), [
                        'plugin' => $plugin->root_dir,
                        'exception' => $e,
                    ]); */
                    
                    // Optionally, you can continue or rethrow the exception based on your needs
                    continue;
                }
            }
        }

        return $next($request);
    }
}
