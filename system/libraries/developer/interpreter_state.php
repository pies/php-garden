<?php
/**
 * Interpreter state
 * Creates a snapshot of the current interpreter state.
 * --------------------------------------------------------------------------
 * @author Michal Tatarynowicz <tatarynowicz@gmail.com>
 * @copyright 2006 Michal Tatarynowicz
 * @package Package
 * @version $Revision: $
 * @license Public Domain (see system/licence.txt)
*/

class InterpreterState {
	private $data;

	function __construct($dump_file=false, $gzip_dump_file=false) {
		$functions = get_defined_functions();
		$this->data = array(
			'memory_usage'   => memory_get_usage(),
			'interfaces'     => get_declared_interfaces(),
			'classes'        => get_declared_classes(),
			'constants'      => get_defined_constants(),
			'user_functions' => $functions['user'],
			'int_functions'  => $functions['internal'],
		);
		if ($dump_file) $this->dump($dump_file, $gzip_dump_file);
	}
	
	function __get($name) {
		if (isset($this->$name)) return $this->$name;
		else return false;
	}
	
	function differences(InterpreterState $state) {
		$diff = array();
		$data = $state->data;
		
		foreach ($this->data as $key=>$val) {
			if (is_array($val)) {
				$diff[$key] = array_diff($data[$key], $val);
			}
			elseif (is_int($val)) {
				$prefix = ($data[$key] > $val)? '+': '-';
				$diff[$key] = $prefix.($data[$key] - $val);
			}
		}

		return $diff;
	}

	function dump ($filename, $gzip_dump_file=false) {
		$data = serialize($this);

		if ($gzip_dump_file && function_exists('gzip')) {
			$data = gzip($data, 6, $filename, 'Interpreter state dump (Garden)');
			$filename .= '.gz';
		}

		if (!($f = fopen($filename, 'w') AND fwrite($f, $data) AND fclose($f))) {
			throw new Exception('Could not write state dump.');
		}
	}
	
}

?>