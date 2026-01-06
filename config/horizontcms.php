<?php

return [

	'version' => 'v1.4.2',

	'installed' => env('INSTALLED', false),

	'backend_prefix' => env('HCMS_ADMIN_PREFIX','admin'),

	'charset' => 'utf-8',

	'default_controller' => 'login',

	'admin_logo' => 'resources/logo.png',
	
	'default_date_format' => env('HCMS_DEFAULT_DATE_FORMAT', 'Y.m.d H:i:s'),

	'max_upload_file_size' => env('HCMS_MAX_UPLOAD_FILE_SIZE', 2560), // 2.5 MB

	'sattelite_url' => env('HCMS_CENTRAL_REPO','https://hcms-store.bluehill.hu'),

	'css' => [
				'resources/css/horizontcms-next.css',
			],

	'js' => [
				'resources/js/app.js',
			],

	'meta' =>[
				[ "name" => "viewport", 
				   "content" => "width=device-width, initial-scale=1.0"
				],
			],

	'modules' => [ //namespaces and root dirs for modules
				'Theme' =>  'themes',
				'Plugin' => 'plugins',
			],

	'languages' => [ //available languages
				'en' => 'English',
				'de' => 'Deutsch',
				'hu' => 'Magyar',
			],
	
	
	'theme_engines' => [ //available theme engines
			 	'hcms' => \App\Services\ThemeEngine::class,
        		'blade' => \App\Services\BladeThemeEngine::class,
				'spa' => \App\Services\SpaThemeEngine::class,
	],

	'default_theme_engine' => env('HCMS_DEFAULT_THEME_ENGINE', 'hcms'),

	'thumbnail' => [
				'width' => env('HCMS_THUMBNAIL_WIDTH', 300),
				'height' => env('HCMS_THUMBNAIL_HEIGHT', 200)
	],

	'creator' => [
				'name' => 'Timot Tarjani',
				'twitter' => 'http://www.twitter.com/timottarjani',
				'github' => 'https://github.com/ttimot24/HorizontCMS'
	]

];
