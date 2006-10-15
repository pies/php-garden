<?php

/** 
 * Facilitates an object-oriented way of working with arrays.
 * 
 * @author Michal Tatarynowicz <tatarynowicz@gmail.com>
 * @copyright 2006 Michal Tatarynowicz
 * @license Public Domain (see system/licence.txt)
 *
 * @package Extensions
 * @subpackage Arrays
 * @version $Revision: 13 $
*/
class Enum extends ArrayObject {
	
	public function __construct ($value=array()) {
		if ($value instanceof ArrayObject) $value = iterator_to_array($value);
		$this->set($value);
	}
	
	/** 
	 * Returns an array of all elements.
	 *
	 * @return array
	 */
	public function get () {
		return iterator_to_array($this);
	}
	
	/** 
	 * Assigns an array as this object's value.
	 *
	 * @param array $value
	 * @return unknown
	 */
	public function set ($value) {
		parent::__construct($value);
		return $this;
	}

	/** 
	 * Merges $array into the Enum.
	 *
	 * @param Array $array Array of elements to merge in.
	 * @param Bool $prevent_overwrite If true, values are not overwriten.
	 * @return Enum
	 */
	public function add ($array, $prevent_overwrite=false) {
		if ($array instanceof ArrayObject) $array = iterator_to_array($array);
		foreach ($array as $key=>$value) {
			if ($prevent_overwrite && isset($this[$key])) continue;
			$this[$key] = $value;
		}
		return $this;
	}
	
	/** 
	 * Iterates with $callback and returns unchanged Enum.
	 * The $callback is passed two arguments, element value and key (in this order).
	 * 
	 * @param callback $callback
	 * @return Enum
	 */
	public function each ($callback) {
		$this->map($callback);
		return $this;
	}
	
	/**
	 * Iterates with $filter and returns an Enum with return values.
	 * The $callback is passed two arguments, element value and key (in this order).
	 *
	 * @param callback $callback
	 * @return Enum
	 */
	public function map ($callback, $recursively=false) {
		foreach ($array as $key=>$value) {
			is_array($value)?
				$output = array_merge($output, enum($value)->flatten()):
				$output[$key] = $value;
		}
		
		$output = array();
		foreach ($array as $key=>$value) {
			$recursively && is_array($value)?
				$output = array_merge($output, enum($value)->map($callback, $recursively)):
				$output[$key] = call($callback, array($value, $key));
		}
		return new Enum($output);
	}
	
	/** 
	 * Iterates with sprintf($pattern, $value, $key) and returns an Enum with return values.
	 * See sprintf() documentation for $pattern format (including reordering and repeating of arguments).
	 *
	 * @param string $pattern
	 * @return Enum
	 */
	public function format ($pattern) {
		return $this->map(
			create_function('$value, $key', 'return sprintf(\''.addslashes($pattern).'\', $value, $key);')
		);
	}
	
	/** 
	 * Returns an Enum of those elements for which $callback($value, $key) is true.
	 * The $callback is passed two arguments, element value and key (in this order).
	 * 
	 * @param callback $callback
	 * @return Enum
	 */
	public function filter ($callback) {
		$output = array();
		foreach ($array as $key=>$value) {
			if (call($callback, array($value, $key))) $output[$key] = $value;
		}
		return new Enum($output);
	}

	/** 
	 * Iterates with $callback($result, $value, $key) and returns $result.
	 * $result is assigned previous iteration's return value (or null on first iteration).
	 * 
	 * Example:
	 * <code>
	 *   $array = array(1,2,3);
	 *   $adder = create_function('$sum,$elem', 'return $sum + $elem;');
	 *   print enum($array)->reduce($adder); // prints 6
	 *   print enum($array)->reduce('max');  // prints 3
	 * </code>
	 *
	 * @param callback $callback
	 * @return mixed
	 */
	public function reduce ($callback) {
		$output = null;
		foreach ($this as $key=>$value) {
			$output = call($callback, array($output, $value, $key));
		}
		return $output;
	}

	/** 
	 * Creates as string from all elements with $glue in between.
	 *
	 * @param String $glue
	 * @return String
	 */
	public function join ($glue='') {
		return join($glue, $this->get());
	}

	/** 
	 * Return an Enum with elements with specified keys removed (opposite to Enum::only).
	 * Accepts the list of excluded keys as an array or as successive arguments.
	 * 
	 * Example:
	 *   $array = array('a'=>'foo','b'=>'bar','c'=>'baz');
	 *   $result = enum($array)->exclude('a','c')->get(); // array('b'=>'bar')
	 *   $result = enum($array)->exclude(array('a','c'))->get(); // array('b'=>'bar')
	 *
	 * @param mixed $keys Keys to remove from the Enum
	 * @return Enum
	 */
	public function exclude ($keys) {
		$args = func_get_args();
		$excluded_keys = is_array($args[1])? $args[1]: $args;

		$output = array();
		foreach ($this as $key=>$value) {
			if (!in_array($key, $excluded_keys)) {
				$output[$key] = $value;
			}
		}
		return new Enum($output);
	}

	/** 
	 * Returns an Enum leaving only elements with specified keys (opposite to Enum::exclude)
	 * Accepts the list of included keys as an array or as successive arguments.
	 *
	 * @param mixed $keys Keys to leave in the Enum
	 * @return Enum
	 */
	public function only ($keys) {
		$args = func_get_args();
		$included_keys = is_array($args[1])? $args[1]: $args;

		foreach ($this as $key=>$value) {
			if (in_array($key, $included_keys)) {
				$output[$key] = $value;
			}
		}
		return new Enum($output);
	}

	/** 
	 * Returns true of Enum contains elements with all of the specified keys, false otherwise.
	 *
	 * @param Array $keys
	 * @param Bool  $non_empty
	 * @return Bool
	 */
	public function has ($keys, $non_empty=false) {
		foreach ($keys as $key) {
			if (!isset($this[$key]) || ($non_empty && empty($this[$key]))) return false;
		}
		return true;
	}
	
	/** 
	 * Recursively moves values from nested arrays to the top level array.
	 * 
	 * Example:
	 *   $array = array( $one, array($two, three, array($four)), $five );
	 *   enum($array)->flatten() == array( $one, $two, $three, $four, $five ); // true
	 *
	 * @return Enum
	 */
	public function flatten () {
		$output = array();
		foreach ($array as $key=>$value) {
			is_array($value)?
				$output = array_merge($output, enum($value)->flatten()):
				$output[$key] = $value;
		}
		return $this->set($output);
	}	

	/** 
	 * Return value of first element that matches $func
	 *
	 * @param array $array
	 * @param callback $selector
	 * @param mixed $default
	 * @return matched element or $default
	 */
	public function find ($selector, $default=false) {
		foreach ($this as $key=>$value) {
			if (call($selector, array($value, $key))) return $value;
		}
		return $default;
	}

}

/** 
 * Creates a new Enum object in flight from an array or a list of arguments
 *
 * @return Enum
 */
function enum() {
	$args = func_get_args();
	if (count($args)==1 && is_array($args[0])) $args = $args[0];
	return new Enum($args);
}

?>