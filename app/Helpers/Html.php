<?php

/**
 * @deprecated deprecated since version 1.0.0
 */
class Html {

	/**
	 * @deprecated deprecated since version 1.0.0
	 */
	public static function cssFile(string $file): string {
		return "<link rel='stylesheet' type='text/css' media='all' href='".$file."' />";
	}

	/**
	 * @deprecated deprecated since version 1.0.0
	 */
	public static function jsFile(string $file): string {
		return "<script src='".$file."' type='text/javascript' charset='utf-8'></script>";
	}

	/**
	 * @deprecated deprecated since version 1.0.0
	 */
	public static function meta(string $property, string $content): string {
		return "<meta property='".$property."' content='".$content."' />";
	}

	/**
	 * @deprecated deprecated since version 1.0.0
	 */
	public static function favicon(string $string): string {

		$ext = pathinfo($string, PATHINFO_EXTENSION);

		return "<link rel='shortcut icon' type='image/".$ext."' href='".$string."'/>";
	}


}