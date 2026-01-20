<?php
/*
|--------------------------------------------------------------------------
| HorizontCMS Module Auto Loader
|--------------------------------------------------------------------------
|
| Defines the autoloader for the modules added in config/horizontcms.php
| modules section.
|
*/
/**
* @deprecated deprecated since version 1.4.0
*/
function module_loader($class): void
{

	$split = explode("\\", $class);

	$class = array_pop($split);

	$module = $split[0] ?? null;

	$config = require base_path('config/horizontcms.php');

	if (empty($module) || empty($config['modules'])) {
		return;
	}

	if (!in_array($module, array_keys($config['modules']))) {
		return;
	}


	$module_base = base_path($config['modules'][$module] . DIRECTORY_SEPARATOR . $split[1]);

	$in_app = false;


	foreach (array_slice($split, 2) as $each) {

		if ($in_app) {
			$module_base .= DIRECTORY_SEPARATOR . $each;
		} else {
			$module_base .= DIRECTORY_SEPARATOR . strtolower($each);
		}

		if ($each == 'App') {
			$in_app = true;
		}
	}


	$file = $module_base . DIRECTORY_SEPARATOR . $class . ".php";

	if (file_exists($file)) {
		require_once($file);
	}
};
