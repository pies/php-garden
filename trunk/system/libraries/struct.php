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

class User extends Actor {
	private 
	$fields = array(
		'email'    => array(PROP_TEXT, '.+@.+\..+'),
		'password' => array(PROP_TEXT, '.{4,64}'),
		'created');
}

abstract class Actor {
	function create ();
	function read   ();
	function write  ();
	function reset  ();
}

class DataError       extends Error {};

class DefinitionError extends DataError {};
class ValidationError extends DataError {};

//class Data

abstract class Data {
	private $id     = null;
	private $data   = null;
	private $fields = array();

	function __construct ($id=null) {
		$this->id = $id;
		foreach ($this->fields as $name=>$attr) {
			$this->data[$name] = false;
		}
	}
	
	function __get ($name) {
		if ($this->id && ($this->data === null)) $this->load();
		return $this->data[$name];
	}
	
	function __set ($name, $value) {
		if (!isset($this->fields[$name])) {
			throw new UndefinedError("No such field defined: [$name]");
			return false;
		}
		
		$descr = $this->fields[$name];
		
		if (PROP_TEXT==$descr[0] && !preg_match("/^{$descr[1]}\$/", $value))
			throw new ValidationError("Value [$value] does not match [{$descr[1]}]"); 

		if (PROP_DATE==$descr[0] && !is_int($value))
			throw new ValidationError("Value [$value] does not an integer"); 
			
		return $this->data[$name] = $value;
	}
	
	function add () { // TODO
	}
	
	function load () { // TODO
	}
	
	function save   ();
	function remove ();
	function set    (Array $data);
	function map    (Filter $filter);
}

abstract class Action {
	function run ();
	function run_before (Action $action);
	function run_after  (Action $action);
	function run_every  (String $period);
	function undo ();
	function redo ();
}

abstract class Set { // Collection
	function add ();
	function get ();
	function index ();
	function remove ();
	function each (Action $action);
}

abstract class Filter {
	function __construct ();
	function only   (Limit $limit);
	function except (Limit $limit);
}

abstract class Limit {
	function test   (Item $item);
}






class StoredAlert extends Alert {};
class StoredError extends Error {};

class StoredNoTableError extends StoredError {};

class Stored
{
	/**
	 * Connection to the database.
	 * @var SQLiteDatabase
	 */
	public static $conn = null;
	
	/**
	 * Table name
	 * @var string
	 */
	private $name = null;
	
	public  function __construct($name) {
		$this->connect();
		$this->name = (string) $name;
	}

	public  function select($filter) {
		$sql = "SELECT * FROM {$this->name} WHERE ".$this->filterToSql($filter);
		$res = self::$conn->query($sql);
		
		if ($res === false) {
			$error = self::$conn->errorInfo();
			switch ($error[0]) {
				case 'HY000': throw new StoredNoTableError("Table [{$this->name}] doesn't exists in [{$sql}]"); break;
				default:      throw new StoredError("Error {$error[2]} ({$error[0]}/{$error[1]}) in query $sql");
			}
			return false;
		}

		return $res->fetch();
	}
	
	public  function insert($data) { // TODO
	}
	
	public  function update($filter, $data) { // TODO
	}

	private function connect() {
		if (self::$conn) return true;
		self::$conn = new PDO('sqlite:'.DIR_DATA.'/data.db');
	}
	
	private function filterToSql($filter) {
		if (!is_array($filter)) $filter = array('id'=>$filter);
		return join(' AND ', array_map(array($this,'pairToSql'), array_values($filter), array_keys($filter)));
	}
	
}







class SetAction  extends Action {};
class ItemAction extends Action {};
class DataAction extends Action {};
class ActionSet  extends Set {};
class ItemSet    extends Set {};
class DataSet    extends Set {};
class ActionItem extends Item {};
class SetItem    extends Item {};
class DataItem   extends Item {};
class ActionData extends Data {};
class SetData    extends Data {};
class ItemData   extends Data {};

class UserAction extends Action {};
class UserSet    extends Set {};
class UserItem   extends Item {};
class UserData   extends Data {};

class UserSetAction extends SetAction {};
class UserItemSet extends ItemSet {};
class UserActionData extends Data {};

?>