<?php

/** 
 * Compresses a string into .gz with proper headers.
 * 
 * @author Michal Tatarynowicz <tatarynowicz@gmail.com>
 * @copyright 2006 Michal Tatarynowicz
 * @license Public Domain (see system/licence.txt)
 *
 * @package Storage
 * @subpackage Compression
 * @version $Revision: 13 $
 *
 * @param string $data Data to gzip
 * @param int $level Compression level
 * @param string $filename File name (for headers)
 * @param string $comments File comment (for headers)
 * @return string
 */
function gzip ($data = "", $level = 6, $filename = "", $comments = "") {
	$flags = (empty($comment)? 0 : 16) + (empty($filename)? 0 : 8);
	$mtime = time();
	return (
		pack("C1C1C1C1VC1C1", 0x1f, 0x8b, 8, $flags, $mtime, 2, 0xFF) .
		(empty($filename) ? "" : $filename . "\0") .
		(empty($comment) ? "" : $comment . "\0") .
		gzdeflate($data, $level) .
		pack("VV", crc32($data), strlen($data))
	);
}

?>