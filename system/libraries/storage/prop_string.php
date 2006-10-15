<?

abstract class StringProp {
	
	private $value = '';
	private $max   = 0;
	
	function fromString($input) {
		$value = ''.$input;
		if (strlen($value) > $this->max) throw new ConversionError($this, 'too long', $input);
		return $this->value = $value;
	}
	
	function toString() {
		return $this->value;
	}
	
}

class LineProp extends StringProp {
	private $max = 255;
}

class MemoProp extends StringProp {
	private $max = 1048576;
}

?>