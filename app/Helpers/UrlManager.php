<?php


/* This helper is for legacy theme compability purposes */

/**
 * @deprecated deprecated since version 1.0.0
 */
class UrlManager
{

	/**
	 * @deprecated deprecated since version 1.0.0
	 */
	public static function seo_url(string|null $string): string
	{
		if(empty($string)) {
			return "";
		}

		$string = explode("/", $string);

		if (count($string) > 1) {

			$url = "";

			foreach ($string as $slug) {
				$url .= "/" . str_slug($slug, "-");
			}

			return ltrim($url, "/");
		} else {
			return str_slug($string[0], "-");
		}
	}

	/**
	 * @deprecated deprecated since version 1.0.0
	 */
	public static function http_protocol(string $string): string
	{

		if (strpos($string, 'http') === false) {
			$string = "http://" . $string;
		}

		return $string;
	}
}
