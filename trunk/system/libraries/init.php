<?php
/**
 * Garden defines
 * --------------------------------------------------------------------------
 * @author Michal Tatarynowicz <tatarynowicz@gmail.com>
 * @copyright 2006 Michal Tatarynowicz
 * @package Core
 * @version $Revision: 15 $
 * @license Public Domain (see system/licence.txt)
*/

class Garden 
{
	const 
		PRIORITY_LOW    =   1, // DEBUGGING
		PRIORITY_MEDIUM =  10, // INFORMATION
		PRIORITY_HIGH   = 100, // ERROR
		
		MODULE_UNKNOWN  = 'UNKNOWN', 
		MODULE_CORE     = 'CORE',
		MODULE_DATABASE = 'DATABASE';
		
	static function notify (GardenMessageInterface $event) {
		$priority = $event->getPriority();

		if ($priority == Garden::PRIORITY_LOW) 
			$log = 'debug';
		elseif ($priority == Garden::PRIORITY_MEDIUM) 
			$log = 'info';
		else 
			$log = 'error';
		
		self::logToFile($log, $event->__toString());
	}

	static function logToFile ($type, $message) {
		$path = DIR_ROOT.'/logs/'.date('y-m-d').'_'.ucfirst(strtolower($type)).'.log';
		return ($log = fopen($path, 'a+')) && 
		       fwrite($log, date('H:i:s ').$message."\n") && 
		       fclose($log);
	}	
}

?>