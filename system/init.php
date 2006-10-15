<?php
/**
 * Core: Initialization script
 * --------------------------------------------------------------------------
 * @author Michal Tatarynowicz <tatarynowicz@gmail.com>
 * @copyright 2006 Michal Tatarynowicz
 * @package Core
 * @version $Revision: 18 $
 * @license Public Domain (see system/licence.txt)
*/

define('DIR_ROOT', dirname(__FILE__));

ini_set('error_log', DIR_ROOT.'/data/logs/debug.log');
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (function_exists('date_default_timezone_set')) {
	date_default_timezone_set('Europe/Warsaw');
}

//dl('php_pdo.dll');
//dl('php_pdo_mysql.dll');
//dl('php_pdo_sqlite.dll');

require DIR_ROOT.'/libraries/garden/init.php';
require DIR_ROOT.'/libraries/garden/message.php';
require DIR_ROOT.'/libraries/garden/error.php';


class Meta 
{

	static function getAutoloadPath($classname) {
		$parts = array_map('strtolower', preg_split('/([A-Z][^A-Z]+)/', $classname, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE));
		$path  = $parts[0];
		$file  = join('_', $parts).'.php';
		return DIR_ROOT."/libraries/$path/$file";
	}

}

?>