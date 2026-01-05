<?php 

namespace App\Services;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Config;

class Website {

	public static $_BLOGPOST;
	public static $_USER;
	public static $_CURRENT_USER;
	public static $_PAGE;
	public static $_REQUESTED_PAGE;
	public static $_SETTINGS;
	public static $_THEME_PATH;
	public static $_SLUGS;
	public static $_PLUGINS;
	public static $_HEADER_IMAGES;
	public static $_SOCIAL_MEDIA;
	public static $_THEME;
	
	public static $system,$pages;

	public static function initalize($wengine): void {
		

		self::$_SLUGS = Request::segments();

		self::$_SETTINGS = json_decode(json_encode($wengine->request->settings));

		self::$_THEME_PATH = rtrim($wengine->getTheme()->getPath(),'/');

		self::$_THEME = $wengine->getTheme();

		self::$_CURRENT_USER = \Auth::user();

		self::$_REQUESTED_PAGE = Request::segment(1)===null ? \App\Model\Page::find($wengine->request->settings['home_page']) : \App\Model\Page::findBySlug($wengine->request->segment(1));

		self::$_HEADER_IMAGES =  collect(\App\Model\HeaderImage::all());

	}

	public static function define_base(): void {

		echo "<base href=".Config::get('app.url') ." />";
	}

	public static function handle_routing(): void {

		    if(isset(Website::$_REQUESTED_PAGE->url) 
		    	&& Website::$_REQUESTED_PAGE->url!="" 
		    	&& file_exists(Website::$_THEME_PATH ."/page_templates/".Website::$_REQUESTED_PAGE->url)){
		    	
                require_once(Website::$_THEME_PATH ."/page_templates/".Website::$_REQUESTED_PAGE->url);
            	          
            }
            else if(isset(Website::$_REQUESTED_PAGE)){
            	
            	if(file_exists(Website::$_THEME_PATH ."/page.php")){
                	require_once(Website::$_THEME_PATH ."/page.php");
            	}
            	else{
            		echo "<h1 class='page-header pb-2 mt-4 mb-2 border-bottom'>".Website::$_REQUESTED_PAGE->name."</h1>";
            		echo "<p>".Website::$_REQUESTED_PAGE->page."</p>";
            	}

            }
            else{
               
                if(file_exists(Website::$_THEME_PATH ."/404.php")){
                    require_once(Website::$_THEME_PATH ."/404.php");
                }
                else{
                    echo "404 - Sorry page not found!";    
                }

            }
            
	}

	public static function jsPlugins(): void {
		foreach(app()->plugins as $plugin){
			foreach($plugin->getRegister('injectWebsiteJs',[]) as $js){
				if(file_exists($plugin->getPath().$js)){
					echo \Html::jsFile($plugin->getPath().$js);
				}else{
					echo \Html::jsFile($js);
				}
			}
		}
	}

	public static function logo(): string {
		return 'storage/images/logos/'.Website::$_SETTINGS->logo;
	}


	public static function require_theme_file(string $file): void {
		require(base_path(Website::$_THEME_PATH."/".$file));
	}

	public static function require_theme_stylesheet(string $file): string {
		return Website::$_THEME_PATH."/".$file;
	}

	public static function customStyle(): string {
		
		if(!empty(Website::$_SETTINGS->{'custom_css_'.snake_case(Website::$_SETTINGS->theme)})){
			return "<style id='custom_css'>".Website::$_SETTINGS->{'custom_css_'.snake_case(Website::$_SETTINGS->theme)}."</style>";
		}

		return "";
	}

	public static function isActivePlugin(string $plugin_name): bool {

		foreach(app()->plugins as $plugin){
			if($plugin->root_dir==$plugin_name){ return true; }
		}

		return false;
	}

	public static function getPageUrlByTemplate(string|null $page_template): string {
		$page = \App\Model\Page::withTemplate($page_template)->first();

		return $page == null ? "" : $page->getSlug();
	}

}
