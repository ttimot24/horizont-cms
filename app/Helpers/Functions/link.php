<?php 

/**
 * @deprecated deprecated since version 1.0.0
 */
function plugin_link(string|null $link,$param = null): string {

	$link = config('horizontcms.backend_prefix')."/plugin/run/".$link;

	return isset($param)? $link."/".$param : $link;

}

/**
 * @deprecated deprecated since version 1.0.0
 */
function namespace_to_slug(string $string): string {
	return ltrim(strtolower(preg_replace('/(?<!\ )[A-Z]/', '-$0', $string)),"-");
}

/**
 * @deprecated deprecated since version 1.0.0
 */
function remove_linebreaks(string $string): string {
	return str_replace(["\n", "\\n", "\r\n","\\r\\n", "\r", "\\r"],"", $string);
}