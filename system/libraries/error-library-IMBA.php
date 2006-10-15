<?php
/**
 * Base Error Exception
 * --------------------------------------------------------------------------
 * @author Michal Tatarynowicz <tatarynowicz@gmail.com>
 * @copyright 2006 Michal Tatarynowicz
 * @package Garden
 * @version $Revision: 16 $
 * @license Public Domain (see system/licence.txt)
*/


class Error extends Exception {};

class CodeError extends Error {};
class DataError extends Error {};

class ConversionError extends DataError {
	function __construct($obj, $error, $value) {
		$class = get_class($obj);
		$type  = gettype($value);
		parent::__construct("Conversion error: $type $value is $error for $class.", 1);
	}
}; 



class GardenError extends Exception implements GardenMessageInterface
{
	protected
		$title    = null,
		$module   = Garden::MODULE_UNKNOWN,
		$priority = Garden::PRIORITY_HIGH,
		$place    = null,
		$data     = array();

	function __construct($msg){
		$this->data  = func_get_args();
		$this->title = $this->data[0];
		$this->place = str_replace(DIR_ROOT, '', $this->getFile().':'.$this->getLine());
		Garden::notify($this);
		parent::__construct($this->title, $this->priority);
	}

	function getTitle(){
		return $this->title;
	}
	
	function getPriority(){
		return $this->priority;
	}
	
	function getPlace(){
		return $this->place;		
	}

	function __toString() {
		return "ERROR ({$this->priority}) in {$this->module} at {$this->place} - {$this->title}\n";
	}
}

?>