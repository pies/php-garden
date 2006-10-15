<?php
/** 
 * @author Michal Tatarynowicz <tatarynowicz@gmail.com>
 * @copyright 2006 Michal Tatarynowicz
 * @package Storage
 * @subpackage Database
 * @version $Revision: $
 * @license Public Domain (see system/licence.txt)
*/

class ValidationError extends GardenError {};

abstract class Field 
{
	private 
	$value   = null,
	$matches = '.*',
	$length  = null,
	$type    = null;

	function __construct($value) {
		try { 
			$this->validate($value);
			$this->value = $value;
		}
		catch (Exception $E) {
			throw $E;
		}
	}
	
	function validate($value) {

		if (!preg_match('/^'.$this->matches.'$/', $value)) {
			$msg = sprintf('Value [%s] must match pattern /^%s$/', $value, $this->matches);
			throw new ValidationError($msg);
		}
		
		if (!is_null($this->length)) {
			if (!is_array($this->length)) $this->length = array($this->length, $this->length);
			$len = strlen($value);
			if ($len < $this->length[0] || $len < $this->length[1]) {
				$msg = sprintf('Length of [%s] must be between %d and %d', $value, $this->length[0], $this->length[1]);
				throw new ValidationError($msg);
			}
		}
	}
	
	function __toString() {
		//return 
	}
}

class EmailField extends Field 
{
	private 
	$matches = '.+@.+\..+',
	$length  = array(6,128),
	$type    = 'varchar';
}

class PasswordField extends Field 
{
	private
	$matches = '.+',
	$length  = array(3,64),
	$type    = 'varchar';
}

class TimestampField extends Field 
{
	private
	$default;
}


class UserTable
{
	protected $fields = array(
		'email*'    => 'EmailField',
		'password*' => 'PasswordField',
		'created!'  =>  Data::FIELD_TYPE_TIMESTAMP
	);
}

?>