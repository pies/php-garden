<?

class NumericProp extends Prop {
	protected $max = 0;
	
	function fromString($input) {
		$value = (int) $input;
		if ($this->max && ($value > $this->max)) throw new ConversionError($this, 'too big', $input);
		return $this->value = $value;
	}
	
	function toString() {
		return ''.$this->value;
	}
}

class NumberProp extends NumericProp {
	protected $value = 0;
	protected $max   = 2147483647;
}

class AmountProp extends NumericProp {
	protected $value = 0.0;

	function fromString($input) {
		$value = (float) $input;
		if ($this->max && ($value > $this->max)) throw new ConversionError($this, 'too big', $input);
		return $this->value = $value;
	}
}






class FormInput {
	
	public $value;
	
	function __construct($value='') {
		$this->value = $value;
	}
	
	function __toString() {
		return "<label>{$this->value->label}: <input value=\"{$this->value->string}\"/></label>";
	}
	
}


class PriceProp extends AmountProp {
	protected $label = 'Price';
	protected $value = 0.00;
	
	function toHTML(){
		return '$'.$this->toString();
	}
	
	function toForm(){
		$element = new FormInput($this);
		print $element;
		return sprintf('%s', $element);
	}

	function toString(){
		return sprintf('%01.2f', $this->value);
	}

}

?>