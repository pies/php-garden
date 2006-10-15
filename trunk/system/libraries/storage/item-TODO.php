<?php

/**
 * @author Michal Tatarynowicz <tatarynowicz@gmail.com>
 * @copyright 2006 Michal Tatarynowicz
 * @package Storage
 * @subpackage Database
 * @version $Revision: $
 * @license Public Domain (see system/licence.txt)
 */
class Item {

	private $_id = false;
	private $_type = false;

	private $_data = false;
	private $_modified = false;

	public function __construct($id=false, $type=false) {
	
		if ($type!==false) $this->_type = $type;
		elseif ($this->_type===false) $this->_type = underscore(get_class($this));
		if ($id!==false) $this->_id = $id;
		// TODO: PERHAPS $this->addCondition('id', ATTR_IS, $id);
	}

	public function __destruct() {
		if ($this->_modified) $this->save();
	}

	public function __get ($name) {
		if ($this->_data === false) $this->_load();
		return @$this->_data[$name];
	}
	
	public function __set ($name, $value) {
		return $name == 'id'? $this->_id = $value: $this->data[$name] = $value;
		$this->_modified = true;
	}








	private function getConditions () {
		return DatabaseAdapter::Filter($this->conditions);
	}

	private function addCondition ($name, $relation, $value) {
		$this->conditions[] = array($name, $relation, $value);
	}








	private function _load() {
		if (!$this->_id) return array();

		$this->_data = Data::store($this->_type)->select($this->_id);
		return $this->_data;
	}

	public function save ($data=array()) {
		$this->set($data);
		$store = Data::store($this->_type);
		$this->_id? 
			$store->insert($this->_data):
			$store->update($this->_id, $this->_data);
	}





	function Create ($data=array()) {
		$data['created'] = time();
		$data = array_merge($this->data, $data);
		return DB::insert($this->type, $data);
	}

	function Update ($data=array()) {
		$data['updated'] = time();
		$data = array_merge($this->data, $data);
		return DB::update($this->type, $data, $this->getConditions());
	}

	function Delete () {
		return DB::delete($this->type, $this->getConditions());
	}







	public function set ($data) {
		if (func_num_args()==2 && is_string($data)) {
			$args = func_get_args();
			$data = array($args[0]=>$args[1]);
		}
		$tmp = array_merge(is_array($this->data)? $this->data: array(), $data);
		if ($this->data == $tmp) return true;
		$this->data = $tmp;
		$this->_changed = true;
	}

	public function belongs_to ($item) {
		$this->set(array($item->foreign_key_name()=>$item->id));
	}

	public function foreign_key_name () {
		return $this->_name.'_id';
	}

	function has_multiple ($item, $item2) {
		$args = func_get_args();
		$item = $args[0];

		$name = $item->foreign_key_name();
		for ($ii=0;$ii<count($args);$ii++) {
			$this->set($name.'_'.($ii+1), $args[$ii]->id);
		}
	}

}

?>