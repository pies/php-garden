<?php
/**
 * Name
 * --------------------------------------------------------------------------
 * @author Michal Tatarynowicz <tatarynowicz@gmail.com>
 * @copyright 2006 Michal Tatarynowicz
 * @package Package
 * @version $Revision: $
 * @license Public Domain (see system/licence.txt)
*/

function HTML(){ 
	return new HtmlFragment(); 
}

class HtmlFragment {
	
	private $stacked = '';

	function UL ($content, $params=array()) {
		$content = enum($content)->format('<li>%2$s</li>')->join("\n");
		$this->__call('UL', array($content, $params));
		return $this;
	}
	
	function __call($name, $arguments=array()) {
		$name = strtolower($name);

		$content = array_shift($arguments);
		$params = count($arguments) && count($arguments[0])?
			' '.enum(array_shift($arguments))->format('%s="%s"')->join(' '):
			'';

		$type = (substr($name, -1) == '_')? 'opening': (substr($name, -1) == '_'? 'closing': 'single');

		/** Opening tag */
		if (substr($name, -1) == '_') {
			$name = substr($name, 0, -1);
			$this->stacked .= "<$name$params>\n"; 
		}
		/** Closing tag */
		elseif ($name[0] == '_') {
			$name = substr($name, 1);
			$this->stacked .= "</$name>\n";
		}
		/** Self-contained */
		else {
			$this->stacked .= "<$name$params>\n$content\n</$name>\n"; 
		}
		
		return $this;
	}

	function HTML(){
		$output = $this->stacked;
		$this->stacked = '';
		return $output;
	}
	
	function __toString (){
		return $this->HTML();
	}
	
	function __help(){
		return 'Programatically generates HTML fragments. ';
	}
}

?>