<?php
/**
 * Name
 * --------------------------------------------------------------------------
 * @author Michal Tatarynowicz <tatarynowicz@gmail.com>
 * @copyright 2006 Michal Tatarynowicz
 * @package Package
 * @version $Revision: $
 * @license Public Domain (see system/licence.txt)
*/

function error_handler ($id, $str, $file, $line) {
	print "<p>Error <b>($id) $str in $file:$line</b></p>\n";
}

function exception_handler (Exception $exception) {
	
	$reflector = new Introspection($exception);
	print $reflector->asHtml();
	
	$id   = $exception->getCode();
	$str  = $exception->getMessage();
	$file = $exception->getFile();
	$line = $exception->getLine();
	print "<p>Exception <b>($id) $str in $file:$line</b></p>\n";
}

function shutdown_handler () {
	print "<p><b>Script shuting down.</b></p>";
}

set_error_handler('error_handler');
set_exception_handler('exception_handler');
register_shutdown_function('shutdown_handler');

?>