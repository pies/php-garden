<?php

function color_str_to_values ($color) {
	if (preg_match('/^#([A-Z0-9])([A-Z0-9])([A-Z0-9])$/', $color, $R)) {
		return array( hexdec($R[1].$R[1]), hexdec($R[2].$R[2]), hexdec($R[3].$R[3]));
	}
	elseif (preg_match('/^#([A-Z0-9][A-Z0-9])([A-Z0-9][A-Z0-9])([A-Z0-9][A-Z0-9])$/', $color, $R)) {
		return array( hexdec($R[1]), hexdec($R[2]), hexdec($R[3]));
	}
	else {
		return $color;
	}
}

function color_values_to_str ($color) {
	return sprintf('#%02s%02s%02s', dechex($color[0]), dechex($color[1]), dechex($color[2]));
}

function color_mix ($A, $B, $ratio=.5) {
	$A = color_str_to_values($A);
	$B = color_str_to_values($B);

	$C1 = $A[0] + (($B[0]-$A[0])*(1-$ratio));
	$C2 = $A[1] + (($B[1]-$A[1])*(1-$ratio));
	$C3 = $A[2] + (($B[2]-$A[2])*(1-$ratio));

	return color_values_to_str(array($C1, $C2, $C3));
}

?>