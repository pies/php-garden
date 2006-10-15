<?php

/**
 * Debug 
 *
 * @param mixed $arg1
 * @param mixed $arg2
 * @param mixed $arg3
 */
function d ($var1=null, $var2=null, $var3=null) {
	$args = func_get_args();
	Meta::call(array('Meta','debug'), $args);
}

class Meta 
{
	static function caller() {
		$trace = self::trace();
		return $trace[2]['place'];
	}

	static function debug($msg) {
		$msg = 'Debug at '.self::caller();
		$args = func_get_args();
		$vars = array_map('toString', $args);
		new GardenDebugMessage($msg . array_map('toString', $vars));
//		GardenLog::toScreen($msg, $vars);
//		self::writeLog($msg . array_map('toString', $vars), 'debug');
	}

}







function format_trace ($trace) {
	foreach ($trace as $step){
		if (empty($step['file'])) continue;

		$class = str_replace(' ', '_', ucwords(str_replace('_', ' ', isset($step['class'])? $step['class']: '')));
		$type  = isset($step['type'])? $step['type']: '';
		$funct = isset($step['function'])? $step['function']: '';

		$output[] = array(
			'name'  => $class? $class.$type.$funct.'()': $funct.'()',
			'place' => str_replace(DIR_ROOT, '', $step['file']).':'.$step['line'],
			'file'  => $step['file'],
			'class' => $class,
			'type'  => $type,
			'funct' => $funct
		);
	}
	return $output;
}



/* Returns caller function's full stack trace */
function caller_path ($skip=1, $bare=false){
	$parsed = caller_backtrace($skip);
	$is_bare = $bare || (defined('BARE') && BARE);

	$output = array();
	foreach ($parsed as $step){
		list($name, $place) = array_values($step);
		$output[] = $is_bare? $place: sprintf('<span title="%s">%s</span>', $name, $place);
	}

	return $output;
}

/* Returns calling method or function name */
function caller_name ($skip=1) {
	$step = caller_backtrace($skip);
	return $step['name'];
}

/* Retuns caller function's filename and line number */
function caller_place ($skip=1) {
	$step = caller_backtrace($skip);
	return $step[0]['place'];
}

function backtrace_step_has_file ($step) { return @$step['file']; }

/* Debug calling file path */
function caller_file ($skip=1) {
	$trace = caller_backtrace($skip);
	while (empty($step['file'])) $step = array_shift($trace);
	return $step['file'];
}
?>