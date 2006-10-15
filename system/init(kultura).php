<?php
/**
 * Bootstrap
 * --------------------------------------------------------------------------
 * @author Michal Tatarynowicz <tatarynowicz@gmail.com>
 * @copyright 2006 Michal Tatarynowicz
 * @package Core
 * @version $Revision: $
 * @license Public Domain (see system/licence.txt)
*/

define('DIR_ROOT', dirname(dirname(__FILE__)));
define('DIR_CORE', DIR_ROOT.'/core');
define('DIR_DATA', DIR_ROOT.'/data');
define('DIR_LOGS', DIR_ROOT.'/logs');
define('DIR_MODS', DIR_ROOT.'/mods');

ini_set('include_path', '.'.PATH_SEPARATOR.DIR_MODS.PATH_SEPARATOR.DIR_CORE);

ini_set('error_log', DIR_LOGS.'/debug.log');
ini_set('display_errors', 1);
error_reporting(E_ALL);

class Alert extends Exception {};
class Error extends Exception {};

class CoreError extends Error {};

function __autoload ($class) {
	$parts = preg_split('/([A-Z][^A-Z]+)/', $name, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	$file  = strtolower(join('_', $parts)).'.php';
	$path  = "{$parts[0]}/$file";

	foreach( explode(PATH_SEPARATOR, get_include_path()) as $place ){
		if (is_file("$place/$path") && is_readable("$place/$path")) {
			require_once("$place/$path");
			return true;
		}
	}
	
	throw new CoreError("Class {$name} not found (tried $file and $path)");
	return false;
}

/**
 * Executes a callback (flexible input)
 * 
 * Accepts arguments:
 * 	- "className",                      "methodName",    [ array(arguments) ]
 *  - object,                           "methodName",    [ array(arguments) ]
 *  - array("className", "methodName"), [ array(arguments) ]
 *  - array(object, "methodName"),      [ array(arguments) ]
 *  - "functionName"                    [ array(arguments) ]
 *
 * @param mixed $arg1 String (function callback) or array (class and object callbacks)
 * @param mixed $arg2 String (for objects and classes) or array (for function callbacks)
 * @param array $arg3 Array for objects and classes
 */
function call ($arg1, $arg2=false, $arg3=array()) {
	switch (func_num_args()) {
		default: 
		case 3:                     call_user_method_array( array($arg1,$arg2), $arg3 ); break;
		case 2 && is_array($arg2):  call_user_func_array( $arg1, $arg2 ); break;
		case 1 && is_string($arg1): call_user_func( $arg1 ); break;
	}
}


require('enum.php');

?>