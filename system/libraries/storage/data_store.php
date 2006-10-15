<?php
/** 
 * Implements a simple SQLite automatic storage system
 *
 * @author Michal Tatarynowicz <tatarynowicz@gmail.com>
 * @copyright 2006 Michal Tatarynowicz
 * @package Storage
 * @subpackage Database
 * @version $Revision: $
 * @license Public Domain (see system/licence.txt)
*/

class DataStoreAlert extends Alert {};
class DataStoreError extends Error {};

class DataStoreNoTableError extends DataStoreError {};

class DataStore
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
	
	public function __construct($name) {
		$this->connect();
		$this->name = (string) $name;
	}

	public function select($filter) {
		$sql = "SELECT * FROM {$this->name} WHERE ".$this->filterToSql($filter);
		$res = self::$conn->query($sql);
		
		if ($res === false) {
			$error = self::$conn->errorInfo();
			switch ($error[0]) {
				case 'HY000': throw new DataStoreNoTableError("Table [{$this->name}] doesn't exists in [{$sql}]"); break;
				default:      throw new DataStoreError("Error {$error[2]} ({$error[0]}/{$error[1]}) in query $sql");
			}
			return false;
		}

		return $res->fetch();
	}
	
	public function insert($data) {
		// TODO
	}
	
	public function update($filter, $data) {
		// TODO
	}

	private function connect() {
		if (self::$conn) return true;
		self::$conn = new PDO('sqlite:'.DIR_DATA.'/data.db');
	}
	
	private function filterToSql($filter) {
		if (!is_array($filter)) $filter = array('id'=>$filter);
		// TODO: REFACTOR TO USE ENUM
		return join(' AND ', array_map(array($this,'pairToSql'), array_values($filter), array_keys($filter)));
	}
	
}

?>