<?php


/* Debugging
   -------------------------------------------------------------------------- */
FASE::requires('std/enum');



/* Returns a string representation of a variable */
function inspect ($var) {
	return stripslashes(var_export($var, true));
}


/* Trace + write to the warnings logfile */
function warning ($message, $area=MSG_AREA_CONTEXT) {
	FASE::warn($area, $message);
}

/* Trace + writes to the errors logfile + aborts processing */
function error ($message, $area=MSG_AREA_CONTEXT) {
	FASE::alert($area, $message);
	die();
}

?>