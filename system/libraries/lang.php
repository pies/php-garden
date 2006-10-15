<?

/** Simplifies call_user_func_array() syntax */
function call (&$callback, $args=array(), $extra=array()) {
	/* if user passed an object and a method name */
	if (is_string($args)) {
		$callback = array($callback, $args);
		$args = $extra;
	}
	return call_user_func_array($callback, $args);
}

/** Check if a path is a readable file */
function is_readable_file ($path) {
	return is_readable($path) && is_file($path);
}

/** Remove magic quotes from request data */
static function handle_magic_quotes () {
	if (get_magic_quotes_gpc()) {
		$stripslashes = Lang::func('$string', 'return stripslashes($string);');
		$_POST   = Enum::map_deep($_POST, $stripslashes);
		$_GET    = Enum::map_deep($_GET, $stripslashes);
		$_COOKIE = Enum::map_deep($_COOKIE, $stripslashes);
	}
}

/** Recursively create a path of directories */
function force_mkdir ($path) {
	return is_dir($path) || (force_mkdir(dirname($path)) && mkdir($path));
}

?>