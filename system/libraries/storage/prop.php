<?php
/**
 * Provides auto-validating and convertable object properties
 * 
 * Prop-descendant classes perform conversions and validations of basic 
 * data structures.
 *  
 * Prop's constructor has the task of converting (if possible) all input 
 * values to a single format. If possible, it should be able to accept 
 * input from all of its output methods.
 * 
 * Methods with names starting with "__for" are considered value
 * converters and expected to return correctly formatted data. 
 * 
 * If incorrect input is received, a ValidationError is thrown.
 * It contains the Prop object and incorrect input data.
 * 
 * If non-supported output format is requested, a ConversionError is thrown.
 * It contains the Prop object and the format name.
 * 
 * You can either wrap an input loop in a try/catch block to catch the very 
 * first reported error, or use the try/catch inside the loop to collect 
 * the erronious value names and throw them forward to be displayed to 
 * the user.
 * 
 * Prop classes are required to have a _toSql() function that outputs 
 * SQL-formatted value.
 * 
 * <code><?PHP
 *  
 * // assume that "06-10-01 2:32" (string) was the input
 * $input = $_POST['alarm_time']
 * $date = new DateProp($input); 
 * 
 * // uses $date->_toRss()
 * print $date->rss; // outputs "2006-10-01T02:32:00" (string)
 * 
 * // uses $date->_toDb()
 * print $date->db;  // outputs 1159666354 (integer)
 * 
 * // to add a new output format:
 * 
 * class MyDateProp extends DateProp {
 *   function _toLogName($time) {
 *     return date('Ymd', $time);
 *   }
 * }
 * 
 * $date = new DateProp($_POST['alarm_time');
 * print $date->logName; // outputs "20061001" (string)
 * 
 * ?></code>
 * 
 * @author Michal Tatarynowicz <tatarynowicz@gmail.com>
 * @copyright 2006 Michal Tatarynowicz
 * @license Public Domain (see system/licence.txt)
 * 
 * @package Storage
 * @subpackage Database
 * @version $Revision: $
 */


abstract class Prop {
	
	/** Value of this property in internal format.
	 *  @var mixed Value of this property.
	 */
	protected $value;
	
	protected $label;
	
	/** Converts input data into property value.
	 *  @param mixed $input Data to be used as property value
	 */
	public function __construct($value) {
		$this->__set('string', $value);
	}

	/** Converts property value to a specified format.
	 *  @param string $name Output format name (i.e. 'html', 'rss', 'db')
	 *  @throws ConversionError On incorrect format name
	 *  @return mixed Property value converted to specified format
	 */
	public function __get ($name) {
		if (isset($this->$name)) {
			return $this->$name;
		}
		elseif (method_exists($this, 'to'.$name)) {
			$method = 'to'.$name;
			return $this->$method($this->value);
		}
		else {
			throw new ConversionError($this, 'unknown input format', $name);
			return false;
		}
	}
	
	/** Sets property value using specified input format.
	 *  @param string $name Input format name (i.e. 'form', 'rss', 'db')
	 *  @param mixed  $value Value to set
	 *  @throws ConversionError On incorrect format name
	 *  @return mixed New value (converted)
	 */
	public function __set ($name, $value) {
		if ('value'==$name) {
			$this->value = $value;
		}
		elseif (method_exists($this, 'from'.$name)) {
			$method = 'from'.$name;
			$this->value = $this->$method($value);
		}
		else {
			throw new ConversionError($this, 'unknown output format', $name);
		}
	}
	
	abstract function fromString ($input);

	abstract function toString ();
	
	public function __toString(){
		return $this->toString();
	}

	private function fromSQL($value) {
		return $this->fromString($value);
	}
	
	private function toSQL() {
		return $this->toString();
	}

}

?>