<?php

/** 
 * Returns a sprintf() function call with custom embedded pattern.
 * 
 * @author Michal Tatarynowicz <tatarynowicz@gmail.com>
 * @copyright 2006 Michal Tatarynowicz
 * @license Public Domain (see system/licence.txt)
 *
 * @package Extensions
 * @subpackage Strings
 * @version $Revision: 13 $
 *
 * @param string $pattern
 * @return callback
 */
function formatter ($pattern) {
	$src = '
if (is_string($data)) $data = array($data); else array_unshift($data, \''.$pattern.'\');
return call_user_func_array("sprintf", $data);';
	return create_function('$data', $src);
}

?>