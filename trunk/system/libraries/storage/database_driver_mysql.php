<?php
/**
 * "Old" (i.e. not-PDO) Mysql Database Driver
 * 
 * @author Michal Tatarynowicz <tatarynowicz@gmail.com>
 * @copyright 2006 Michal Tatarynowicz
 * @package Storage
 * @subpackage Database
 * @version $Revision: $
 * @license Public Domain (see system/licence.txt)
 */

     class DatabaseDriverMysql 
   extends DatabaseDriver 
implements DatabaseDriverApi 
{
	function connect (Array $config) {
		$host = $config['host'] . ($config['port']? ':'.$config['port']: '');
		return mysql_connect($host, $config['login'], $config['password']) &&
		       mysql_select_db($config['database']);
	}

	function prepare ($var) {
		return "'".mysql_escape_string($str)."'";
	}

	function execute ($query) {
		return mysql_query($query, $this->connection);
	}

	function getError () {
		return mysql_errno($this->connection).': '.mysql_error($this->connection);
	}
	
	function getModified () {
		return mysql_affected_rows($this->connection);
	}
	
	function getInsertId () {
		return mysql_insert_id($this->connection);
	}

	function _fetch_row ($res, $mode=MYSQL_ASSOC) {
		return mysql_fetch_array($res, $mode);
	}
}

     class DatabaseResultsetMysql 
   extends DatabaseResultset 
implements DatabaseResultsetApi
{
	private $result, $marker = 0, $count = 0;
	
	public function __construct($result) {
		$this->result = $result;
		$this->count = mysql_num_rows($this->result);
	}
	
	public function rewind() {
		$this->marker = 0;
	}
	
	public function current() {
		return mysql_result($this->result, $this->marker);
	}
	
	public function key() {
		return $this->marker;
	}
	
	public function valid() {
		return $this->marker < $this->count;
	}
	
	public function next() {
		$this->marker++;
		return $this->valid()? $this->current(): false;
	}
}

?>