<?

class DateProp extends Prop {
	
	function fromString($value) {
		return strtotime($value);
	}
	
	function toString() {
		return date('Y-m-d H:i:s', $this->value);
	}
	
	function fromSQL($value) {
		$this->value = $value;
	}
	
	function toSQL() {
		return $this->value;
	}
}

?>