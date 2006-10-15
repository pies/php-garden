<?php

/**
 * Returns a string representation of a variable
 * 
 * @param  mixed  $var   Variable or object to be stringified
 * @param  bool   $html  Whether to prettify output with HTML and CSS
 * @return string        String representation of input variable or object
 */
function to_string ($var, $html=false) {
	$fn = formatter($html? '<em>%s</em>': '%s');

	switch (strtoupper(gettype($var))) {
		case 'ARRAY':   return array_to_string($var, $html);
		case 'BOOLEAN': return $fn($var? '(TRUE)': '(FALSE)');
		case 'NULL':    return $fn("(NULL)");
		case 'INTEGER': return sprintf('%d', $var);
		case 'DOUBLE':  return str_replace('.', $fn("."), print_r($var, true));
		case 'STRING':  return $fn('"').$var.$fn('"');
		default:        return $fn(ucfirst(gettype($var))).' '.print_r($var, true);
	}

	return false;
}

/**
 * Returns a string representation of an array
 * 
 * @param  array  $array  Array to be stringified
 * @param  bool   $html   Whether to prettify output with HTML and CSS
 * @return string         String representation of input array
 */
function array_to_string ($array, $html=false) {
	$fn = formatter($html? '<em>%s</em>': '%s');
	$s1 = $html? "\n": '';
	$s2 = $html? "&nbsp;&nbsp;&nbsp;": ' ';
	
	$OUT = array();
	$MAX = array_reduce(array_map('strlen', array_keys($array)), 'max');

	foreach ($array as $key=>$val) {
		$SPACER = str_repeat('&nbsp;', max($MAX-strlen($key), 0));
		$OUT[] = to_string($key).$SPACER.$fn(' = ').str_replace("\n", $s2, to_string($val));
	}
	return $fn("[$s2").join(",$s2", $OUT).$fn("$s1]");
}

?>