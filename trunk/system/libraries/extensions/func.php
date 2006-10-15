<?
/** 
 * Function and execution helpers.
 * 
 * @author Michal Tatarynowicz <tatarynowicz@gmail.com>
 * @copyright 2006 Michal Tatarynowicz
 * @license Public Domain (see system/licence.txt)
 *
 * @package Extensions
 * @subpackage Functions
 * @version $Revision: 13 $
 */

/**
 * Executes a callback.
 *
 * @param mixed $arg1
 * @param mixed $arg2
 * @param array $arg3
 */
function call ($arg1, $arg2=false, $arg3=array()) {
	switch (func_num_args()) {
		default: 
		case 3:                     call_user_method_array( array($arg1,$arg2), $arg3 ); break;
		case 2 && is_array($arg2):  call_user_func_array( $arg1, $arg2 ); break;
		case 1 && is_string($arg1): 
			if (is_callable($arg))  call_user_func( $arg1 ); break;
	}
}

/**
 * Exports class methods (specified with $funcs) into global namespace as functions.
 *
 * @param string $class Class from which the export is made.
 * @param array $funcs List of methods to export.
 */
function export ($class, $funcs=array()) {
	$php = '';
	foreach ($funcs as $func=>$alias) {
		if (!is_string($class)) $class = get_class($class);
		if (!is_string($func)) $func = $alias;
		if (!function_exists($alias)) {
			$php .=
				 "function {$alias}(){ \n"
				."print '$alias';"
				."\t\$args=func_get_args();\n"
				."\treturn Lang::call('$class', '$func', \$args);\n"
				."}\n";
		}
	}

	if ($php) eval($php);
}


?>