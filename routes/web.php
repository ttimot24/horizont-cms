<?php

use Illuminate\Support\Facades\Route;
use \App\Model\Settings;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::group(['prefix'=>'/'],function(){
	Route::resource('/', \App\Controllers\WebsiteController::class);
});

if (app()->isInstalled()) {

	$_THEME_NAME = Settings::get('theme');

	if (!defined('THEME_CONTROLLER_PATH')) {
		define('THEME_CONTROLLER_PATH', 'themes' . DIRECTORY_SEPARATOR . $_THEME_NAME . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Controllers');
	}

	if (is_dir(base_path(THEME_CONTROLLER_PATH))){
		foreach (array_diff(scandir(base_path(THEME_CONTROLLER_PATH)), ['.', '..']) as $file) {
			if (is_file(THEME_CONTROLLER_PATH . "/" . $file)) {
				$actualName = pathinfo($file, PATHINFO_FILENAME);
				$controller_route = strtolower(str_replace("Controller", "", $actualName));


				Route::resource("/" . $controller_route, "\Theme\\" . $_THEME_NAME . "\App\Controllers\\" . $actualName)
					->names(collect(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])->mapWithKeys(function ($item) use ($controller_route) {
						return [$item => 'theme.' . $controller_route . '.' . $item];
					})->toArray());
			}
		}
	}


	Route::any('/{slug?}/{args?}', function ($slug = "", $args = null) use ($_THEME_NAME) {

		try {

			try {

				$this->router->changeNamespace("Theme\\" . $_THEME_NAME . "\\App\\Controllers\\");

				$action = explode("/", $args)[0];

				return $this->router->resolve($slug, $action, ltrim($args, $action . "/"));
			} catch (Exception $e) {


				if ($e instanceof \App\Exceptions\FileNotFoundException || $e instanceof BadMethodCallException) {

					/** @var \Illuminate\Routing\Controller $controller */
					$controller = \App::make('\App\Controllers\WebsiteController');

					if (method_exists($controller, 'before')) {
						$controller->before();
					}

					if (method_exists($controller, $slug)) {

						return $controller->callAction($slug, [$slug, $args]);
					}

					return $controller->callAction('show', [$slug, $args]);
				}


				throw $e;
			}
		} catch (Exception $e) {
			$handler = new \App\Exceptions\WebsiteExceptionHandler(app());

			return $handler->render(request(), $e);
		}
	})->where('args', '(.*)');
}
